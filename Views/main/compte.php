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
        <!-- Carte 1 : Informations du profil -->
        <div class="formContainer profile-card-box">
            <div>
                <h3><i class="material-icons">person</i> Informations du profil</h3>
                <p>
                    <b>Identifiant :</b> <?php echo htmlspecialchars($userInfo['username'] ?? 'Marsy', ENT_QUOTES, 'UTF-8'); ?><br>
                    <b>Rôle :</b> <?php echo htmlspecialchars($userInfo['role'] ?? 'user', ENT_QUOTES, 'UTF-8'); ?><br>
                    <b>Membre depuis :</b> <?php echo htmlspecialchars($userInfo['created_at'] ?? '29/10/2026', ENT_QUOTES, 'UTF-8'); ?>
                </p>
            </div>
            <form method="POST" action="?url=compte/updateEmail">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                <label for="email"><b>Adresse Email</b></label>
                <input type="email" id="email" name="email" class="inputMonEspace" required
                    value="<?php echo htmlspecialchars($userInfo['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <div class="boutonAligne">
                    <button type="submit" class="btnBase vert">
                        <i class="material-icons">save</i>
                        <span class="spanIconText">Mettre à jour l'email</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Carte 2 : Activité & Stockage Multimédia -->
        <div class="formContainer profile-card-box">
            <div>
                <h3><i class="material-icons">analytics</i> Activité & Stockage</h3>
                <p>
                    <b>Total des éléments :</b> <?php echo (int)($stats['total_items'] ?? 0); ?> média(s)<br>
                    <b>Espace multimédia occupé :</b> <?php echo number_format((float)($stats['storage_mb'] ?? 0), 2, ',', ' '); ?> Mo<br>
                    <b>Dernière connexion :</b> <?php echo !empty($userInfo['last_login']) ? date('d/m/Y à H:i', strtotime($userInfo['last_login'])) : 'Session en cours'; ?>
                </p>
            </div>
            <div>
                <p><b>Accès rapide à la création :</b></p>
                <div class="boutonAligne">
                    <a href="?url=texte/list" class="btnBase theme" title="Nouvelle note texte">
                        <i class="material-icons">note_add</i>
                        <span class="spanIconText">Note</span>
                    </a>
                    <a href="?url=audio/list#record" class="btnBase theme" title="Nouvelle note vocale">
                        <i class="material-icons">mic</i>
                        <span class="spanIconText">Vocal</span>
                    </a>
                    <a href="?url=photo/list" class="btnBase theme" title="Scanner une photo OCR">
                        <i class="material-icons">document_scanner</i>
                        <span class="spanIconText">OCR</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Carte 3 : Sécurité & Mot de passe -->
        <div class="formContainer profile-card-box">
            <div>
                <h3><i class="material-icons">lock</i> Sécurité & Mot de passe</h3>
            </div>
            <form method="POST" action="?url=compte/updatePassword">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                <label for="current_password"><b>Mot de passe actuel</b></label>
                <input type="password" id="current_password" name="current_password" class="inputMonEspace" required>

                <label for="new_password"><b>Nouveau mot de passe</b></label>
                <input type="password" id="new_password" name="new_password" class="inputMonEspace" required>

                <label for="confirm_password"><b>Confirmer le nouveau mot de passe</b></label>
                <input type="password" id="confirm_password" name="confirm_password" class="inputMonEspace" required>

                <div class="boutonAligne">
                    <button type="submit" class="btnBase orange">
                        <i class="material-icons">vpn_key</i>
                        <span class="spanIconText">Changer le mot de passe</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>