<h2><?php echo $pageTitle; ?></h2>

<section class="monEspace">

    <button type="button" class="collapsible">Audio</button>
    <div class="contentCollapse">
        <button type="button" class="btnBase invisible invisibleTxt" id="form-add"> </button>
        <form id="formCrud" class="formFont" method="POST" action="?url=audio/create" enctype="multipart/form-data">
            <div class="rowEspace">
                <div class="collabel">
                    <label for="title">Titre</label>
                </div>
                <div class="colinput">
                    <input type="text" name="title" id="title" class="inputMonEspace"
                        required
                        pattern="[A-Za-z0-9\s\-_\.]{2,100}"
                        title="Le titre doit contenir entre 2 et 100 caractères alphanumériques"
                        maxlength="100" />
                </div>
            </div>
            <div class="rowEspace">
                <div class="collabel">
                    <label for="artist">Artiste</label>
                </div>
                <div class="colinput">
                    <input type="text" name="artiste" id="artist" class="inputMonEspace"
                        required
                        pattern="[A-Za-z0-9\s\-_\.]{2,100}"
                        title="Le nom de l'artiste doit contenir entre 2 et 100 caractères alphanumériques"
                        maxlength="100" />
                </div>
            </div>
            <div class="rowEspace">
                <div class="collabel">
                    <label for="image">Pochette</label>
                </div>
                <div class="colinput">
                    <input type="file" name="image" id="image" class="inputMonEspace"
                        required
                        accept="image/jpeg,image/png,image/webp" />
                </div>
            </div>
            <div class="rowEspace">
                <div class="collabel">
                    <label for="path">Fichier Audio</label>
                </div>
                <div class="colinput">
                    <input type="file" name="path" id="path" class="inputMonEspace"
                        required
                        accept="audio/mpeg,audio/mp4,audio/wav,audio/x-m4a,audio/aac,audio/ogg,video/mp4,video/x-m4v,application/mp4" />
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo $session->regenerateToken(); ?>">
            <input type="hidden" name="hide" id="hidden" />
            <div id="msg"></div>
            <hr>
            <button type="submit" class="btnBase vert" id="formsave">Exécuter</button>
            <a href="?url=audio/list"><button type="button" class="btnBase gris" id="formcancel">Annuler</button></a>
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

    <button type="button" class="collapsible">Vidéo</button>
    <div class="contentCollapse"><span class="br"></span>
        <button type="button" class="btnBase blue" id="VideoAdd">Ajouter une Vidéo</button>
        <div class="formFont" id="contentVideo">
        </div><span class="br"></span>
    </div>
    <span class="br"></span>

    <button type="button" class="collapsible">Photo</button>
    <div class="contentCollapse"><span class="br"></span>
        <button type="button" class="btnBase blue" id="PhotoAdd">Ajouter une Photo</button>
        <div class="formFont" id="contentPhoto">
        </div>
    </div>
    <span class="br"></span>

    <button type="button" class="collapsible">Texte</button>
    <div class="contentCollapse"><span class="br"></span>
        <button type="button" class="btnBase blue" id="texteAdd">Ajouter un texte</button>
        <div class="formFont" id="contentTexte">
        </div>
    </div>

</section>