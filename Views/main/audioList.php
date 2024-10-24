<section>
    <h1>Liste des Enregistrements Audio</h1>
    <ul id="audio-list">
        <!-- Les enregistrements audio seront listés ici -->
        <?php
        // Supposons que $audios soit un tableau d'enregistrements audio
        foreach ($audios as $audio) {
            echo "<li data-id='{$audio['id']}'>{$audio['title']} - {$audio['artist']}</li>";
        }
        ?>
    </ul>
    <button id="play-selected">Lire la sélection</button>
</section>
