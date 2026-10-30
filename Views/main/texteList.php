<?php

/**
 * @var \Session\SessionManager $session
 * @var \Models\Texte[] $textes
 * @var \Models\Texte|null $editTexte
 */
$session = $session ?? \Session\SessionManager::getInstance();
$textes = $textes ?? [];
$editTexte = $editTexte ?? null;

$session->ensureCsrfToken();
$csrfToken = $session->get('csrf_token');
$isEditing = ($editTexte !== null);
$formAction = $isEditing ? "?url=texte/update/" . $editTexte->getId() : "?url=texte/create";
?>

<section class="texte-section">
    <div class="searchBox">
        <input type="text" id="searchTexteInput" class="searchInput" placeholder="Rechercher dans mes notes...">
        <button type="button" class="searchButton">
            <i class="material-icons">search</i>
        </button>
    </div>

    <div class="boutonAligne">
        <button type="button" class="btnBase theme js-toggle-note-editor">
            <i class="iconColor material-icons md-36">note_add</i>
            <span class="spanIconText"><?php echo $isEditing ? 'Modifier la note' : 'Nouvelle note'; ?></span>
        </button>
        <a href="?url=texte/viewer" class="btnBase theme">
            <i class="iconColor material-icons md-36">auto_stories</i>
            <span class="spanIconText">Mode Lecture</span>
        </a>
    </div>

    <div id="note-editor-box" class="formContainer note-editor-box <?php echo $isEditing ? '' : 'is-hidden'; ?>">
        <h3><?php echo $isEditing ? 'Modifier la note' : 'Rédiger une nouvelle note'; ?></h3>
        <form method="POST" action="<?php echo $formAction; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

            <div class="note-field">
                <label for="note-title"><b>Titre de la note</b></label>
                <input type="text" id="note-title" name="title" class="inputModal" required maxlength="255"
                    placeholder="Ex: Idées projet, Liste de courses, Compte-rendu..."
                    value="<?php echo $isEditing ? htmlspecialchars($editTexte->getTitle(), ENT_QUOTES, 'UTF-8') : ''; ?>">
            </div>

            <div class="note-field">
                <label for="note-content"><b>Contenu</b></label>
                <textarea id="note-content" name="content" rows="8" class="note-textarea" required
                    placeholder="Écrivez votre note ici..."><?php echo $isEditing ? htmlspecialchars($editTexte->getContent(), ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
            </div>

            <div class="editor-actions">
                <?php if ($isEditing): ?>
                    <a href="?url=texte/list" class="btnBase gris">Annuler</a>
                <?php else: ?>
                    <button type="button" class="btnBase gris js-toggle-note-editor">Fermer</button>
                <?php endif; ?>
                <button type="submit" class="btnBase vert">
                    <i class="material-icons">save</i>
                    <span class="spanIconText">Enregistrer</span>
                </button>
            </div>
        </form>
    </div>

    <div class="texte-grid" id="texteGrid">
        <?php if (!empty($textes)): ?>
            <?php foreach ($textes as $texte): ?>
                <div class="texte-item" data-texte-id="<?php echo $texte->getId(); ?>">
                    <div class="texte-info">
                        <h4><?php echo htmlspecialchars($texte->getTitle(), ENT_QUOTES, 'UTF-8'); ?></h4>
                        <small>Créé le : <?php echo $texte->getCreatedAt() ? date('d/m/Y à H:i', strtotime($texte->getCreatedAt())) : 'Date inconnue'; ?></small>
                    </div>
                    <div class="texte-preview">
                        <?php
                        $preview = mb_substr($texte->getContent(), 0, 180);
                        echo nl2br(htmlspecialchars($preview, ENT_QUOTES, 'UTF-8'));
                        if (mb_strlen($texte->getContent()) > 180) echo '...';
                        ?>
                    </div>
                    <div class="texte-controls">
                        <a href="?url=texte/viewer&id=<?php echo $texte->getId(); ?>" class="btnBase vert" title="Lire">
                            <i class="material-icons">visibility</i>
                        </a>
                        <a href="?url=texte/update/<?php echo $texte->getId(); ?>" class="btnBase orange" title="Modifier">
                            <i class="material-icons">edit</i>
                        </a>
                        <a href="?url=texte/delete/<?php echo $texte->getId(); ?>" class="btnBase rouge" title="Supprimer"
                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette note ?');">
                            <i class="material-icons">delete</i>
                        </a>
                        <input type="checkbox" class="select-texte" data-id="<?php echo $texte->getId(); ?>" title="Sélectionner pour lecture groupée">
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-content">Aucune note écrite pour le moment. Cliquez sur "Nouvelle note" pour commencer !</p>
        <?php endif; ?>
    </div>

    <form id="reader-form" action="index.php" method="GET" class="reader-controls">
        <input type="hidden" name="url" value="texte/viewer">
        <div id="selected-inputs-container"></div>
        <button type="submit" class="btnBase orange" id="view-selected" style="display: none;">
            <i class="material-icons">menu_book</i>
            <span class="spanIconText">Lire la sélection</span>
        </button>
    </form>
</section>

<script src="js/texteViewer.js"></script>