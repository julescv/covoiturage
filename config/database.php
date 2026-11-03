<?php

function obtenirConnexion(): PDO
{
    $dossierBase = __DIR__ . '/../database';
    $cheminBase = $dossierBase . '/covoiturage.sqlite';

    if (!is_dir($dossierBase)) {
        throw new RuntimeException('Le dossier database est absent.');
    }

    $connexion = new PDO('sqlite:' . $cheminBase);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $connexion->exec('PRAGMA foreign_keys = ON');

    return $connexion;
}
