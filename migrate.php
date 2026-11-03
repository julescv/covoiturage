<?php

// Ce fichier est lancé depuis la racine de l'application : php migrate.php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Ce script doit être exécuté en ligne de commande.');
}

$connexion = null;
$migrationCourante = null;

try {
    require_once __DIR__ . '/config/database.php';

    // 1. Vérifier tous les fichiers avant de modifier la base.
    $dossierMigrations = __DIR__ . '/migrations';

    if (!is_dir($dossierMigrations)) {
        throw new RuntimeException('Le dossier migrations est absent ou illisible.');
    }

    $fichiers = glob($dossierMigrations . '/*.sql');

    if ($fichiers === false) {
        throw new RuntimeException('Impossible de parcourir le dossier migrations.');
    }

    if ($fichiers === []) {
        throw new RuntimeException('Aucun fichier SQL dans le dossier migrations.');
    }

    sort($fichiers, SORT_STRING);
    $numeros = [];
    $migrations = [];

    foreach ($fichiers as $fichier) {
        $nom = basename($fichier);

        if (preg_match('/^([0-9]{3})_[a-z][a-z0-9_]*\.sql$/', $nom, $correspondances) !== 1) {
            throw new RuntimeException("Nom de migration invalide : {$nom}");
        }

        $numero = $correspondances[1];

        if (isset($numeros[$numero])) {
            throw new RuntimeException("Numéro de migration utilisé deux fois : {$numero}");
        }

        if (!is_file($fichier)) {
            throw new RuntimeException("Fichier de migration illisible : {$nom}");
        }

        // L'échec de lecture est traité par un message explicite ci-dessous.
        $sql = @file_get_contents($fichier);

        if ($sql === false || trim($sql) === '') {
            throw new RuntimeException("Migration vide ou impossible à lire : {$nom}");
        }

        $numeros[$numero] = true;
        $migrations[$nom] = $sql;
    }

    // 2. Ouvrir la base et créer l'historique si nécessaire.
    $connexion = obtenirConnexion();
    $connexion->exec('CREATE TABLE IF NOT EXISTS migrations (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        migration TEXT NOT NULL UNIQUE,
        executed_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
    )');

    // 3. Lire l'historique des migrations déjà exécutées.
    $requete = $connexion->query('SELECT migration FROM migrations');
    $migrationsExecutees = $requete->fetchAll(PDO::FETCH_COLUMN);
    $requete->closeCursor();

    $enregistrerMigration = $connexion->prepare(
        'INSERT INTO migrations (migration) VALUES (:migration)'
    );
    $nombreAppliquees = 0;

    // 4. Appliquer chaque nouveau fichier avec son historique dans une transaction.
    foreach ($migrations as $nom => $sql) {
        if (in_array($nom, $migrationsExecutees, true)) {
            continue;
        }

        $migrationCourante = $nom;
        $connexion->beginTransaction();
        $connexion->exec($sql);
        $enregistrerMigration->execute(['migration' => $nom]);
        $connexion->commit();

        $nombreAppliquees++;
        $migrationCourante = null;
        fwrite(STDOUT, "Migration réussie : {$nom}\n");
    }

    if ($nombreAppliquees === 0) {
        fwrite(STDOUT, "Base de données déjà à jour.\n");
    } else {
        fwrite(STDOUT, "Nombre de migrations appliquées : {$nombreAppliquees}\n");
    }

    exit(0);
} catch (Throwable $erreur) {
    if ($connexion instanceof PDO && $connexion->inTransaction()) {
        $connexion->rollBack();
    }

    $contexte = $migrationCourante === null
        ? 'Erreur de migration'
        : "Erreur sur la migration {$migrationCourante}";

    fwrite(STDERR, $contexte . ' : ' . $erreur->getMessage() . "\n");
    exit(1);
}
