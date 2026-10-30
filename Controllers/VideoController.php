<?php

namespace Controllers;

use Models\VideoRepository;
use Models\Video;
use Exception;
use finfo;

class VideoController extends BaseController implements IController
{
    /** @var VideoRepository */
    protected $repository;

    public function __construct()
    {
        parent::__construct();
        $this->repository = new VideoRepository();
    }

    public function index()
    {
        $this->checkAuth();
        $this->redirect('video/list');
    }

    public function list()
    {
        $this->checkAuth();
        try {
            $userId = (int)$this->session->get('user_id');
            if (!$userId) {
                throw new Exception("Utilisateur non identifié");
            }

            $videos = $this->repository->findAllByUser($userId);
            $datas = [
                'videos' => $videos,
                'session' => $this->session
            ];

            generate("Views/main/videoList.php", $datas, "Views/base.html.php", "Mes Vidéos");
        } catch (Exception $e) {
            $_SESSION['erreur'] = $e->getMessage();
            $this->redirect('mediabox/index');
        }
    }

    public function player()
    {
        $this->checkAuth();
        $userId = (int)$this->session->get('user_id');

        $singleId = isset($_GET['id']) ? (int)$_GET['id'] : null;
        $multipleIds = $_GET['ids'] ?? [];

        $videos = [];
        if ($singleId) {
            $video = $this->repository->findById($singleId);
            if ($video && $video->getUserId() === $userId) {
                $videos[] = $video;
            }
        } elseif (!empty($multipleIds) && is_array($multipleIds)) {
            foreach ($multipleIds as $id) {
                $video = $this->repository->findById((int)$id);
                if ($video && $video->getUserId() === $userId) {
                    $videos[] = $video;
                }
            }
        } else {
            $videos = $this->repository->findAllByUser($userId);
        }

        if (empty($videos)) {
            $_SESSION['erreur'] = "Aucune vidéo disponible à la lecture.";
            $this->redirect('video/list');
        }

        $formattedVideos = [];
        foreach ($videos as $video) {
            $path = $video->getFullPath();
            if (file_exists($path)) {
                $formattedVideos[] = [
                    'id' => $video->getId(),
                    'title' => $video->getTitle(),
                    'description' => $video->getDescription() ?? '',
                    'path' => $path,
                    'thumbnail' => $video->getThumbnailPath()
                ];
            }
        }

        if (empty($formattedVideos)) {
            $_SESSION['erreur'] = "Les fichiers vidéo sélectionnés sont introuvables sur le serveur.";
            $this->redirect('video/list');
        }

        $datas = [
            'pageTitle' => "Lecteur Vidéo",
            'videos' => $videos,
            'formattedVideos' => $formattedVideos,
            'session' => $this->session
        ];

        generate("Views/main/video.php", $datas, "Views/base.html.php", "Lecteur Vidéo");
    }

