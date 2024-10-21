<?php
	class Router{
		
		public function __construct(){
			//----capture de l'url en $_GET
			// ?url=......
			if(isset($_GET['url'])){
				$url=$_GET['url'];
				$urls=explode('/',$url);
				$class=$urls[0];
				
			}else{
				$url='';
				$class='accueil';
			}
			$class=ucfirst($class);
			$class=$class."Controller";
			$class=new $class($url);
			// echo "<h1 style='red'>Vous etes en instanciation</h1>";die;
		}
		
	}
?>