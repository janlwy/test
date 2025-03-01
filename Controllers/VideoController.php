<?php
namespace Controllers;

use Models\VideoRepository;
use Fonctions\Validator;
use Exception;

class VideoController extends BaseController implements IController
{
    protected $repository;
    
    public function __construct() {
        parent::__construct();
        $this->repository = new VideoRepository();
    }

    public function index()
    {
        $this->checkAuth();
        $this->redirect('video/list');
    }

    public function list() {
        $this->checkAuth();
        try {
            $userId = $this->session->get('user_id');
            if (!$userId) {
                throw new Exception("Utilisateur non identifié");
            }
            
            $videos = $this->repository->findAllByUser($userId);
            $datas = [
                'videos' => $videos,
                'session' => $this->session
            ];
            
            generate("Views/main/videoList.php", $datas, "Views/base.html.php", "Liste Vidéos");
        } catch (Exception $e) {
            $_SESSION['erreur'] = $e->getMessage();
            $this->redirect('video/index');
        }
    }

    public function player() {
        $this->checkAuth();
        $userId = $this->session->get('user_id');
        
        if ($userId === null) {
            $_SESSION['erreur'] = "Erreur : utilisateur non identifié.";
            $this->redirect('connexion/index');
            return;
        }

        // Récupérer soit un ID unique soit un tableau d'IDs
        $singleId = $_GET['id'] ?? null;
        $multipleIds = $_GET['ids'] ?? [];
        
        $videos = [];
        
        if ($singleId !== null) {
            // Lecture d'une seule vidéo
            $video = $this->repository->findById($singleId);
            if ($video && $video->getUserId() == $userId) {
                $videos[] = $video;
            }
        } elseif (!empty($multipleIds)) {
            // Lecture d'une playlist
            foreach ($multipleIds as $id) {
                $video = $this->repository->findById($id);
                if ($video && $video->getUserId() == $userId) {
                    $videos[] = $video;
                }
            }
        }

        try {
            if (empty($videos)) {
                $_SESSION['erreur'] = "Aucune vidéo disponible.";
                $this->redirect('video/list');
                return;
            }

            // Préparer les données pour le lecteur
            $formattedVideos = array_map(function($video) {
                return [
                    'id' => $video->getId(),
                    'title' => $video->getTitle(),
                    'description' => $video->getDescription(),
                    'path' => $video->getFullPath(),
                    'thumbnail' => $video->getThumbnailPath()
                ];
            }, $videos);

            $datas = [
                'pageTitle' => "Lecteur Vidéo",
                'videos' => $videos,
                'formattedVideos' => $formattedVideos,
                'session' => $this->session
            ];
        
            generate("Views/main/video.php", $datas, "Views/base.html.php", "Lecteur Vidéo");
        } catch (Exception $e) {
            logError("Erreur dans player: " . $e->getMessage());
            $_SESSION['erreur'] = "Une erreur est survenue lors du chargement des vidéos.";
            $this->redirect('video/list');
        }
    }

    public function create() {
        $this->checkAuth();
        $this->checkCSRF();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validator = new Validator();
            $rules = [
                'title' => ['required', ['min', 2], ['max', 100]],
                'description' => ['required', ['max', 500]]
            ];
            
            if (!$validator->validate($_POST, $rules)) {
                $_SESSION['erreur'] = $validator->getFirstError();
                $this->redirect('video/index');
                return;
            }
            
            // TODO: Implémenter la validation et l'upload de fichier
        }
    }

    public function update($id) {
        $this->checkAuth();
        $this->checkCSRF();
        // TODO: Implémenter la mise à jour
    }

    public function delete($id) {
        $this->checkAuth();
        $this->checkCSRF();
        // TODO: Implémenter la suppression
    }
}
