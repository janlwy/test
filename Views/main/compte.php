<h2>Gestion des médias</h2>

<section class="monEspace">

    <button type="button" class="collapsible">Musiques</button>
    <div class="contentCollapse"><span class="br"></span>
        <button type="button" class="btnBase blue" id="form-add">Édition</button>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (window.location.hash === '#form-add') {
                    document.querySelector('.collapsible').click();
                }
            });
        </script>

        <form id="formCrud" class="formFont" method="POST" action="?url=audio/addMusic" enctype="multipart/form-data">

            <div class="rowEspace">
                <div class="collabel">
                    <!-- collabel et colinput font reference à un affichage en "colone" ecran > 600px -->
                    <label for="title">Titre</label>
                </div>
                <div class="colinput">
                    <input type="text" name="title" id="title" class="inputMonEspace" required />
                </div>
            </div>
            <div class="rowEspace">
                <div class="collabel">
                    <label for="artist">Artiste</label>
                </div>
                <div class="colinput">
                    <input type="text" name="artiste" id="artist" class="inputMonEspace" required />
                </div>
            </div>
            <div class="rowEspace">
                <div class="collabel">
                    <label for="image">Pochette</label>
                </div>
                <div class="colinput">
                    <input type="file" name="image" id="image" class="inputMonEspace" required />
                </div>
            </div>
            <div class="rowEspace">
                <div class="collabel">
                    <label for="path">Source</label>
                </div>
                <div class="colinput">
                    <input type="file" name="path" id="path" class="inputMonEspace" required />
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="hide" id="hidden" />
            <div id="msg"></div>
            <hr>
            <button type="submit" class="btnBase vert" id="formsave">Exécuter</button>
            <button type="button" class="btnBase gris" id="formcancel">Annuler</button>
        </form><span class="br"></span>
        <hr>

        <div class="formFont" id="contentMusic">
            <!-- <div class="rowEspace">
                <div class="collabel">
                    <label for="search">Recherche</label>
                </div>
                <div class="colinput">
                    <input type="search" class="inputMonEspace" placeholder="Rechercher par titre"/>
                </div>
            </div> -->
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
