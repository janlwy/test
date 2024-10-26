<?php

class AudioController
{
    public function index()
    {
        // Démarrer la session si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['pseudo'])) {
            // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
            header('Location: ?url=connexion/index');
            exit();
        }

        $datas = [];
        generate("Views/main/audio.php", $datas, "Views/base.html.php", "Audio");
    }

    public function listeAudio()
    {
        $manager = new Manager();
        $userId = $_SESSION['user_id'] ?? null;
        if ($userId === null) {
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
                $id = $audio['id'];
                $title = htmlspecialchars($audio['title'], ENT_QUOTES, 'UTF-8');
                $artist = htmlspecialchars($audio['artist'], ENT_QUOTES, 'UTF-8');
                $image = htmlspecialchars($audio['image'], ENT_QUOTES, 'UTF-8');
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
        // Démarrer la session si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Vérifier si l'utilisateur est connecté
        if (isset($_SESSION['pseudo'])) {
            if (isset($_SESSION['user_id'])) {
                $userId = $_SESSION['user_id'];
                $manager = new Manager();
                $audios = $manager->readTableAll('audio', $userId);
                $audioList = $this->generateAudioTable($audios);
                $datas = [
                    'audioList' => $audioList,
                    'audios' => $audios
                ];
                // Assurez-vous que la vue est générée même si la liste est vide
                generate("Views/main/audioList.php", $datas, "Views/base.html.php", "Liste des Audio");
            } else {
                $_SESSION['erreur'] = "Erreur : utilisateur non identifié.";
                header('Location: ?url=connexion/index');
                exit();
            }
        } else {
            // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
            header('Location: ?url=connexion/index');
            exit();
        }
    }
    public function addMusic()
    {
        // Démarrer la session si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['pseudo'])) {
            header('Location: ?url=connexion/index');
            exit();
        }

        logInfo("Début de la méthode addMusic");
        // Vérifier que la requête est bien de type POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            logInfo("Requête POST reçue");
            // Vérifier la validité du token CSRF
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    logError("Erreur CSRF : jeton invalide.");
                    $_SESSION['erreur'] = "Erreur CSRF : jeton invalide.";
                    header('Location: ?url=compte/index');
                    exit();
                }
            }

            // Vérifier que les champs requis sont présents
            if (isset($_POST['title'], $_POST['artiste'], $_FILES['image'], $_FILES['path'])) {
                logInfo("Champs requis présents");
                $title = $_POST['title'];
                $artist = $_POST['artiste'];
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
        }

        $_SESSION['erreur'] = "Erreur inattendue lors de l'ajout de la musique.";
        header('Location: ?url=compte/index');
        exit();
    }
}
