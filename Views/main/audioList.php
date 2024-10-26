<section class="audio-section dl">
    <div class="search-container">
        <form method="GET" action="?url=audio/list" class="search-form">
            <input type="text" name="search" 
                   value="<?php echo htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
                   placeholder="Rechercher par titre ou artiste"
                   class="searchInput">
            
            <select name="filter" class="filter-select">
                <option value="">Tous</option>
                <option value="title" <?php echo ($_GET['filter'] ?? '') === 'title' ? 'selected' : ''; ?>>Titres</option>
                <option value="artist" <?php echo ($_GET['filter'] ?? '') === 'artist' ? 'selected' : ''; ?>>Artistes</option>
            </select>
            
            <button type="submit" class="searchButton">
                <i class="material-icons">search</i>
            </button>
        </form>
    </div>

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
