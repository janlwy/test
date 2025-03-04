<?php
namespace Controllers;

use Models\Audio;
use Models\AudioRepository;
use Models\Manager;
use Session\SessionManager;
use Controllers\IController;

class AudioController extends BaseController implements IController
{
    private $audioRepository;
    
    public function __construct() {
        parent::__construct();
        $this->audioRepository = new AudioRepository();
    }

    public function index()
    {
        $this->checkAuth();
        $datas = [];
        generate("Views/main/audioList.php", $datas, "Views/base.html.php", "Audio");
    }

    public function listeAudio()
    {
        try {
            $userId = $_SESSION['user_id'] ?? null;
            if ($userId === null) {
                throw new Exception("Utilisateur non identifié");
            }
            $audios = $this->audioRepository->findAllByUser($userId);
            return $this->generateAudioTable($audios);
        } catch (Exception $e) {
            logError("Erreur dans listeAudio: " . $e->getMessage());
            $_SESSION['erreur'] = $e->getMessage();
            header('Location: ?url=connexion/index');
            exit();
        }
    }

    public function generateAudioTable($audios)
    {
        if (empty($audios)) {
            $list = "<p>Aucun enregistrement audio trouvé.</p>";
        } else {
            $list = "<br><br><dl class='dl'>";
            foreach ($audios as $audio) {
                // Préparer les données pour chaque audio
                $id = $audio->getId();
                $title = htmlspecialchars($audio->getTitle(), ENT_QUOTES, 'UTF-8');
                $artist = htmlspecialchars($audio->getArtist(), ENT_QUOTES, 'UTF-8');
                $image = htmlspecialchars($audio->getImage(), ENT_QUOTES, 'UTF-8');
                $path = htmlspecialchars($audio->getPath(), ENT_QUOTES, 'UTF-8');
                
                // Construire les chemins complets
                $fullPath = 'Ressources/audio/' . $path;
                $fullImage = 'Ressources/images/pochettes/' . $image;
                
                // Créer les éléments HTML
                $selectionligne = "<input type='checkbox' class='select-audio' data-audio-id='$id' data-audio-path='$path'>";
                $modifier = "<a class='btnBase vert' href='index.php?url=audio/update/$id#form-add'><i class='material-icons'>update</i></a>";
                $supprimer = "<a class='btnBase rouge' href='index.php?url=audio/delete/$id'><i class='material-icons'>delete</i></a>";
                
                $list .= "<div class='audio-item'>";
                $list .= "<img class='photoAudio' src='$fullImage' alt='cover'>";
                $list .= "<div class='audio-details'>";
                $list .= "<h4>$title</h4>";
                $list .= "<p>$artist</p>";
                $list .= "<div class='button-group'>$selectionligne $modifier $supprimer</div>";
                $list .= "</div></div>";
            }
            $list .= "</dl><br><br>";
        }
        return $list;
    }

