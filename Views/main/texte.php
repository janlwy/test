<section>
    <?php if (empty($textes)): ?>
        <div class="info-message">
            Aucun texte sélectionné. Veuillez retourner à la liste et sélectionner des textes à lire.
        </div>
        <a href="?url=texte/list" class="btnBase theme">Retour à la liste</a>
    <?php else: ?>
        <?php if (isset($_SESSION['erreur'])): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($_SESSION['erreur']); ?>
                <?php unset($_SESSION['erreur']); ?>
            </div>
        <?php endif; ?>

        <div id="texte-viewer" class="texte-viewer">
            <div class="texte-container">
                <h2 id="texte-title"></h2>
                <div id="texte-content" class="texte-content"></div>
                <div class="texte-metadata">
                    <p>Créé le : <span id="texte-date"></span></p>
                </div>
            </div>
            
            <div class="texte-navigation">
                <button class="prev-texte">
                    <i class="material-icons md-36">navigate_before</i>
                </button>
                <button class="next-texte">
                    <i class="material-icons md-36">navigate_next</i>
                </button>
            </div>

            <div class="texte-list">
                <?php foreach ($textes as $texte): ?>
                    <div class="texte-item" 
                         data-id="<?php echo $texte->getId(); ?>"
                         data-title="<?php echo htmlspecialchars($texte->getTitle()); ?>"
                         data-content="<?php echo htmlspecialchars($texte->getContent()); ?>"
                         data-date="<?php echo date('d/m/Y', strtotime($texte->getCreatedAt())); ?>">
                        <h4><?php echo htmlspecialchars($texte->getTitle()); ?></h4>
                        <p><?php echo nl2br(htmlspecialchars(substr($texte->getContent(), 0, 100))); ?>...</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="boutonAligne">
            <a href="?url=texte/list" class="btnBase theme">
                <i class="iconColor material-icons md-36">undo</i>
            </a>
            <a href="?url=compte/index#form-add" class="btnBase theme">
                <i class="iconColor material-icons md-36">note_add</i>
            </a>
        </div>
    <?php endif; ?>
</section>

<script src="js/texteViewer.js"></script>
