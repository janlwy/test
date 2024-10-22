<!-- <div id="log" class="modal"> -->

<form class="modalContenu Anime formFont" method="POST" action="/connexion/connect">

	<!--div class="closeContainer">
				<span onclick="document.getElementById('log').style.display='none'" class="close" title="Fermer">&times;</span>
			</div-->

	<div class="formContainer">
		<h2>Connexion à votre compte</h2>
		<h3>Veuillez remplir ce formulaire pour vous connecter à votre compte.</h3>
		<hr>

		<label for="pseudo"><b>Nom d'utilisateur</b></label>
		<input class="inputModal" type="text" placeholder="Entrer un pseudonyme" name="pseudo" id="pseudo" required>

		<label for="mdp"><b>Mot de passe</b></label>
		<input class="inputModal" type="password" placeholder="Entrer votre mot de passe" name="mdp" id="mdp" required>

		<!-- <label for="repMdp"><b>Répéter le mot de passe</b></label>
				<input class="inputModal" type="password" placeholder="Repeter le mot de passe" name="repeat" id="repMdp" required> -->
		<hr>

		<p>En créant un compte vous acceptez de vous soumettre à nos <a href="#">conditions</a>.</p>
		<button type="submit" class="modalButton" name="connexion" value="Connexion">Connexion</button>
	</div>

	<div class="formContainer signin">
		<p>Vous n'avez pas de compte ? <a href="createUserForm.php">Création de compte</a>.</p>
	</div>

</form>

<!-- </div> -->
