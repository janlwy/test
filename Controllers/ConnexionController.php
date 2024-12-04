<?php

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
        $this->loginAttemptManager = new LoginAttemptManager();
    }

    public function connect()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && 
            isset($_POST['connexion']) && 
            $_POST['connexion'] == 'Connexion') {

            if (!isset($_POST['pseudo']) || empty($_POST['pseudo']) || 
                !isset($_POST['mdp']) || empty($_POST['mdp'])) {
                $this->session->set('erreur', 'Tous les champs sont requis');
                $this->redirect('connexion/index');
            }

            $pseudo = $_POST['pseudo'];
            $ip = $_SERVER['REMOTE_ADDR'];

            // Vérifier si l'utilisateur ou l'IP est bloqué
            if ($this->loginAttemptManager->isLockedOut($pseudo, $ip)) {
                $this->session->set('erreur', 'Compte temporairement bloqué. Veuillez réessayer dans 15 minutes.');
                $this->redirect('connexion/index');
            }

            try {
                $manager = new Manager();
                $connexion = $manager->getConnexion();

                $sql = 'SELECT * FROM users WHERE pseudo=:pseudo';
                $req = $connexion->prepare($sql);
                $req->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
                $req->execute();
                $user = $req->fetch(PDO::FETCH_ASSOC);

                // Vérification du format du mot de passe
                if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,72}$/', $_POST['mdp'])) {
                    $this->session->set('erreur', 'Format du mot de passe invalide');
                    $this->redirect('connexion/index');
                }

                if ($user && password_verify($_POST['mdp'], $user['mdp'])) {
                    // Nettoyer les anciennes tentatives avant de connecter
                    $this->loginAttemptManager->cleanupOldAttempts();
                    $this->session->setAuthenticated($_POST['pseudo'], $user['id'], $user['role']);
                    
                    // Vérifier si l'utilisateur est un admin
                    if ($user['role'] === 'admin') {
                        $this->session->set('admin', true);
                        $this->redirect('database_manager/index');
                        return;
                    }
                    
                    $this->redirect('mediabox/index');
                } else {
                    // Enregistrer la tentative échouée
                    $this->loginAttemptManager->recordAttempt($pseudo, $ip);
                    
                    // Obtenir le nombre de tentatives restantes
                    $remainingAttempts = $this->loginAttemptManager->getRemainingAttempts($pseudo, $ip);
                    
                    logError("Erreur de connexion : identifiants invalides pour l'utilisateur $pseudo depuis $ip");
                    $this->session->set('erreur', "Login ou mot de passe non reconnu ! Il vous reste $remainingAttempts tentatives avant le blocage temporaire.");
                    $this->redirect('connexion/index');
                }
            } catch (PDOException $e) {
                logError("Erreur PDO : " . $e->getMessage());
                $this->session->set('erreur', "Une erreur est survenue lors de la connexion.");
                $this->redirect('connexion/index');
            }
        }
    }
}
?>
