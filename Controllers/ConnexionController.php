<?php

class ConnexionController
{
	public function index()
	{
		// Rediriger vers le formulaire de connexion
		header('Location: index.php?url=connexion/connect');
		exit();
	}

	public function connect()
	{
		// Informations de connexion à la base de données
		$host = "localhost";
		$dbname = "cda_projet";
		$username = "root";
		$password = "";

		// On vérifie que le visiteur a correctement saisi puis envoyé le formulaire
		if (isset($_POST['connexion']) && $_POST['connexion'] == 'Connexion') {
			if ((isset($_POST['pseudo']) && !empty($_POST['pseudo'])) && (isset($_POST['mdp']) && !empty($_POST['mdp']))) {
				// On se connecte à la bdd avec PDO
				try {
					$manager = new Manager();
					$connexion = $manager->getConnexion();

					//Requête préparée pour éviter les injections SQL
					$sql = 'SELECT * FROM users WHERE id=:pseudo';
					$req = $connexion->prepare($sql);
					$req->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
					$req->execute();
					$user = $req->fetch(PDO::FETCH_ASSOC);

					// Vérification du mot de passe avec password_verify()
					if ($user && password_verify($_POST['mdp'], $user['mdp'])) {
						session_start();
						$_SESSION['pseudo'] = $_POST['pseudo'];
						header('Location: ./Views/main/mediabox.php');
						exit();
					} else {
						$erreur = 'Login ou mot de passe non reconnu !';
						// Enregistrer l'erreur dans un fichier de log
						logError("Erreur de connexion : " . $erreur);

						// Rediriger l'utilisateur vers le formulaire de connexion avec un message d'erreur
						$_SESSION['erreur'] = $erreur;
						header('Location: index.php?url=connexion/connect'); 
						exit();
					}
				} catch (PDOException $e) {
					// Enregistrer l'erreur dans un fichier de log
					logError("Erreur PDO : " . $e->getMessage());

					// Afficher un message d'erreur générique à l'utilisateur
					$_SESSION['erreur'] = "Une erreur est survenue lors de la connexion.";
					header('Location: connectForm.php'); // Rediriger vers le formulaire de connexion
					exit();
				}
			}
		}
	}
}
?>
