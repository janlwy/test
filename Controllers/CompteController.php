<?php

class CompteController extends BaseController implements IController
{
    protected $session;

    public function __construct() {
        parent::__construct();
        $this->session = SessionManager::getInstance();
    }

    public function list()
    {
        $this->checkAuth();
        // Pour l'instant, redirige vers index car la liste n'est pas implémentée
        header('Location: ?url=compte/index');
        exit();
    }

    public function create()
    {
        $this->checkAuth();
        // Pour l'instant, redirige vers index car la création n'est pas implémentée
        header('Location: ?url=compte/index');
        exit();
    }

    public function update($id = null)
    {
        $this->checkAuth();
        // Pour l'instant, redirige vers index car la mise à jour n'est pas implémentée
        header('Location: ?url=compte/index');
        exit();
    }

    public function delete($id = null)
    {
        $this->checkAuth();
        // Pour l'instant, redirige vers index car la suppression n'est pas implémentée
        header('Location: ?url=compte/index');
        exit();
    }
    public function index()
    {
        $this->checkAuth();
        
            $datas = [
                'session' => $this->session,
                'pageTitle' => "Gestion des médias"
            ];
            generate("Views/main/compte.php", $datas, "Views/base.html.php", "Mon compte");
        } else {
            // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
            $this->redirectToLogin();
        }
    }

    public function addMusic()
    {
        $this->startSession();

        // Vérifier si l'utilisateur est connecté
        if ($this->isUserLoggedIn()) {
            // Vérifier que le formulaire a été soumis
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Récupérer les données du formulaire
                if ($this->isFormDataValid()) {
                    logError("Données du formulaire reçues : " . json_encode($_POST));
                    $title = $_POST['title'];
                    $artist = $_POST['artiste'];
                    $image = $_POST['image'];
                    $path = $_POST['path'];

                    // Connexion à la base de données
                    $manager = new Manager();
                    $connexion = $manager->getConnexion();

                    // Insérer les données dans la base de données
                    $this->insertMusicData($connexion, $title, $artist, $image, $path);
                } else {
                    logError("Données du formulaire manquantes");
                }

                // Rediriger vers la page de gestion des médias
                $this->redirectToMediaManagement();
            }
        } else {
            // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
            $this->redirectToLogin();
        }
    }

    private function startSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }


    private function isFormDataValid()
    {
        return isset($_POST['title'], $_POST['artiste'], $_POST['image'], $_POST['path']);
    }

    private function insertMusicData($connexion, $title, $artist, $image, $path)
    {
        $sql = 'INSERT INTO audio (title, artist, image, path, user_id) VALUES (:title, :artist, :image, :path, :user_id)';
        $req = $connexion->prepare($sql);
        $req->bindValue(':title', $title, PDO::PARAM_STR);
        $req->bindValue(':artist', $artist, PDO::PARAM_STR);
        $req->bindValue(':image', $image, PDO::PARAM_STR);
        $req->bindValue(':path', $path, PDO::PARAM_STR);
        $req->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        if ($req->execute()) {
            logError("Données insérées avec succès dans la base de données.");
        } else {
            logError("Échec de l'insertion des données dans la base de données.");
        }
    }

    private function redirectToLogin()
    {
        header('Location: ?url=connexion/index');
        exit();
    }

    private function redirectToMediaManagement()
    {
        header('Location: ?url=compte/index');
        exit();
    }
}
