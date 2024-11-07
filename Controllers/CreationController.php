<?php

class CreationController extends BaseController implements IController
{
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->redirect('creation/createUserForm');
    }

    public function list() {
        // Non implémenté pour ce contrôleur
        $this->redirect('creation/createUserForm');
    }

    public function create() {
        // Redirige vers la méthode existante
        $this->createUser();
    }

    public function update($id = null) {
        // Non implémenté pour ce contrôleur
        $this->redirect('creation/createUserForm');
    }

    public function delete($id = null) {
        // Non implémenté pour ce contrôleur
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
            'pseudo' => [
                ['rule' => 'required', 'message' => 'Le nom d\'utilisateur est requis'],
                ['rule' => ['min', 3], 'message' => 'Le nom d\'utilisateur doit faire au moins 3 caractères'],
                ['rule' => ['max', 50], 'message' => 'Le nom d\'utilisateur ne peut pas dépasser 50 caractères'],
                ['rule' => ['pattern', '/^[a-zA-Z0-9_-]{3,20}$/'], 'message' => 'Le nom d\'utilisateur ne peut contenir que des lettres, chiffres, tirets et underscores']
            ],
            'email' => [
                ['rule' => 'required', 'message' => 'L\'email est requis'],
                ['rule' => 'email', 'message' => 'Veuillez entrer une adresse email valide']
            ],
            'mdp' => [
                ['rule' => 'required', 'message' => 'Le mot de passe est requis'],
                ['rule' => ['min', 8], 'message' => 'Le mot de passe doit faire au moins 8 caractères'],
                ['rule' => ['pattern', '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'], 
                 'message' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial']
            ],
            'mdp2' => [
                ['rule' => 'required', 'message' => 'La confirmation du mot de passe est requise'],
                ['rule' => ['match', 'mdp'], 'message' => 'Les mots de passe ne correspondent pas']
            ]
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
