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
                    $this->session->setAuthenticated($_POST['pseudo'], $user['id']);
                    $this->redirect('mediabox/index');
                } else {
                    logError("Erreur de connexion : identifiants invalides");
                    $this->session->set('erreur', 'Login ou mot de passe non reconnu !');
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
