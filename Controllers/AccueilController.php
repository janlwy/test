<?php
class AccueilController
{
	public function index()
	{
		// Démarrer la session si elle n'est pas déjà démarrée
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}

		if (isset($_SESSION['pseudo'])) {
			header('Location: /?url=mediabox/index');
			exit();
		} else {
			$datas = ['hideNav' => true]; // Masquer le menu de navigation
			generate("Views/main/accueil.html.php", $datas, "Views/base.html.php");
		}
	}
}
?>
