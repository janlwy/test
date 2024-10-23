<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="Jean-Louis Marsillon">
        <meta name="description" content="Mon projet de certif DWWM">
        <title>MédiaBox-Videos</title>
        <link rel="icon" type="image/x-icon" href="/ressources/favicon/favicon.ico">
        <link rel="stylesheet" href="/css/media.css">
        <link rel="stylesheet" href="/css/mediavideo.css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    </head>

    <body class="clair-theme" id="themeBody">

        <header>
            <h1>Vidéos</h1>
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

       <!-- <div class="searchBox">
            <input class="searchInput" type="text" name="chrch" placeholder="Trouver un artiste">
            <button type="button" class="searchButton" href="#trouver">
                <i class="material-icons">search</i>
            </button>
        </div> -->

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
                    <div></div><span aria-label="timer">00:00</span>
                </div>
                <button type="button" class="rwd" data-icon="B" aria-label="rewind"></button>
                <button type="button" class="fwd" data-icon="F" aria-label="fast forward"></button>
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
            <div class="socleRadiusUp">
            </div>
        </footer>

    <script src="/js/media.js" type="text/javascript"></script>
    <script src="/js/mediavideo.js" type="text/javascript"></script>
    <noscript>Vous devez activer javascript pour une expérience complête de ce site</noscript>
    </body>    
</html>
