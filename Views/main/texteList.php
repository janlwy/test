<section class="texte-section">
    <?php if (isset($_SESSION['message'])): ?>
        <div class="success-message">
            <?php echo htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8'); ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['erreur'])): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($_SESSION['erreur'], ENT_QUOTES, 'UTF-8'); ?>
        </div>
        <?php unset($_SESSION['erreur']); ?>
    <?php endif; ?>

    <br><br>
    <div class="boutonAligne">
        <a href="?url=compte/index#form-add" class="btnBase theme">
            <i class="iconColor material-icons md-36">note_add</i>
            <span class="spanIconText">Ajouter</span>
        </a>
        <a href="?url=texte/viewer" class="btnBase theme">
            <i class="iconColor material-icons md-36">auto_stories</i>
            <span class="spanIconText">Lecteur</span>
        </a>
    </div>

    <div class="texte-grid">
        <?php if (!empty($textes)): ?>
            <?php foreach ($textes as $texte): ?>
                <div class="texte-item" data-texte-id="<?php echo $texte->getId(); ?>">
                    <div class="texte-preview">
                        <?php echo nl2br(htmlspecialchars(substr($texte->getContent(), 0, 200))); ?>...
                    </div>
                    <div class="texte-info">
                        <h4><?php echo htmlspecialchars($texte->getTitle()); ?></h4>
                        <p>Créé le : <?php echo date('d/m/Y', strtotime($texte->getCreatedAt())); ?></p>
                    </div>
                    <div class="texte-controls">
                        <a href="?url=texte/viewer&id=<?php echo $texte->getId(); ?>" class="btnBase vert">
                            <i class="material-icons">visibility</i>
                        </a>
                        <a href="?url=texte/update/<?php echo $texte->getId(); ?>#form-add" class="btnBase orange">
                            <i class="material-icons">edit</i>
                        </a>
                        <a href="?url=texte/delete/<?php echo $texte->getId(); ?>" class="btnBase rouge" 
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce texte ?');">
                            <i class="material-icons">delete</i>
                        </a>
                        <input type="checkbox" class="select-texte" data-id="<?php echo $texte->getId(); ?>">
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-content">Aucun texte n'a été ajouté.</p>
        <?php endif; ?>
    </div>

    <form id="reader-form" action="?url=texte/viewer" method="GET" class="reader-controls">
        <input type="hidden" name="url" value="texte/viewer">
        <button type="submit" class="btnBase orange" id="view-selected" style="display: none;">
            <i class="material-icons">menu_book</i> Lire la sélection
        </button>
    </form>
</section>
