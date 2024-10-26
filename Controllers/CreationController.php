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
        startSessionIfNeeded();
        
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
            
            if (!$req->execute()) {
                throw new Exception('Erreur lors de l\'insertion en base de données');
            }
            
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

        } catch (Exception $e) {
            logError("Erreur lors de la création du compte : " . $e->getMessage());
            $_SESSION['erreur'] = "Une erreur est survenue lors de la création du compte";
            header('Location: ?url=creation/createUserForm');
            exit();
        }
    }
}
?>
