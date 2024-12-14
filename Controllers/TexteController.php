<?php

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
        $this->checkCSRF();
        $datas = [];
        generate("Views/main/texte.php", $datas, "Views/base.html.php", "Texte");
    }

    public function list() {
        $this->checkAuth();
        // TODO: Implémenter l'affichage de la liste
    }

    public function create() {
        $this->checkAuth();
        $this->checkCSRF();
        // TODO: Implémenter la création
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
