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
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Création de table -->
    <h3>Créer une nouvelle table</h3>
    <form method="post" id="createTableForm" class="formCentered">
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
    <form method="post" class="formCentered">
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
    <a href="?url=admin/logout/index" class="btnBase rouge" style="text-decoration: none; display: inline-block;">
        <i class="material-icons">exit_to_app</i>
        <span class="spanIconText">Quitter l'administration</span>
    </a>
</div>


<script>
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
