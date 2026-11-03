<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titre, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <header>
        <div class="brand">
            <div>
                <p class="brand-title">Covoiturage entre enseignants</p>
                <p class="subtitle">Partageons nos trajets entre collègues.</p>
            </div>
        </div>

        <nav class="nav-bar" aria-label="Navigation principale">
            <a href="index.php?action=bienvenue"<?= $pageCourante === 'bienvenue' ? ' class="active" aria-current="page"' : '' ?>>Bienvenue</a>
            <a href="index.php?action=inscription"<?= in_array($pageCourante, ['inscription', 'creer-compte', 'creerCompte'], true) ? ' class="active" aria-current="page"' : '' ?>>Créer un compte</a>
        </nav>
    </header>

    <main>
        <?= $contenu ?>
    </main>
</body>

</html>
