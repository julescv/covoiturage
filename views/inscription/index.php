<?php if ($succes): ?>
    <section aria-labelledby="titre-succes">
        <h1 id="titre-succes">Compte créé avec succès</h1>
        <div class="alert alert-success" role="status">
            <p>Votre compte a été créé avec succès avec votre numéro de téléphone. Vous pouvez maintenant vous connecter.</p>
        </div>
        <p>Utilisez votre adresse électronique et votre mot de passe pour vous connecter.</p>
        <div class="form-actions">
            <a href="index.php?action=connexion" class="btn btn-primary">Se connecter</a>
        </div>
    </section>
<?php else: ?>
    <section aria-labelledby="titre-inscription">
        <h1 id="titre-inscription">Créer un compte</h1>

        <?php if (!empty($erreurs)): ?>
            <div class="alert alert-error" role="alert">
                <p><strong>Des erreurs sont survenues :</strong></p>
                <ul>
                    <?php foreach ($erreurs as $erreur): ?>
                        <li><?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <p class="form-notice">Tous les champs sont obligatoires.</p>

        <form method="post" action="index.php?action=inscription" novalidate>
            <div class="form-group">
                <label for="nom">Nom <span class="champ-obligatoire" aria-hidden="true">*</span></label>
                <input type="text" id="nom" name="nom" class="form-control" value="<?= htmlspecialchars($nom ?? '', ENT_QUOTES, 'UTF-8') ?>" required autocomplete="family-name">
            </div>

            <div class="form-group">
                <label for="prenom">Prénom <span class="champ-obligatoire" aria-hidden="true">*</span></label>
                <input type="text" id="prenom" name="prenom" class="form-control" value="<?= htmlspecialchars($prenom ?? '', ENT_QUOTES, 'UTF-8') ?>" required autocomplete="given-name">
            </div>

            <div class="form-group">
                <label for="email">Adresse électronique <span class="champ-obligatoire" aria-hidden="true">*</span></label>
                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8') ?>" required autocomplete="email">
                <small class="form-text">Elle servira d’identifiant pour vous connecter.</small>
            </div>

            <div class="form-group">
                <label for="telephone">Numéro de téléphone <span class="champ-obligatoire" aria-hidden="true">*</span></label>
                <input type="tel" id="telephone" name="telephone" class="form-control" value="<?= htmlspecialchars($telephone ?? '', ENT_QUOTES, 'UTF-8') ?>" aria-describedby="aide-telephone" required autocomplete="tel">
                <small id="aide-telephone" class="form-text">Format attendu : 10 chiffres (ex. 06 00 00 00 00 ou 0600000000).</small>
            </div>

            <div class="form-group">
                <label for="mot_de_passe">Mot de passe <span class="champ-obligatoire" aria-hidden="true">*</span></label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control" required autocomplete="new-password">
                <small class="form-text">Mot de passe personnel masqué pendant la saisie.</small>
            </div>

            <div class="form-group">
                <label for="code_inscription">Code d’inscription <span class="champ-obligatoire" aria-hidden="true">*</span></label>
                <input type="password" id="code_inscription" name="code_inscription" class="form-control" required autocomplete="off">
                <small class="form-text">Code commun communiqué par le lycée.</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Créer mon compte</button>
            </div>
        </form>
    </section>
<?php endif; ?>
