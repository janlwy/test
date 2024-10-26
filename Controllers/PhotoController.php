<?php

class PhotoController extends BaseController implements IController
{
    private $repository;
    
    public function __construct() {
        parent::__construct();
        // TODO: Ajouter PhotoRepository quand il sera créé
    }

    public function index()
    {
        $this->checkAuth();
        $this->checkCSRF();
        $datas = [];
        generate("Views/main/photo.php", $datas, "Views/base.html.php", "Photo");
    }

    public function list() {
        $this->checkAuth();
        // TODO: Implémenter l'affichage de la liste
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
