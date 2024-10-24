<section>
    <span class="br"></span>
    <ul id="audio-list">
        <?php if (!empty($audios)): ?>
            <?php foreach ($audios as $audio): ?>
                <li data-id="<?php echo htmlspecialchars($audio['id']); ?>">
                    <?php echo htmlspecialchars($audio['title']); ?> - <?php echo htmlspecialchars($audio['artist']); ?>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>Aucun enregistrement audio trouvé.</li>
        <?php endif; ?>
    </ul>
    <script>
        var audios = <?php echo json_encode($audios); ?>;
    </script>
    <button id="play-selected">Lire la sélection</button>
</section>
