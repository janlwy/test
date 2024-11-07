<?php

require_once __DIR__ . '/../Models/Manager.php';
require_once __DIR__ . '/../Fonctions/RoleManager.php';
require_once __DIR__ . '/../Controllers/BaseController.php';

class Database_managerController extends BaseController implements IController {
    private $manager;

    public function __construct() {
        parent::__construct();
        $this->manager = new Manager();
    }

    public function index() {
        if ($this->session->get('role') !== RoleManager::ROLE_ADMIN) {
            logError("Tentative d'accès non autorisé à l'interface d'administration");
            $this->redirect('connexion/index');
            return;
        }

        $datas = [
            'session' => $this->session,
            'message' => '',
            'error' => '',
            'isAdmin' => true
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->session->validateToken($_POST['csrf_token'] ?? null)) {
                logError("Erreur CSRF : jeton invalide");
                $datas['error'] = "Erreur de sécurité. Veuillez réessayer.";
            } else {
                try {
                    switch ($_POST['action']) {
                        case 'create_table':
                            $columns = [];
                            foreach ($_POST['column_names'] as $index => $name) {
                                if (!empty($name) && !empty($_POST['column_types'][$index])) {
                                    $columns[$name] = $_POST['column_types'][$index];
                                }
                            }
                            // Validation du nom de table
                            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $_POST['table_name'])) {
                                throw new DatabaseException("Le nom de la table n'est pas valide");
                            }
                            
                            // Validation des types de colonnes autorisés
                            $allowedTypes = ['INT', 'VARCHAR', 'TEXT', 'DATE', 'DATETIME', 'BOOLEAN'];
                            foreach ($columns as $name => $type) {
                                if (!in_array(strtoupper($type), $allowedTypes)) {
                                    throw new DatabaseException("Type de colonne non autorisé: $type");
                                }
                            }
                            
                            $this->manager->createTable($_POST['table_name'], $columns);
                            $datas['message'] = "Table '{$_POST['table_name']}' créée avec succès";
                            break;

                        case 'add_column':
                            try {
                                // Validation des entrées
                                if (empty($_POST['table_name']) || empty($_POST['column_name']) || empty($_POST['column_definition'])) {
                                    throw new DatabaseException("Tous les champs sont requis");
                                }
                                
                                // Nettoyage des entrées
                                $tableName = trim($_POST['table_name']);
                                $columnName = trim($_POST['column_name']);
                                $definition = trim($_POST['column_definition']);
                                
                                // Validation du nom de la colonne
                                if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $columnName)) {
                                    throw new DatabaseException("Le nom de la colonne n'est pas valide. Utilisez uniquement des lettres, chiffres et underscores");
                                }
                                
                                $this->manager->addColumn($tableName, $columnName, $definition);
                                $datas['message'] = "Colonne '$columnName' ajoutée avec succès à la table '$tableName'";
                                logInfo($datas['message']);
                            } catch (DatabaseException $e) {
                                $datas['error'] = $e->getMessage();
                                logError("Erreur lors de l'ajout de la colonne : " . $datas['error']);
                            }
                            break;

                        case 'modify_column':
                            $this->manager->modifyColumn(
                                $_POST['table_name'],
                                $_POST['column_name'],
                                $_POST['new_definition']
                            );
                            $datas['message'] = "Colonne modifiée avec succès";
                            break;
                    }
                } catch (DatabaseException $e) {
                    $datas['error'] = $e->getMessage();
                }
            }
        }

        $pageTitle = 'Gestionnaire de Base de Données - Administration';

        ob_start();
        require_once 'Views/admin/database_manager_content.php';
        $content = ob_get_clean();

        require_once '../Views/base.html.php';
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
