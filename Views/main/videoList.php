<section class="video-section">
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
            <i class="iconColor material-icons md-36">video_call</i>
            <span class="spanIconText">Ajouter</span>
        </a>
        <a href="?url=video/player" class="btnBase theme">
            <i class="iconColor material-icons md-36">play_circle</i>
            <span class="spanIconText">Lecteur</span>
        </a>
        <a href="?url=compte/index#form-add" class="btnBase theme">
            <i class="iconColor material-icons md-36">videocam</i>
            <span class="spanIconText">Enregistrer</span>
        </a>
    </div>

    <div class="video-grid">
        <?php if (!empty($videos)): ?>
            <?php foreach ($videos as $video): ?>
                <div class="video-item" data-video-id="<?php echo $video->getId(); ?>">
                    <video class="video-thumbnail" poster="<?php echo $video->getThumbnailPath(); ?>">
                        <source src="<?php echo $video->getFullPath(); ?>" type="video/mp4">
                        Votre navigateur ne supporte pas la lecture vidéo.
                    </video>
                    <div class="video-info">
                        <h4><?php echo htmlspecialchars($video->getTitle()); ?></h4>
                        <p><?php echo htmlspecialchars($video->getDescription()); ?></p>
                    </div>
                    <div class="video-controls">
                        <button class="play-single btnBase vert" onclick="window.location='?url=video/player&id=<?php echo $video->getId(); ?>'">
                            <i class="material-icons">play_arrow</i>
                        </button>
                        <a href="?url=video/update/<?php echo $video->getId(); ?>#form-add" class="btnBase orange">
                            <i class="material-icons">edit</i>
                        </a>
                        <a href="?url=video/delete/<?php echo $video->getId(); ?>" class="btnBase rouge" 
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette vidéo ?');">
                            <i class="material-icons">delete</i>
                        </a>
                        <input type="checkbox" class="select-video" data-id="<?php echo $video->getId(); ?>">
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-content">Aucune vidéo n'a été ajoutée.</p>
        <?php endif; ?>
    </div>

    <form id="playlist-form" action="?url=video/player" method="GET" class="playlist-controls">
        <input type="hidden" name="url" value="video/player">
        <button type="submit" class="btnBase orange" id="play-selected" style="display: none;">
            <i class="material-icons">playlist_play</i> Lire la sélection
        </button>
    </form>
</section>
