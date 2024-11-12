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

            const selectedTracks = Array.from(selectedCheckboxes).map(checkbox => {
                const audioItem = checkbox.closest('.audio-item');
                return {
                    id: parseInt(checkbox.getAttribute('data-audio-id')),
                    title: audioItem.querySelector('h4').textContent,
                    artist: audioItem.querySelector('p').textContent,
                    image: audioItem.querySelector('.photoAudio').src,
                    path: `Ressources/audio/${checkbox.getAttribute('data-audio-path')}`
                };
            });
            
            // Sauvegarder la sélection via AJAX
            // Récupérer le token CSRF
            const csrfToken = '<?php echo $session->get('csrf_token'); ?>';
            
            fetch('index.php?url=audio/saveSelection', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-Token': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache'
                },
                cache: 'no-store',
                body: JSON.stringify({ 
                    tracks: selectedTracks.map(track => track.id),
                    trackData: selectedTracks 
                }),
                credentials: 'same-origin'
            })
            .then(async response => {
                const text = await response.text();
                
                // Vérifier si la réponse est vide
                if (!text.trim()) {
                    throw new Error('Réponse vide du serveur');
                }
                
                try {
                    // Tenter de parser le JSON
                    const data = JSON.parse(text);
                    
                    // Vérifier si la réponse indique une erreur
                    if (!response.ok || data.error) {
                        throw new Error(data.message || 'Erreur serveur');
                    }
                    
                    return data;
                } catch (e) {
                    console.error('Réponse brute:', text);
                    throw new Error(`Erreur de parsing JSON: ${e.message}`);
                }
            })
            .then(data => {
                if (data.success) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '?url=audio/player';
                    
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = 'csrf_token';
                    csrfInput.value = csrfToken;
                    
                    form.appendChild(csrfInput);
                    document.body.appendChild(form);
                    form.submit();
                } else {
                    alert(data.message || 'Une erreur est survenue lors de la sélection des pistes.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Afficher un message d'erreur plus détaillé
                alert('Erreur: ' + (error.message || 'Une erreur est survenue lors de la communication avec le serveur.'));
                // Log l'erreur complète pour le débogage
                console.log('Détails de l\'erreur:', {
                    message: error.message,
                    stack: error.stack,
                    response: error.response
                });
            });
        }
        </script>
    </div>
</section>
