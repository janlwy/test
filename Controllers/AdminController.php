<?php

class AdminController extends BaseController implements IController {
    private $manager;

    public function __construct() {
        parent::__construct();
        $this->manager = new Manager();
    }

    public function index() {
        $this->redirect('database_manager/index');
    }

    public function setAdminRole() {
        if ($this->session->get('role') !== RoleManager::ROLE_ADMIN) {
            logError("Tentative non autorisée de définir le rôle admin");
            die('Accès non autorisé');
        }

        try {
            $connexion = $this->manager->getConnexion();
            
            $sql = "UPDATE users SET role = :role WHERE pseudo = :pseudo";
            $req = $connexion->prepare($sql);
            $req->execute([
                ':role' => RoleManager::ROLE_ADMIN,
                ':pseudo' => 'admin'
            ]);
            
            if ($req->rowCount() > 0) {
                $this->session->set('success', "Le rôle admin a été attribué avec succès à l'utilisateur 'admin'");
            } else {
                $this->session->set('erreur', "Aucune modification n'a été effectuée. Vérifiez que l'utilisateur 'admin' existe.");
            }
        } catch (Exception $e) {
            logError("Erreur lors de la définition du rôle admin : " . $e->getMessage());
            $this->session->set('erreur', "Une erreur est survenue lors de la modification du rôle");
        }
        
        $this->redirect('database_manager/index');
    }

    public function list() {
        $this->redirect('database_manager/index');
    }

    public function create() {
        $this->redirect('database_manager/index');
    }

    public function update($id = null) {
        $this->redirect('database_manager/index');
    }

    public function delete($id = null) {
        $this->redirect('database_manager/index');
    }
}
