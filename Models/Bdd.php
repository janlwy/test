<?php

class Operation
{
	public function connecter()
	{
		$dsn = "mysql:host=localhost;dbname=cda_projet;charset=utf8";
		$connexion = new PDO($dsn, 'root', '');
		return $connexion;
	}
}
?>