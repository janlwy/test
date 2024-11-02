<section class="audio-section dl">
    <div class="searchBox">
        <form method="GET" class="search-form">
            <input type="hidden" name="url" value="audio/list">
            <input class="searchInput" type="text" name="search"
                value="<?php echo htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                placeholder="Rechercher par titre ou artiste"
                onkeydown="if(event.key === 'Enter') this.form.submit();">
            <button type="button" class="clearButton" onclick="clearSearch()" style="display: <?php echo isset($_GET['search']) && $_GET['search'] !== '' ? 'block' : 'none'; ?>">
                <i class="material-icons">close</i>
            </button>
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
    <br><br>
    <div class="boutonAligne">
        <button type="button" class="btnBase theme" onclick="window.location.href='?url=compte/index#form-add';"><i class="iconColor material-icons md-36">library_add</i><span class="spanIconText">Ajouter</span></button>
        <button type="button" class="btnBase theme" onclick="window.location.href='?url=audio/player';"><i class="iconColor material-icons md-36">radio</i><span class="spanIconText">Lecteur</span></button>
        <button type="button" class="btnBase theme" onclick="window.location.href='?url=compte/index#form-add';"><i class="iconColor material-icons md-36">mic</i><span class="spanIconText">Enregistrer</span></button>
    </div>

    <?php echo $audioList; ?>

    <div id="audioData"
        data-audios='<?php echo htmlspecialchars(json_encode(array_map(function ($audio) {
                            return $audio->jsonData;
                        }, $audios)), ENT_QUOTES, 'UTF-8'); ?>'
        style="display: none;">
    </div>
    <div class="button-container">
        <button type="button" id="play-selected" class="btnBase orange" onclick="playSelectedTracks()">Lire la sélection</button>
    </div>
</section>
