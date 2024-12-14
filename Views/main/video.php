<section>
    <?php if (empty($videos)): ?>
        <div class="info-message">
            Aucune vidéo sélectionnée. Veuillez retourner à la liste et sélectionner des vidéos à lire.
        </div>
        <a href="?url=video/list" class="btnBase theme">Retour à la liste</a>
    <?php else: ?>
        <?php if (isset($_SESSION['erreur'])): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($_SESSION['erreur']); ?>
                <?php unset($_SESSION['erreur']); ?>
            </div>
        <?php endif; ?>

        <div id="player-data" 
             data-videos='<?php echo htmlspecialchars(json_encode($formattedVideos), ENT_QUOTES, 'UTF-8'); ?>'>
        </div>

        <div class="player-video">
            <video id="video-player" controls>
                <source src="" type="video/mp4">
                <source src="" type="video/webm">
                Votre navigateur ne supporte pas la lecture vidéo.
            </video>
            
            <div class="controls-video">
                <button type="button" class="play" data-icon="P" aria-label="play pause toggle"></button>
                <button type="button" class="stop" data-icon="S" aria-label="stop"></button>
                <div class="timer">
                    <div></div>
                    <span aria-label="timer">00:00</span>
                </div>
                <button type="button" class="rwd" data-icon="B" aria-label="rewind"></button>
                <button type="button" class="fwd" data-icon="F" aria-label="fast forward"></button>
            </div>

            <div class="video-info">
                <h3 id="video-title"></h3>
                <p id="video-description"></p>
            </div>

            <div class="video-playlist">
                <?php foreach ($videos as $video): ?>
                    <div class="playlist-item" data-video-id="<?php echo $video->getId(); ?>"
                         data-video-path="<?php echo $video->getFullPath(); ?>"
                         data-video-title="<?php echo htmlspecialchars($video->getTitle()); ?>"
                         data-video-description="<?php echo htmlspecialchars($video->getDescription()); ?>">
                        <img src="<?php echo $video->getThumbnailPath(); ?>" alt="Miniature">
                        <div class="playlist-item-info">
                            <h4><?php echo htmlspecialchars($video->getTitle()); ?></h4>
                            <p><?php echo htmlspecialchars($video->getDescription()); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="boutonAligne">
            <a href="?url=video/list" class="btnBase theme">
                <i class="iconColor material-icons md-36">undo</i>
            </a>
            <a href="?url=compte/index#form-add" class="btnBase theme">
                <i class="iconColor material-icons md-36">video_call</i>
            </a>
            <a href="?url=compte/index#form-add" class="btnBase theme">
                <i class="iconColor material-icons md-36">videocam</i>
            </a>
        </div>
    <?php endif; ?>
</section>
