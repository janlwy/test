<section class="audio-section dl">
    <button type="button" class="btnBase theme" onclick="window.location.href='?url=compte/index#form-add';">Ajouter</button>
    <?php echo $audioList; ?>
    <script>
        var audios = <?php echo json_encode($audios); ?>;
    </script>
    <button id="play-selected" class="btnBase orange">Lire la sélection</button>
</section>