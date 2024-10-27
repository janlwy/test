<?php

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
        $manager = new Manager();
        $userId = $_SESSION['user_id'] ?? null;
        if ($userId === null) {
            logError("Erreur : user_id est null");
            $_SESSION['erreur'] = "Erreur : utilisateur non identifié.";
            header('Location: ?url=connexion/index');
            exit();
        }
        $audios = $manager->readTableAll('audio', $userId);
        if ($audios) {
            $_SESSION['audios'] = $audios; // Stocker les audios dans la session
        } else {
            $_SESSION['audios'] = [];
        }
        return $this->generateAudioTable($audios);
    }

    public function generateAudioTable($audios)
    {
        if (empty($audios)) {
            $list = "<p>Aucun enregistrement audio trouvé.</p>";
        } else {
            $list = "<br><br><dl class='dl'>";
            foreach ($audios as $audio) {
                $id = $audio->getId();
                $title = htmlspecialchars($audio->getTitle(), ENT_QUOTES, 'UTF-8');
                $artist = htmlspecialchars($audio->getArtist(), ENT_QUOTES, 'UTF-8');
                $image = htmlspecialchars($audio->getImage(), ENT_QUOTES, 'UTF-8');
                $afficher = "<input type='checkbox' class='select-audio' data-audio-id='$id'>";
                $modifier = "<a class='btnBase vert' href='index.php?url=audio/update/$id'>Modifier</a>";
                $supprimer = "<a class='btnBase rouge' href='index.php?url=audio/delete/$id'>Supprimer</a>";
                $list .= "<div class='audio-item'>";

                $list .= "<img class='photoAudio' src='Ressources/images/pochettes/$image' alt='cover'>";
                $list .= "<div class='audio-details'>";
                $list .= "<h4>$title</h4>";
                $list .= "<p>$artist</p>";

                $list .= "<div class='button-group'>$afficher $modifier $supprimer</div>";
                $list .= "</div></div>";
            }
            $list .= "</dl><br><br>";
        }
        return $list;
    }

    public function list()
    {
        $this->checkAuth();
        
        $userId = $_SESSION['user_id'];
        $search = $_GET['search'] ?? '';
        $filter = $_GET['filter'] ?? '';
        
        $audios = $this->audioRepository->findAllByUser($userId);
        
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
        
        generate("Views/main/audioList.php", $datas, "Views/base.html.php", "Liste des Audio");
    }
    public function create() {
        $this->checkAuth();
        $this->checkCSRF();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->addMusic($_POST, $_FILES);
        } else {
            header('Location: ?url=compte/index#form-add');
            exit();
        }
    }

    public function update($id) {
        $this->checkAuth();
        $this->checkCSRF();
        
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
                // TODO: Implement update logic
                $_SESSION['message'] = "Audio mis à jour avec succès";
                header('Location: ?url=audio/list');
            } else {
                // TODO: Display update form
                header('Location: ?url=audio/list');
            }
        } catch (Exception $e) {
            $_SESSION['erreur'] = $e->getMessage();
            header('Location: ?url=audio/list');
        }
        exit();
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
        // Vérifier que les champs requis sont présents
        if (isset($_POST['title'], $_POST['artiste'], $_FILES['image'], $_FILES['path'])) {
            logInfo("Champs requis présents");
            
            // Validation des données
            $title = htmlspecialchars(trim($_POST['title']), ENT_QUOTES, 'UTF-8');
            $artist = htmlspecialchars(trim($_POST['artiste']), ENT_QUOTES, 'UTF-8');
            
            if (empty($title) || empty($artist)) {
                $_SESSION['erreur'] = "Les champs titre et artiste sont requis.";
                header('Location: ?url=compte/index');
                exit();
            }
            
            // Validation des fichiers
            $allowedImageTypes = ['image/jpeg', 'image/png', 'image/webp'];
            $allowedAudioTypes = ['audio/mpeg', 'audio/mp4', 'audio/wav', 'audio/x-m4a'];
            
            // Vérification supplémentaire du type MIME réel
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $imageMimeType = $finfo->file($_FILES['image']['tmp_name']);
            $audioMimeType = $finfo->file($_FILES['path']['tmp_name']);
            
            if (!in_array($imageMimeType, $allowedImageTypes)) {
                $_SESSION['erreur'] = "Type de fichier image non autorisé.";
                header('Location: ?url=compte/index');
                exit();
            }
            
            if (!in_array($audioMimeType, $allowedAudioTypes)) {
                $_SESSION['erreur'] = "Type de fichier audio non autorisé.";
                header('Location: ?url=compte/index');
                exit();
            }
            
            $imageErrors = $this->session->validateFileUpload($_FILES['image'], $allowedImageTypes, 2 * 1024 * 1024); // 2MB
            $audioErrors = $this->session->validateFileUpload($_FILES['path'], $allowedAudioTypes, 10 * 1024 * 1024); // 10MB
            
            if (!empty($imageErrors) || !empty($audioErrors)) {
                $_SESSION['erreur'] = implode("\n", array_merge($imageErrors, $audioErrors));
                header('Location: ?url=compte/index');
                exit();
            }
            
            $image = $_FILES['image'];
            $path = $_FILES['path'];

            // Valider et déplacer les fichiers uploadés
            $imagePath = 'Ressources/images/pochettes/' . basename($image['name']);
            $audioPath = 'Ressources/audio/' . basename($path['name']);

            logInfo("Tentative de déplacement de l'image vers : $imagePath");
            logInfo("Tentative de déplacement de l'audio vers : $audioPath");

            if (move_uploaded_file($image['tmp_name'], $imagePath)) {
                logInfo("Image déplacée avec succès");
            } else {
                logError("Erreur lors du déplacement de l'image : " . print_r(error_get_last(), true));
                $_SESSION['erreur'] = "Erreur lors du téléchargement des fichiers. Vérifiez les permissions des dossiers.";
            }

            if (move_uploaded_file($path['tmp_name'], $audioPath)) {
                logInfo("Audio déplacé avec succès");
                // Insérer les données dans la base de données
                $manager = new Manager();
                $userId = $_SESSION['user_id'] ?? null;
                if ($userId === null) {
                    logError("Erreur : user_id est null");
                    $_SESSION['erreur'] = "Erreur : utilisateur non identifié.";
                    header('Location: ?url=compte/index');
                    exit();
                }

                $data = [
                    'title' => $title,
                    'artist' => $artist,
                    'image' => basename($image['name']),
                    'path' => basename($path['name']),
                    'user_id' => $userId
                ];
                $manager->insertTable('audio', $data);

                logInfo("Musique ajoutée avec succès dans la base de données");
                $_SESSION['message'] = "Musique ajoutée avec succès.";
                header('Location: ?url=audio/list');
                exit();
            } else {
                logError("Erreur lors du déplacement des fichiers");
                $_SESSION['erreur'] = "Erreur lors du téléchargement des fichiers. Vérifiez les permissions des dossiers.";
            }
        } else {
            $_SESSION['erreur'] = "Tous les champs sont requis. Assurez-vous que le formulaire est correctement rempli.";
        }

        $_SESSION['erreur'] = "Erreur inattendue lors de l'ajout de la musique.";
        header('Location: ?url=compte/index');
        exit();
    }
}
