<span class="br"></span>

<div class="player">

    <!-- Define the section for displaying details -->
    <div class="details">
        <div class="now-playing">Piste x de y</div>
        <div class="track-art"></div>
        <div class="track-name">Track Name</div>
        <div class="track-artist">Track Artist</div>
    </div>

    <!-- Define the section for displaying track buttons -->
    <div class="buttons">
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

    <!-- Define the section for displaying the seek slider-->
    <div class="slider_container">
        <div class="current-time">00:00</div>
        <input type="range" aria-label="nolabel" min="1" max="100" value="0" class="seek_slider" onchange="seekTo()">
        <div class="total-duration">00:00</div>
    </div>

    <!-- Define the section for displaying the volume slider-->
    <div class="slider_container">
        <i class="material-icons md-24">volume_down</i>
        <input type="range" aria-label="nolabel" min="1" max="100" value="99" class="volume_slider" onchange="setVolume()">
        <i class="material-icons md-24">volume_up</i>
    </div>
</div>