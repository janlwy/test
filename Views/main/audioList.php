<section class="audio-section dl">
    <?php if (isset($_SESSION['message'])): ?>
        <div class="success-message">
            <?php echo htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8');
            unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['erreur'])): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($_SESSION['erreur'], ENT_QUOTES, 'UTF-8');
            unset($_SESSION['erreur']); ?>
        </div>
    <?php endif; ?>
    <button type="button" class="btnBase theme" onclick="window.location.href='?url=compte/index#form-add';">Ajouter</button>
    <?php echo $audioList; ?>
    <script>
        var audios = <?php echo json_encode($audios); ?>;
    </script>
    <button id="play-selected" class="btnBase orange">Lire la sélection</button>
</section>
