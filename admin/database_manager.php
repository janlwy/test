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
                    
                    $manager->addColumn($tableName, $columnName, $definition);
                    $message = "Colonne '$columnName' ajoutée avec succès à la table '$tableName'";
                    logInfo($message);
                } catch (DatabaseException $e) {
                    $error = $e->getMessage();
                    logError("Erreur lors de l'ajout de la colonne : " . $error);
                }
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
