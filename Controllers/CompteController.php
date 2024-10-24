<?php

class CompteController
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
            generate("Views/main/compte.php", $datas,"Views/base.html.php", "Mon calepin");
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
        if (isset($_SESSION['pseudo'])) {
            // Vérifier que le formulaire a été soumis
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Récupérer les données du formulaire
                if (isset($_POST['title'], $_POST['artiste'], $_POST['image'], $_POST['path'])) {
                    $title = $_POST['title'];
                    $artist = $_POST['artiste'];
                    $image = $_POST['image'];
                    $path = $_POST['path'];

                    // Connexion à la base de données
                    $manager = new Manager();
                    $connexion = $manager->getConnexion();

                    // Insérer les données dans la base de données
                    $sql = 'INSERT INTO musics (title, artist, image, path, user_id) VALUES (:title, :artist, :image, :path, :user_id)';
                    $req = $connexion->prepare($sql);
                    $req->bindValue(':title', $title, PDO::PARAM_STR);
                    $req->bindValue(':artist', $artist, PDO::PARAM_STR);
                    $req->bindValue(':image', $image, PDO::PARAM_STR);
                    $req->bindValue(':path', $path, PDO::PARAM_STR);
                    $req->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                    $req->execute();
                } else {
                    logError("Données du formulaire manquantes");
                }

                // Rediriger vers la page de gestion des médias
                header('Location: ?url=compte/index');
                exit();
            }
        } else {
            // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
            header('Location: ?url=connexion/index');
            exit();
        }
    }
}
