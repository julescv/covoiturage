<?php

/**
 * Affiche un fragment de views/ dans le layout commun.
 * Le nom de la vue et les données sont choisis par le contrôleur.
 */
function afficherVue(string $vue, array $donnees = []): void
{
    extract($donnees, EXTR_SKIP);
    $titre = $titre ?? 'Application de covoiturage';
    $pageCourante = $pageCourante ?? '';

    ob_start();
    require __DIR__ . '/../../views/' . $vue . '.php';
    $contenu = ob_get_clean();

    require __DIR__ . '/../../views/layout.php';
}
