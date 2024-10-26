<?php

class CreationController
{
    public function createUserForm()
    {
        startSessionIfNeeded();
        if (isset($_SESSION['pseudo'])) {
            header('Location: ?url=mediabox/index');
            exit();
        }

        // Afficher le formulaire de création d'utilisateur
        $datas = [];
        generate("Views/connect/createUserForm.php", $datas, "Views/base.html.php", "Calepin", true);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['creation']) || $_POST['creation'] !== 'Creation') {
            header('Location: ?url=creation/createUserForm');
            exit();
        }

        $validator = new Validator();
        $rules = [
            'pseudo' => ['required', 'username'],
            'mdp1' => ['required', 'password'],
            'mdp2' => ['required', ['matches', $_POST['mdp1'] ?? null]]
        ];

        if (!$validator->validate($_POST, $rules)) {
            $_SESSION['erreur'] = $validator->getFirstError();
            header('Location: ?url=creation/createUserForm');
            exit();
        }

        try {
            $manager = new Manager();
            $connexion = $manager->getConnexion();
            
            // Vérifier si le pseudo existe déjà
            $sql = 'SELECT count(*) FROM users WHERE pseudo = :pseudo';
            $req = $connexion->prepare($sql);
            $req->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
            $req->execute();
            
            if ($req->fetchColumn() > 0) {
                $_SESSION['erreur'] = 'Ce nom d\'utilisateur est déjà pris';
                header('Location: ?url=creation/createUserForm');
                exit();
            }

            // Créer le nouvel utilisateur
            $sql = 'INSERT INTO users (pseudo, mdp, created_at) VALUES (:pseudo, :mdp, NOW())';
            $req = $connexion->prepare($sql);
            $req->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
            $req->bindValue(':mdp', password_hash($_POST['mdp1'], PASSWORD_DEFAULT), PDO::PARAM_STR);
            
            if ($req->execute()) {
                $_SESSION['message'] = 'Inscription réussie !';
                $_SESSION['pseudo'] = $_POST['pseudo'];
                $_SESSION['user_id'] = $connexion->lastInsertId();
                
                // Créer un cookie de session sécurisé
                setcookie('session_id', session_id(), [
                    'expires' => time() + (86400 * 30),
                    'path' => '/',
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'Strict'
                ]);
                
                header('Location: ?url=mediabox/index');
                exit();
            }
            
            throw new Exception('Erreur lors de l\'insertion en base de données');

        } catch (Exception $e) {
            logError("Erreur lors de la création du compte : " . $e->getMessage());
            $_SESSION['erreur'] = "Une erreur est survenue lors de la création du compte";
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
