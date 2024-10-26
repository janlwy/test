<?php

class CreationController
{
    public function createUserForm()
    {
        // Démarrer la session si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            // Vérifier si un cookie de session existe
            if (isset($_COOKIE['session_id'])) {
                session_id($_COOKIE['session_id']);
            }
            session_start();
            if (isset($_SESSION['pseudo'])) {
                header('Location: ?url=mediabox/index');
                exit();
            }
        }

        // Afficher le formulaire de création d'utilisateur
        $datas = ['hideNav' => true];
        generate("Views/connect/createUserForm.php", $datas, "Views/base.html.php");
    }

    public function create()
    {
        // Informations de connexion à la base de données
        $host = "localhost";
        $dbname = "cda_projet";
        $username = "root";
        $password = "";

        // On vérifie que le visiteur a correctement envoyé le formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['creation']) && $_POST['creation'] == 'Creation') {
            // On teste l'existence des variables et on vérifie qu'elles ne sont pas vides
            if ((isset($_POST['pseudo']) && !empty($_POST['pseudo'])) && (isset($_POST['mdp1']) && !empty($_POST['mdp1']))
                && (isset($_POST['mdp2']) && !empty($_POST['mdp2']))) {
                
                // Si les variables existent, on vérifie que les deux mots de passe sont identiques
                if ($_POST['mdp1'] != $_POST['mdp2']) {
                    $erreur = 'Les 2 mots de passe sont differents.';
                    $_SESSION['erreur'] = $erreur;
                    header('Location: ?url=creation/createUserForm');
                    exit();
                } else {
                    // Si les deux mots de passe sont identiques, on se connecte à la bdd avec PDO
                    try {
                        $connexion = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        // On parcourt la bdd et on range les éventuels login identiques existants dans un tableau
                        $sql = 'SELECT count(*) FROM users WHERE pseudo=:pseudo';
                        $req = $connexion->prepare($sql);
                        $req->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
                        $req->execute();
                        $data = $req->fetch(PDO::FETCH_NUM);
                        // Si aucun autre login identique existe, on inscrit ce nouveau login
                        if ($data[0] == 0) {
                            $sql = 'INSERT INTO users (pseudo, mdp) VALUES( :pseudo, :mdp1)';
                            $req = $connexion->prepare($sql);
                            $req->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
                            $req->bindValue(':mdp1', password_hash($_POST['mdp1'], PASSWORD_DEFAULT), PDO::PARAM_STR);
                            $req->execute();
                            $_SESSION['message'] = 'Inscription réussie !';
                            // Créer un cookie de session
                            setcookie('session_id', session_id(), time() + (86400 * 30), "/"); // Expire dans 30 jours
                            $_SESSION['pseudo'] = $_POST['pseudo'];
                            $_SESSION['user_id'] = $connexion->lastInsertId(); // Récupérer l'ID de l'utilisateur
                            header('Location: ?url=mediabox/index');
                            exit();
                        }
                        // Sinon on n'inscrit pas ce login
                        else {
                            $erreur = 'Echec de l\'inscription !<br/>Un membre possède déjà ce pseudo !';
                            $_SESSION['erreur'] = $erreur;
                            header('Location: ?url=creation/createUserForm');
                            exit();
                        }
                    } catch (PDOException $e) {
                        $_SESSION['erreur'] = "Erreur de connexion : " . $e->getMessage();
                        header('Location: ?url=creation/createUserForm');
                        exit();
                    }
                }
            }
            // Si au moins un des champs est vide
            else {
                $erreur = 'Echec de l\'inscription !<br/>Au moins un des champs est vide !';
                $_SESSION['erreur'] = $erreur;
                header('Location: ?url=creation/createUserForm');
                exit();
            }
        }
    }
}
?>
