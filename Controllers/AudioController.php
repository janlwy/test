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
            $datas = ['audios' => array_map(function($audio) {
                return [
                    'id' => $audio['id'],
                    'title' => $audio['title'],
                    'artist' => $audio['artist'],
                    'image' => $audio['image'],
                    'path' => $audio['path']
                ];
            }, $audios)];
            generate("Views/main/audioList.php", $datas, "Views/base.html.php", "Liste des Audio");
        } else {
            // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
            header('Location: ?url=connexion/index');
            exit();
        }
    }
}

?>
