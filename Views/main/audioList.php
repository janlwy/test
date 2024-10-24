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

<script>
    document.getElementById('play-selected').addEventListener('click', function() {
        const selectedAudios = Array.from(document.querySelectorAll('#audio-list li.selected')).map(li => li.dataset.id);
        if (selectedAudios.length > 0) {
            window.location.href = `?url=audio/index&ids=${selectedAudios.join(',')}`;
        } else {
            alert('Veuillez sélectionner au moins un enregistrement audio.');
        }
    });

    document.querySelectorAll('#audio-list li').forEach(li => {
        li.addEventListener('click', function() {
            this.classList.toggle('selected');
        });
    });
</script>
