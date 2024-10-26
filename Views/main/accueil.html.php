<?php $hideNav = true; 
<h5>
	<img src="Ressources/images/startPic.png" alt="Logo" class="logo" style="display: block; margin: 0 auto; width: 25%;">
	<br>
	Une application de prise de notes
</h5>

<!-- bouton effet 3D -->
<section class="sectionBouton3d">
	<a href="?url=connexion/index" class="clic3d">
		<div class="ombre3d"></div>
		<div class="bord3d"></div>
		<span class="bouton3d">
			Commencer !
		</span>
	</a>
</section>
$content = ob_get_clean(); 
include 'accueil.html.php';
?>
