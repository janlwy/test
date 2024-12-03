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
            'pageTitle' => "Gestion des notes"
        ];
        generate("Views/main/compte.php", $datas, "Views/base.html.php", "Mon compte", false);
    }

    public function addMusic()
    {
        $this->checkAuth();
        $this->checkCSRF();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->isFormDataValid()) {
                logInfo("Données du formulaire reçues : " . json_encode($_POST));
                $title = $_POST['title'];
                $artist = $_POST['artiste'];
                $image = $_POST['image'];
                $path = $_POST['path'];

                $manager = new Manager();
                $connexion = $manager->getConnexion();

                try {
                    $this->insertMusicData($connexion, $title, $artist, $image, $path);
                    $this->redirect('compte/index');
                } catch (Exception $e) {
                    logError("Erreur lors de l'insertion : " . $e->getMessage());
                    $this->session->set('erreur', "Erreur lors de l'ajout du média");
                    $this->redirect('compte/index');
                }
            } else {
                logError("Données du formulaire manquantes");
                $this->session->set('erreur', "Tous les champs sont requis");
                $this->redirect('compte/index');
            }
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
        $req->bindValue(':user_id', $this->session->get('user_id'), PDO::PARAM_INT);
        if ($req->execute()) {
            logInfo("Données insérées avec succès dans la base de données.");
        } else {
            logError("Échec de l'insertion des données dans la base de données.");
        }
    }
}
