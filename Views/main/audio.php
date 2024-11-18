<section>
    <?php if (empty($audios)): ?>
        <div class="info-message">Aucune piste sélectionnée</div>
        <a href="?url=audio/list" class="btnBase theme">Retour à la liste</a>
    <?php else: ?>
        <div id="player-container" class="player-container">
            <div class="player-audio">
                <div class="details-audio">
                    <div id="track-art" class="track-art">
                        <img src="<?php echo $audios[0]->getFullImagePath(); ?>" alt="Pochette">
                    </div>
                    <div id="track-name" class="track-name"><?php echo htmlspecialchars($audios[0]->getTitle()); ?></div>
                    <div id="track-artist" class="track-artist"><?php echo htmlspecialchars($audios[0]->getArtist()); ?></div>
                </div>

                <audio controls class="audio-player" autoplay>
                    <?php foreach ($audios as $audio): ?>
                        <source src="<?php echo $audio->getFullPath(); ?>" type="audio/mpeg">
                    <?php endforeach; ?>
                    Votre navigateur ne supporte pas la lecture audio.
                </audio>
            </div>
            
            <div class="boutonAligne">
                <a href="?url=audio/list" class="btnBase theme">
                    <i class="iconColor material-icons md-36">undo</i>
                </a>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['erreur'])): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($_SESSION['erreur']); ?>
        </div>
        <?php unset($_SESSION['erreur']); ?>
    <?php endif; ?>

    <div id="player-data" 
         data-tracks='<?php echo htmlspecialchars(json_encode(array_map(function($audio) {
             return [
                 'title' => $audio->getTitle(),
                 'artist' => $audio->getArtist(),
                 'audio' => 'Ressources/audio/' . $audio->getPath(),
                 'cover' => 'Ressources/images/pochettes/' . $audio->getImage()
             ];
         }, $audios)), ENT_QUOTES, 'UTF-8'); ?>'>
    </div>
    <?php if (empty($audios)): ?>
        <div class="info-message">
            Aucune piste sélectionnée. Veuillez retourner à la liste et sélectionner des pistes à lire.
        </div>
    <?php endif; ?>
    <div id="player-container" class="player-container">
        <div class="player-audio">
            <!-- Section pour afficher les détails -->
            <div class="details-audio">
                <div id="now-playing" class="now-playing">Piste 0 de 0</div>
                <div id="track-art" class="track-art"></div>
                <div id="track-name" class="track-name">Nom de la piste</div>
                <div id="track-artist" class="track-artist">Artiste de la piste</div>
            </div>

            <!-- Section pour afficher les boutons de contrôle -->
            <div class="buttons-audio">
                <div class="prev-track" onclick="prevTrack()">
                    <i class="material-icons md-36">skip_previous</i>
                </div>
                <div class="playpause-track" onclick="playpauseTrack()">
                    <i class="material-icons md-48">play_circle</i>
                </div>
                <div class="next-track" onclick="nextTrack()">
                    <i class="material-icons md-36">skip_next</i>
                </div>
            </div>

            <!-- Section pour afficher le curseur de recherche -->
            <div class="slider_container-audio">
                <div class="current-time">00:00</div>
                <input type="range" aria-label="nolabel" min="1" max="100" value="0" class="seek_slider" onchange="seekTo()">
                <div class="total-duration">00:00</div>
            </div>

            <!-- Section pour afficher le curseur de volume -->
            <div class="slider_container-audio">
                <i class="material-icons md-24">volume_down</i>
                <input type="range" aria-label="nolabel" min="1" max="100" value="99" class="volume_slider" onchange="setVolume()">
                <i class="material-icons md-24">volume_up</i>
            </div>
        </div><br>
        <hr><br>
        <div class="boutonAligne">
            <a href="?url=audio/list" class="btnBase theme" style="display: inline-block;">
                <i class="iconColor material-icons md-36">undo</i>
            </a>
            <a href="?url=compte/index#form-add" class="btnBase theme" style="display: inline-block;">
                <i class="iconColor material-icons md-36">library_add</i>
            </a>
            <a href="?url=compte/index#form-add" class="btnBase theme" style="display: inline-block;">
                <i class="iconColor material-icons md-36">mic</i>
            </a>
        </div>
</section>
