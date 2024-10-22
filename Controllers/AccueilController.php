<?php
class AccueilController
{
	public function index()
	{
		logError("Début de la méthode index dans AccueilController");
		// Démarrer la session si elle n'est pas déjà démarrée
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}

		// Afficher la page d'accueil si l'utilisateur n'est pas connecté
		if (!isset($_SESSION['pseudo'])) {
			logError("Utilisateur non connecté, affichage de l'accueil");
			$datas = ['hideNav' => true]; // Masquer le menu de navigation
			generate("Views/main/accueil.html.php", $datas, "Views/base.html.php");
		} else {
			// Rediriger vers la page de médiabox si l'utilisateur est connecté
			logError("Utilisateur connecté, redirection vers mediabox");
			header('Location: /?url=mediabox/index');
			exit();
		}
	}
}
?>
