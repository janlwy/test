<?php
require_once '../Models/Manager.php';
session_start();

// Vérification de l'authentification admin (à adapter selon votre système)
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php');
    exit();
}

$manager = new Manager();
$message = '';
$error = '';

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
                $manager->addColumn(
                    $_POST['table_name'],
                    $_POST['column_name'],
                    $_POST['column_definition']
                );
                $message = "Colonne ajoutée avec succès";
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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestionnaire de Base de Données - Administration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Gestionnaire de Base de Données</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Création de table -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Créer une nouvelle table</h3>
            </div>
            <div class="card-body">
                <form method="post" id="createTableForm">
                    <input type="hidden" name="action" value="create_table">
                    
                    <div class="mb-3">
                        <label class="form-label">Nom de la table</label>
                        <input type="text" name="table_name" class="form-control" required>
                    </div>

                    <div id="columns">
                        <div class="row mb-2">
                            <div class="col">
                                <input type="text" name="column_names[]" class="form-control" placeholder="Nom de la colonne">
                            </div>
                            <div class="col">
                                <input type="text" name="column_types[]" class="form-control" placeholder="Type (ex: VARCHAR(50) NOT NULL)">
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-secondary mb-3" onclick="addColumn()">+ Ajouter une colonne</button>
                    <button type="submit" class="btn btn-primary">Créer la table</button>
                </form>
            </div>
        </div>

        <!-- Ajout de colonne -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Ajouter une colonne</h3>
            </div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="action" value="add_column">
                    
                    <div class="mb-3">
                        <label class="form-label">Table</label>
                        <input type="text" name="table_name" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nom de la colonne</label>
                        <input type="text" name="column_name" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Définition</label>
                        <input type="text" name="column_definition" class="form-control" placeholder="VARCHAR(50) NOT NULL" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Ajouter la colonne</button>
                </form>
            </div>
        </div>

        <!-- Modification de colonne -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Modifier une colonne</h3>
            </div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="action" value="modify_column">
                    
                    <div class="mb-3">
                        <label class="form-label">Table</label>
                        <input type="text" name="table_name" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nom de la colonne</label>
                        <input type="text" name="column_name" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nouvelle définition</label>
                        <input type="text" name="new_definition" class="form-control" placeholder="VARCHAR(100) NOT NULL" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Modifier la colonne</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function addColumn() {
            const columns = document.getElementById('columns');
            const newRow = document.createElement('div');
            newRow.className = 'row mb-2';
            newRow.innerHTML = `
                <div class="col">
                    <input type="text" name="column_names[]" class="form-control" placeholder="Nom de la colonne">
                </div>
                <div class="col">
                    <input type="text" name="column_types[]" class="form-control" placeholder="Type (ex: VARCHAR(50) NOT NULL)">
                </div>
            `;
            columns.appendChild(newRow);
        }
    </script>
</body>
</html>
