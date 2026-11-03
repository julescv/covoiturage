<section class="error-card" aria-labelledby="titre-erreur">
    <div class="error-code">404</div>
    <h1 id="titre-erreur">Page introuvable</h1>
    <p>La page ou l’action demandée n’existe pas.</p>
    <?php if ($action !== ''): ?>
        <p>Action demandée : <code><?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8') ?></code></p>
    <?php endif; ?>
    <div class="form-actions">
        <a href="index.php?action=bienvenue" class="btn btn-primary">Retour à la page de bienvenue</a>
    </div>
</section>
