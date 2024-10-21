<?php

	function charger($class){
		$fileModels="Models/$class.php";
		$fileControllers="Controllers/$class.php";
		$fileViews="Views/$class.php";
		$fileRessources="Ressources/$class.php";
		$fileFonctions = "Fonctions/$class.php";
		$files=array($fileModels,$fileControllers,$fileViews,$fileRessources, $fileFonctions);
		foreach($files as $file){
			if(file_exists($file)){
				require_once($file);			
			}

		}
	}

	function printr($tableaux){
		echo "<h1><pre>";
		print_r($tableaux);
		echo "</pre>-----------------</h1>";//die;
	}
	
	function generate($file,$datas,$layout="Views/base.html.php"){
		$content=generateFile($file,$datas);
		echo generateFile($layout,array('content'=>$content));
	}

	function generateFile($file, $datas) {
		if(file_exists($file)){
			ob_start();
			extract($datas);
			require_once($file);
			return ob_get_clean();
		}else{
			
			echo "Le fichier $file n'existe pas!";die;
		}
	}

?>