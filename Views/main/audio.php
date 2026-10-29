<section>
    <?php if (empty($formattedAudios)): ?>
        <div class="info-message">
            Aucune piste sélectionnée. Veuillez retourner à la liste et sélectionner des pistes à lire.
        </div>
        <div class="boutonAligne">
            <a href="?url=audio/list" class="btnBase theme">Retour à la liste</a>
        </div>
    <?php else: ?>
        <div id="player-data"
            data-tracks="<?php echo htmlspecialchars(json_encode($formattedAudios), ENT_QUOTES, 'UTF-8'); ?>">
        </div>

        <div id="player-container" class="player-container">
            <div class="player-audio">
                <div class="details-audio">
                    <div id="now-playing" class="now-playing">Piste 1 sur <?php echo count($formattedAudios); ?></div>
                    <div id="track-art" class="track-art"></div>
                    <div id="track-name" class="track-name">Nom de la piste</div>
                    <div id="track-artist" class="track-artist">Artiste de la piste</div>
                </div>

                <div class="buttons-audio">
                    <div class="prev-track" title="Piste précédente">
                        <i class="material-icons md-36">skip_previous</i>
                    </div>
                    <div class="playpause-track" title="Lecture / Pause">
                        <i class="material-icons md-48">play_circle</i>
                    </div>
                    <div class="next-track" title="Piste suivante">
                        <i class="material-icons md-36">skip_next</i>
                    </div>
                </div>

                <div class="slider_container-audio">
                    <div class="current-time">00:00</div>
                    <input type="range" aria-label="Progression" min="0" max="100" value="0" class="seek_slider">
                    <div class="total-duration">00:00</div>
                </div>

                <div class="slider_container-audio">
                    <i class="material-icons md-24">volume_down</i>
                    <input type="range" aria-label="Volume" min="0" max="100" value="90" class="volume_slider">
                    <i class="material-icons md-24">volume_up</i>
                </div>
            </div>

            <div class="boutonAligne">
                <a href="?url=audio/list" class="btnBase theme" title="Retour à la liste">
                    <i class="iconColor material-icons md-36">undo</i>
                </a>
                <a href="?url=audio/list#upload" class="btnBase theme" title="Importer un fichier">
                    <i class="iconColor material-icons md-36">library_add</i>
                </a>
                <a href="?url=audio/list#record" class="btnBase theme" title="Enregistrer une note vocale">
                    <i class="iconColor material-icons md-36">mic</i>
                </a>
            </div>
        </div>
    <?php endif; ?>
</section>