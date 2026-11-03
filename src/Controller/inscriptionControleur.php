<?php

require_once __DIR__ . '/../View/view.php';
require_once __DIR__ . '/../Model/parametreModele.php';
require_once __DIR__ . '/../Model/utilisateurModele.php';

/**
 * Affiche le formulaire d'inscription ou traite sa soumission.
 */
function traiterInscription(PDO $connexion, string $methode): void
{
    if ($methode === 'POST') {
        enregistrerInscription($connexion);
        return;
    }

    afficherFormulaireInscription($connexion);
}

/**
 * Affiche le formulaire de création de compte.
 */
function afficherFormulaireInscription(
    PDO $connexion,
    array $erreurs = [],
    array $donnees = [],
    bool $succes = false
): void {
    $codeAttendu = obtenirCodeInscription($connexion);
    $serviceDisponible = ($codeAttendu !== null);

    if (!$serviceDisponible && empty($erreurs)) {
        $erreurs[] = 'Le service d’inscription est indisponible. Réessayez plus tard.';
    }

    afficherVue('inscription/index', [
        'titre' => $succes ? 'Compte créé — Application de covoiturage' : 'Créer un compte — Application de covoiturage',
        'pageCourante' => 'inscription',
        'erreurs' => $erreurs,
        'nom' => $donnees['nom'] ?? '',
        'prenom' => $donnees['prenom'] ?? '',
        'email' => $donnees['email'] ?? '',
        'telephone' => $donnees['telephone'] ?? '',
        'succes' => $succes,
        'serviceDisponible' => $serviceDisponible,
    ]);
}

/**
 * Traite la validation et l'enregistrement du nouveau compte.
 */
function enregistrerInscription(PDO $connexion): void
{
    $nom = isset($_POST['nom']) ? (string) $_POST['nom'] : '';
    $prenom = isset($_POST['prenom']) ? (string) $_POST['prenom'] : '';
    $email = isset($_POST['email']) ? (string) $_POST['email'] : (isset($_POST['adresse_electronique']) ? (string) $_POST['adresse_electronique'] : '');
    $telephone = isset($_POST['telephone']) ? (string) $_POST['telephone'] : (isset($_POST['numero_telephone']) ? (string) $_POST['numero_telephone'] : '');
    $motDePasse = isset($_POST['mot_de_passe']) ? (string) $_POST['mot_de_passe'] : '';
    $codeInscription = isset($_POST['code_inscription']) ? (string) $_POST['code_inscription'] : (isset($_POST['code']) ? (string) $_POST['code'] : '');

    // CA10 : Vérification de la disponibilité du code en base
    $codeAttendu = obtenirCodeInscription($connexion);

    if ($codeAttendu === null) {
        afficherVue('inscription/index', [
            'titre' => 'Créer un compte — Application de covoiturage',
            'pageCourante' => 'inscription',
            'erreurs' => ['Le service d’inscription est indisponible. Réessayez plus tard.'],
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'telephone' => $telephone,
            'succes' => false,
            'serviceDisponible' => false,
        ]);
        return;
    }

    $erreurs = [];

    // CA03 : Champs obligatoires (vides ou espaces)
    if (trim($nom) === '') {
        $erreurs[] = 'Le champ nom est obligatoire.';
    }

    if (trim($prenom) === '') {
        $erreurs[] = 'Le champ prénom est obligatoire.';
    }

    if (trim($email) === '') {
        $erreurs[] = 'Le champ adresse électronique est obligatoire.';
    } elseif (!filter_var(trim($email), FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', trim($email))) {
        // CA04 : Adresse électronique incorrecte
        $erreurs[] = 'Veuillez saisir une adresse électronique valide.';
    } elseif (trouverUtilisateurParEmail($connexion, $email) !== null) {
        // CA05 : Adresse déjà existante
        $erreurs[] = 'Cette adresse électronique est déjà utilisée par un autre compte.';
    }

    // CA14 : Numéro de téléphone obligatoire et vérification du format
    if (trim($telephone) === '') {
        $erreurs[] = 'Le champ numéro de téléphone est obligatoire.';
    } elseif (!preg_match('/^(?:(?:\+|00)33|0)[1-9](?:[ .-]?\d{2}){4}$/', trim($telephone))) {
        $erreurs[] = 'Le numéro de téléphone n’est pas valide. Format attendu : 10 chiffres (ex. 06 00 00 00 00).';
    }

    if ($motDePasse === '') {
        $erreurs[] = 'Le champ mot de passe est obligatoire.';
    }

    // CA07 : Code obligatoire ou incorrect
    if (trim($codeInscription) === '') {
        $erreurs[] = 'Code d’inscription obligatoire.';
    } elseif (!hash_equals($codeAttendu, trim($codeInscription))) {
        $erreurs[] = 'Code d’inscription incorrect.';
    }

    // CA06 : En cas d'erreur, re-présenter le formulaire en conservant nom, prénom, email, téléphone.
    // Le mot de passe et le code d'inscription ne sont pas conservés et ne figurent pas dans les messages.
    if ($erreurs !== []) {
        afficherVue('inscription/index', [
            'titre' => 'Créer un compte — Application de covoiturage',
            'pageCourante' => 'inscription',
            'erreurs' => $erreurs,
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'telephone' => $telephone,
            'succes' => false,
            'serviceDisponible' => true,
        ]);
        return;
    }

    // CA11 : Transaction pour éviter les comptes incomplets en cas d'erreur
    try {
        $connexion->beginTransaction();

        creerUtilisateur($connexion, [
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'telephone' => $telephone,
            'mot_de_passe' => $motDePasse,
        ]);

        $connexion->commit();

        // CA02 : Compte créé, confirmation de réussite avec rappel du téléphone, lien vers connexion, pas connecté automatiquement.
        afficherVue('inscription/index', [
            'titre' => 'Compte créé — Application de covoiturage',
            'pageCourante' => 'inscription',
            'erreurs' => [],
            'nom' => '',
            'prenom' => '',
            'email' => '',
            'telephone' => '',
            'succes' => true,
            'serviceDisponible' => true,
        ]);
    } catch (PDOException $erreur) {
        if ($connexion->inTransaction()) {
            $connexion->rollBack();
        }

        if ($erreur->getCode() === '23000' || str_contains($erreur->getMessage(), 'UNIQUE')) {
            $erreurs[] = 'Cette adresse électronique est déjà utilisée par un autre compte.';
        } else {
            $erreurs[] = 'Un problème est survenu pendant l’enregistrement du compte. Réessayez plus tard.';
        }

        afficherVue('inscription/index', [
            'titre' => 'Créer un compte — Application de covoiturage',
            'pageCourante' => 'inscription',
            'erreurs' => $erreurs,
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'telephone' => $telephone,
            'succes' => false,
            'serviceDisponible' => true,
        ]);
    }
}
