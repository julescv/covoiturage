<?php

/**
 * Récupère le code commun d’inscription depuis la table parametres.
 * Retourne null si le code n’est pas configuré ou impossible à lire.
 */
function obtenirCodeInscription(PDO $connexion): ?string
{
    try {
        $requete = $connexion->query('SELECT code FROM parametres LIMIT 1');
        $ligne = $requete->fetch(PDO::FETCH_ASSOC);

        if ($ligne === false || !isset($ligne['code'])) {
            return null;
        }

        $code = trim((string)$ligne['code']);

        return $code !== '' ? $code : null;
    } catch (Throwable) {
        return null;
    }
}
