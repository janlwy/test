
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

            <label for="email"><b>Email</b></label>
            <input class="inputModal" type="email" placeholder="Entrer votre email" name="email" id="email" required
                   autocomplete="email">

            <label for="pseudo"><b>Nom d'utilisateur</b></label>
			<input class="inputModal" type="text" placeholder="Entrer votre nom" name="pseudo" id="pseudo" value="" required
                   pattern="[a-zA-Z0-9_-]{3,20}" 
                   title="3 à 20 caractères alphanumériques, tirets et underscores autorisés"
                   maxlength="20"
                   autocomplete="username">

			<label for="mdp1"><b>Mot de passe</b></label>
			<input class="inputModal" type="password" placeholder="Entrez votre mot de passe" name="mdp" id="mdp1" value="" required
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
			let form, password1, password2, submitBtn, pseudo;
			
			document.addEventListener('DOMContentLoaded', function() {
				form = document.querySelector('form');
				password1 = document.getElementById('mdp1');
				password2 = document.getElementById('mdp2');
				submitBtn = document.getElementById('submitBtn');
				pseudo = document.getElementById('pseudo');
				
				// Ajouter les event listeners
				form.addEventListener('submit', function(e) {
					if (!validateForm()) {
						e.preventDefault();
						return false;
					}
					return true;
				});

				[password1, password2, pseudo].forEach(input => {
					input.addEventListener('input', validateForm);
					input.addEventListener('change', validateForm);
				});

				// Validation initiale
				validateForm();
			});

			function validateForm() {
                    // Validation du mot de passe
                    const isPasswordValid = password1.value === password2.value 
                        && password1.value.length >= 8 
                        && /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,72}$/.test(password1.value);
                    
                    // Validation du pseudo
                    const isPseudoValid = /^[a-zA-Z0-9_-]{3,20}$/.test(pseudo.value);

                    // Afficher les messages d'erreur spécifiques
                    if (!isPasswordValid) {
                        if (password1.value !== password2.value) {
                            password2.setCustomValidity('Les mots de passe ne correspondent pas');
                        } else if (password1.value.length < 8) {
                            password1.setCustomValidity('Le mot de passe doit contenir au moins 8 caractères');
                        } else {
                            password1.setCustomValidity('Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial');
                        }
                    } else {
                        password1.setCustomValidity('');
                        password2.setCustomValidity('');
                    }
                    
                    // Vérification du token CSRF
                    const csrfToken = document.querySelector('input[name="csrf_token"]').value;
                    if (!csrfToken) {
                        alert('Erreur de sécurité: token CSRF manquant');
                        return false;
                    }

                    submitBtn.disabled = !(isPasswordValid && isPseudoValid);
                    
                    if (!isPasswordValid) {
                        password2.setCustomValidity('Les mots de passe doivent correspondre et respecter les critères de sécurité');
                        return false;
                    } else {
                        password2.setCustomValidity('');
                    }
                    
                    if (!isPseudoValid) {
                        pseudo.setCustomValidity('Le nom d\'utilisateur doit contenir entre 3 et 20 caractères alphanumériques');
                        return false;
                    } else {
                        pseudo.setCustomValidity('');
                    }
                    
                    return true;
                }


			</script>

			<a href="?url=accueil/index" class="cancelButton modalButton"><span>Annuler</span></a>

			<div class="formContainer signin">
				<p>Vous avez déjà un compte ? <a href="?url=connexion/index"> Me connecter à mon compte</a>.</p>
			</div>
		</div>
	</form>
</div>
