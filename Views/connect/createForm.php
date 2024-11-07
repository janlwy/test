
<div>
	<form class="modalContenu Anime formFont" method="POST" action="?url=creation/create" enctype="multipart/form-data">

		<div class="formContainer">
			<h2>Création de votre compte</h2>
			<h3>Veuillez remplir ce formulaire pour créer votre profil.</h3>
			<?php $token = SessionManager::getInstance()->regenerateToken(); ?>
			<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">
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
			document.addEventListener('DOMContentLoaded', function() {
				const form = document.querySelector('form');
				const password1 = document.getElementById('mdp1');
				const password2 = document.getElementById('mdp2');
				const submitBtn = document.getElementById('submitBtn');
				const pseudo = document.getElementById('pseudo');

                function validateForm() {
                    const isPasswordValid = password1.value === password2.value 
                        && password1.value.length >= 8 
                        && /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,72}$/.test(password1.value);
                    
                    const isPseudoValid = /^[a-zA-Z0-9_-]{3,20}$/.test(pseudo.value);

                    submitBtn.disabled = !(isPasswordValid && isPseudoValid);
                    
                    if (!isPasswordValid) {
                        password2.setCustomValidity('Les mots de passe doivent correspondre et respecter les critères de sécurité');
                    } else {
                        password2.setCustomValidity('');
                    }
                    
                    return isPasswordValid && isPseudoValid;
                }

                form.addEventListener('submit', function(e) {
                    const isValid = validateForm();
                    if (!isValid) {
                        e.preventDefault();
                    }
                });

				[password1, password2, pseudo].forEach(input => {
					input.addEventListener('input', validateForm);
					input.addEventListener('change', validateForm);
				});

				// Validation initiale
				validateForm();
			});
			</script>

			<a href="?url=accueil/index" class="cancelButton modalButton"><span>Annuler</span></a>

			<div class="formContainer signin">
				<p>Vous avez déjà un compte ? <a href="?url=connexion/index"> Me connecter à mon compte</a>.</p>
			</div>
		</div>
	</form>
</div>
