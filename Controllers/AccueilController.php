<?php
class AccueilController
{
	public function index()
	{
		$datas = []; // Assurez-vous que les données nécessaires sont transmises
		generate("Views/main/accueil.html.php", $datas, "Views/base_public.html.php"); // Vérifiez le chemin du fichier
	}
}
?>
