<section class="audio-section">
    <button type="button" class="btnBase theme" onclick="document.getElementById('form-add').click();">Ajouter</button>
    <?php echo $audioList; ?>
    <script>
        var audios = <?php echo json_encode($audios); ?>;
    </script>
    <button id="play-selected" class="btnBase orange">Lire la sélection</button>
</section>
