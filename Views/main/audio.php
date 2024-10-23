<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="Jean-Louis Marsillon">
        <meta name="description" content="Mon projet de certif DWWM">
        <title>MédiaBox-Musiques</title>
        <link rel="icon" type="image/x-icon" href="/ressources/favicon/favicon.ico">
        <link rel="stylesheet" href="/css/media.css">
        <link rel="stylesheet" href="/css/mediamusic.css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    </head>

    <body class="clair-theme" id="themeBody">

        <header>
            <h1>Musiques</h1>
            <button type="button" id="boutonTheme" class="boutonTheme material-icons" title="no"></button>
        </header>

        <nav class="menunav" id="myMenunav">
        <a href="/media/box.html" class="active"><i class="material-icons">home</i></a>
        <a href="/media/medias/compte.html" class="barefut"><!--i class="material-icons">face_6</i-->Profile</a>
        <a href="/media/medias/music.html">Musique</a>
        <a href="/media/medias/video.html">Vidéos</a>
        <a href="/media/medias/photo.html">Photos</a>
        <a href="/media/medias/lyrics.html">Paroles</a>
        <a href="javascript:void(0);" class="menuicon" onclick="myFunction()">
            <i class="material-icons">menu</i>
        </a>
        </nav>
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
                <input type="range" aria-label="nolabel" min="1" max="100" value="0" class="seek_slider"onchange="seekTo()">
                <div class="total-duration">00:00</div>
            </div>
    
            <!-- Define the section for displaying the volume slider-->
            <div class="slider_container">
                <i class="material-icons md-24">volume_down</i>
                <input type="range" aria-label="nolabel" min="1" max="100" value="99" class="volume_slider"onchange="setVolume()">
                <i class="material-icons md-24">volume_up</i>
            </div>
        </div>
        
        <footer class="playerFooter">
            <div class="socle">
                <div class="icon-bar">
                    <a href="/media/box.html" class="navlabel"><i class="material-icons">home</i>
                        <span>Accueil</span>
                    </a>
                    <a href="/media/medias/compte.html" class="navlabel"><i class="material-icons">face_6</i>
                        <span>Profile</span>
                    </a>
                    <a href="#" class="navlabel"><i class="iconeTheme material-icons">brightness_4</i>
                        <span>Thème</span>
                    </a>
                    <a href="/inbox.html" class="navlabel"><i class="material-icons">logout</i>
                        <span>Quitter</span>
                    </a>
                </div>
                <p class="txtfootr">Copyright © 2023 par moimême. All rights reversed.</p>
            </div>
        </footer>
        
        <script src="/js/media.js" type="text/javascript"></script>
        <script src="/js/mediamusic.js" type="text/javascript"></script>
        <noscript>Vous devez activer javascript pour une expérience complête de ce site</noscript>
    </body>    
</html>