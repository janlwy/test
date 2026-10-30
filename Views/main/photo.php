<?php

/** @var \Models\Photo[] $photos */
$photos = $photos ?? [];
?>

<section>
    <?php if (empty($photos)): ?>
        <div class="info-message">
            Aucune photo sélectionnée ou disponible.
        </div>
        <div class="boutonAligne">
            <a href="?url=photo/list" class="btnBase theme">Retour à la liste</a>
        </div>
    <?php else: ?>
        <div id="photo-viewer" class="photo-viewer">
            <div class="photo-container">
                <img id="current-photo" src="" alt="Photo courante">
                <div class="photo-details">
                    <h3 id="photo-title"></h3>
                    <p id="photo-description"></p>
                    <div id="photo-ocr-box" class="photo-ocr-box is-hidden">
                        <h4><i class="material-icons md-16">document_scanner</i> Texte extrait (OCR)</h4>
                        <pre id="photo-ocr-text" class="photo-ocr-text"></pre>
                    </div>
                    <div class="viewer-item-actions">
                        <a id="photo-edit-link" href="#" class="btnBase orange">
                            <i class="material-icons">edit</i>
                            <span class="spanIconText">Modifier</span>
                        </a>
                        <a id="photo-convert-link" href="#" class="btnBase blue is-hidden">
                            <i class="material-icons">note_add</i>
                            <span class="spanIconText">Convertir en Note Texte</span>
                        </a>
                    </div>
                </div>
            </div>

            <?php if (count($photos) > 1): ?>
                <div class="photo-controls-nav">
                    <button type="button" class="btnBase theme prev-photo" title="Photo précédente">
                        <i class="iconColor material-icons md-36">navigate_before</i>
                    </button>
                    <button type="button" class="btnBase theme next-photo" title="Photo suivante">
                        <i class="iconColor material-icons md-36">navigate_next</i>
                    </button>
                </div>
            <?php endif; ?>

            <div class="photo-thumbnails">
                <?php foreach ($photos as $photo): ?>
                    <img src="<?php echo htmlspecialchars($photo->getFullPath(), ENT_QUOTES, 'UTF-8'); ?>"
                        alt="<?php echo htmlspecialchars($photo->getTitle(), ENT_QUOTES, 'UTF-8'); ?>"
                        class="thumbnail"
                        data-id="<?php echo $photo->getId(); ?>"
                        data-title="<?php echo htmlspecialchars($photo->getTitle(), ENT_QUOTES, 'UTF-8'); ?>"
                        data-description="<?php echo htmlspecialchars($photo->getDescription() ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        data-ocr="<?php echo htmlspecialchars($photo->getOcrText() ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <?php endforeach; ?>
            </div>
        </div>

        <div class="boutonAligne">
            <a href="?url=photo/list" class="btnBase theme" title="Retour à la liste">
                <i class="iconColor material-icons md-36">undo</i>
            </a>
        </div>
    <?php endif; ?>
</section>

<script src="js/photoViewer.js"></script>