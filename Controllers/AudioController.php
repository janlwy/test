<?php

namespace Controllers;

use Models\Audio;
use Models\AudioRepository;
use Exception;
use finfo;

class AudioController extends BaseController implements IController
{
    private AudioRepository $audioRepository;

    public function __construct()
    {
        parent::__construct();
        $this->audioRepository = new AudioRepository();
    }

    public function index()
    {
        $this->checkAuth();
        $this->redirect('audio/list');
    }

    public function list()
    {
        $this->checkAuth();
        try {
            $userId = (int)$this->session->get('user_id');
            if (!$userId) {
                throw new Exception("Utilisateur non identifié");
            }

            $audios = $this->audioRepository->findAllByUser($userId);
            $datas = [
                'audios' => $audios,
                'session' => $this->session
            ];

            generate("Views/main/audioList.php", $datas, "Views/base.html.php", "Mes Audios & Notes Vocales");
        } catch (Exception $e) {
            logError("Erreur dans AudioController::list(): " . $e->getMessage());
            $_SESSION['erreur'] = "Erreur lors du chargement de la liste audio : " . $e->getMessage();
            $this->redirect('mediabox/index');
        }
    }

    public function create()
    {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkCSRF();
            $this->addMusic($_POST, $_FILES);
        } else {
            $this->redirect('audio/list');
        }
    }

    public function update($id = null)
    {
        $this->checkAuth();
        $userId = (int)$this->session->get('user_id');
        $id = (int)$id;

        if (!$id) {
            $_SESSION['erreur'] = "ID non spécifié";
            $this->redirect('audio/list');
        }

        try {
            $audio = $this->audioRepository->findById($id);
            if (!$audio || $audio->getUserId() !== $userId) {
                throw new Exception("Audio introuvable ou accès non autorisé.");
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->checkCSRF();

                $title = trim($_POST['title'] ?? '');
                $artist = trim($_POST['artiste'] ?? 'Note vocale');

                if (empty($title)) {
                    throw new Exception("Le titre est requis.");
                }

                $audio->setTitle($title);
                $audio->setArtist($artist ?: 'Note vocale');

                // Si une nouvelle pochette est uploadée
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK && $_FILES['image']['size'] > 0) {
                    $allowedImageTypes = ['image/jpeg', 'image/png', 'image/webp'];
                    $imageErrors = $this->session->validateFileUpload($_FILES['image'], $allowedImageTypes, 5 * 1024 * 1024);

                    if (!empty($imageErrors)) {
                        throw new Exception(implode("\n", $imageErrors));
                    }

                    if (!is_dir('Ressources/images/pochettes/')) {
                        mkdir('Ressources/images/pochettes/', 0755, true);
                    }

                    $imageExt = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    $uniqueImage = uniqid('cov_') . '.' . $imageExt;
                    $imagePath = 'Ressources/images/pochettes/' . $uniqueImage;

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                        $oldImage = $audio->getImage();
                        if (!empty($oldImage) && $oldImage !== 'default_cover.png') {
                            $oldPath = 'Ressources/images/pochettes/' . $oldImage;
                            if (file_exists($oldPath)) {
                                @unlink($oldPath);
                            }
                        }
                        $audio->setImage($uniqueImage);
                    }
                }

                $this->audioRepository->save($audio);
                $_SESSION['message'] = "Audio mis à jour avec succès.";
                $this->redirect('audio/list');
            }

            $audios = $this->audioRepository->findAllByUser($userId);
            $datas = [
                'audios' => $audios,
                'editAudio' => $audio,
                'session' => $this->session
            ];
            generate("Views/main/audioList.php", $datas, "Views/base.html.php", "Modifier l'Audio");
        } catch (Exception $e) {
            $_SESSION['erreur'] = $e->getMessage();
            $this->redirect('audio/list');
        }
    }

    public function player()
    {
        $this->checkAuth();
        $userId = (int)$this->session->get('user_id');

        $singleId = isset($_GET['id']) ? (int)$_GET['id'] : null;
        $multipleIds = $_GET['ids'] ?? [];

        $audios = [];
        if ($singleId) {
            $audio = $this->audioRepository->findById($singleId);
            if ($audio && $audio->getUserId() === $userId) {
                $audios[] = $audio;
            }
        } elseif (!empty($multipleIds) && is_array($multipleIds)) {
            foreach ($multipleIds as $id) {
                $audio = $this->audioRepository->findById((int)$id);
                if ($audio && $audio->getUserId() === $userId) {
                    $audios[] = $audio;
                }
            }
        } else {
            $audios = $this->audioRepository->findAllByUser($userId);
        }

        try {
            if (empty($audios)) {
                $_SESSION['erreur'] = "Aucune piste audio disponible à la lecture.";
                $this->redirect('audio/list');
            }

            $formattedAudios = [];
            foreach ($audios as $audio) {
                $audioPath = $audio->getFullPath();
                if (file_exists($audioPath)) {
                    $formattedAudios[] = [
                        'id' => $audio->getId(),
                        'title' => $audio->getTitle(),
                        'artist' => $audio->getArtist(),
                        'fullPath' => $audioPath,
                        'fullImage' => $audio->getFullImagePath()
                    ];
                } else {
                    logError("Fichier audio manquant sur le disque : " . $audioPath);
                }
            }

            if (empty($formattedAudios)) {
                $_SESSION['erreur'] = "Les fichiers audio sélectionnés sont introuvables sur le serveur.";
                $this->redirect('audio/list');
            }

            $datas = [
                'pageTitle' => "Lecteur Audio",
                'audios' => $audios,
                'formattedAudios' => $formattedAudios,
                'session' => $this->session
            ];

            generate("Views/main/audio.php", $datas, "Views/base.html.php", "Lecteur Audio");
        } catch (Exception $e) {
            logError("Erreur dans player: " . $e->getMessage());
            $_SESSION['erreur'] = "Une erreur est survenue lors du chargement du lecteur.";
            $this->redirect('audio/list');
        }
    }

    public function delete($id = null)
    {
        $this->checkAuth();
        $userId = (int)$this->session->get('user_id');
        $id = (int)$id;

        if (!$id) {
            $_SESSION['erreur'] = "ID non spécifié";
            $this->redirect('audio/list');
        }

        try {
            $audio = $this->audioRepository->findById($id);
            if (!$audio || $audio->getUserId() !== $userId) {
                throw new Exception("Audio introuvable ou suppression non autorisée.");
            }
            $this->audioRepository->delete($id);
            $_SESSION['message'] = "Audio supprimé avec succès.";
        } catch (Exception $e) {
            $_SESSION['erreur'] = $e->getMessage();
        }

        $this->redirect('audio/list');
    }

    private function addMusic(array $post, array $files): void
    {
        try {
            $title = trim($post['title'] ?? '');
            $artist = trim($post['artiste'] ?? '');
            if (empty($artist)) {
                $artist = 'Note vocale';
            }

            if (empty($title)) {
                throw new Exception("Le titre de l'audio ou de la note vocale est requis.");
            }

            if (!isset($files['path']) || $files['path']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Veuillez sélectionner ou enregistrer un fichier audio valide.");
            }

            $allowedAudioTypes = [
                'audio/mpeg' => 'mp3',
                'audio/mp3' => 'mp3',
                'audio/mp4' => 'm4a',
                'audio/x-m4a' => 'm4a',
                'audio/mp4a-latm' => 'm4a',
                'audio/aac' => 'aac',
                'audio/x-hx-aac-adts' => 'm4a',
                'audio/wav' => 'wav',
                'audio/x-wav' => 'wav',
                'audio/wave' => 'wav',
                'audio/ogg' => 'ogg',
                'audio/webm' => 'webm',
                'video/webm' => 'webm', // MediaRecorder sous Chrome/Edge génère souvent un conteneur video/webm pour l'audio
                'video/mp4' => 'm4a',
                'application/mp4' => 'm4a',
                'application/ogg' => 'ogg'
            ];

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $audioMimeType = $finfo->file($files['path']['tmp_name']);

            if (!array_key_exists($audioMimeType, $allowedAudioTypes)) {
                throw new Exception("Format audio non supporté ($audioMimeType). Formats acceptés : MP3, M4A, WAV, OGG, WEBM, AAC.");
            }

            if ($files['path']['size'] > 50 * 1024 * 1024) {
                throw new Exception("Le fichier audio dépasse la limite de 50 Mo.");
            }

            $ext = strtolower(pathinfo($files['path']['name'], PATHINFO_EXTENSION));
            if (empty($ext) || !in_array($ext, ['mp3', 'm4a', 'wav', 'ogg', 'webm', 'aac'])) {
                $ext = $allowedAudioTypes[$audioMimeType];
            }

            if (!is_dir('Ressources/audio/')) {
                mkdir('Ressources/audio/', 0755, true);
            }
            if (!is_dir('Ressources/images/pochettes/')) {
                mkdir('Ressources/images/pochettes/', 0755, true);
            }

            // Pochette optionnelle
            $uniqueImage = 'default_cover.png';
            if (isset($files['image']) && $files['image']['error'] === UPLOAD_ERR_OK && $files['image']['size'] > 0) {
                $allowedImageTypes = ['image/jpeg', 'image/png', 'image/webp'];
                $imageMimeType = $finfo->file($files['image']['tmp_name']);
                if (!in_array($imageMimeType, $allowedImageTypes)) {
                    throw new Exception("Format d'image non autorisé ($imageMimeType). Utilisez JPG, PNG ou WEBP.");
                }
                if ($files['image']['size'] > 5 * 1024 * 1024) {
                    throw new Exception("L'image dépasse la taille maximale de 5 Mo.");
                }
                $imageExt = strtolower(pathinfo($files['image']['name'], PATHINFO_EXTENSION)) ?: 'png';
                $uniqueImage = uniqid('cov_') . '.' . $imageExt;
                if (!move_uploaded_file($files['image']['tmp_name'], 'Ressources/images/pochettes/' . $uniqueImage)) {
                    throw new Exception("Erreur lors de l'enregistrement de la pochette.");
                }
            }

            $uniqueAudio = uniqid('aud_') . '.' . $ext;
            $audioPath = 'Ressources/audio/' . $uniqueAudio;

            if (!move_uploaded_file($files['path']['tmp_name'], $audioPath)) {
                if ($uniqueImage !== 'default_cover.png' && file_exists('Ressources/images/pochettes/' . $uniqueImage)) {
                    @unlink('Ressources/images/pochettes/' . $uniqueImage);
                }
                throw new Exception("Erreur lors de la sauvegarde du fichier audio.");
            }

            $audio = new Audio([
                'title' => $title,
                'artist' => $artist,
                'image' => $uniqueImage,
                'path' => $uniqueAudio,
                'user_id' => (int)$this->session->get('user_id')
            ]);

            $this->audioRepository->save($audio);
            $_SESSION['message'] = "Enregistrement audio ajouté avec succès.";
        } catch (Exception $e) {
            logError("Erreur dans addMusic: " . $e->getMessage());
            $_SESSION['erreur'] = $e->getMessage();
        }
        $this->redirect('audio/list');
    }
}
