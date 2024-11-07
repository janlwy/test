<?php

class DatabaseManagerController extends BaseController implements IController {
    private $manager;

    public function __construct() {
        parent::__construct();
        $this->manager = new Manager();
    }

    public function index() {
        if (!$this->session->get('role') === RoleManager::ROLE_ADMIN) {
            logError("Tentative d'accès non autorisé à l'interface d'administration");
            $this->redirect('admin/login/index');
            return;
        }

        $datas = [
            'session' => $this->session,
            'message' => '',
            'error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePostRequest($datas);
        }

        $datas['pageTitle'] = 'Gestionnaire de Base de Données - Administration';
        generate("Views/admin/database_manager_content.php", $datas, "Views/base.html.php", $datas['pageTitle'], true);
    }

    private function handlePostRequest(&$datas) {
        if (!$this->session->validateToken($_POST['csrf_token'] ?? null)) {
            logError("Erreur CSRF : jeton invalide");
            $datas['error'] = "Erreur de sécurité. Veuillez réessayer.";
            return;
        }

        try {
            switch ($_POST['action']) {
                case 'create_table':
                    $this->handleCreateTable($datas);
                    break;
                case 'add_column':
                    $this->handleAddColumn($datas);
                    break;
                case 'modify_column':
                    $this->handleModifyColumn($datas);
                    break;
            }
        } catch (DatabaseException $e) {
            $datas['error'] = $e->getMessage();
        }
    }

    private function handleCreateTable(&$datas) {
        $columns = [];
        foreach ($_POST['column_names'] as $index => $name) {
            if (!empty($name) && !empty($_POST['column_types'][$index])) {
                $columns[$name] = $_POST['column_types'][$index];
            }
        }
        
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $_POST['table_name'])) {
            throw new DatabaseException("Le nom de la table n'est pas valide");
        }
        
        $allowedTypes = ['INT', 'VARCHAR', 'TEXT', 'DATE', 'DATETIME', 'BOOLEAN'];
        foreach ($columns as $name => $type) {
            if (!in_array(strtoupper($type), $allowedTypes)) {
                throw new DatabaseException("Type de colonne non autorisé: $type");
            }
        }
        
        $this->manager->createTable($_POST['table_name'], $columns);
        $datas['message'] = "Table '{$_POST['table_name']}' créée avec succès";
    }

    private function handleAddColumn(&$datas) {
        if (empty($_POST['table_name']) || empty($_POST['column_name']) || empty($_POST['column_definition'])) {
            throw new DatabaseException("Tous les champs sont requis");
        }
        
        $tableName = trim($_POST['table_name']);
        $columnName = trim($_POST['column_name']);
        $definition = trim($_POST['column_definition']);
        
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $columnName)) {
            throw new DatabaseException("Le nom de la colonne n'est pas valide");
        }
        
        $this->manager->addColumn($tableName, $columnName, $definition);
        $datas['message'] = "Colonne '$columnName' ajoutée avec succès à la table '$tableName'";
        logInfo($datas['message']);
    }

    private function handleModifyColumn(&$datas) {
        $this->manager->modifyColumn(
            $_POST['table_name'],
            $_POST['column_name'],
            $_POST['new_definition']
        );
        $datas['message'] = "Colonne modifiée avec succès";
    }

    // Méthodes requises par l'interface IController mais non utilisées
    public function list() { $this->redirect('database_manager/index'); }
    public function create() { $this->redirect('database_manager/index'); }
    public function update($id = null) { $this->redirect('database_manager/index'); }
    public function delete($id = null) { $this->redirect('database_manager/index'); }
}
