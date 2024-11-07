
<div>
	<form class="modalContenu Anime formFont" method="POST" action="?url=creation/create" enctype="multipart/form-data">

		<div class="formContainer">
			<h2>Création de votre compte</h2>
			<h3>Veuillez remplir ce formulaire pour créer votre profil.</h3>
			<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
			<hr>
			<?php if (isset($_SESSION['erreur'])): ?>
				<div class="error-message">
					<?php echo htmlspecialchars($_SESSION['erreur'], ENT_QUOTES, 'UTF-8');
					unset($_SESSION['erreur']); ?>
				</div>
			<?php endif; ?>

			<label for="pseudo"><b>Nom d'utilisateur</b></label>
			<input class="inputModal" type="text" placeholder="Entrer votre nom" name="pseudo" id="pseudo" value="" required
                   pattern="[a-zA-Z0-9_-]{3,20}" 
                   title="3 à 20 caractères alphanumériques, tirets et underscores autorisés"
                   maxlength="20"
                   autocomplete="username">

			<label for="mdp1"><b>Mot de passe</b></label>
			<input class="inputModal" type="password" placeholder="Entrez votre mot de passe" name="mdp1" id="mdp1" value="" required
                   pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,72}$"
                   title="Au moins 8 caractères, incluant au moins une lettre, un chiffre et un caractère spécial"
                   maxlength="72"
                   autocomplete="new-password">

			<label for="mdp2"><b>Répéter le mot de passe</b></label>
			<input class="inputModal" type="password" placeholder="Répétez le mot de passe" name="mdp2" id="mdp2" value="" required
                   pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,72}$"
                   title="Au moins 8 caractères, incluant au moins une lettre, un chiffre et un caractère spécial"
                   maxlength="72"
                   autocomplete="new-password"
                   oninput="validatePasswordMatch()">

			<br>
			<hr>

			<p>En créant un compte vous acceptez de vous soumettre à nos <a href="#">conditions</a>.</p>
			<button type="submit" class="validButton modalButton" name="creation" value="Creation" id="submitBtn" disabled>Création de compte</button>

			<script>
			function validatePasswordMatch() {
				const password1 = document.getElementById('mdp1').value;
				const password2 = document.getElementById('mdp2').value;
				const submitBtn = document.getElementById('submitBtn');
				
				if (password1 === password2 && password1.length >= 8) {
					submitBtn.disabled = false;
					document.getElementById('mdp2').setCustomValidity('');
				} else {
					submitBtn.disabled = true;
					document.getElementById('mdp2').setCustomValidity('Les mots de passe ne correspondent pas');
				}
			}

			// Ajouter les écouteurs d'événements
			document.getElementById('mdp1').addEventListener('input', validatePasswordMatch);
			document.getElementById('mdp2').addEventListener('input', validatePasswordMatch);
			</script>

			<a href="?url=accueil/index" class="cancelButton modalButton"><span>Annuler</span></a>

			<div class="formContainer signin">
				<p>Vous avez déjà un compte ? <a href="?url=connexion/index"> Me connecter à mon compte</a>.</p>
			</div>
		</div>
	</form>
</div>
