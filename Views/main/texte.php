<section>
    <?php if (empty($textes)): ?>
        <div class="info-message" style="text-align: center; margin: 30px 0;">
            <p>Aucune note sélectionnée ou disponible.</p>
        </div>
        <div class="boutonAligne">
            <a href="?url=texte/list" class="btnBase theme">Retour aux notes</a>
        </div>
    <?php else: ?>
        <div id="texte-viewer" class="texte-viewer">
            <div class="texte-container">
                <h2 id="texte-title"></h2>
                <div id="texte-content" class="texte-content"></div>
                <div class="texte-metadata" style="display: flex; justify-content: space-between; align-items: center;">
                    <span>Créé le : <strong id="texte-date"></strong> (<span id="texte-counter"></span>)</span>
                    <a id="texte-edit-link" href="#" class="btnBase orange">
                        <i class="material-icons">edit</i>
                        <span class="spanIconText">Modifier</span>
                    </a>
                </div>
            </div>

            <?php if (count($textes) > 1): ?>
                <div class="texte-navigation">
                    <button type="button" class="btnBase theme prev-texte" title="Note précédente (Flèche gauche)">
                        <i class="iconColor material-icons md-36">navigate_before</i>
                    </button>
                    <button type="button" class="btnBase theme next-texte" title="Note suivante (Flèche droite)">
                        <i class="iconColor material-icons md-36">navigate_next</i>
                    </button>
                </div>
            <?php endif; ?>

            <div class="texte-list">
                <?php foreach ($textes as $texte): ?>
                    <div class="texte-item"
                        data-id="<?php echo $texte->getId(); ?>"
                        data-title="<?php echo htmlspecialchars($texte->getTitle(), ENT_QUOTES, 'UTF-8'); ?>"
                        data-content="<?php echo htmlspecialchars($texte->getContent(), ENT_QUOTES, 'UTF-8'); ?>"
                        data-date="<?php echo $texte->getCreatedAt() ? date('d/m/Y à H:i', strtotime($texte->getCreatedAt())) : ''; ?>">
                        <h4><?php echo htmlspecialchars($texte->getTitle(), ENT_QUOTES, 'UTF-8'); ?></h4>
                        <p><?php echo htmlspecialchars(mb_substr($texte->getContent(), 0, 100), ENT_QUOTES, 'UTF-8'); ?>...</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="boutonAligne" style="margin-top: 20px;">
            <a href="?url=texte/list" class="btnBase theme" title="Retour à la liste">
                <i class="iconColor material-icons md-36">undo</i>
            </a>
        </div>
    <?php endif; ?>
</section>

<script src="js/texteViewer.js"></script>