<section class="error-card" aria-labelledby="titre-erreur">
    <div class="error-code">405</div>
    <h1 id="titre-erreur">Méthode non autorisée</h1>
    <p>La méthode HTTP utilisée n’est pas autorisée pour cette page.</p>
    <?php if ($methode !== ''): ?>
        <p>Méthode employée : <code><?= htmlspecialchars($methode, ENT_QUOTES, 'UTF-8') ?></code></p>
    <?php endif; ?>
    <?php if ($methodesAutorisees !== []): ?>
        <p>Méthodes acceptées : <code><?= htmlspecialchars(implode(', ', $methodesAutorisees), ENT_QUOTES, 'UTF-8') ?></code></p>
    <?php endif; ?>
    <div class="form-actions">
        <a href="index.php?action=bienvenue" class="btn btn-primary">Retour à la page de bienvenue</a>
    </div>
</section>
