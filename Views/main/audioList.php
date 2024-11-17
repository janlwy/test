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
        <?php 
            error_log("Erreur affichée dans audioList: " . $_SESSION['erreur']);
            unset($_SESSION['erreur']); 
        ?>
    <?php endif; ?>
    <br><br>
    <div class="boutonAligne">
        <a href="?url=compte/index#form-add" class="btnBase theme">
            <i class="iconColor material-icons md-36">library_add</i>
            <span class="spanIconText">Ajouter</span>
        </a>
        <a href="?url=audio/player" class="btnBase theme">
            <i class="iconColor material-icons md-36">radio</i>
            <span class="spanIconText">Lecteur</span>
        </a>
        <a href="?url=compte/index#form-add" class="btnBase theme">
            <i class="iconColor material-icons md-36">mic</i>
            <span class="spanIconText">Enregistrer</span>
        </a>
    </div>

    <?php echo $audioList; ?>

    <input type="hidden" name="csrf_token" value="<?php echo $session->get('csrf_token'); ?>">
    <div id="audioList-data"
        data-csrf-token="<?php echo htmlspecialchars($session->get('csrf_token')); ?>"
        data-audios='<?php echo htmlspecialchars(json_encode(array_map(function ($audio) {
                            return [
                                'id' => $audio->getId(),
                                'title' => $audio->getTitle(), 
                                'artist' => $audio->getArtist(),
                                'path' => $audio->getPath(),
                                'fullPath' => 'Ressources/audio/' . $audio->getPath(),
                                'image' => $audio->getImage(),
                                'fullImage' => 'Ressources/images/pochettes/' . $audio->getImage()
                            ];
                        }, $audios)), ENT_QUOTES, 'UTF-8'); ?>'
        data-user-id="<?php echo htmlspecialchars($_SESSION['user_id']); ?>"
        style="display: none;">
    </div>
    <div class="button-container">
        <button type="button" id="play-selected" class="btnBase orange">
            <i class="material-icons">play_arrow</i> Lire la sélection
        </button>
    </div>
    <div id="message-container"></div>
</section>
