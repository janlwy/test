<section>
    <span class="br"></span>
    <a href='index.php?url=audio/insert' class='btnBase gris'>Ajouter</a>
    <?php echo $audioList; ?>
    <script>
        var audios = <?php echo json_encode($audios); ?>;
    </script>
    <button id="play-selected">Lire la sélection</button>
</section>
