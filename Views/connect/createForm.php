
<div>
	<form class="modalContenu Anime formFont" method="POST" action="index.php?url=creation/createUser" id="createForm">

		<div class="formContainer">
			<h2>Création de votre compte</h2>
			<h3>Veuillez remplir ce formulaire pour créer votre profil.</h3>
			<?php $token = \Fonctions\SessionManager::getInstance()->regenerateToken(); ?>
			<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">
			<hr>
			<?php 
            if (isset($_SESSION['erreur'])): ?>
				<div class="error-message">
					<?php echo htmlspecialchars($_SESSION['erreur'], ENT_QUOTES, 'UTF-8');
					unset($_SESSION['erreur']); ?>
				</div>
			<?php endif; 
            
            // Affichage des erreurs de validation
            if (isset($_SESSION['validation_errors']) && is_array($_SESSION['validation_errors'])): ?>
                <div class="error-message">
                    <ul>
                    <?php foreach ($_SESSION['validation_errors'] as $field => $errors): ?>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($field . ': ' . $error, ENT_QUOTES, 'UTF-8'); ?></li>
                        <?php endforeach; ?>
                    <?php endforeach;
                    unset($_SESSION['validation_errors']); ?>
                    </ul>
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
                   pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,72}$"
                   title="Au moins 8 caractères, incluant au moins une majuscule, une minuscule, un chiffre et un caractère spécial"
                   maxlength="72"
                   autocomplete="new-password">
			<div class="password-strength-meter">
				<div class="strength-bar"></div>
				<div class="strength-text">Force du mot de passe: <span>Faible</span></div>
			</div>

			<label for="mdp2"><b>Répéter le mot de passe</b></label>
			<input class="inputModal" type="password" placeholder="Répétez le mot de passe" name="mdp2" id="mdp2" value="" required
                   maxlength="72"
                   autocomplete="new-password"
                   oninput="validatePasswordMatch()">
			<div class="password-match-indicator"></div>
			
			<div class="password-toggle">
				<input type="checkbox" id="showPasswords" class="show-password">
				<label for="showPasswords">Afficher les mots de passe</label>
			</div>

			<br>
			<hr>

			<p>En créant un compte vous acceptez de vous soumettre à nos <a href="#">conditions</a>.</p>
			<input type="hidden" name="submitted" value="1">
			<input type="hidden" name="debug_info" value="form_submitted_from_browser">
			<button type="submit" class="validButton modalButton" name="creation" value="Creation" id="submitBtn">Création de compte</button>

			<script>
			document.addEventListener('DOMContentLoaded', function() {
				const form = document.getElementById('createForm');
				const password1 = document.getElementById('mdp1');
				const password2 = document.getElementById('mdp2');
				const submitBtn = document.getElementById('submitBtn');
				const pseudo = document.getElementById('pseudo');
				const email = document.getElementById('email');
				const showPasswordsCheckbox = document.getElementById('showPasswords');
				const passwordStrengthBar = document.querySelector('.strength-bar');
				const passwordStrengthText = document.querySelector('.strength-text span');
				const passwordMatchIndicator = document.querySelector('.password-match-indicator');
				
				// Activer le bouton submit par défaut
				submitBtn.disabled = false;
				
				// Afficher/masquer les mots de passe
				if (showPasswordsCheckbox) {
					showPasswordsCheckbox.addEventListener('change', function() {
						const type = this.checked ? 'text' : 'password';
						password1.type = type;
						password2.type = type;
					});
				}
				
				// Fonction pour évaluer la force du mot de passe
				function checkPasswordStrength(password) {
					let strength = 0;
					
					// Longueur
					if (password.length >= 8) strength += 1;
					if (password.length >= 12) strength += 1;
					
					// Complexité
					if (/[a-z]/.test(password)) strength += 1;
					if (/[A-Z]/.test(password)) strength += 1;
					if (/[0-9]/.test(password)) strength += 1;
					if (/[^a-zA-Z0-9]/.test(password)) strength += 1;
					
					// Variété
					const uniqueChars = new Set(password).size;
					if (uniqueChars > 5) strength += 1;
					if (uniqueChars > 10) strength += 1;
					
					return strength;
				}
				
				// Mettre à jour l'indicateur de force du mot de passe
				function updatePasswordStrength() {
					const password = password1.value;
					const strength = checkPasswordStrength(password);
					
					let strengthText = 'Faible';
					let strengthColor = '#ff4d4d';
					let strengthWidth = '25%';
					
					if (strength >= 4 && strength < 6) {
						strengthText = 'Moyen';
						strengthColor = '#ffaa00';
						strengthWidth = '50%';
					} else if (strength >= 6 && strength < 8) {
						strengthText = 'Fort';
						strengthColor = '#2db300';
						strengthWidth = '75%';
					} else if (strength >= 8) {
						strengthText = 'Très fort';
						strengthColor = '#00b33c';
						strengthWidth = '100%';
					}
					
					passwordStrengthBar.style.width = strengthWidth;
					passwordStrengthBar.style.backgroundColor = strengthColor;
					passwordStrengthText.textContent = strengthText;
				}
				
				// Fonction pour valider la correspondance des mots de passe
				function validatePasswordMatch() {
					if (password1.value === password2.value && password2.value !== '') {
						password2.setCustomValidity('');
						passwordMatchIndicator.textContent = '✓ Les mots de passe correspondent';
						passwordMatchIndicator.style.color = '#00b33c';
					} else if (password2.value !== '') {
						password2.setCustomValidity('Les mots de passe ne correspondent pas');
						passwordMatchIndicator.textContent = '✗ Les mots de passe ne correspondent pas';
						passwordMatchIndicator.style.color = '#ff4d4d';
					} else {
						passwordMatchIndicator.textContent = '';
					}
				}
				
				// Ajouter les event listeners
				form.addEventListener('submit', function(e) {
					console.log('Formulaire soumis');
					
					// Ne pas empêcher la soumission par défaut immédiatement
					// Vérifier d'abord la validité
					
					if (validateForm()) {
						console.log('Form is valid, submitting...');
						
						// Vérifier que le token CSRF est présent
						const csrfToken = document.querySelector('input[name="csrf_token"]').value;
						if (!csrfToken) {
							console.error('Token CSRF manquant');
							alert('Erreur de sécurité: token CSRF manquant');
							e.preventDefault();
							return false;
						}
						
						// Désactiver le bouton et changer son texte
						submitBtn.disabled = true;
						submitBtn.textContent = 'Création en cours...';
						
						// Ajouter un log pour déboguer
						console.log('Soumission du formulaire vers: ' + form.action);
						console.log('Méthode: ' + form.method);
						console.log('Données: ', {
							pseudo: document.getElementById('pseudo').value,
							email: document.getElementById('email').value,
							csrf_token: csrfToken
						});
						
						// Laisser le formulaire se soumettre naturellement
						return true;
					} else {
						console.log('Form validation failed');
						e.preventDefault();
						return false;
					}
				});

				password1.addEventListener('input', function() {
					updatePasswordStrength();
					validatePasswordMatch();
					validateForm();
				});
				
				password2.addEventListener('input', function() {
					validatePasswordMatch();
					validateForm();
				});
				
				[pseudo, email].forEach(input => {
					input.addEventListener('input', validateForm);
					input.addEventListener('change', validateForm);
				});

				validateForm(); // Validation initiale
			});

			function validateForm() {
                console.log('Validation du formulaire en cours...');
                const password1 = document.getElementById('mdp1');
                const password2 = document.getElementById('mdp2');
                const pseudo = document.getElementById('pseudo');
                const email = document.getElementById('email');
                const submitBtn = document.getElementById('submitBtn');

                // Validation du mot de passe
                const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,72}$/;
                const isPasswordValid = password1.value === password2.value && passwordPattern.test(password1.value);

                // Validation du pseudo
                const isPseudoValid = /^[a-zA-Z0-9_-]{3,20}$/.test(pseudo.value);

                // Validation de l'email
                const isEmailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value);

                console.log('Validation:', {
                    password: isPasswordValid,
                    pseudo: isPseudoValid,
                    email: isEmailValid
                });

                // Réinitialiser tous les messages d'erreur
                [password1, password2, pseudo, email].forEach(input => input.setCustomValidity(''));

                // Vérifier le token CSRF
                const csrfToken = document.querySelector('input[name="csrf_token"]').value;
                if (!csrfToken) {
                    console.error('Token CSRF manquant');
                    alert('Erreur de sécurité: token CSRF manquant');
                    return false;
                }

                // Gérer les messages d'erreur
                if (!isPasswordValid) {
                    if (password1.value !== password2.value) {
                        password2.setCustomValidity('Les mots de passe ne correspondent pas');
                    } else {
                        password1.setCustomValidity('Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial');
                    }
                }

                if (!isPseudoValid) {
                    pseudo.setCustomValidity('Le nom d\'utilisateur doit contenir entre 3 et 20 caractères alphanumériques, tirets ou underscores');
                }

                if (!isEmailValid) {
                    email.setCustomValidity('Veuillez entrer une adresse email valide');
                }

                // Activer/désactiver le bouton submit
                submitBtn.disabled = !(isPasswordValid && isPseudoValid && isEmailValid);

                return isPasswordValid && isPseudoValid && isEmailValid;
            }
			</script>

			<a href="?url=accueil/index" class="cancelButton modalButton"><span>Annuler</span></a>

			<div class="formContainer signin">
				<p>Vous avez déjà un compte ? <a href="?url=connexion/index"> Me connecter à mon compte</a>.</p>
			</div>
		</div>
	</form>
</div>
