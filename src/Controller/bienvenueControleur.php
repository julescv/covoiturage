<?php

require_once __DIR__ . '/../View/view.php';

function afficherBienvenue(PDO $connexion): void
{
    afficherVue('bienvenue/index', [
        'titre' => 'Application de covoiturage',
        'pageCourante' => 'bienvenue',
    ]);
}
