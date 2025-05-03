
<div>
	<form class="modalContenu Anime formFont" method="POST" action="?url=connexion/connect" id="loginForm">
		<div class="formContainer ">
			<h2>Connexion à votre compte</h2>
			<h3>Veuillez remplir ce formulaire pour vous connecter à votre compte.</h3>
			<?php $token = \Fonctions\SessionManager::getInstance()->regenerateToken(); ?>
			<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">
			<hr>
			<?php if (isset($_SESSION['erreur'])): ?>
				<div class="error-message">
					<?php echo htmlspecialchars($_SESSION['erreur'], ENT_QUOTES, 'UTF-8');
					unset($_SESSION['erreur']); ?>
				</div>
			<?php endif; ?>
			
			<?php if (isset($_SESSION['message'])): ?>
				<div class="success-message">
					<?php echo htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8');
					unset($_SESSION['message']); ?>
				</div>
			<?php endif; ?>

			<label for="pseudo"><b>Nom d'utilisateur</b></label>
			<input class="inputModal" type="text" placeholder="Entrez votre nom" name="pseudo" id="pseudo" required>

			<label for="mdp"><b>Mot de passe</b></label>
			<input class="inputModal" type="password" placeholder="Entrez votre mot de passe" name="mdp" id="mdp" required
                   maxlength="72"
                   autocomplete="current-password">
			<div class="password-toggle">
				<input type="checkbox" id="showPassword" class="show-password">
				<label for="showPassword">Afficher le mot de passe</label>
			</div>

			<br>
			<hr>

			<p>En créant un compte vous acceptez de vous soumettre à nos <a href="#">conditions</a>.</p>
			<input type="hidden" name="role_redirect" value="1">
			<button type="submit" class="validButton modalButton" name="connexion" value="Connexion" id="loginButton">Connexion</button>

			<a href="?url=accueil/index" class="cancelButton modalButton"><span>Annuler</span></a>

			<div class="formContainer signin">
				<p>Vous n'avez pas de compte ? <a href="?url=creation/createUserForm">Création de compte</a>.</p>
			</div>
		</div>
	</form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Afficher/masquer le mot de passe
    const passwordInput = document.getElementById('mdp');
    const showPasswordCheckbox = document.getElementById('showPassword');
    
    if (showPasswordCheckbox && passwordInput) {
        showPasswordCheckbox.addEventListener('change', function() {
            passwordInput.type = this.checked ? 'text' : 'password';
        });
    }
    
    // Désactiver temporairement le bouton lors de la soumission pour éviter les doubles soumissions
    const loginForm = document.getElementById('loginForm');
    const loginButton = document.getElementById('loginButton');
    
    if (loginForm && loginButton) {
        loginForm.addEventListener('submit', function() {
            loginButton.disabled = true;
            loginButton.textContent = 'Connexion en cours...';
            
            // Réactiver le bouton après 5 secondes au cas où la soumission échoue
            setTimeout(function() {
                loginButton.disabled = false;
                loginButton.textContent = 'Connexion';
            }, 5000);
        });
    }
});
</script>

