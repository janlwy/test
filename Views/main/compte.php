<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Jean-Louis Marsillon">
    <meta name="description" content="Mon projet de certif DWWM">
    <title>Mon Espace</title>
    <link rel="icon" type="image/x-icon" href="/ressources/favicon/favicon.ico">
    <link rel="stylesheet" href="/css/media.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
</head>

<body class="clair-theme" id="themeBody">

    <header>
        <h1>Mon Espace</h1>
        <button type="button" id="boutonTheme" class="boutonTheme material-icons" title="no"></button>
    </header>

        <nav class="menunav" id="myMenunav">
            <a href="/media/box.html" class="active barefut"><i class="material-icons">home</i></a>
            <a href="/media/medias/compte.html" class="barefut">Profile</a>
            <a href="/media/medias/music.html">Musique</a>
            <a href="/media/medias/video.html">Vidéos</a>
            <a href="/media/medias/photo.html">Photos</a>
            <a href="/media/medias/lyrics.html">Paroles</a>
            <a href="/inbox.html" class="sortie barefut"><i class="material-icons">logout</i></a>
            <a href="javascript:void(0);" class="menuicon" onclick="myFunction()">
                <i class="material-icons">menu</i>
            </a>
        </nav>

        <h2>Gestion des médias</h2>

        <section class="monEspace">

            <button type="button" class="collapsible">Musiques</button>
            <div class="contentCollapse"><span class="br"></span>
                <button type="button" class="btnBase blue" id="form-add">Édition</button>

                <form id="formCrud" class="formFont">

                    <div class="rowEspace">
                        <div class="collabel"> 
                    <!-- collabel et colinput font reference à un affichage en "colone" ecran > 600px -->
                            <label for="title">Titre</label>
                        </div>
                        <div class="colinput">
                            <input type="text" name="title" id="title" class="inputMonEspace"/>
                        </div>
                    </div>
                    <div class="rowEspace">
                        <div class="collabel">
                            <label for="artist">Artiste</label>
                        </div>
                        <div class="colinput">
                            <input type="text" name="artiste" id="artist" class="inputMonEspace"/>
                        </div>
                    </div>
                    <div class="rowEspace">
                        <div class="collabel">
                            <label for="image">Pochette</label>
                        </div>
                        <div class="colinput">
                            <input type="text" name="image" id="image" class="inputMonEspace"/>
                        </div>
                    </div>
                    <div class="rowEspace">
                        <div class="collabel">
                            <label for="path">Source</label>
                        </div>
                        <div class="colinput">
                            <input type="text" name="path" id="path" class="inputMonEspace"/>
                        </div>
                    </div>
                    <input type="hidden" name="hide" id="hidden"/>
                    <div id="msg"></div>
                    <hr>
                    <button type="submit"  class="btnBase vert" id="formsave">Exécuter</button>
                    <button type="button" class="btnBase gris" id="formcancel">Annuler</button>
                </form><span class="br"></span><hr>

                <div class="formFont" id="contentMusic">
                    <div class="rowEspace">
                        <div class="collabel">
                            <label for="search">Recherche</label>
                        </div>
                        <div class="colinput">
                            <input type="search" class="inputMonEspace" placeholder="Rechercher par titre"/>
                        </div>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th scope="col">Titre</th>
                                <th scope="col">Artiste</th>
                                <th scope="col" hidden>Pochette</th>
                                <th scope="col" hidden>Source</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <span class="br"></span>
                </div>

            </div>
            <span class="br"></span>
            
            <button type="button" class="collapsible">Vidéos</button>
            <div class="contentCollapse"><span class="br"></span>
                <button type="button" class="btnBase blue" id="VideoAdd">Ajouter une Vidéo</button>
                <div class="formFont" id="contentVideo">
                </div><span class="br"></span>
            </div>
            <span class="br"></span>

            <button type="button" class="collapsible">Photos</button>
            <div class="contentCollapse"><span class="br"></span>
                <button type="button" class="btnBase blue" id="PhotoAdd">Ajouter une Photo</button>
                <div class="formFont" id="contentPhoto">
                </div>
            </div>
            <span class="br"></span>

            <button type="button" class="collapsible">Textes</button>
            <div class="contentCollapse"><span class="br"></span>
                <button type="button" class="btnBase blue" id="texteAdd">Ajouter un texte</button>
                <div class="formFont" id="contentTexte">
                </div>
            </div>
            
        </section>

    <footer>
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

    <script src="/js/appCRUD.js" type="text/javascript"></script>
    <script src="/js/media.js" type="text/javascript"></script>
    <noscript>Vous devez activer javascript pour une expérience complête de ce site</noscript>
    
</body>

</html>