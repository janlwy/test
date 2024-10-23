<div>

	<form class="formFont" method="POST" action="?url=connexion/connect">


		<div class="formContainer">
			<h2>Connexion à votre compte</h2>
			<h3>Veuillez remplir ce formulaire pour vous connecter à votre compte.</h3>
			<hr>

			<label for="pseudo"><b>Nom d'utilisateur</b></label>
			<input class="inputModal" type="text" placeholder="Entrer un pseudonyme" name="pseudo" id="pseudo" required>

			<label for="mdp"><b>Mot de passe</b></label>
			<input class="inputModal" type="password" placeholder="Entrer votre mot de passe" name="mdp" id="mdp" required>

			<hr>

			<p>En créant un compte vous acceptez de vous soumettre à nos <a href="#">conditions</a>.</p>
			<button type="submit" class="validButton modalButton" name="connexion" value="Connexion">Connexion</button>

			<a href="?url=accueil/index" class="cancelButton modalButton"><span>Annuler</span></a>

		</div>

		<div class="formContainer signin">
			<p>Vous n'avez pas de compte ? <a href="?url=creation/create">Création de compte</a>.</p>
		</div>

	</form>

</div>