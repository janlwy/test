<section>
    <span class="br"></span>
    <a href='index.php?url=audio/insert' class='btnBase gris'>Ajouter</a>
    <dl>
        <?php foreach ($audios as $audio): ?>
            <dt>Titre de l'audio</dt>
            <dd><?php echo htmlspecialchars($audio['title'], ENT_QUOTES, 'UTF-8'); ?></dd>
            
            <dt>Artiste</dt>
            <dd><?php echo htmlspecialchars($audio['artist'], ENT_QUOTES, 'UTF-8'); ?></dd>
            
            <dt>Image de l'album</dt>
            <dd><img src="<?php echo htmlspecialchars($audio['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="Image de l'album"></dd>
        <?php endforeach; ?>
    </dl>
    <script>
        var audios = <?php echo json_encode($audios); ?>;
    </script>
    <button id="play-selected">Lire la sélection</button>
</section>
