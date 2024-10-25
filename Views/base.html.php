<?php
// Démarrer la session au début du fichier
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
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
<link rel="stylesheet" href="css/mediamusic.css" type="text/css">
<link rel="stylesheet" href="css/mediavideo.css" type="text/css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Display:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

<body class="clair-theme" id="themeBody">
	<header>
		<h1><?php echo isset($pageTitle) ? $pageTitle : 'Média Box'; ?></h1>
		<button type="button" id="boutonTheme" class="boutonTheme modalButton material-icons md-36" title="Changer le thème" aria-label="Changer le thème"></button>
	</header>
	<main>
		<?php if (isset($_SESSION['pseudo'])): ?>
			<nav class="menunav" id="myMenunav">
				<a href="?url=mediabox/index" class="active" aria-label="Accueil"><i class="material-icons">home</i></a>
				<a href="?url=compte/index" class="active" aria-label="Profil"><?php echo htmlentities(trim($_SESSION['pseudo'])); ?> </a>
				<a href="?url=audio/list" aria-label="Audio">Audio</a>
				<a href="?url=video/index" aria-label="Vidéo">Vidéo</a>
				<a href="?url=photo/index" aria-label="Photo">Photo</a>
				<a href="?url=texte/index" aria-label="Texte">Texte</a>
				<a href="?url=deconnexion/index" class="sortie boutonright" aria-label="Déconnexion"><i class="material-icons">logout</i></a>
				<a href="javascript:void(0);" class="menuicon" onclick="myFunction()" aria-label="Menu"><i class="material-icons">menu</i></a>
			</nav>
		<?php endif; ?>
		<section>
			<?php
			if (isset($content)) {
				echo $content;
			} else {
				echo "Contenu non disponible.";
			}
			?>
		</section>
	</main>

	<footer class="">
		<div class="socle">
			<div class="icon-bar">
				<a href="?url=mediabox/index" class="navlabel"><i class="material-icons">home</i>
					<span>Accueil</span>
				</a>
				<a href="?url=compte/index" class="navlabel"><i class="material-icons">face_6</i>
					<span>Profile</span>
				</a>
				<a href="#" class="navlabel"><i class="iconeTheme material-icons">brightness_4</i>
					<span>Thème</span>
				</a>
				<a href="?url=deconnexion/index" class="navlabel"><i class="material-icons">logout</i>
					<span>Quitter</span>
				</a>
			</div>
			<p class="txtfootr">Copyright © 2024 par moimême. All rights reversed.</p>
		</div>
	</footer>

	<script src="js/media.js"></script>
	<script src="js/mediavideo.js"></script>
	<script src="js/mediamusic.js"></script>
	<noscript>Vous devez activer javascript pour une expérience complête de ce site</noscript>
</body>

</html>
