<section>
    <span class="br"></span>
    <a href='index.php?url=audio/insert' class='btnBase gris'>Ajouter</a>
    <table class='' border=1 style='border-collapse:collapse;' id="audio-table">
        <tr class='fond-blue'>
            <th class=''>TITRE</th>
            <th class=''>ARTISTE</th>
            <th class=''>IMAGE</th>
            <th class='actions-column'>ACTIONS</th>
        </tr>
        <?php foreach ($audios as $audio): ?>
            <tr data-id="<?php echo $audio['id']; ?>">
                <td class='title-column'><?php echo $audio['title']; ?></td>
                <td class='artist-column'><?php echo $audio['artist']; ?></td>
                <td class='image-column'><img class='photoAudio' src='Ressources/images/pochettes/<?php echo $audio['image']; ?>' alt='cover'></td>
                <td class='action-buttons'>
                    <div class='button-group'>
                        <a class='btnBase blue' href='index.php?url=audio/show/<?php echo $audio['id']; ?>'>Afficher</a>
                        <a class='btnBase vert' href='index.php?url=audio/update/<?php echo $audio['id']; ?>'>Modifier</a>
                        <a class='btnBase rouge' href='index.php?url=audio/delete/<?php echo $audio['id']; ?>'>Supprimer</a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <script>
        var audios = <?php echo json_encode($audios); ?>;
    </script>
    <button id="play-selected">Lire la sélection</button>
</section>
