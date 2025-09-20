<?php
namespace Controllers;

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Fonctions/RoleManager.php';
require_once __DIR__ . '/../Models/Manager.php';
use Fonctions\RoleManager;
use Models\Manager;
use PDO;
use Exception;
use PDOException;

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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->createUser();
        } else {
            $this->redirect('creation/createUserForm');
        }
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

        // Debug log
        logInfo('CreateUser method called with POST data: ' . print_r($_POST, true));
        logInfo('Méthode de requête: ' . $_SERVER['REQUEST_METHOD']);
        logInfo('Content-Type: ' . ($_SERVER['CONTENT_TYPE'] ?? 'Non défini'));

        try {
            // Vérifier si les données POST sont vides
            if (empty($_POST)) {
                logError("Aucune donnée POST reçue");
                $this->session->set('erreur', 'Aucune donnée reçue. Veuillez réessayer.');
                $this->redirect('creation/createUserForm');
                return;
            }

            // Activer la validation CSRF
            if (!isset($_POST['csrf_token'])) {
                logError("Erreur CSRF : jeton manquant lors de la création de compte");
                $this->session->set('erreur', 'Erreur de sécurité (jeton manquant). Veuillez réessayer.');
                $this->redirect('creation/createUserForm');
                return;
            }
            
            if (!$this->session->validateToken($_POST['csrf_token'])) {
                logError("Erreur CSRF : jeton invalide lors de la création de compte");
                $this->session->set('erreur', 'Erreur de sécurité (jeton invalide). Veuillez réessayer.');
                $this->redirect('creation/createUserForm');
                return;
            }

            // Validation manuelle des données au lieu d'utiliser le validateur
            $errors = [];
            
            // Validation du pseudo
            if (empty($_POST['pseudo'])) {
                $errors['pseudo'][] = 'Le nom d\'utilisateur est requis';
            } elseif (strlen($_POST['pseudo']) < 3) {
                $errors['pseudo'][] = 'Le nom d\'utilisateur doit faire au moins 3 caractères';
            } elseif (strlen($_POST['pseudo']) > 50) {
                $errors['pseudo'][] = 'Le nom d\'utilisateur ne peut pas dépasser 50 caractères';
            } elseif (!preg_match('/^[a-zA-Z0-9_-]{3,20}$/', $_POST['pseudo'])) {
                $errors['pseudo'][] = 'Le nom d\'utilisateur ne peut contenir que des lettres, chiffres, tirets et underscores';
            }
            
            // Validation de l'email
            if (empty($_POST['email'])) {
                $errors['email'][] = 'L\'email est requis';
            } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'][] = 'Veuillez entrer une adresse email valide';
            }
            
            // Validation du mot de passe
            if (empty($_POST['mdp'])) {
                $errors['mdp'][] = 'Le mot de passe est requis';
            } elseif (strlen($_POST['mdp']) < 8) {
                $errors['mdp'][] = 'Le mot de passe doit faire au moins 8 caractères';
            } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&.])[A-Za-z\d@$!%*?&.]{8,72}$/', $_POST['mdp'])) {
                $errors['mdp'][] = 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial (@$!%*?&.)';
            }
            
            // Validation de la confirmation du mot de passe
            if (empty($_POST['mdp2'])) {
                $errors['mdp2'][] = 'La confirmation du mot de passe est requise';
            } elseif ($_POST['mdp'] !== $_POST['mdp2']) {
                $errors['mdp2'][] = 'Les mots de passe ne correspondent pas';
            }

            // Si des erreurs sont présentes
            if (!empty($errors)) {
                $this->session->set('erreur', 'Veuillez corriger les erreurs dans le formulaire');
                $this->session->set('validation_errors', $errors);
                $this->redirect('creation/createUserForm');
                return;
            }
            
            // Les données sont valides, on peut continuer
            logInfo("Validation réussie pour la création de compte: " . $_POST['pseudo']);
            logInfo("Début de la création en base de données pour: " . $_POST['pseudo']);

            // Vérifier que la classe Manager existe
            if (!class_exists('Models\Manager')) {
                logError("La classe Models\Manager n'existe pas");
                $this->session->set('erreur', 'Erreur de configuration. Veuillez contacter l\'administrateur.');
                $this->redirect('creation/createUserForm');
                return;
            }
            logInfo("Classe Manager trouvée");

            $manager = null;
            $connexion = null;
            
            try {
                logInfo("Tentative de création du Manager");
                $manager = new Manager();
                logInfo("Manager créé avec succès");
                
                logInfo("Tentative d'obtention de la connexion");
                $connexion = $manager->getConnexion();
                logInfo("Connexion à la base de données obtenue");
                
                if (!$connexion) {
                    throw new Exception("Connexion à la base de données échouée");
                }
                
                // Vérifier si la table users existe et a la structure requise
                logInfo("Vérification de la structure de la table users");
                if (!$manager->checkUsersTableStructure()) {
                    logError("Structure de la table users incorrecte ou manquante");
                    $this->session->set('erreur', 'Erreur de configuration de la base de données. Veuillez contacter l\'administrateur.');
                    $this->redirect('creation/createUserForm');
                    return;
                }
                logInfo("Structure de la table users validée");

                // Vérifier si le pseudo existe déjà
                logInfo("Vérification de l'unicité du pseudo: " . $_POST['pseudo']);
                $sql = 'SELECT COUNT(*) FROM users WHERE pseudo = :pseudo';
                $req = $connexion->prepare($sql);
                $req->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
                $req->execute();
                
                $pseudoCount = $req->fetchColumn();
                logInfo("Nombre de pseudos existants: " . $pseudoCount);
                
                if ($pseudoCount > 0) {
                    logInfo("Pseudo déjà existant, redirection vers le formulaire");
                    $this->session->set('erreur', 'Ce nom d\'utilisateur existe déjà');
                    $this->redirect('creation/createUserForm');
                    return;
                }

                // Vérifier si l'email existe déjà
                logInfo("Vérification de l'unicité de l'email: " . $_POST['email']);
                $sql = 'SELECT COUNT(*) FROM users WHERE email = :email';
                $req = $connexion->prepare($sql);
                $req->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
                $req->execute();
                
                $emailCount = $req->fetchColumn();
                logInfo("Nombre d'emails existants: " . $emailCount);
                
                if ($emailCount > 0) {
                    logInfo("Email déjà existant, redirection vers le formulaire");
                    $this->session->set('erreur', 'Cet email est déjà utilisé');
                    $this->redirect('creation/createUserForm');
                    return;
                }

                // Insérer le nouvel utilisateur
                $sql = 'INSERT INTO users (pseudo, email, mdp, role, created_at, active) VALUES (:pseudo, :email, :mdp, :role, NOW(), 1)';
                $req = $connexion->prepare($sql);
                $req->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
                $req->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
                $req->bindValue(':mdp', password_hash($_POST['mdp'], PASSWORD_DEFAULT), PDO::PARAM_STR);
                $req->bindValue(':role', RoleManager::getDefaultRole(), PDO::PARAM_STR);
                
                logInfo("Tentative d'insertion d'un nouvel utilisateur: " . $_POST['pseudo']);
                
                $connexion->beginTransaction();
                
                if ($req->execute()) {
                    $connexion->commit();
                    // Stocker un message de succès
                    $this->session->set('message', 'Compte créé avec succès. Vous pouvez maintenant vous connecter.');
                    logInfo("Compte créé avec succès pour l'utilisateur: " . $_POST['pseudo']);
                    $this->redirect('connexion/index');
                    return; // Important : arrêter l'exécution ici
                } else {
                    $connexion->rollBack();
                    $errorInfo = $req->errorInfo();
                    logError("Erreur SQL lors de la création du compte: " . json_encode($errorInfo));
                    $this->session->set('erreur', 'Erreur lors de la création du compte: ' . $errorInfo[2]);
                    $this->redirect('creation/createUserForm');
                    return;
                }
            } catch (PDOException $e) {
                if (isset($connexion) && $connexion->inTransaction()) {
                    $connexion->rollBack();
                }
                logError("Erreur PDO lors de la création du compte : " . $e->getMessage());
                logError("Code d'erreur PDO : " . $e->getCode());
                
                // Vérifier si c'est une erreur de clé dupliquée
                if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    if (strpos($e->getMessage(), 'pseudo') !== false) {
                        $this->session->set('erreur', 'Ce nom d\'utilisateur est déjà utilisé');
                    } elseif (strpos($e->getMessage(), 'email') !== false) {
                        $this->session->set('erreur', 'Cette adresse email est déjà utilisée');
                    } else {
                        $this->session->set('erreur', 'Un compte avec ces informations existe déjà');
                    }
                } else {
                    $this->session->set('erreur', 'Erreur de base de données lors de la création du compte');
                }
                $this->redirect('creation/createUserForm');
                return;
            } catch (Exception $e) {
                if (isset($connexion) && $connexion && $connexion->inTransaction()) {
                    $connexion->rollBack();
                }
                logError("Erreur générale lors de la création du compte : " . $e->getMessage());
                logError("Trace de l'erreur : " . $e->getTraceAsString());
                
                $this->session->set('erreur', 'Une erreur est survenue lors de la création du compte');
                $this->redirect('creation/createUserForm');
                return;
            }
        } catch (Exception $e) {
            logError("Erreur générale lors de la création du compte : " . $e->getMessage());
            $this->session->set('erreur', 'Une erreur est survenue lors de la création du compte');
            $this->redirect('creation/createUserForm');
            return;
        }
    }
}