    public function create()
    {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkCSRF();
            try {
                $title = trim($_POST['title'] ?? '');
                $description = trim($_POST['description'] ?? '');

                if (empty($title)) {
                    throw new Exception("Le titre de la vidéo est requis.");
                }

                if (!isset($_FILES['video_file']) || $_FILES['video_file']['error'] !== UPLOAD_ERR_OK) {
                    throw new Exception("Veuillez sélectionner ou enregistrer un fichier vidéo valide.");
                }

                $allowedVideoTypes = [
                    'video/mp4' => 'mp4',
                    'video/webm' => 'webm',
                    'video/ogg' => 'ogv',
                    'video/quicktime' => 'mov',
                    'video/x-m4v' => 'm4v'
                ];

                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->file($_FILES['video_file']['tmp_name']);

                if (!array_key_exists($mimeType, $allowedVideoTypes)) {
                    throw new Exception("Format vidéo non autorisé ($mimeType). Formats acceptés : MP4, WEBM, OGG, MOV.");
                }

                if ($_FILES['video_file']['size'] > 100 * 1024 * 1024) {
                    throw new Exception("La vidéo dépasse la limite de 100 Mo.");
                }

                if (!is_dir('Ressources/videos/')) {
                    mkdir('Ressources/videos/', 0755, true);
                }
                if (!is_dir('Ressources/images/thumbnails/')) {
                    mkdir('Ressources/images/thumbnails/', 0755, true);
                }

                $uniqueThumb = 'default_thumb.png';
                if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK && $_FILES['thumbnail']['size'] > 0) {
                    $allowedImg = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
                    $imgMime = $finfo->file($_FILES['thumbnail']['tmp_name']);
                    if (!array_key_exists($imgMime, $allowedImg)) {
                        throw new Exception("Format de miniature non supporté.");
                    }
                    $uniqueThumb = uniqid('thb_') . '.' . $allowedImg[$imgMime];
                    move_uploaded_file($_FILES['thumbnail']['tmp_name'], 'Ressources/images/thumbnails/' . $uniqueThumb);
                }

                $ext = $allowedVideoTypes[$mimeType];
                $uniqueVideo = uniqid('vid_') . '.' . $ext;
                if (!move_uploaded_file($_FILES['video_file']['tmp_name'], 'Ressources/videos/' . $uniqueVideo)) {
                    throw new Exception("Erreur lors de la sauvegarde du fichier vidéo.");
                }

                $video = new Video([
                    'title' => $title,
                    'description' => $description,
                    'thumbnail' => $uniqueThumb,
                    'path' => $uniqueVideo,
                    'user_id' => (int)$this->session->get('user_id')
                ]);

                $this->repository->save($video);
                $_SESSION['message'] = "Vidéo ajoutée avec succès.";
            } catch (Exception $e) {
                logError("Erreur dans VideoController::create: " . $e->getMessage());
                $_SESSION['erreur'] = $e->getMessage();
            }
        }
        $this->redirect('video/list');
    }

    public function update($id = null)
    {
        $this->checkAuth();
        $userId = (int)$this->session->get('user_id');
        $id = (int)$id;

        try {
            $video = $this->repository->findById($id);
            if (!$video || $video->getUserId() !== $userId) {
                throw new Exception("Vidéo introuvable ou accès refusé.");
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->checkCSRF();
                $title = trim($_POST['title'] ?? '');
                $description = trim($_POST['description'] ?? '');

                if (empty($title)) {
                    throw new Exception("Le titre est requis.");
                }

                $video->setTitle($title);
                $video->setDescription($description);

                if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK && $_FILES['thumbnail']['size'] > 0) {
                    $allowedImg = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
                    $finfo = new finfo(FILEINFO_MIME_TYPE);
                    $imgMime = $finfo->file($_FILES['thumbnail']['tmp_name']);
                    if (array_key_exists($imgMime, $allowedImg)) {
                        if (!is_dir('Ressources/images/thumbnails/')) {
                            mkdir('Ressources/images/thumbnails/', 0755, true);
                        }
                        $uniqueThumb = uniqid('thb_') . '.' . $allowedImg[$imgMime];
                        if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], 'Ressources/images/thumbnails/' . $uniqueThumb)) {
                            $oldThumb = $video->getThumbnail();
                            if (!empty($oldThumb) && $oldThumb !== 'default_thumb.png') {
                                @unlink('Ressources/images/thumbnails/' . $oldThumb);
                            }
                            $video->setThumbnail($uniqueThumb);
                        }
                    }
                }

                $this->repository->save($video);
                $_SESSION['message'] = "Vidéo mise à jour avec succès.";
                $this->redirect('video/list');
            }

            $videos = $this->repository->findAllByUser($userId);
            $datas = [
                'videos' => $videos,
                'editVideo' => $video,
                'session' => $this->session
            ];
            generate("Views/main/videoList.php", $datas, "Views/base.html.php", "Modifier la Vidéo");
        } catch (Exception $e) {
            $_SESSION['erreur'] = $e->getMessage();
            $this->redirect('video/list');
        }
    }

    public function delete($id = null)
    {
        $this->checkAuth();
        $userId = (int)$this->session->get('user_id');
        $id = (int)$id;

        try {
            $video = $this->repository->findById($id);
            if (!$video || $video->getUserId() !== $userId) {
                throw new Exception("Vidéo introuvable ou suppression non autorisée.");
            }
            $this->repository->delete($id);
            $_SESSION['message'] = "Vidéo supprimée avec succès.";
        } catch (Exception $e) {
            $_SESSION['erreur'] = $e->getMessage();
        }
        $this->redirect('video/list');
    }
}
