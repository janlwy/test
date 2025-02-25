<?php
$message = $datas['message'] ?? '';
$error = $datas['error'] ?? '';
$datas['hideNav'] = true; // Pour masquer la navigation standard
$datas['isAdmin'] = true; // Pour ajuster les chemins CSS
?>

<div class="formContainer">
    <h2>Gestionnaire de Base de Données</h2>
    
    <?php if ($message): ?>
        <div class="success-message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($error); ?>
            <?php if (strpos($error, 'SQLSTATE') !== false): ?>
                <div class="sql-error-details">
                    Erreur technique : veuillez contacter l'administrateur
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Création de table -->
    <h3>Créer une nouvelle table</h3>
    <form method="post" id="createTableForm" class="formCentered" onsubmit="return validateCreateTable()">
            <input type="hidden" name="csrf_token" value="<?php echo $session->get('csrf_token'); ?>">
            <input type="hidden" name="action" value="create_table">
            
            <div class="rowEspace">
                <div class="collabel">
                    <label>Nom de la table</label>
                </div>
                <div class="colinput">
                    <input type="text" name="table_name" class="inputMonEspace" required>
                </div>
            </div>

            <div id="columns">
                <div class="rowEspace">
                    <div class="collabel">
                        <label>Nom de la colonne</label>
                    </div>
                    <div class="colinput">
                        <input type="text" name="column_names[]" class="inputMonEspace" placeholder="Nom de la colonne">
                    </div>
                </div>
                <div class="rowEspace">
                    <div class="collabel">
                        <label>Type de la colonne</label>
                    </div>
                    <div class="colinput">
                        <input type="text" name="column_types[]" class="inputMonEspace" placeholder="Type (ex: VARCHAR(50) NOT NULL)">
                    </div>
                </div>
            </div>

            <div class="rowEspace">
                <div class="colinput">
                    <button type="button" class="btnBase gris" onclick="addColumn()">+ Ajouter une colonne</button>
                    <button type="submit" class="btnBase blue">Créer la table</button>
                </div>
            </div>
    </form>

    <!-- Ajout de colonne -->
    <h3>Ajouter une colonne</h3>
    <form method="post" class="formCentered" onsubmit="return validateAddColumn()">
            <input type="hidden" name="csrf_token" value="<?php echo $session->get('csrf_token'); ?>">
            <input type="hidden" name="action" value="add_column">
            
            <div class="rowEspace">
                <div class="collabel">
                    <label>Table</label>
                </div>
                <div class="colinput">
                    <input type="text" name="table_name" class="inputMonEspace" required>
                </div>
            </div>
            
            <div class="rowEspace">
                <div class="collabel">
                    <label>Nom de la colonne</label>
                </div>
                <div class="colinput">
                    <input type="text" name="column_name" class="inputMonEspace" required>
                </div>
            </div>
            
            <div class="rowEspace">
                <div class="collabel">
                    <label>Définition</label>
                </div>
                <div class="colinput">
                    <input type="text" name="column_definition" class="inputMonEspace" placeholder="VARCHAR(50) NOT NULL" required>
                </div>
            </div>

            <div class="rowEspace">
                <div class="colinput">
                    <button type="submit" class="btnBase blue">Ajouter la colonne</button>
                </div>
            </div>
    </form>

    <!-- Modification de colonne -->
    <h3>Modifier une colonne</h3>
    <form method="post" class="formCentered">
            <input type="hidden" name="csrf_token" value="<?php echo $session->get('csrf_token'); ?>">
            <input type="hidden" name="action" value="modify_column">
            
            <div class="rowEspace">
                <div class="collabel">
                    <label>Table</label>
                </div>
                <div class="colinput">
                    <input type="text" name="table_name" class="inputMonEspace" required>
                </div>
            </div>
            
            <div class="rowEspace">
                <div class="collabel">
                    <label>Nom de la colonne</label>
                </div>
                <div class="colinput">
                    <input type="text" name="column_name" class="inputMonEspace" required>
                </div>
            </div>
            
            <div class="rowEspace">
                <div class="collabel">
                    <label>Nouvelle définition</label>
                </div>
                <div class="colinput">
                    <input type="text" name="new_definition" class="inputMonEspace" placeholder="VARCHAR(100) NOT NULL" required>
                </div>
            </div>

            <div class="rowEspace">
                <div class="colinput">
                    <button type="submit" class="btnBase blue">Modifier la colonne</button>
                </div>
            </div>
    </form>
</div>

