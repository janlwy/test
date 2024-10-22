<?php
class AccueilController
{
	public function index()
	{
		$datas = ['hideNav' => true]; // Masquer le menu de navigation
		generate("Views/main/accueil.html.php", $datas, "Views/base_public.html.php"); // Vérifiez le chemin du fichier
	}
}
?>
