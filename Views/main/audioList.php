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

    <script>
        function clearSearch() {
            document.querySelector('.searchInput').value = '';
            document.querySelector('.search-form').submit();
        }

        // Afficher/masquer le bouton clear
        document.querySelector('.searchInput').addEventListener('input', function() {
            document.querySelector('.clearButton').style.display = this.value ? 'block' : 'none';
        });
    </script>

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

    <form method="POST" action="?url=audio/create" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo $session->get('csrf_token'); ?>">
        <button type="button" class="btnBase theme" onclick="window.location.href='?url=compte/index#form-add';">Ajouter</button>
    </form>

    <?php echo $audioList; ?>

    <script>
        var audios = <?php echo json_encode($audios); ?>;
    </script>
    <button id="play-selected" class="btnBase orange">Lire la sélection</button>
</section>
