<section>
    <span class="br"></span>
    <ul id="audio-list">
        <!-- Les enregistrements audio seront listés ici -->
        <?php
        // Supposons que $audios soit un tableau d'enregistrements audio
        foreach ($audios as $audio) {
            echo "<li data-id='{$audio['id']}'>{$audio['title']} - {$audio['artist']}</li>";
        }
        ?>
    </ul>
    <script>
        var audios = <?php echo json_encode($audios); ?>;
    </script>
    <button id="play-selected">Lire la sélection</button>
</section>
