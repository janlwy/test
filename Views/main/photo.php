<section>
    <?php if (empty($photos)): ?>
        <div class="info-message">
            Aucune photo sélectionnée. Veuillez retourner à la liste et sélectionner des photos à visualiser.
        </div>
        <a href="?url=photo/list" class="btnBase theme">Retour à la liste</a>
    <?php else: ?>
        <?php if (isset($_SESSION['erreur'])): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($_SESSION['erreur']); ?>
                <?php unset($_SESSION['erreur']); ?>
            </div>
        <?php endif; ?>

        <div id="photo-viewer" class="photo-viewer">
            <div class="photo-container">
                <img id="current-photo" src="" alt="Photo courante">
                <div class="photo-details">
                    <h3 id="photo-title"></h3>
                    <p id="photo-description"></p>
                </div>
            </div>
            
            <div class="photo-controls">
                <button class="prev-photo">
                    <i class="material-icons md-36">navigate_before</i>
                </button>
                <button class="next-photo">
                    <i class="material-icons md-36">navigate_next</i>
                </button>
            </div>

            <div class="photo-thumbnails">
                <?php foreach ($photos as $photo): ?>
                    <img src="<?php echo $photo->getFullPath(); ?>" 
                         alt="<?php echo htmlspecialchars($photo->getTitle()); ?>"
                         class="thumbnail"
                         data-id="<?php echo $photo->getId(); ?>"
                         data-title="<?php echo htmlspecialchars($photo->getTitle()); ?>"
                         data-description="<?php echo htmlspecialchars($photo->getDescription()); ?>">
                <?php endforeach; ?>
            </div>
        </div>

        <div class="boutonAligne">
            <a href="?url=photo/list" class="btnBase theme">
                <i class="iconColor material-icons md-36">undo</i>
            </a>
            <a href="?url=compte/index#form-add" class="btnBase theme">
                <i class="iconColor material-icons md-36">add_a_photo</i>
            </a>
        </div>
    <?php endif; ?>
</section>

<script src="js/photoViewer.js"></script>
