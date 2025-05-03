<?php
namespace Controllers;

require_once __DIR__ . '/BaseController.php';
use PDO;
use Models\Manager;

class ConnexionController extends BaseController
{
    public function index()
    {
        logInfo("Début de la méthode index dans ConnexionController");

        if (!$this->session->isAuthenticated()) {
            $datas = ['hideNav' => true];
            generate("Views/connect/connectForm.php", $datas, "Views/base.html.php", "Calepin", true);
        } else {
            $this->redirect('mediabox/index');
        }
    }

    public function connectForm()
    {
        logInfo("Début de la méthode connectForm dans ConnexionController");
        generate("Views/connect/connectForm.php", ['hideNav' => true], "Views/base.html.php", "Calepin", true);
    }
    
    private $loginAttemptManager;
    
    public function __construct() {
        parent::__construct();
        if (class_exists('\Models\LoginAttemptManager')) {
            $this->loginAttemptManager = new \Models\LoginAttemptManager();
        }
    }

    public function connect()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && 
            isset($_POST['connexion']) && 
            $_POST['connexion'] == 'Connexion') {

            // Vérification CSRF
            if (!$this->session->validateToken($_POST['csrf_token'] ?? null)) {
                logError("Erreur CSRF : jeton invalide lors de la tentative de connexion");
                $this->session->set('erreur', 'Erreur de sécurité. Veuillez réessayer.');
                $this->redirect('connexion/index');
                return;
            }

            if (!isset($_POST['pseudo']) || empty($_POST['pseudo']) || 
                !isset($_POST['mdp']) || empty($_POST['mdp'])) {
                $this->session->set('erreur', 'Tous les champs sont requis');
                $this->redirect('connexion/index');
                return;
            }

            $pseudo = trim($_POST['pseudo']);
            $ip = $_SERVER['REMOTE_ADDR'];
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

            // Vérifier si l'utilisateur ou l'IP est bloqué
            if (isset($this->loginAttemptManager) && $this->loginAttemptManager->isLockedOut($pseudo, $ip)) {
                logError("Tentative de connexion sur un compte bloqué: $pseudo depuis $ip");
                $this->session->set('erreur', 'Compte temporairement bloqué. Veuillez réessayer dans 15 minutes.');
                $this->redirect('connexion/index');
                return;
            }

            try {
                $manager = new Manager();
                $connexion = $manager->getConnexion();

                $sql = 'SELECT * FROM users WHERE pseudo=:pseudo';
                $req = $connexion->prepare($sql);
                $req->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
                $req->execute();
                $user = $req->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($_POST['mdp'], $user['mdp'])) {
                    // Vérifier si le compte est actif
                    if (isset($user['active']) && $user['active'] == 0) {
                        logError("Tentative de connexion sur un compte désactivé: $pseudo depuis $ip");
                        $this->session->set('erreur', 'Ce compte a été désactivé. Veuillez contacter l\'administrateur.');
                        $this->redirect('connexion/index');
                        return;
                    }
                    
                    // Nettoyer les anciennes tentatives avant de connecter
                    if (isset($this->loginAttemptManager)) {
                        $this->loginAttemptManager->cleanupOldAttempts();
                    }
                    
                    // Mettre à jour la dernière connexion
                    $manager->updateLastLogin($user['id']);
                    
                    // Journaliser la connexion réussie
                    logInfo("Connexion réussie pour l'utilisateur $pseudo depuis $ip avec $userAgent");
                    
                    // Authentifier l'utilisateur
                    $this->session->setAuthenticated($pseudo, $user['id'], $user['role']);
                    
                    // Vérifier si l'utilisateur est un admin
                    if ($user['role'] === 'admin') {
                        $this->session->set('admin', true);
                        $this->redirect('database_manager/index');
                        return;
                    }
                    
                    $this->redirect('mediabox/index');
                } else {
                    // Enregistrer la tentative échouée
                    $remainingAttempts = 5; // Valeur par défaut
                    if (isset($this->loginAttemptManager)) {
                        $this->loginAttemptManager->recordAttempt($pseudo, $ip);
                        // Obtenir le nombre de tentatives restantes
                        $remainingAttempts = $this->loginAttemptManager->getRemainingAttempts($pseudo, $ip);
                    }
                    
                    logError("Erreur de connexion : identifiants invalides pour l'utilisateur $pseudo depuis $ip avec $userAgent");
                    $this->session->set('erreur', "Login ou mot de passe non reconnu ! Il vous reste $remainingAttempts tentatives avant le blocage temporaire.");
                    $this->redirect('connexion/index');
                }
            } catch (\PDOException $e) {
                logError("Erreur PDO lors de la connexion : " . $e->getMessage());
                $this->session->set('erreur', "Une erreur est survenue lors de la connexion.");
                $this->redirect('connexion/index');
            } catch (\Exception $e) {
                logError("Exception lors de la connexion : " . $e->getMessage());
                $this->session->set('erreur', "Une erreur inattendue est survenue. Veuillez réessayer ultérieurement.");
                $this->redirect('connexion/index');
            }
        } else {
            // Si ce n'est pas une requête POST valide, rediriger vers la page de connexion
            $this->redirect('connexion/index');
        }
    }
}
?>
