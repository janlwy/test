
<div>
	<form class="modalContenu Anime formFont" method="POST" action="?url=creation/createUser" enctype="multipart/form-data" id="createForm">

		<div class="formContainer">
			<h2>Création de votre compte</h2>
			<h3>Veuillez remplir ce formulaire pour créer votre profil.</h3>
			<?php $token = SessionManager::getInstance()->regenerateToken(); ?>
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
                    <?php foreach ($_SESSION['validation_errors'] as $field => $errors): ?>
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo htmlspecialchars($field . ': ' . $error, ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php endforeach; ?>
                    <?php endforeach;
                    unset($_SESSION['validation_errors']); ?>
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

			<label for="mdp2"><b>Répéter le mot de passe</b></label>
			<input class="inputModal" type="password" placeholder="Répétez le mot de passe" name="mdp2" id="mdp2" value="" required
                   pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,72}$"
                   title="Au moins 8 caractères, incluant au moins une majuscule, une minuscule, un chiffre et un caractère spécial"
                   maxlength="72"
                   autocomplete="new-password"
                   oninput="validatePasswordMatch()">

			<br>
			<hr>

			<p>En créant un compte vous acceptez de vous soumettre à nos <a href="#">conditions</a>.</p>
			<button type="submit" class="validButton modalButton" name="creation" value="Creation" id="submitBtn" disabled>Création de compte</button>

			<script>
			document.addEventListener('DOMContentLoaded', function() {
				const form = document.getElementById('createForm');
				const password1 = document.getElementById('mdp1');
				const password2 = document.getElementById('mdp2');
				const submitBtn = document.getElementById('submitBtn');
				const pseudo = document.getElementById('pseudo');
				const email = document.getElementById('email');
				
				// Activer le bouton submit par défaut
				submitBtn.disabled = false;
				
				// Fonction pour valider la correspondance des mots de passe
				function validatePasswordMatch() {
					if (password1.value !== password2.value) {
						password2.setCustomValidity('Les mots de passe ne correspondent pas');
					} else {
						password2.setCustomValidity('');
					}
				}
				
				// Ajouter les event listeners
				form.addEventListener('submit', function(e) {
					e.preventDefault(); // Toujours prévenir la soumission par défaut
					
					if (validateForm()) {
						console.log('Form is valid, submitting...');
						form.submit(); // Soumettre explicitement le formulaire
					} else {
						console.log('Form validation failed');
					}
				});

				[password1, password2, pseudo, email].forEach(input => {
					input.addEventListener('input', validateForm);
					input.addEventListener('change', validateForm);
				});
				
				// Ajouter un événement spécifique pour la validation des mots de passe
				password2.addEventListener('input', validatePasswordMatch);

				validateForm(); // Validation initiale
			});

			function validateForm() {
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

                // Réinitialiser tous les messages d'erreur
                [password1, password2, pseudo, email].forEach(input => input.setCustomValidity(''));

                // Vérifier le token CSRF
                const csrfToken = document.querySelector('input[name="csrf_token"]').value;
                if (!csrfToken) {
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
