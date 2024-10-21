<?php
	class AccueilController{
		
		public function __construct($url){
			
			$file="Views/main/accueil.html.php";
			$data=array();
			generate($file,$data);
			
		}
	}

?>