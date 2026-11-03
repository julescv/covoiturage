<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Router/router.php';

$connexion = obtenirConnexion();
routerRequete($connexion);
