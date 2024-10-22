<?php
class AccueilController
{
	public function index()
	{
		$datas = [];
		generate("Views/main/accueil.html.php", $datas, "Views/base_public.html.php");
	}
}
?>