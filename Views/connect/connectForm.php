
<div>
	<form class="modalContenu Anime formFont" method="POST" action="index.php?url=connexion/connect" id="loginForm" onsubmit="return validateLoginForm()">
		<div class="formContainer ">
			<h2>Connexion à votre compte</h2>
			<h3>Veuillez remplir ce formulaire pour vous connecter à votre compte.</h3>
			<?php $token = \Session\SessionManager::getInstance()->regenerateToken(); ?>
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
			<input type="hidden" name="connexion" value="Connexion">
			<button type="submit" class="validButton modalButton" id="loginButton">Connexion</button>
			<input type="hidden" name="submitted" value="1">

			<a href="?url=accueil/index" class="cancelButton modalButton"><span>Annuler</span></a>

			<div class="formContainer signin">
				<p>Vous n'avez pas de compte ? <a href="?url=creation/createUserForm">Création de compte</a>.</p>
			</div>
		</div>
	</form>
</div>

<script>
function validateLoginForm() {
    console.log('Validation du formulaire de connexion');
    
    // Récupérer les valeurs des champs
    const pseudo = document.getElementById('pseudo').value.trim();
    const mdp = document.getElementById('mdp').value;
    const csrfToken = document.querySelector('input[name="csrf_token"]').value;
    const loginButton = document.getElementById('loginButton');
    
    // Vérifier que tous les champs sont remplis
    if (!pseudo) {
        alert('Veuillez entrer votre nom d\'utilisateur');
        return false;
    }
    
    if (!mdp) {
        alert('Veuillez entrer votre mot de passe');
        return false;
    }
    
    if (!csrfToken) {
        console.error('Token CSRF manquant');
        alert('Erreur de sécurité: token CSRF manquant');
        return false;
    }
    
    // Désactiver le bouton pour éviter les doubles soumissions
    loginButton.disabled = true;
    loginButton.textContent = 'Connexion en cours...';
    
    // Afficher les données qui seront envoyées (sans le mot de passe)
    console.log('Soumission du formulaire avec les données:', {
        pseudo: pseudo,
        csrf_token: csrfToken,
        action: 'index.php?url=connexion/connect'
    });
    
    // Autoriser la soumission du formulaire
    return true;
}

document.addEventListener('DOMContentLoaded', function() {
    // Afficher/masquer le mot de passe
    const passwordInput = document.getElementById('mdp');
    const showPasswordCheckbox = document.getElementById('showPassword');
    
    if (showPasswordCheckbox && passwordInput) {
        showPasswordCheckbox.addEventListener('change', function() {
            passwordInput.type = this.checked ? 'text' : 'password';
        });
    }
});
</script>

