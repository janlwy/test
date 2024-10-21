
<?php
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
			$connexion = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
			$connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			// On parcourt la bdd pour chercher l'existence du login mot et du mot de passe saisis par l'internaute
			// et on range le résultat dans le tableau $data
			$sql = 'SELECT count(*) FROM users WHERE id=:pseudo AND md5=:mdp';
			$req = $connexion->prepare($sql);
			$req->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
			$req->bindValue(':mdp', md5($_POST['mdp']), PDO::PARAM_STR);
			$req->execute();
			$data = $req->fetch(PDO::FETCH_NUM);
			// Si on obtient une réponse, alors l'utilisateur est un membre
			// On ouvre une session pour cet utilisateur et on le connecte à l'espace membre
			if ($data[0] == 1) {
				session_start();
				$_SESSION['pseudo'] = $_POST['pseudo'];
				header('Location: ./Views/mediabox.php');
				exit();
			}
			// Si le visiteur a saisi un mauvais login ou mot de passe, on ne trouve aucune réponse
			elseif ($data[0] == 0) {
				$erreur = 'Login ou mot de passe non reconnu !';
				echo $erreur;
				echo "<br/><a href='connectForm.php'>Retour au formulaire</a>";
				exit();
			}
			// Sinon, il existe un problème dans la base de données
			else {
				$erreur = 'Plusieurs membres ont<br/>les memes login et mot de passe !';
				echo $erreur;
				echo "<br/><a href='connectForm.php'>Retour au formulaire</a>";
				exit();
			}
		} catch (PDOException $e) {
			echo "Erreur de connexion : " . $e->getMessage();
			exit();
		}
	} else {
		$erreur = 'Erreur de saisie !<br/>Au moins un des champs est vide !';
		echo $erreur;
		echo "<br/><a href='connectForm.php'>Retour au formulaire</a>";
		exit();
	}
}
?>