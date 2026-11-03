<?php

/**
 * Suite de tests automatisés pour US-01 — Créer mon compte
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Model/parametreModele.php';
require_once __DIR__ . '/../src/Model/utilisateurModele.php';
require_once __DIR__ . '/../src/Controller/inscriptionControleur.php';
require_once __DIR__ . '/../src/Router/router.php';

$connexion = obtenirConnexion();

function assertTest(bool $condition, string $titre): void
{
    if ($condition) {
        echo " [OK] " . $titre . "\n";
    } else {
        echo " [ECHEC] " . $titre . "\n";
        exit(1);
    }
}

// 0. Réinitialiser la base pour les tests
$connexion->exec('DELETE FROM utilisateurs');
$connexion->exec('DELETE FROM parametres');
$connexion->exec("INSERT INTO parametres (code) VALUES ('COVOIT2026')");

echo "=== Démarrage des tests US-01 ===\n\n";

// --- CA01 : Affichage du formulaire ---
$_GET = ['action' => 'inscription'];
$_SERVER['REQUEST_METHOD'] = 'GET';
$_POST = [];

ob_start();
routerRequete($connexion);
$html = ob_get_clean();

assertTest(str_contains($html, 'Créer un compte'), 'CA01 : Le titre Créer un compte est présent');
assertTest(str_contains($html, 'name="nom"'), 'CA01 : Le champ nom est présent');
assertTest(str_contains($html, 'name="prenom"'), 'CA01 : Le champ prénom est présent');
assertTest(str_contains($html, 'name="email"'), 'CA01 : Le champ email est présent');
assertTest(str_contains($html, 'name="telephone"'), 'CA01 : Le champ téléphone est présent');
assertTest(str_contains($html, 'type="password" id="mot_de_passe"'), 'CA01 : Le mot de passe est masqué');
assertTest(str_contains($html, 'name="code_inscription"'), 'CA01 : Le champ code d’inscription est présent');
assertTest(str_contains($html, 'Format attendu : 10 chiffres'), 'CA01 : Le format attendu de téléphone est indiqué');
assertTest(!str_contains($html, 'COVOIT2026'), 'CA01 : Le code attendu n’est jamais affiché');
assertTest(str_contains($html, '<nav class="nav-bar"'), 'CA01 : La navigation contient le lien');
assertTest(str_contains($html, 'action=inscription') && str_contains($html, 'Créer un compte</a>'), 'CA01 : Le lien Créer un compte est dans la navigation');

// --- CA02 : Inscription valide ---
$_GET = ['action' => 'inscription'];
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'nom' => 'Dupont',
    'prenom' => 'Jean',
    'email' => 'jean.dupont@lycee.test',
    'telephone' => '06 11 22 33 44',
    'mot_de_passe' => 'MonSecret123!',
    'code_inscription' => 'COVOIT2026',
];

ob_start();
routerRequete($connexion);
$html = ob_get_clean();

$user = trouverUtilisateurParEmail($connexion, 'jean.dupont@lycee.test');
assertTest($user !== null, 'CA02 : Compte créé en base');
assertTest($user['telephone'] === '06 11 22 33 44', 'CA02 : Numéro de téléphone bien enregistré');
assertTest(password_verify('MonSecret123!', $user['mot_de_passe']), 'CA02 / CA13 : Mot de passe correctement haché');
assertTest(str_contains($html, 'Compte créé avec succès'), 'CA02 : Message de confirmation de création');
assertTest(str_contains($html, 'action=connexion') && str_contains($html, 'Se connecter'), 'CA02 : Propose le lien pour se connecter');
assertTest(empty($_SESSION['utilisateur']), 'CA02 : L’utilisateur n’est pas connecté automatiquement');

// --- CA03 : Champs vides ou espaces ---
$_POST = [
    'nom' => '   ',
    'prenom' => '',
    'email' => '  ',
    'telephone' => '   ',
    'mot_de_passe' => '',
    'code_inscription' => '   ',
];
$compteAvant = (int) $connexion->query('SELECT COUNT(*) FROM utilisateurs')->fetchColumn();

ob_start();
routerRequete($connexion);
$html = ob_get_clean();

$compteApres = (int) $connexion->query('SELECT COUNT(*) FROM utilisateurs')->fetchColumn();
assertTest($compteAvant === $compteApres, 'CA03 : Aucun compte créé lors de champs vides');
assertTest(str_contains($html, 'Le champ nom est obligatoire'), 'CA03 : Erreur nom obligatoire');
assertTest(str_contains($html, 'Le champ prénom est obligatoire'), 'CA03 : Erreur prénom obligatoire');
assertTest(str_contains($html, 'Le champ adresse électronique est obligatoire'), 'CA03 : Erreur adresse obligatoire');
assertTest(str_contains($html, 'Le champ numéro de téléphone est obligatoire'), 'CA03 : Erreur téléphone obligatoire');
assertTest(str_contains($html, 'Le champ mot de passe est obligatoire'), 'CA03 : Erreur mot de passe obligatoire');
assertTest(str_contains($html, 'Code d’inscription obligatoire'), 'CA03 / CA07 : Erreur code obligatoire');

// --- CA04 : Adresse incorrecte ---
$_POST = [
    'nom' => 'Martin',
    'prenom' => 'Marie',
    'email' => 'marie.exemple',
    'telephone' => '06 00 00 00 01',
    'mot_de_passe' => 'Secret123',
    'code_inscription' => 'COVOIT2026',
];
$compteAvant = (int) $connexion->query('SELECT COUNT(*) FROM utilisateurs')->fetchColumn();

ob_start();
routerRequete($connexion);
$html = ob_get_clean();

$compteApres = (int) $connexion->query('SELECT COUNT(*) FROM utilisateurs')->fetchColumn();
assertTest($compteAvant === $compteApres, 'CA04 : Aucun compte créé avec adresse invalide');
assertTest(str_contains($html, 'Veuillez saisir une adresse électronique valide'), 'CA04 : Message demandant adresse valide');

// --- CA05 : Adresse déjà existante ---
$_POST = [
    'nom' => 'Dupont',
    'prenom' => 'Autre',
    'email' => 'jean.dupont@lycee.test', // Déjà utilisé par CA02
    'telephone' => '06 99 88 77 66',
    'mot_de_passe' => 'AutreSecret123',
    'code_inscription' => 'COVOIT2026',
];
$compteAvant = (int) $connexion->query('SELECT COUNT(*) FROM utilisateurs')->fetchColumn();

ob_start();
routerRequete($connexion);
$html = ob_get_clean();

$compteApres = (int) $connexion->query('SELECT COUNT(*) FROM utilisateurs')->fetchColumn();
assertTest($compteAvant === $compteApres, 'CA05 : Refus de doublon d’adresse électronique');
assertTest(str_contains($html, 'Cette adresse électronique est déjà utilisée'), 'CA05 : Message explicite doublon');

// --- CA06 : Conservation des champs hors mot de passe et code ---
$_POST = [
    'nom' => 'Durand',
    'prenom' => 'Sophie',
    'email' => 'sophie.durand@lycee.test',
    'telephone' => '06 55 44 33 22',
    'mot_de_passe' => 'MonSecret',
    'code_inscription' => 'MAUVAIS_CODE',
];

ob_start();
routerRequete($connexion);
$html = ob_get_clean();

assertTest(str_contains($html, 'value="Durand"'), 'CA06 : Nom conservé dans le formulaire');
assertTest(str_contains($html, 'value="Sophie"'), 'CA06 : Prénom conservé dans le formulaire');
assertTest(str_contains($html, 'value="sophie.durand@lycee.test"'), 'CA06 : Email conservé dans le formulaire');
assertTest(str_contains($html, 'value="06 55 44 33 22"'), 'CA06 : Téléphone conservé dans le formulaire');
assertTest(!str_contains($html, 'value="MonSecret"'), 'CA06 : Mot de passe jamais conservé');
assertTest(!str_contains($html, 'value="MAUVAIS_CODE"'), 'CA06 : Code jamais conservé');
assertTest(!str_contains($html, 'MonSecret') && !str_contains($html, 'MAUVAIS_CODE'), 'CA06 : Mot de passe et code absents des messages d’erreur');

// --- CA07 : Mauvais code ---
assertTest(str_contains($html, 'Code d’inscription incorrect'), 'CA07 : Message Code d’inscription incorrect');
assertTest(!str_contains($html, 'COVOIT2026'), 'CA07 : Ne donne jamais le bon code');

// --- CA08 : Deux enseignants avec le même code ---
$_POST = [
    'nom' => 'Curie',
    'prenom' => 'Marie',
    'email' => 'marie.curie@lycee.test',
    'telephone' => '06 01 02 03 04',
    'mot_de_passe' => 'Radioactivite1!',
    'code_inscription' => 'COVOIT2026',
];
ob_start();
routerRequete($connexion);
ob_end_clean();

$_POST = [
    'nom' => 'Langevin',
    'prenom' => 'Paul',
    'email' => 'paul.langevin@lycee.test',
    'telephone' => '06 05 06 07 08',
    'mot_de_passe' => 'Magnetisme2!',
    'code_inscription' => 'COVOIT2026',
];
ob_start();
routerRequete($connexion);
ob_end_clean();

$u1 = trouverUtilisateurParEmail($connexion, 'marie.curie@lycee.test');
$u2 = trouverUtilisateurParEmail($connexion, 'paul.langevin@lycee.test');
assertTest($u1 !== null && $u2 !== null, 'CA08 : Deux comptes créés avec le même code d’inscription');

// --- CA10 : Code non configuré ou impossible à lire ---
$connexion->exec('DELETE FROM parametres');
$_POST = [
    'nom' => 'Testeur',
    'prenom' => 'SansCode',
    'email' => 'sanscode@lycee.test',
    'telephone' => '06 12 34 56 78',
    'mot_de_passe' => 'Secret123',
    'code_inscription' => 'COVOIT2026',
];
$compteAvant = (int) $connexion->query('SELECT COUNT(*) FROM utilisateurs')->fetchColumn();

ob_start();
routerRequete($connexion);
$html = ob_get_clean();

$compteApres = (int) $connexion->query('SELECT COUNT(*) FROM utilisateurs')->fetchColumn();
assertTest($compteAvant === $compteApres, 'CA10 : Aucun compte créé quand code non configuré');
assertTest(str_contains($html, 'Le service d’inscription est indisponible. Réessayez plus tard.'), 'CA10 : Message service indisponible');

// Remettre le code
$connexion->exec("INSERT INTO parametres (code) VALUES ('NOUVEAU_CODE_2026')");

// --- CA12 : Changement de code dans la base ---
// L'ancien code COVOIT2026 doit être rejeté
$_POST = [
    'nom' => 'Testeur',
    'prenom' => 'AncienCode',
    'email' => 'anciencode@lycee.test',
    'telephone' => '06 12 34 56 78',
    'mot_de_passe' => 'Secret123',
    'code_inscription' => 'COVOIT2026',
];
ob_start();
routerRequete($connexion);
$html = ob_get_clean();
assertTest(str_contains($html, 'Code d’inscription incorrect'), 'CA12 : Ancien code refusé');

// Le nouveau code permet de s'inscrire
$_POST['code_inscription'] = 'NOUVEAU_CODE_2026';
ob_start();
routerRequete($connexion);
$html = ob_get_clean();
assertTest(str_contains($html, 'Compte créé avec succès'), 'CA12 : Nouveau code accepté');

// Les comptes créés précédemment restent en base
assertTest(trouverUtilisateurParEmail($connexion, 'marie.curie@lycee.test') !== null, 'CA12 : Les anciens comptes restent valides');

// --- CA14 : Numéro de téléphone invalide (abcdef) ---
$_POST = [
    'nom' => 'Testeur',
    'prenom' => 'TelInvalide',
    'email' => 'telinvalide@lycee.test',
    'telephone' => 'abcdef',
    'mot_de_passe' => 'Secret123',
    'code_inscription' => 'NOUVEAU_CODE_2026',
];
$compteAvant = (int) $connexion->query('SELECT COUNT(*) FROM utilisateurs')->fetchColumn();

ob_start();
routerRequete($connexion);
$html = ob_get_clean();

$compteApres = (int) $connexion->query('SELECT COUNT(*) FROM utilisateurs')->fetchColumn();
assertTest($compteAvant === $compteApres, 'CA14 : Aucun compte créé avec tel=abcdef');
assertTest(str_contains($html, 'Le numéro de téléphone n’est pas valide') && str_contains($html, 'Format attendu'), 'CA14 : Message rappelant format attendu');

// --- Scénario de démonstration officiel (US-01) ---
echo "\n=== Exécution du scénario de démonstration officiel ===\n";
$connexion->exec('DELETE FROM utilisateurs');
$connexion->exec('DELETE FROM parametres');
$connexion->exec("INSERT INTO parametres (code) VALUES ('COVOIT2026')");

// 1. Créer le compte de Marie avec marie@example.test, le numéro fictif 06 00 00 00 01 et le bon code.
$_POST = [
    'nom' => 'Dupont',
    'prenom' => 'Marie',
    'email' => 'marie@example.test',
    'telephone' => '06 00 00 00 01',
    'mot_de_passe' => 'MotDePasseMarie!',
    'code_inscription' => 'COVOIT2026',
];
ob_start();
routerRequete($connexion);
$html = ob_get_clean();
assertTest(str_contains($html, 'Compte créé avec succès') && trouverUtilisateurParEmail($connexion, 'marie@example.test') !== null, 'Démo 1 : Inscription Marie réussie');

// 2. Créer le compte de Paul avec paul@example.test, le numéro fictif 06 00 00 00 02 et le même code.
$_POST = [
    'nom' => 'Durand',
    'prenom' => 'Paul',
    'email' => 'paul@example.test',
    'telephone' => '06 00 00 00 02',
    'mot_de_passe' => 'MotDePassePaul!',
    'code_inscription' => 'COVOIT2026',
];
ob_start();
routerRequete($connexion);
$html = ob_get_clean();
assertTest(str_contains($html, 'Compte créé avec succès') && trouverUtilisateurParEmail($connexion, 'paul@example.test') !== null, 'Démo 2 : Inscription Paul réussie');

// 3. Essayer de créer un autre compte avec marie@example.test : l’inscription doit être refusée.
$_POST = [
    'nom' => 'Autre',
    'prenom' => 'Marie',
    'email' => 'marie@example.test',
    'telephone' => '06 00 00 00 03',
    'mot_de_passe' => 'AutrePasse!',
    'code_inscription' => 'COVOIT2026',
];
ob_start();
routerRequete($connexion);
$html = ob_get_clean();
assertTest(str_contains($html, 'Cette adresse électronique est déjà utilisée'), 'Démo 3 : Inscription doublon Marie refusée');

// 4. Essayer avec un mauvais code : aucun compte supplémentaire ne doit être créé.
$nbComptes = (int) $connexion->query('SELECT COUNT(*) FROM utilisateurs')->fetchColumn();
$_POST = [
    'nom' => 'Test',
    'prenom' => 'MauvaisCode',
    'email' => 'mauvaiscode@example.test',
    'telephone' => '06 00 00 00 04',
    'mot_de_passe' => 'Passe123!',
    'code_inscription' => 'FAUX_CODE',
];
ob_start();
routerRequete($connexion);
$html = ob_get_clean();
assertTest(str_contains($html, 'Code d’inscription incorrect') && ((int) $connexion->query('SELECT COUNT(*) FROM utilisateurs')->fetchColumn() === $nbComptes), 'Démo 4 : Mauvais code refusé sans création');

// 5. Essayer avec un téléphone vide, puis abcdef : les deux inscriptions doivent être refusées et le champ à corriger indiqué.
$_POST = [
    'nom' => 'Test',
    'prenom' => 'TelVide',
    'email' => 'telvide@example.test',
    'telephone' => '   ',
    'mot_de_passe' => 'Passe123!',
    'code_inscription' => 'COVOIT2026',
];
ob_start();
routerRequete($connexion);
$html = ob_get_clean();
assertTest(str_contains($html, 'Le champ numéro de téléphone est obligatoire'), 'Démo 5a : Téléphone vide refusé');

$_POST['telephone'] = 'abcdef';
ob_start();
routerRequete($connexion);
$html = ob_get_clean();
assertTest(str_contains($html, 'Le numéro de téléphone n’est pas valide'), 'Démo 5b : Téléphone abcdef refusé');

echo "\n Tous les critères d’acceptation et le scénario de démonstration sont validés avec succès !\n";
