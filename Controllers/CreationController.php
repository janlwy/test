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
<?php

class CreationController extends BaseController implements IController
{
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->redirect('creation/createUserForm');
    }

    public function createUserForm() {
        $datas = ['hideNav' => true];
        generate("Views/connect/createForm.php", $datas, "Views/base.html.php", "Création de compte", true);
    }

    public function createUser() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('creation/createUserForm');
            return;
        }

        $this->checkCSRF();

        $validator = new Validator();
        $rules = [
            'pseudo' => ['required', ['min', 3]],
            'email' => ['required', 'email'],
            'mdp' => ['required', ['min', 8]],
            'mdp2' => ['required']
        ];

        if ($validator->validate($_POST, $rules)) {
            if ($_POST['mdp'] !== $_POST['mdp2']) {
                $this->session->set('erreur', 'Les mots de passe ne correspondent pas');
                $this->redirect('creation/createUserForm');
                return;
            }

            try {
                $manager = new Manager();
                $connexion = $manager->getConnexion();

                // Vérifier si le pseudo existe déjà
                $sql = 'SELECT COUNT(*) FROM users WHERE pseudo = :pseudo';
                $req = $connexion->prepare($sql);
                $req->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
                $req->execute();
                
                if ($req->fetchColumn() > 0) {
                    $this->session->set('erreur', 'Ce nom d\'utilisateur existe déjà');
                    $this->redirect('creation/createUserForm');
                    return;
                }

                // Vérifier si l'email existe déjà
                $sql = 'SELECT COUNT(*) FROM users WHERE email = :email';
                $req = $connexion->prepare($sql);
                $req->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
                $req->execute();
                
                if ($req->fetchColumn() > 0) {
                    $this->session->set('erreur', 'Cet email est déjà utilisé');
                    $this->redirect('creation/createUserForm');
                    return;
                }

                // Insérer le nouvel utilisateur
                $sql = 'INSERT INTO users (pseudo, email, mdp, role) VALUES (:pseudo, :email, :mdp, :role)';
                $req = $connexion->prepare($sql);
                $req->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
                $req->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
                $req->bindValue(':mdp', password_hash($_POST['mdp'], PASSWORD_DEFAULT), PDO::PARAM_STR);
                $req->bindValue(':role', 'user', PDO::PARAM_STR);
                
                if ($req->execute()) {
                    $this->session->set('success', 'Compte créé avec succès');
                    $this->redirect('connexion/index');
                } else {
                    throw new Exception('Erreur lors de la création du compte');
                }
            } catch (Exception $e) {
                logError("Erreur lors de la création du compte : " . $e->getMessage());
                $this->session->set('erreur', 'Une erreur est survenue lors de la création du compte');
                $this->redirect('creation/createUserForm');
            }
        } else {
            $this->session->set('erreur', 'Veuillez corriger les erreurs dans le formulaire');
            $this->session->set('validation_errors', $validator->getErrors());
            $this->redirect('creation/createUserForm');
        }
    }
}
