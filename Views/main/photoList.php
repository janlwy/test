<section class="photo-section">
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
            <i class="iconColor material-icons md-36">add_a_photo</i>
            <span class="spanIconText">Ajouter</span>
        </a>
        <a href="?url=photo/viewer" class="btnBase theme">
            <i class="iconColor material-icons md-36">photo_library</i>
            <span class="spanIconText">Visionneuse</span>
        </a>
    </div>

    <div class="photo-grid">
        <?php if (!empty($photos)): ?>
            <?php foreach ($photos as $photo): ?>
                <div class="photo-item" data-photo-id="<?php echo $photo->getId(); ?>">
                    <img src="<?php echo $photo->getFullPath(); ?>" alt="<?php echo htmlspecialchars($photo->getTitle()); ?>" class="photo-thumbnail">
                    <div class="photo-info">
                        <h4><?php echo htmlspecialchars($photo->getTitle()); ?></h4>
                        <p><?php echo htmlspecialchars($photo->getDescription()); ?></p>
                    </div>
                    <div class="photo-controls">
                        <a href="?url=photo/viewer&id=<?php echo $photo->getId(); ?>" class="btnBase vert">
                            <i class="material-icons">visibility</i>
                        </a>
                        <a href="?url=photo/update/<?php echo $photo->getId(); ?>#form-add" class="btnBase orange">
                            <i class="material-icons">edit</i>
                        </a>
                        <a href="?url=photo/delete/<?php echo $photo->getId(); ?>" class="btnBase rouge" 
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette photo ?');">
                            <i class="material-icons">delete</i>
                        </a>
                        <input type="checkbox" class="select-photo" data-id="<?php echo $photo->getId(); ?>">
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-content">Aucune photo n'a été ajoutée.</p>
        <?php endif; ?>
    </div>

    <form id="slideshow-form" action="?url=photo/viewer" method="GET" class="slideshow-controls">
        <input type="hidden" name="url" value="photo/viewer">
        <button type="submit" class="btnBase orange" id="view-selected" style="display: none;">
            <i class="material-icons">slideshow</i> Voir la sélection
        </button>
    </form>
</section>
