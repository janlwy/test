<section>
    <div class="searchBox">
        <form method="GET" action="index.php" class="search-form">
            <input type="hidden" name="url" value="mediabox/index">
            <input class="searchInput" type="text" name="q"
                value="<?php echo htmlspecialchars($searchQuery ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                placeholder="Recherche globale (notes textes, audios, photos OCR, vidéos)...">
            <button type="submit" class="searchButton" title="Rechercher">
                <i class="material-icons">search</i>
            </button>
            <?php if (!empty($searchQuery)): ?>
                <a href="?url=mediabox/index" class="clearButton" title="Effacer la recherche">
                    <i class="material-icons">clear</i>
                </a>
            <?php endif; ?>
        </form>
    </div>

    <?php if (isset($searchResults) && $searchResults !== null): ?>
        <div class="global-search-results">
            <h3>
                <i class="material-icons">manage_search</i>
                Résultats pour « <?php echo htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8'); ?> »
                (<?php echo (int)$searchResults['total']; ?>)
            </h3>

            <?php if ($searchResults['total'] === 0): ?>
                <p class="no-content">Aucun élément correspondant trouvé dans vos notes, audios, photos ou vidéos.</p>
            <?php else: ?>
                <div class="search-results-grid">
                    <?php foreach ($searchResults['textes'] as $texte): ?>
                        <div class="search-result-card">
                            <div class="search-result-header">
                                <i class="material-icons">auto_stories</i>
                                <span><?php echo htmlspecialchars($texte->getTitle(), ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            <p><?php echo htmlspecialchars(mb_substr($texte->getContent(), 0, 110), ENT_QUOTES, 'UTF-8'); ?>...</p>
                            <a href="?url=texte/viewer&id=<?php echo $texte->getId(); ?>" class="btnBase vert">
                                <i class="material-icons">visibility</i> Ouvrir la note
                            </a>
                        </div>
                    <?php endforeach; ?>

                    <?php foreach ($searchResults['audios'] as $audio): ?>
                        <div class="search-result-card">
                            <div class="search-result-header">
                                <i class="material-icons">headphones</i>
                                <span><?php echo htmlspecialchars($audio->getTitle(), ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            <p><?php echo htmlspecialchars($audio->getArtist(), ENT_QUOTES, 'UTF-8'); ?></p>
                            <a href="?url=audio/player&id=<?php echo $audio->getId(); ?>" class="btnBase vert">
                                <i class="material-icons">play_arrow</i> Écouter
                            </a>
                        </div>
                    <?php endforeach; ?>

                    <?php foreach ($searchResults['photos'] as $photo): ?>
                        <div class="search-result-card">
                            <div class="search-result-header">
                                <i class="material-icons">photo_camera_back</i>
                                <span><?php echo htmlspecialchars($photo->getTitle(), ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            <p><?php echo htmlspecialchars(mb_substr($photo->getOcrText() ?: ($photo->getDescription() ?? ''), 0, 100), ENT_QUOTES, 'UTF-8'); ?></p>
                            <a href="?url=photo/viewer&id=<?php echo $photo->getId(); ?>" class="btnBase vert">
                                <i class="material-icons">visibility</i> Voir la photo
                            </a>
                        </div>
                    <?php endforeach; ?>

                    <?php foreach ($searchResults['videos'] as $video): ?>
                        <div class="search-result-card">
                            <div class="search-result-header">
                                <i class="material-icons">live_tv</i>
                                <span><?php echo htmlspecialchars($video->getTitle(), ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            <p><?php echo htmlspecialchars(mb_substr($video->getDescription() ?? '', 0, 100), ENT_QUOTES, 'UTF-8'); ?></p>
                            <a href="?url=video/player&id=<?php echo $video->getId(); ?>" class="btnBase vert">
                                <i class="material-icons">play_arrow</i> Regarder
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- CARTES DE NAVIGATION -->
    <div class="row">
        <div class="column">
            <a href="?url=audio/list">
                <article class="card c1">
                    <i class="material-icons">headphones</i>
                    <h2 class="titrecarte">Audio</h2>
                </article>
            </a>
        </div>
        <div class="column">
            <a href="?url=video/index">
                <article class="card c2">
                    <i class="material-icons">live_tv</i>
                    <h2 class="titrecarte">Vidéo</h2>
                </article>
            </a>
        </div>
        <div class="column">
            <a href="?url=photo/index">
                <article class="card c3">
                    <i class="material-icons">photo_camera_back</i>
                    <h2 class="titrecarte">Photo</h2>
                </article>
            </a>
        </div>
        <div class="column">
            <a href="?url=texte/index">
                <article class="card c4">
                    <i class="material-icons">auto_stories</i>
                    <h2 class="titrecarte">Texte</h2>
                </article>
            </a>
        </div>
    </div>
</section>