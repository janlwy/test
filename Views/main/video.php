<section>
    <?php if (empty($formattedVideos)): ?>
        <div class="info-message">
            Aucune vidéo sélectionnée. Veuillez retourner à la liste et sélectionner des vidéos à lire.
        </div>
        <div class="boutonAligne">
            <a href="?url=video/list" class="btnBase theme">Retour à la liste</a>
        </div>
    <?php else: ?>
        <div id="video-player-data"
            data-videos="<?php echo htmlspecialchars(json_encode($formattedVideos), ENT_QUOTES, 'UTF-8'); ?>">
        </div>

        <div class="player-video">
            <video id="video-player" controls preload="metadata">
                Votre navigateur ne supporte pas la lecture vidéo.
            </video>

            <div class="video-info-main">
                <h3 id="video-title"></h3>
                <p id="video-description"></p>
            </div>

            <?php if (count($formattedVideos) > 1): ?>
                <div class="video-playlist">
                    <?php foreach ($videos as $index => $video): ?>
                        <div class="playlist-item" data-index="<?php echo $index; ?>"
                            data-video-path="<?php echo htmlspecialchars($video->getFullPath(), ENT_QUOTES, 'UTF-8'); ?>"
                            data-video-title="<?php echo htmlspecialchars($video->getTitle(), ENT_QUOTES, 'UTF-8'); ?>"
                            data-video-description="<?php echo htmlspecialchars($video->getDescription() ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <img src="<?php echo htmlspecialchars($video->getThumbnailPath(), ENT_QUOTES, 'UTF-8'); ?>" alt="Miniature">
                            <div class="playlist-item-info">
                                <h4><?php echo htmlspecialchars($video->getTitle(), ENT_QUOTES, 'UTF-8'); ?></h4>
                                <p><?php echo htmlspecialchars($video->getDescription() ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="boutonAligne">
            <a href="?url=video/list" class="btnBase theme" title="Retour à la liste">
                <i class="iconColor material-icons md-36">undo</i>
            </a>
            <a href="?url=video/list#upload" class="btnBase theme" title="Importer une vidéo">
                <i class="iconColor material-icons md-36">video_call</i>
            </a>
            <a href="?url=video/list#record" class="btnBase theme" title="Enregistrer via webcam">
                <i class="iconColor material-icons md-36">videocam</i>
            </a>
        </div>
    <?php endif; ?>
</section>