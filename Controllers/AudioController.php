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
        $list = "<br>";
        $list .= "<table class='' border=1 style='border-collapse:collapse;'>";
        $list .= "<tr class='fond-blue'>";
        $list .= "<th class=''>TITRE</th>";
        $list .= "<th class=''>ARTISTE</th>";
        $list .= "<th class=''>IMAGE</th>";
        $list .= "<th class='actions-column'>ACTIONS</th>";
        $list .= "</tr>";
        foreach ($audios as $audio) {
            $id = $audio['id'];
            $title = $audio['title'];
            $artist = $audio['artist'];
            $image = $audio['image'];
            $path = $audio['path'];
            $afficher = "<a class='btnBase blue' href='index.php?url=audio/show/$id'>Afficher</a>";
            $modifier = "<a class='btnBase vert' href='index.php?url=audio/update/$id'>Modifier</a>";
            $supprimer = "<a class='btnBase rouge' href='index.php?url=audio/delete/$id'>Supprimer</a>";
            $list .= "<tr>";
            $list .= "<td class='title-column'>$title</td>";
            $list .= "<td class='artist-column'>$artist</td>";
            $list .= "<td class='image-column'><img class='photoAudio' src='Ressources/images/pochettes/$image' alt='cover'></td>";
            $list .= "<td class='action-buttons'><div class='button-group'>$afficher $modifier $supprimer</div></td>";
            $list .= "</tr>";
        }
        $list .= "</table><br><br>";
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
