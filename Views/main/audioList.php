<section class="audio-section dl">
    <div class="searchBox">
        <input type="hidden" name="url" value="audio/list">
        <input class="searchInput" type="text" name="search" 
            value="<?php echo htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
            placeholder="Rechercher par titre ou artiste"
            oninput="if(this.value === '') document.querySelector('form').submit();">
        <button type="submit" class="searchButton" onclick="document.querySelector('form').submit();">
            <i class="material-icons">search</i>
        </button>
        <form method="GET" class="search-form" style="display:none;">
            <input type="hidden" name="url" value="audio/list">
            <input type="hidden" name="search" id="search-hidden">
        </form>
    </div>

    <script>
        document.querySelector('.searchInput').addEventListener('input', function(e) {
            document.getElementById('search-hidden').value = e.target.value;
        });
    </script>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="success-message">
            <?php echo htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8'); ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['erreur'])): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($_SESSION['erreur'], ENT_QUOTES, 'UTF-8'); ?>
        </div>
        <?php unset($_SESSION['erreur']); ?>
    <?php endif; ?>

    <button type="button" class="btnBase theme" onclick="window.location.href='?url=compte/index#form-add';">Ajouter</button>

    <?php echo $audioList; ?>

    <script>
        var audios = <?php echo json_encode($audios); ?>;
    </script>
    <button id="play-selected" class="btnBase orange">Lire la sélection</button>
</section>
