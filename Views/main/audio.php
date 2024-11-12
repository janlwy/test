<?php
// Protection contre l'accès direct
if (!defined('ROOT_PATH')) {
    header('Location: ../index.php?url=audio/player');
    exit();
}
?>
<section>
    <?php if (isset($_SESSION['message'])): ?>
        <div class="success-message">
            <?php echo htmlspecialchars($_SESSION['message']); ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['erreur'])): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($_SESSION['erreur']); ?>
        </div>
        <?php unset($_SESSION['erreur']); ?>
    <?php endif; ?>

    <?php if (empty($audios)): ?>
        <div class="info-message">
            Aucune piste sélectionnée. Veuillez retourner à la liste et sélectionner des pistes à lire.
        </div>
    <?php else: ?>
        <div id="audioData" style="display: none;" 
             data-user-id="<?php echo htmlspecialchars($_SESSION['user_id']); ?>"
             data-audios='<?php echo htmlspecialchars(json_encode(array_map(function ($audio) {
                                return [
                                    'id' => $audio->getId(),
                                    'title' => $audio->getTitle(),
                                    'artist' => $audio->getArtist(),
                                    'path' => $audio->getPath(),
                                    'image' => 'Ressources/images/pochettes/' . $audio->getImage()
                                ];
                            }, $audios)), ENT_QUOTES, 'UTF-8'); ?>'>
    <?php endif; ?>
    </div>
    <div id="player-container" class="player-container" <?php echo empty($audios) ? 'style="display: none;"' : ''; ?>>
        <!--     <div class="searchBox">
        <input class="searchInput" type="text" name="chrch" placeholder="Trouver un artiste">
        <button type="button" class="searchButton" href="#">
            <i class="material-icons">search</i>
        </button>
    </div> -->
        <div class="player-audio">
            <!-- Section pour afficher les détails -->
            <div class="details-audio">
                <div class="now-playing">Piste 0 de 0</div>
                <div class="track-art"></div>
                <div class="track-name">Nom de la piste</div>
                <div class="track-artist">Artiste de la piste</div>
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
            <form method="POST" style="display: inline;">
                <?php if (isset($session)): ?>
                    <input type="hidden" name="csrf_token" value="<?php echo $session->get('csrf_token'); ?>">
                <?php endif; ?>
                <button type="submit" class="btnBase theme" formaction="?url=audio/list"><i class="iconColor material-icons md-36">undo</i></button>
            </form>
            <form method="POST" style="display: inline;">
                <input type="hidden" name="csrf_token" value="<?php echo $session->get('csrf_token'); ?>">
                <button type="submit" class="btnBase theme" formaction="?url=compte/index#form-add"><i class="iconColor material-icons md-36">library_add</i></button>
            </form>
            <form method="POST" style="display: inline;">
                <input type="hidden" name="csrf_token" value="<?php echo $session->get('csrf_token'); ?>">
                <button type="submit" class="btnBase theme" formaction="?url=compte/index#form-add"><i class="iconColor material-icons md-36">mic</i></button>
            </form>
        </div>
</section>
