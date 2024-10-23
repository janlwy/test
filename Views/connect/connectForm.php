<div>
	<form class="modalContenu Anime formFont" method="POST" action="?url=connexion/connect">
		<div class="formContainer ">
			<h2>Connexion à votre compte</h2>
			<h3>Veuillez remplir ce formulaire pour vous connecter à votre compte.</h3>
			<hr>
			<?php if (isset($_SESSION['erreur'])): ?>
				<div class="error-message">
					<?php echo $_SESSION['erreur'];
					unset($_SESSION['erreur']); ?>
				</div>
			<?php endif; ?>

			<label for="pseudo"><b>Nom d'utilisateur</b></label>
			<input class="inputModal" type="text" placeholder="Entrez votre nom" name="pseudo" id="pseudo" required>

			<label for="mdp"><b>Mot de passe</b></label>
			<input class="inputModal" type="password" placeholder="Entrez votre mot de passe" name="mdp" id="mdp" required>

			<br>
			<hr>

			<p>En créant un compte vous acceptez de vous soumettre à nos <a href="#">conditions</a>.</p>
			<button type="submit" class="validButton modalButton" name="connexion" value="Connexion">Connexion</button>

			<a href="?url=accueil/index" class="cancelButton modalButton"><span>Annuler</span></a>

			<div class="formContainer signin">
				<p>Vous n'avez pas de compte ? <a href="?url=creation/createUserForm">Création de compte</a>.</p>
			</div>
		</div>
	</form>
</div>