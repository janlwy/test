

<!-- <div id="log" class="modal"> -->

<form class="modalContenu Anime formFont" method="POST" action="verification.php">

	<!--div class="closeContainer">
				<span onclick="document.getElementById('log').style.display='none'" class="close" title="Fermer">&times;</span>
			</div-->

	<div class="formContainer">
		<h2>Création de votre compte</h2>
		<h3>Veuillez remplir ce formulaire pour créer votre profil.</h3>
		<hr>

		<label for="pseudo"><b>Nom d'utilisateur</b></label>
		<input class="inputModal" type="text" name="pseudo" id="pseudo" value="" required>

		<label for="mdp1"><b>Mot de passe</b></label>
		<input class="inputModal" type="password" name="mdp1" id="mdp1" value="" required>

		<label for="mdp2"><b>Répéter le mot de passe</b></label>
		<input class="inputModal" type="password" name="mdp2" id="mdp2" value="" required>
		<hr>

		<p>En créant un compte vous acceptez de vous soumettre à nos <a href="#">conditions</a>.</p>
		<button type="submit" class="modalButton" name="creation" value="Creation">Création de compte</button>
	</div>

	<div class="formContainer signin">
		<p>Vous avez déjâ un compte ? <a href="connectForm.php">Connexion à mon compte</a>.</p>
	</div>

</form>

<!-- </div> -->