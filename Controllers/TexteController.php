<?php
namespace Controllers;

use Models\TexteRepository;
use Controllers\BaseController;
use Controllers\IController;

class TexteController extends BaseController implements IController
{
    protected $repository;
    
    public function __construct() {
        parent::__construct();
        $this->repository = new TexteRepository();
    }

    public function index()
    {
        $this->checkAuth();
        $this->redirect('texte/list');
    }

    public function list() {
        $this->checkAuth();
        try {
            $userId = $this->session->get('user_id');
            if (!$userId) {
                throw new Exception("Utilisateur non identifié");
            }
            
            $textes = $this->repository->findAllByUser($userId);
            $datas = [
                'textes' => $textes,
                'session' => $this->session
            ];
            
            generate("Views/main/texteList.php", $datas, "Views/base.html.php", "Liste Textes");
        } catch (Exception $e) {
            $_SESSION['erreur'] = $e->getMessage();
            $this->redirect('texte/index');
        }
    }

    public function create() {
        $this->checkAuth();
        $this->checkCSRF();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $texte = new Texte();
                $texte->setTitle($_POST['title']);
                $texte->setContent($_POST['content']);
                $texte->setUserId($this->session->get('user_id'));
                
                if ($texte->validate()) {
                    $this->repository->save($texte);
                    $_SESSION['message'] = "Texte créé avec succès";
                    $this->redirect('texte/list');
                } else {
                    throw new Exception("Données invalides");
                }
            } catch (Exception $e) {
                $_SESSION['erreur'] = $e->getMessage();
                $this->redirect('texte/index');
            }
        }
    }

    public function update($id) {
        $this->checkAuth();
        $this->checkCSRF();
        
        try {
            $texte = $this->repository->findById($id);
            if (!$texte) {
                throw new Exception("Texte non trouvé");
            }
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $texte->setTitle($_POST['title']);
                $texte->setContent($_POST['content']);
                
                if ($texte->validate()) {
                    $this->repository->save($texte);
                    $_SESSION['message'] = "Texte mis à jour avec succès";
                    $this->redirect('texte/list');
                } else {
                    throw new Exception("Données invalides");
                }
            }
            
            $datas = [
                'texte' => $texte,
                'session' => $this->session
            ];
            generate("Views/main/texte.php", $datas, "Views/base.html.php", "Modifier Texte");
        } catch (Exception $e) {
            $_SESSION['erreur'] = $e->getMessage();
            $this->redirect('texte/list');
        }
    }

    public function delete($id) {
        $this->checkAuth();
        $this->checkCSRF();
        
        try {
            $this->repository->delete($id);
            $_SESSION['message'] = "Texte supprimé avec succès";
        } catch (Exception $e) {
            $_SESSION['erreur'] = $e->getMessage();
        }
        $this->redirect('texte/list');
    }
}
