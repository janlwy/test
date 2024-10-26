<?php

class ConnexionController
{
	public function index()
	{

		// Démarrer la session si elle n'est pas déjà démarrée
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}

		// Afficher l'accueil si l'utilisateur n'est pas connecté
		if (!isset($_SESSION['pseudo'])) {
			$datas = ['hideNav' => true];
			generate("Views/connect/connectForm.php", $datas, "Views/base.html.php");
		} else {
			// Rediriger vers la page principale si l'utilisateur est déjà connecté
			header('Location: ?url=mediabox/index');
			exit();
		}
	}

	public function connectForm()
	{
		logError("Début de la méthode connectForm dans ConnexionController");
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
			logError("Session démarrée");
		}
		logError("Génération du formulaire de connexion");
		generate("Views/connect/connectForm.php", [],"Views/base.html.php");
	}
	
	public function connect()
	{
		$datas = [];
		// On vérifie que le visiteur a correctement saisi puis envoyé le formulaire
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['connexion']) && $_POST['connexion'] == 'Connexion') {
			if ((isset($_POST['pseudo']) && !empty($_POST['pseudo'])) && (isset($_POST['mdp']) && !empty($_POST['mdp']))) {
				// On se connecte à la bdd avec PDO
				try {
					$manager = new Manager();
					$connexion = $manager->getConnexion();

					// Requête préparée pour éviter les injections SQL
					$sql = 'SELECT * FROM users WHERE pseudo=:pseudo';
					$req = $connexion->prepare($sql);
					$req->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
					$req->execute();
					$user = $req->fetch(PDO::FETCH_ASSOC);

					// Vérification du mot de passe avec password_verify()
					if ($user && password_verify($_POST['mdp'], $user['mdp'])) {
						session_start();
						$_SESSION['pseudo'] = $_POST['pseudo'];
						header('Location: ?url=mediabox/index');
						exit();
					} else {
						$erreur = 'Login ou mot de passe non reconnu !';
						// Enregistrer l'erreur dans un fichier de log
						logError("Erreur de connexion : " . $erreur);

						// Rediriger l'utilisateur vers le formulaire de connexion avec un message d'erreur
						$_SESSION['erreur'] = $erreur;
						header('Location: ?url=connexion/index'); 
						exit();
					}
				} catch (PDOException $e) {
					// Enregistrer l'erreur dans un fichier de log
					logError("Erreur PDO : " . $e->getMessage());

					// Afficher un message d'erreur générique à l'utilisateur
					$_SESSION['erreur'] = "Une erreur est survenue lors de la connexion.";
					header('Location: ?url=connexion/connect'); // Rediriger vers le formulaire de connexion
					exit();
				}
			}
		}
	}
}
?>
