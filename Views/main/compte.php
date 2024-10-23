
<h2>Gestion des médias</h2>

<section class="monEspace">

    <button type="button" class="collapsible">Musiques</button>
    <div class="contentCollapse"><span class="br"></span>
        <button type="button" class="btnBase blueid="form-add">Édition</button>

        <form id="formCrud" class="formFont">

            <div class="rowEspace">
                <div class="collabel"> 
            <!-- collabel et colinput font reference à uaffichage en "colone" ecran > 600px -->
                    <label for="title">Titre</label>
                </div>
                <div class="colinput">
                    <input type="text" name="title" id="titleclass="inputMonEspace"/>
                </div>
            </div>
            <div class="rowEspace">
                <div class="collabel">
                    <label for="artist">Artiste</label>
                </div>
                <div class="colinput">
                    <input type="text" name="artiste" id="artistclass="inputMonEspace"/>
                </div>
            </div>
            <div class="rowEspace">
                <div class="collabel">
                    <label for="image">Pochette</label>
                </div>
                <div class="colinput">
                    <input type="text" name="image" id="imageclass="inputMonEspace"/>
                </div>
            </div>
            <div class="rowEspace">
                <div class="collabel">
                    <label for="path">Source</label>
                </div>
                <div class="colinput">
                    <input type="text" name="path" id="pathclass="inputMonEspace"/>
                </div>
            </div>
            <input type="hidden" name="hide" id="hidden"/>
            <div id="msg"></div>
            <hr>
            <button type="submit"  class="btnBase vertid="formsave">Exécuter</button>
            <button type="button" class="btnBase grisid="formcancel">Annuler</button>
        </form><span class="br"></span><hr>

        <div class="formFont" id="contentMusic">
            <div class="rowEspace">
                <div class="collabel">
                    <label for="search">Recherche</label>
                </div>
                <div class="colinput">
                    <input type="search" class="inputMonEspaceplaceholder="Rechercher par titre"/>
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
        <button type="button" class="btnBase blueid="VideoAdd">Ajouter une Vidéo</button>
        <div class="formFont" id="contentVideo">
        </div><span class="br"></span>
    </div>
    <span class="br"></span>

    <button type="button" class="collapsible">Photos</button>
    <div class="contentCollapse"><span class="br"></span>
        <button type="button" class="btnBase blueid="PhotoAdd">Ajouter une Photo</button>
        <div class="formFont" id="contentPhoto">
        </div>
    </div>
    <span class="br"></span>

    <button type="button" class="collapsible">Textes</button>
    <div class="contentCollapse"><span class="br"></span>
        <button type="button" class="btnBase blueid="texteAdd">Ajouter un texte</button>
        <div class="formFont" id="contentTexte">
        </div>
    </div>

</section>
