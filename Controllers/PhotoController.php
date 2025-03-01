<?php
namespace Controllers;

class PhotoController extends BaseController implements IController
{
    protected $repository;
    
    public function __construct() {
        parent::__construct();
        $this->repository = new PhotoRepository();
    }

    public function index()
    {
        $this->checkAuth();
        $this->redirect('photo/list');
    }

    public function list() {
        $this->checkAuth();
        try {
            $userId = $this->session->get('user_id');
            if (!$userId) {
                throw new Exception("Utilisateur non identifié");
            }
            
            $photos = $this->repository->findAllByUser($userId);
            $datas = [
                'photos' => $photos,
                'session' => $this->session
            ];
            
            generate("Views/main/photoList.php", $datas, "Views/base.html.php", "Liste Photos");
        } catch (Exception $e) {
            $_SESSION['erreur'] = $e->getMessage();
            $this->redirect('photo/index');
        }
    }

    public function create() {
        $this->checkAuth();
        $this->checkCSRF();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validator = new Validator();
            $rules = [
                'title' => ['required', ['min', 2], ['max', 100]],
                'description' => ['required', ['max', 500]]
            ];
            
            if (!$validator->validate($_POST, $rules)) {
                $_SESSION['erreur'] = $validator->getFirstError();
                header('Location: ?url=photo/index');
                exit();
            }
            
            // TODO: Implémenter la validation et l'upload de fichier
        }
    }

    public function update($id) {
        $this->checkAuth();
        $this->checkCSRF();
        // TODO: Implémenter la mise à jour
    }

    public function delete($id) {
        $this->checkAuth();
        $this->checkCSRF();
        // TODO: Implémenter la suppression
    }
}
