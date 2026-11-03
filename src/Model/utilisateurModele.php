<?php

/**
 * Recherche un utilisateur par son adresse électronique (insensible à la casse).
 */
function trouverUtilisateurParEmail(PDO $connexion, string $email): ?array
{
    $requete = $connexion->prepare('SELECT * FROM utilisateurs WHERE LOWER(email) = LOWER(:email) LIMIT 1');
    $requete->execute([':email' => trim($email)]);
    $utilisateur = $requete->fetch(PDO::FETCH_ASSOC);

    return $utilisateur !== false ? $utilisateur : null;
}

/**
 * Enregistre un nouvel utilisateur dans la base de données.
 * Le mot de passe est haché avant l'insertion.
 * Retourne l'identifiant généré.
 */
function creerUtilisateur(PDO $connexion, array $donnees): int
{
    $requete = $connexion->prepare(
        'INSERT INTO utilisateurs (nom, prenom, email, telephone, mot_de_passe)
         VALUES (:nom, :prenom, :email, :telephone, :mot_de_passe)'
    );

    $motDePasseHash = password_hash($donnees['mot_de_passe'], PASSWORD_DEFAULT);

    $requete->execute([
        ':nom' => trim($donnees['nom']),
        ':prenom' => trim($donnees['prenom']),
        ':email' => trim($donnees['email']),
        ':telephone' => trim($donnees['telephone']),
        ':mot_de_passe' => $motDePasseHash,
    ]);

    return (int) $connexion->lastInsertId();
}
