<section>
    <div id="audioData" style="display: none;"></div>
    <div id="player-container" class="player-container">
    <!--     <div class="searchBox">
        <input class="searchInput" type="text" name="chrch" placeholder="Trouver un artiste">
        <button type="button" class="searchButton" href="#">
            <i class="material-icons">search</i>
        </button>
    </div> -->
    <div class="player-audio">
        <!-- Section pour afficher les détails -->
        <div class="details-audio">
            <div class="now-playing">Piste x de y</div>
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
    </div>
</section>
