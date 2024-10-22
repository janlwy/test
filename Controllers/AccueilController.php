<?php
class AccueilController
{
	public function index()
	{
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