    public function list()
    {
        try {
            $this->checkAuth();
            
            // Ne pas vérifier CSRF pour les requêtes GET
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->checkCSRF();
            }
            
            $userId = $_SESSION['user_id'] ?? null;
            if (!$userId) {
                throw new Exception("Utilisateur non identifié");
            }
            
            $search = $_GET['search'] ?? '';
            $filter = $_GET['filter'] ?? '';
            
            $audios = $this->audioRepository->findAllByUser($userId);
            if ($audios === false) {
                throw new Exception("Erreur lors du chargement des pistes audio");
            }
        
        // Préparer les données complètes pour chaque audio
        foreach ($audios as $audio) {
            $audioPath = 'Ressources/audio/' . $audio->getPath();
            $imagePath = 'Ressources/images/pochettes/' . $audio->getImage();
                
            // Ajouter les données complètes pour le data-audios
            $audio->jsonData = [
                'id' => $audio->getId(),
                'title' => $audio->getTitle(),
                'artist' => $audio->getArtist(),
                'path' => $audio->getPath(),
                'fullPath' => $audioPath,
                'image' => $audio->getImage(),
                'fullImage' => $imagePath
            ];
        }
        
        // Filtrer les résultats
        if (!empty($search)) {
            $audios = array_filter($audios, function($audio) use ($search) {
                return stripos($audio->getTitle(), $search) !== false || 
                       stripos($audio->getArtist(), $search) !== false;
            });
        }
        
        $audioList = $this->generateAudioTable($audios);
        $datas = [
            'audioList' => $audioList,
            'audios' => $audios,
            'search' => $search,
            'filter' => $filter,
            'session' => $this->session
        ];
        
        generate("Views/main/audioList.php", $datas, "Views/base.html.php", "Liste Audio");
        } catch (Exception $e) {
            logError("Erreur dans list(): " . $e->getMessage());
            $_SESSION['erreur'] = "Erreur lors du chargement de la liste audio: " . $e->getMessage();
            header('Location: ?url=compte/index');
            exit();
        }
    }
    public function create() {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if (!$this->session->has('csrf_token') || 
                    !isset($_POST['csrf_token']) ||
                    !hash_equals($this->session->get('csrf_token'), $_POST['csrf_token'])) {
                    throw new Exception("jeton invalide.");
                }
                $this->addMusic($_POST, $_FILES);
            } catch (Exception $e) {
                logError("Erreur CSRF : " . $e->getMessage());
                $_SESSION['erreur'] = "Erreur de sécurité. Veuillez réessayer.";
                header('Location: ?url=compte/index#form-add');
                exit();
            }
        } else {
            header('Location: ?url=compte/index#form-add');
            exit();
        }
    }

    public function update($id) {
        $this->checkAuth();
        
        if (!$id) {
            $_SESSION['erreur'] = "ID non spécifié";
            header('Location: ?url=audio/list');
            exit();
        }

        try {
            $audio = $this->audioRepository->findById($id);
            if (!$audio) {
                throw new Exception("Audio non trouvé");
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->checkCSRF();
                
                // Validation des données
                $title = trim($_POST['title']);
                $artist = trim($_POST['artiste']);
                
                if (empty($title) || empty($artist)) {
                    throw new Exception("Les champs titre et artiste sont requis");
                }

                $data = [
                    'title' => $title,
                    'artist' => $artist,
                    'id' => $id
                ];

                // Gestion de l'image si une nouvelle est uploadée
                if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
                    $allowedImageTypes = ['image/jpeg', 'image/png', 'image/webp'];
                    $imageErrors = $this->session->validateFileUpload($_FILES['image'], $allowedImageTypes, 5 * 1024 * 1024);
                    
                    if (!empty($imageErrors)) {
                        throw new Exception(implode("\n", $imageErrors));
                    }

                    $imageExt = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $uniqueImage = uniqid() . '.' . $imageExt;
                    $imagePath = 'Ressources/images/pochettes/' . $uniqueImage;

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                        // Supprimer l'ancienne image
                        $oldImage = 'Ressources/images/pochettes/' . $audio->getImage();
                        if (file_exists($oldImage)) {
                            unlink($oldImage);
                        }
                        $data['image'] = $uniqueImage;
                    } else {
                        throw new Exception("Erreur lors du téléchargement de l'image");
                    }
                }

                // Mise à jour dans la base de données
                $manager = new Manager();
                $manager->updateTable('audio', $data, $id);

                $_SESSION['message'] = "Audio mis à jour avec succès";
                header('Location: ?url=audio/list');
                exit();
            } else {
                $datas = [
                    'pageTitle' => 'Modifier un audio',
                    'audio' => $audio,
                    'session' => $this->session,
                    'formAction' => "?url=audio/update/$id"
                ];
                generate("Views/main/compte.php", $datas, "Views/base.html.php", "Modifier un audio");
                echo "<script>document.addEventListener('DOMContentLoaded', function() {
                    var audioCollapsible = document.querySelector('.collapsible');
                    if (audioCollapsible) {
                        audioCollapsible.classList.add('activeCollapse');
                        var content = audioCollapsible.nextElementSibling;
                        content.style.maxHeight = content.scrollHeight + 'px';
                        setTimeout(function() {
                            document.querySelector('#form-add').scrollIntoView({ behavior: 'smooth' });
                        }, 100);
                    }
                });</script>";
            }
        } catch (Exception $e) {
            $_SESSION['erreur'] = $e->getMessage();
            header('Location: ?url=audio/list');
        }
        exit();
    }

    public function player() {
        $this->checkAuth();
        $userId = $_SESSION['user_id'] ?? null;
        
        if ($userId === null) {
            $_SESSION['erreur'] = "Erreur : utilisateur non identifié.";
            header('Location: ?url=connexion/index');
            exit();
        }

        // Récupérer soit un ID unique soit un tableau d'IDs
        $singleId = $_GET['id'] ?? null;
        $multipleIds = $_GET['ids'] ?? [];
        $selectedTracks = $_SESSION['selected_tracks'] ?? [];
        
        $audios = [];
        
        if ($singleId !== null) {
            // Lecture d'une seule piste
            $audio = $this->audioRepository->findById($singleId);
            if ($audio && $audio->getUserId() == $userId) {
                $audios[] = $audio;
            }
        } elseif (!empty($multipleIds)) {
            // Lecture d'une playlist
            foreach ($multipleIds as $id) {
                $audio = $this->audioRepository->findById($id);
                if ($audio && $audio->getUserId() == $userId) {
                    $audios[] = $audio;
                }
            }
        } else {
            // Si aucune piste n'est spécifiée, charger toutes les pistes de l'utilisateur
            $audios = $this->audioRepository->findAllByUser($userId);
        }

        try {
            if (empty($audios)) {
                $_SESSION['erreur'] = "Aucune piste disponible pour l'utilisateur.";
                header('Location: ?url=audio/list');
                exit();
            }

            // Préparer les données pour le lecteur
            $formattedAudios = array_map(function($audio) {
                $audioPath = 'Ressources/audio/' . $audio->getPath();
                if (!file_exists($audioPath)) {
                    logError("Fichier audio manquant: " . $audioPath);
                    return null;
                }
                return [
                    'id' => $audio->getId(),
                    'title' => $audio->getTitle(),
                    'artist' => $audio->getArtist(),
                    'fullPath' => $audioPath,
                    'image' => 'Ressources/images/pochettes/' . $audio->getImage(),
                    'fullImage' => 'Ressources/images/pochettes/' . $audio->getImage()
                ];
            }, $audios);

            $formattedAudios = array_filter($formattedAudios);

            $datas = [
                'pageTitle' => "Lecteur Audio",
                'userId' => $userId,
                'audios' => $audios,
                'formattedAudios' => $formattedAudios,
                'session' => $this->session
            ];
        
            generate("Views/main/audio.php", $datas, "Views/base.html.php", "Lecteur Audio");
        } catch (Exception $e) {
            logError("Erreur dans player: " . $e->getMessage());
            $_SESSION['erreur'] = "Une erreur est survenue lors du chargement des fichiers audio.";
            header('Location: ?url=audio/list');
            exit();
        }
    }

    public function delete($id) {
        $this->checkAuth();
        $this->checkCSRF();
        
        if (!$id) {
            $_SESSION['erreur'] = "ID non spécifié";
            header('Location: ?url=audio/list');
            exit();
        }

        try {
            $this->audioRepository->delete($id);
            $_SESSION['message'] = "Audio supprimé avec succès";
        } catch (Exception $e) {
            $_SESSION['erreur'] = $e->getMessage();
        }
        
        header('Location: ?url=audio/list');
        exit();
    }

    private function addMusic($post, $files)
    {
        logInfo("Début de la méthode addMusic");
        try {
            // Vérifier que les champs requis sont présents
            if (!isset($post['title'], $post['artiste'], $files['image'], $files['path'])) {
                throw new Exception("Tous les champs sont requis");
            }
            
            // Validation des données
            $title = htmlspecialchars(trim($post['title']), ENT_QUOTES, 'UTF-8');
            $artist = htmlspecialchars(trim($post['artiste']), ENT_QUOTES, 'UTF-8');
            
            if (empty($title) || empty($artist)) {
                throw new Exception("Les champs titre et artiste sont requis");
            }
            
            // Validation des fichiers
            $allowedImageTypes = ['image/jpeg', 'image/png', 'image/webp'];
            $allowedAudioTypes = [
                // Types audio standards
                'audio/mpeg' => 'mp3',
                'audio/mp4' => 'm4a',
                'audio/wav' => 'wav',
                'audio/x-m4a' => 'm4a',
                'audio/aac' => 'aac',
                'audio/ogg' => 'ogg',
                // Types alternatifs pour M4A
                'video/mp4' => 'm4a',
                'video/x-m4v' => 'm4a',
                'application/mp4' => 'm4a',
                'audio/x-hx-aac-adts' => 'm4a',
                'audio/x-m4a' => 'm4a',
                'audio/mp4a-latm' => 'm4a'
            ];
            $allowedAudioExtensions = array_values($allowedAudioTypes);
            $allowedAudioMimeTypes = array_keys($allowedAudioTypes);
            
            // Vérification du type MIME
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $imageMimeType = $finfo->file($files['image']['tmp_name']);
            $audioMimeType = $finfo->file($files['path']['tmp_name']);
            
            logInfo("Type MIME de l'image : " . $imageMimeType);
            logInfo("Type MIME de l'audio : " . $audioMimeType);
            
            // Validation de l'image
            if (!in_array($imageMimeType, $allowedImageTypes)) {
                logError("Type de fichier image non autorisé : " . $imageMimeType);
                throw new Exception("Type de fichier image non autorisé. Type détecté : " . $imageMimeType 
                    . ". Types autorisés : " . implode(', ', $allowedImageTypes));
            }
            
            // Validation de l'audio : d'abord vérifier l'extension
            $audioExtension = strtolower(pathinfo($files['path']['name'], PATHINFO_EXTENSION));
            if (!in_array($audioExtension, $allowedAudioExtensions)) {
                $errorMsg = sprintf(
                    "Extension de fichier audio non autorisée.\nExtension : %s\nExtensions autorisées : %s",
                    $audioExtension,
                    implode(', ', $allowedAudioExtensions)
                );
                logError($errorMsg);
                throw new Exception($errorMsg);
            }
            
            // Ensuite vérifier le type MIME
            if (!in_array($audioMimeType, $allowedAudioMimeTypes)) {
                $errorMsg = sprintf(
                    "Type MIME audio non autorisé.\nType détecté : %s\nTypes autorisés : %s",
                    $audioMimeType,
                    implode(', ', $allowedAudioMimeTypes)
                );
                logError($errorMsg);
                throw new Exception($errorMsg);
            }
            
            $imageErrors = $this->session->validateFileUpload($files['image'], $allowedImageTypes, 5 * 1024 * 1024); // 5MB
            $audioErrors = $this->session->validateFileUpload($files['path'], $allowedAudioMimeTypes, 50 * 1024 * 1024); // 50MB
            
            if (!empty($imageErrors) || !empty($audioErrors)) {
                throw new Exception(implode("\n", array_merge($imageErrors, $audioErrors)));
            }

            // Générer des noms de fichiers uniques
            $imageExt = pathinfo($files['image']['name'], PATHINFO_EXTENSION);
            $uniqueImage = uniqid() . '.' . $imageExt;
            $uniqueAudio = uniqid() . '.' . $audioExtension;
            
            // Chemins de destination
            $imagePath = 'Ressources/images/pochettes/' . $uniqueImage;
            $audioPath = 'Ressources/audio/' . $uniqueAudio;

            // Créer les répertoires s'ils n'existent pas
            if (!is_dir('Ressources/images/pochettes/')) {
                mkdir('Ressources/images/pochettes/', 0777, true);
            }
            if (!is_dir('Ressources/audio/')) {
                mkdir('Ressources/audio/', 0777, true);
            }

            // Déplacer les fichiers
            if (!move_uploaded_file($files['image']['tmp_name'], $imagePath)) {
                throw new Exception("Erreur lors du téléchargement de l'image");
            }
            
            if (!move_uploaded_file($files['path']['tmp_name'], $audioPath)) {
                // Si l'audio échoue, supprimer l'image déjà uploadée
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                throw new Exception("Le fichier est trop volumineux. La taille maximale est de 5MB pour les images et 50MB pour les fichiers audio.");
            }

            // Insérer dans la base de données
            $userId = $_SESSION['user_id'] ?? null;
            if ($userId === null) {
                throw new Exception("Utilisateur non identifié");
            }

            $data = [
                'title' => $title,
                'artist' => $artist,
                'image' => $uniqueImage,
                'path' => $uniqueAudio,
                'user_id' => $userId
            ];

            $manager = new Manager();
            $manager->insertTable('audio', $data);

            $_SESSION['message'] = "Audio ajoutée avec succès";
            header('Location: ?url=audio/list');
            exit();

        } catch (Exception $e) {
            logError("Erreur dans addMusic: " . $e->getMessage());
            $_SESSION['erreur'] = $e->getMessage();
            header('Location: ?url=compte/index#form-add');
            exit();
        }
    }
    public function saveSelection() {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['erreur'] = 'Méthode non autorisée';
            header('Location: ?url=audio/list');
            exit();
        }

        $selectedTracks = $_POST['tracks'] ?? [];
        if (empty($selectedTracks)) {
            $_SESSION['erreur'] = 'Aucune piste sélectionnée';
            header('Location: ?url=audio/list');
            exit();
        }

        $userId = $_SESSION['user_id'];
        $audios = [];
        
        foreach ($selectedTracks as $trackId) {
            $audio = $this->audioRepository->findById((int)$trackId);
            if ($audio && $audio->getUserId() == $userId) {
                $audios[] = $audio;
            }
        }

        if (empty($audios)) {
            $_SESSION['erreur'] = 'Aucune piste valide sélectionnée';
            header('Location: ?url=audio/list');
            exit();
        }

        $_SESSION['selected_tracks'] = $selectedTracks;
        header('Location: ?url=audio/player');
        exit();
    }
}
