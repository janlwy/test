<?php
// Démarrer la session au début du fichier
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
/*
if (isset($file) && isset($datas)) {
	$content = generateFile($file, $datas);
}*/
?>
<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="author" content="Jean-Louis Marsillon">
	<meta name="description" content="Mon projet CDA">
	<title>MédiaBox</title>
	<link rel="icon" type="image/x-icon" href="Ressources/favicon/favicon.ico">
</head>
<link rel="stylesheet" href="css/media.css" type="text/css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Display:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

<body class="clair-theme" id="themeBody">
	<header>
		<!-- Base Public HTML -->
		<h1>Média Box</h1>
		<button type="button" id="boutonTheme" class="boutonTheme material-icons" title="no"></button>
	</header>
	<main>
		<?php if (isset($_SESSION['pseudo'])): ?>
		<nav class="menunav" id="myMenunav">
			<a href="/" class="active">
				<i class="material-icons">home</i>
			</a>
			<a href="?url=connexion" class="active">se connecter</a>
			<a href="#">Musique</a>
			<a href="#">Vidéos</a>
			<a href="#">Photos</a>
			<a href="#">Paroles</a>
			<a href="deconnexion.php" class="sortie boutonright">
				<i class="material-icons">logout</i>
			</a>
			<a href="javascript:void(0);" class="menuicon" onclick="myFunction()">
				<i class="material-icons">menu</i>
			</a>
		</nav>
		<?php endif; ?>
		<section>
			<?php if (isset($content)) {
				echo $content;
			} else {
				echo "Contenu non disponible.";
			} ?>
		</section>
	</main>
	<footer>
		<div class="socle">
			<div class="icon-bar">
				<a href="/mediabox.html" class="navlabel"><i class="material-icons">home</i>
					<span>Accueil</span> </a>
				<a href="/compte.html" class="navlabel"><i class="material-icons">face_6</i>
					<span>Profile</span> </a>
				<a href="#" class="navlabel"><i class="iconeTheme material-icons">brightness_4</i>
					<span>Thème</span> </a>
			</div>
		</div>
	</footer>
	<script src="js/media.js"></script>
</body>

</html>
