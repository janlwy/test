<?php
$message = $datas['message'] ?? '';
$error = $datas['error'] ?? '';
?>

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
