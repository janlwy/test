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
        if (isset($_SESSION['pseudo'])) {
            $datas = [];
            generate("Views/main/audio.php", $datas, "Views/base.html.php", "Audio");
        } else {
            // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
            header('Location: ?url=connexion/index');
            exit();
        }
    }

    public function listeAudio()
    {
        $manager = new Manager();
        $audios = $manager->readTableAll('audio');
        return $this->generateAudioTable($audios);
    }

    public function generateAudioTable($audios)
    {
        $manager = new Manager();
        $audios = $manager->readTableAll('audio');
        $list = "<br><dl>";
        foreach ($audios as $audio) {
            $id = $audio['id'];
            $title = htmlspecialchars($audio['title'], ENT_QUOTES, 'UTF-8');
            $artist = htmlspecialchars($audio['artist'], ENT_QUOTES, 'UTF-8');
            $image = htmlspecialchars($audio['image'], ENT_QUOTES, 'UTF-8');
            $afficher = "<a class='btnBase blue' href='index.php?url=audio/show/$id'>Afficher</a>";
            $modifier = "<a class='btnBase vert' href='index.php?url=audio/update/$id'>Modifier</a>";
            $supprimer = "<a class='btnBase rouge' href='index.php?url=audio/delete/$id'>Supprimer</a>";

            $list .= "<dt>Titre de l'audio</dt><dd>$title</dd>";
            $list .= "<dt>Artiste</dt><dd>$artist</dd>";
            
            $list .= "<dt>Actions</dt><dd><div class='button-group'>$afficher $modifier $supprimer</div></dd>";
        }
        $list .= "</dl><br><br>";
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
            $manager = new Manager();
            $audios = $manager->readTableAll('audio');
            $audioList = $this->listeAudio();
            $datas = [
                'audioList' => $audioList,
                'audios' => $audios
            ];
            generate("Views/main/audioList.php", $datas, "Views/base.html.php", "Liste des Audio");
        } else {
            // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
            header('Location: ?url=connexion/index');
            exit();
        }
    }
}

?>
