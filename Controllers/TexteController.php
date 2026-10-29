<?php

namespace Controllers;

use Models\TexteRepository;
use Models\Texte;
use Exception;

class TexteController extends BaseController implements IController
{
    protected $repository;

    public function __construct()
    {
        parent::__construct();
        $this->repository = new TexteRepository();
    }

    public function index()
    {
        $this->checkAuth();
        $this->redirect('texte/list');
    }

    public function list()
    {
        $this->checkAuth();
        try {
            $userId = (int)$this->session->get('user_id');
            if (!$userId) {
                throw new Exception("Utilisateur non identifié");
            }

            $textes = $this->repository->findAllByUser($userId);
            $datas = [
                'textes' => $textes,
                'session' => $this->session
            ];

            generate("Views/main/texteList.php", $datas, "Views/base.html.php", "Mes Notes Textes");
        } catch (Exception $e) {
            $_SESSION['erreur'] = $e->getMessage();
            $this->redirect('mediabox/index');
        }
    }

    public function viewer()
    {
        $this->checkAuth();
        $userId = (int)$this->session->get('user_id');

        $singleId = isset($_GET['id']) ? (int)$_GET['id'] : null;
        $multipleIds = $_GET['ids'] ?? [];

        $textes = [];
        if ($singleId) {
            $t = $this->repository->findById($singleId);
            if ($t && $t->getUserId() === $userId) {
                $textes[] = $t;
            }
        } elseif (!empty($multipleIds) && is_array($multipleIds)) {
            foreach ($multipleIds as $id) {
                $t = $this->repository->findById((int)$id);
                if ($t && $t->getUserId() === $userId) {
                    $textes[] = $t;
                }
            }
        } else {
            $textes = $this->repository->findAllByUser($userId);
        }

        $datas = [
            'textes' => $textes,
            'session' => $this->session
        ];
        generate("Views/main/texte.php", $datas, "Views/base.html.php", "Lecteur de Notes");
    }

    public function create()
    {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkCSRF();
            try {
                $texte = new Texte();
                $texte->setTitle(trim($_POST['title'] ?? ''));
                $texte->setContent(trim($_POST['content'] ?? ''));
                $texte->setUserId((int)$this->session->get('user_id'));

                $errors = $texte->validate();
                if (empty($errors)) {
                    $this->repository->save($texte);
                    $_SESSION['message'] = "Note texte enregistrée avec succès.";
                } else {
                    $_SESSION['erreur'] = implode(' | ', $errors);
                }
            } catch (Exception $e) {
                $_SESSION['erreur'] = "Erreur lors de la création : " . $e->getMessage();
            }
        }
        $this->redirect('texte/list');
    }

    public function update($id = null)
    {
        $this->checkAuth();
        $userId = (int)$this->session->get('user_id');
        $id = (int)$id;

        try {
            $texte = $this->repository->findById($id);
            if (!$texte || $texte->getUserId() !== $userId) {
                throw new Exception("Note introuvable ou accès non autorisé.");
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->checkCSRF();
                $texte->setTitle(trim($_POST['title'] ?? ''));
                $texte->setContent(trim($_POST['content'] ?? ''));

                $errors = $texte->validate();
                if (empty($errors)) {
                    $this->repository->save($texte);
                    $_SESSION['message'] = "Note mise à jour avec succès.";
                    $this->redirect('texte/list');
                } else {
                    throw new Exception(implode(' | ', $errors));
                }
            }

            // Si GET, on affiche la liste avec la note pré-chargée pour édition
            $textes = $this->repository->findAllByUser($userId);
            $datas = [
                'textes' => $textes,
                'editTexte' => $texte,
                'session' => $this->session
            ];
            generate("Views/main/texteList.php", $datas, "Views/base.html.php", "Modifier la Note");
        } catch (Exception $e) {
            $_SESSION['erreur'] = $e->getMessage();
            $this->redirect('texte/list');
        }
    }

    public function delete($id = null)
    {
        $this->checkAuth();
        $userId = (int)$this->session->get('user_id');
        $id = (int)$id;

        try {
            $texte = $this->repository->findById($id);
            if (!$texte || $texte->getUserId() !== $userId) {
                throw new Exception("Note introuvable ou accès refusé.");
            }
            $this->repository->delete($id);
            $_SESSION['message'] = "Note supprimée avec succès.";
        } catch (Exception $e) {
            $_SESSION['erreur'] = $e->getMessage();
        }
        $this->redirect('texte/list');
    }
}
