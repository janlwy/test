<div class="searchBox">
    <input class="searchInput" type="text" name="chrch" placeholder="Trouver un artiste">
    <button type="button" class="searchButton" href="#trouver">
        <i class="material-icons">search</i>
    </button>
</div>


<div class="player-video">
    <video controls>
        <source src="/ressources/videos/sintel-short.mp4" type="video/mp4">
        <source src="/ressources/videos/sintel-short.webm" type="video/webm">
        <!-- fallback content here -->
    </video>
    <div class="controls-video">
        <button type="button" class="play" data-icon="P" aria-label="play pause toggle"></button>
        <button type="button" class="stop" data-icon="S" aria-label="stop"></button>
        <div class="timer">
            <div></div><span aria-label="timer">00:00<span>
        </div>
        <button type="button" class="rwd" data-icon="B" aria-label="rewind"></button>
        <button type="button" class="fwd" data-icon="F" aria-label="fast forward"></button>
    </div>
</div>