<div class="rowEspace" style="margin-top: 20px; text-align: center;">
    <a href="?url=deconnexion/index" class="btnBase rouge" style="text-decoration: none; display: inline-block;">
        <i class="material-icons">exit_to_app</i>
        <span class="spanIconText">Quitter l'administration</span>
    </a>
</div>


<script>
    function validateCreateTable() {
        const tableName = document.querySelector('input[name="table_name"]').value;
        const columnNames = document.querySelectorAll('input[name="column_names[]"]');
        const columnTypes = document.querySelectorAll('input[name="column_types[]"]');
        
        // Validation du nom de la table
        if (!tableName) {
            alert('Le nom de la table est requis');
            return false;
        }
        
        if (!/^[a-zA-Z][a-zA-Z0-9_]*$/.test(tableName)) {
            alert('Le nom de la table doit commencer par une lettre et ne contenir que des caractères alphanumériques et des underscores');
            return false;
        }

        // Vérification qu'il y a au moins une colonne
        if (columnNames.length === 0) {
            alert('Au moins une colonne est requise');
            return false;
        }
        
        // Validation des colonnes
        const allowedTypes = ['INT', 'VARCHAR', 'TEXT', 'DATE', 'DATETIME', 'BOOLEAN'];
            
        for (let i = 0; i < columnNames.length; i++) {
            if (!columnNames[i].value || !columnTypes[i].value) {
                alert('Tous les champs de colonnes doivent être remplis');
                return false;
            }
                
            if (!/^[a-zA-Z][a-zA-Z0-9_]*$/.test(columnNames[i].value)) {
                alert('Les noms de colonnes doivent :\n- Commencer par une lettre\n- Ne contenir que des caractères alphanumériques et des underscores\n- Faire moins de ' + MAX_COLUMN_NAME_LENGTH + ' caractères');
                return false;
            }
            if (columnNames[i].value.length > MAX_COLUMN_NAME_LENGTH) {
                alert('Le nom de la colonne "' + columnNames[i].value + '" est trop long (max ' + MAX_COLUMN_NAME_LENGTH + ' caractères)');
                return false;
            }

            // Vérification du type de colonne
            const typeBase = columnTypes[i].value.split('(')[0].toUpperCase();
            if (!allowedTypes.includes(typeBase)) {
                alert(`Type de colonne non autorisé: ${typeBase}\nTypes autorisés: ${allowedTypes.join(', ')}`);
                return false;
            }
        }
        
        // Vérification du token CSRF
        const csrfToken = document.querySelector('input[name="csrf_token"]').value;
        if (!csrfToken) {
            alert('Erreur de sécurité: token CSRF manquant');
            return false;
        }
        
        return true;
    }
    function addColumn() {
        const columns = document.getElementById('columns');
        const template = `
            <div class="rowEspace">
                <div class="collabel">
                    <label>Nom de la colonne</label>
                </div>
                <div class="colinput">
                    <input type="text" name="column_names[]" class="inputMonEspace" placeholder="Nom de la colonne">
                </div>
            </div>
            <div class="rowEspace">
                <div class="collabel">
                    <label>Type de la colonne</label>
                </div>
                <div class="colinput">
                    <input type="text" name="column_types[]" class="inputMonEspace" placeholder="Type (ex: VARCHAR(50) NOT NULL)">
                </div>
            </div>
        `;
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = template;
        while (tempDiv.firstChild) {
            columns.appendChild(tempDiv.firstChild);
        }
    }
</script>

<script>
function validateAddColumn() {
    const tableName = document.querySelector('form[action="add_column"] input[name="table_name"]').value;
    const columnName = document.querySelector('form[action="add_column"] input[name="column_name"]').value;
    const columnDefinition = document.querySelector('form[action="add_column"] input[name="column_definition"]').value;
    
    if (!tableName || !columnName || !columnDefinition) {
        alert('Tous les champs sont requis');
        return false;
    }
    
    if (!/^[a-zA-Z][a-zA-Z0-9_]*$/.test(columnName)) {
        alert('Le nom de la colonne doit commencer par une lettre et ne contenir que des caractères alphanumériques et des underscores');
        return false;
    }
    
    const allowedTypes = ['INT', 'VARCHAR', 'TEXT', 'DATE', 'DATETIME', 'BOOLEAN'];
    const typeBase = columnDefinition.split('(')[0].toUpperCase();
    if (!allowedTypes.includes(typeBase)) {
        alert(`Type de colonne non autorisé: ${typeBase}\nTypes autorisés: ${allowedTypes.join(', ')}`);
        return false;
    }
    
    return true;
}
</script>
