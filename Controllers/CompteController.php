<?php

namespace Controllers;

use Models\Manager;
use Models\TexteRepository;
use Models\AudioRepository;
use Models\PhotoRepository;
use Models\VideoRepository;
use PDO;
use Exception;

class CompteController extends BaseController implements IController
{
    private Manager $manager;

    public function __construct()
    {
        parent::__construct();
        $this->manager = new Manager();
    }

    public function index()
    {
        $this->checkAuth();
        $userId = (int)$this->session->get('user_id');

        $connexion = $this->manager->getConnexion();
        $stmt = $connexion->prepare("SELECT id, pseudo, email, role, created_at, last_login FROM users WHERE id = :id");
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $userInfo = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        $texteRepo = new TexteRepository();
        $audioRepo = new AudioRepository();
        $photoRepo = new PhotoRepository();
        $videoRepo = new VideoRepository();

        $stats = [
            'textes' => count($texteRepo->findAllByUser($userId)),
            'audios' => count($audioRepo->findAllByUser($userId)),
            'photos' => count($photoRepo->findAllByUser($userId)),
            'videos' => count($videoRepo->findAllByUser($userId))
        ];

        $datas = [
            'session' => $this->session,
            'userInfo' => $userInfo,
            'stats' => $stats,
            'pageTitle' => "Mon Espace Personnel"
        ];
        generate("Views/main/compte.php", $datas, "Views/base.html.php", "Mon Espace Personnel", false);
    }

    public function update($id = null)
    {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('compte/index');
        }

        $this->checkCSRF();
        $userId = (int)$this->session->get('user_id');
        $actionType = $_POST['update_type'] ?? '';

        try {
            $connexion = $this->manager->getConnexion();

            if ($actionType === 'email') {
                $newEmail = trim($_POST['email'] ?? '');
                if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception("Veuillez saisir une adresse email valide.");
                }

                $check = $connexion->prepare("SELECT COUNT(*) FROM users WHERE email = :email AND id != :id");
                $check->execute([':email' => $newEmail, ':id' => $userId]);
                if ((int)$check->fetchColumn() > 0) {
                    throw new Exception("Cette adresse email est déjà utilisée par un autre compte.");
                }

                $upd = $connexion->prepare("UPDATE users SET email = :email WHERE id = :id");
                $upd->execute([':email' => $newEmail, ':id' => $userId]);
                $_SESSION['message'] = "Votre adresse email a été mise à jour avec succès.";
            } elseif ($actionType === 'password') {
                $currentMdp = $_POST['current_mdp'] ?? '';
                $newMdp = $_POST['new_mdp'] ?? '';
                $confirmMdp = $_POST['confirm_mdp'] ?? '';

                $stmt = $connexion->prepare("SELECT mdp FROM users WHERE id = :id");
                $stmt->execute([':id' => $userId]);
                $hash = $stmt->fetchColumn();

                if (!$hash || !password_verify($currentMdp, $hash)) {
                    throw new Exception("Votre mot de passe actuel est incorrect.");
                }

                if ($newMdp !== $confirmMdp) {
                    throw new Exception("Les nouveaux mots de passe ne correspondent pas.");
                }

                if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&.])[A-Za-z\d@$!%*?&.]{8,72}$/', $newMdp)) {
                    throw new Exception("Le nouveau mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.");
                }

                $upd = $connexion->prepare("UPDATE users SET mdp = :mdp WHERE id = :id");
                $upd->execute([
                    ':mdp' => password_hash($newMdp, PASSWORD_DEFAULT),
                    ':id'  => $userId
                ]);
                $_SESSION['message'] = "Votre mot de passe a été modifié avec succès.";
            }
        } catch (Exception $e) {
            $_SESSION['erreur'] = $e->getMessage();
        }

        $this->redirect('compte/index');
    }

    public function list()
    {
        $this->redirect('compte/index');
    }

    public function create()
    {
        $this->redirect('compte/index');
    }

    public function delete($id = null)
    {
        $this->redirect('compte/index');
    }
}
