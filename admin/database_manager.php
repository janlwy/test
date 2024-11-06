<?php
require_once '../Models/Manager.php';
require_once '../Fonctions/SessionManager.php';

$session = SessionManager::getInstance();
$session->startSession();

if (!$session->get('admin', false)) {
    logError("Tentative d'accès non autorisé à l'interface d'administration");
    header('Location: ../index.php?url=admin/login/index');
    exit();
}

$manager = new Manager();
$message = '';
$error = '';
$datas = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        switch ($_POST['action']) {
            case 'create_table':
                $columns = [];
                foreach ($_POST['column_names'] as $index => $name) {
                    if (!empty($name) && !empty($_POST['column_types'][$index])) {
                        $columns[$name] = $_POST['column_types'][$index];
                    }
                }
                $manager->createTable($_POST['table_name'], $columns);
                $message = "Table '{$_POST['table_name']}' créée avec succès";
                break;

            case 'add_column':
                // Vérifier si la table existe
                $tables = $manager->getConnexion()->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                if (!in_array($_POST['table_name'], $tables)) {
                    throw new DatabaseException("La table '{$_POST['table_name']}' n'existe pas");
                }
                
                $manager->addColumn(
                    $_POST['table_name'],
                    $_POST['column_name'],
                    $_POST['column_definition']
                );
                $message = "Colonne '{$_POST['column_name']}' ajoutée avec succès à la table '{$_POST['table_name']}'";
                break;

            case 'modify_column':
                $manager->modifyColumn(
                    $_POST['table_name'],
                    $_POST['column_name'],
                    $_POST['new_definition']
                );
                $message = "Colonne modifiée avec succès";
                break;
        }
    } catch (DatabaseException $e) {
        $error = $e->getMessage();
    }
}

$datas['message'] = $message;
$datas['error'] = $error;
$pageTitle = 'Gestionnaire de Base de Données - Administration';

ob_start();
require_once '../Views/admin/database_manager_content.php';
$content = ob_get_clean();

require_once '../Views/base.html.php';
