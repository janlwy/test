<section class="audio-section">
    <a href='index.php?url=audio/insert' class="btnBase theme">Ajouter</a>
    <?php echo $audioList; ?>
    <script>
        var audios = <?php echo json_encode($audios); ?>;
    </script>
    <button id="play-selected" class="btnBase orange">Lire la sélection</button>
</section>