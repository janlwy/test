<?php

/**
 * @var \Session\SessionManager $session
 * @var array $userInfo
 * @var array $stats
 */
$session = $session ?? \Session\SessionManager::getInstance();
$userInfo = $userInfo ?? [];
$stats = $stats ?? [];
$session->ensureCsrfToken();
$csrfToken = $session->get('csrf_token');
?>

<section class="profile-dashboard">
    <h2>Bienvenue, <?php echo htmlspecialchars($userInfo['pseudo'] ?? $session->get('pseudo', ''), ENT_QUOTES, 'UTF-8'); ?></h2>

    <!-- Cartes de statistiques rapides -->
    <div class="stats-grid">
        <a href="?url=texte/list" class="stat-card">
            <i class="material-icons">auto_stories</i>
            <span class="stat-number"><?php echo (int)($stats['textes'] ?? 0); ?></span>
            <span class="stat-label">Notes Textes</span>
        </a>
        <a href="?url=audio/list" class="stat-card">
            <i class="material-icons">headphones</i>
            <span class="stat-number"><?php echo (int)($stats['audios'] ?? 0); ?></span>
            <span class="stat-label">Audios & Notes Vocales</span>
        </a>
        <a href="?url=photo/list" class="stat-card">
            <i class="material-icons">photo_camera_back</i>
            <span class="stat-number"><?php echo (int)($stats['photos'] ?? 0); ?></span>
            <span class="stat-label">Photos & Scans OCR</span>
        </a>
        <a href="?url=video/list" class="stat-card">
            <i class="material-icons">live_tv</i>
            <span class="stat-number"><?php echo (int)($stats['videos'] ?? 0); ?></span>
            <span class="stat-label">Vidéos</span>
        </a>
    </div>

    <div class="profile-forms-grid">
        <!-- Informations du compte & Email -->
        <div class="formContainer">
            <h3><i class="material-icons">person</i> Informations du profil</h3>
            <p>
                <b>Identifiant :</b> <?php echo htmlspecialchars($userInfo['pseudo'] ?? '', ENT_QUOTES, 'UTF-8'); ?><br>
                <b>Rôle :</b> <?php echo htmlspecialchars($userInfo['role'] ?? 'user', ENT_QUOTES, 'UTF-8'); ?><br>
                <b>Membre depuis :</b> <?php echo !empty($userInfo['created_at']) ? date('d/m/Y', strtotime($userInfo['created_at'])) : 'N/A'; ?>
            </p>
            <hr>
            <form method="POST" action="?url=compte/update">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="update_type" value="email">

                <label for="profile-email"><b>Adresse Email</b></label>
                <input type="email" id="profile-email" name="email" class="inputModal" required
                    value="<?php echo htmlspecialchars($userInfo['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    placeholder="votre.email@exemple.com">

                <div class="boutonAligne">
                    <button type="submit" class="btnBase vert">
                        <i class="material-icons">save</i>
                        <span class="spanIconText">Mettre à jour l'email</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Changement de mot de passe -->
        <div class="formContainer">
            <h3><i class="material-icons">lock</i> Sécurité & Mot de passe</h3>
            <form method="POST" action="?url=compte/update">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="update_type" value="password">

                <label for="current_mdp"><b>Mot de passe actuel</b></label>
                <input type="password" id="current_mdp" name="current_mdp" class="inputModal" required autocomplete="current-password">

                <label for="new_mdp"><b>Nouveau mot de passe</b></label>
                <input type="password" id="new_mdp" name="new_mdp" class="inputModal" required minlength="8" maxlength="72"
                    title="Au moins 8 caractères, 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial"
                    autocomplete="new-password">

                <label for="confirm_mdp"><b>Confirmer le nouveau mot de passe</b></label>
                <input type="password" id="confirm_mdp" name="confirm_mdp" class="inputModal" required minlength="8" maxlength="72"
                    autocomplete="new-password">

                <div class="boutonAligne">
                    <button type="submit" class="btnBase orange">
                        <i class="material-icons">key</i>
                        <span class="spanIconText">Changer le mot de passe</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>