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
        <form method="POST" style="display: inline;">
            <input type="hidden" name="csrf_token" value="<?php echo $session->get('csrf_token'); ?>">
            <button type="submit" class="btnBase theme" formaction="?url=compte/index#form-add">
                <i class="iconColor material-icons md-36">library_add</i>
                <span class="spanIconText">Ajouter</span>
            </button>
        </form>
        <form method="POST" style="display: inline;">
            <input type="hidden" name="csrf_token" value="<?php echo $session->get('csrf_token'); ?>">
            <button type="submit" class="btnBase theme" formaction="?url=audio/player">
                <i class="iconColor material-icons md-36">radio</i>
                <span class="spanIconText">Lecteur</span>
            </button>
        </form>
        <form method="POST" style="display: inline;">
            <input type="hidden" name="csrf_token" value="<?php echo $session->get('csrf_token'); ?>">
            <button type="submit" class="btnBase theme" formaction="?url=compte/index#form-add">
                <i class="iconColor material-icons md-36">mic</i>
                <span class="spanIconText">Enregistrer</span>
            </button>
        </form>
    </div>

    <?php echo $audioList; ?>

    <div id="audioData"
        data-audios='<?php echo htmlspecialchars(json_encode(array_map(function ($audio) {
                            return $audio->jsonData;
                        }, $audios)), ENT_QUOTES, 'UTF-8'); ?>'
        data-user-id="<?php echo htmlspecialchars($_SESSION['user_id']); ?>"
        style="display: none;">
    </div>
    <div class="button-container">
        <button type="button" id="play-selected" class="btnBase orange" onclick="saveAndPlaySelectedTracks()">Lire la sélection</button>
        <script>
        function saveAndPlaySelectedTracks() {
            const selectedCheckboxes = document.querySelectorAll('.select-audio:checked');
            if (selectedCheckboxes.length === 0) {
                alert('Veuillez sélectionner au moins une piste audio.');
                return;
            }

            const selectedTracks = Array.from(selectedCheckboxes)
                .map(checkbox => {
                    const audioItem = checkbox.closest('.audio-item');
                    return {
                        id: checkbox.getAttribute('data-audio-id'),
                        path: checkbox.getAttribute('data-audio-path'),
                        title: audioItem.querySelector('h4').textContent,
                        artist: audioItem.querySelector('p').textContent,
                        image: audioItem.querySelector('.photoAudio').src
                    };
                });

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '?url=audio/player';
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = 'csrf_token';
            csrfInput.value = '<?php echo $session->get('csrf_token'); ?>';
            
            form.appendChild(csrfInput);
            document.body.appendChild(form);
            form.submit();
        }
        </script>
    </div>
</section>
