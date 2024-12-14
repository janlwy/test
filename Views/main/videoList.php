<section class="video-section">
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

    <div class="searchBox">
        <form class="search-form">
            <input type="text" class="searchInput" placeholder="Rechercher une vidéo...">
            <button type="button" class="searchButton">
                <i class="material-icons">search</i>
            </button>
            <button type="button" class="clearButton" style="display: none;" onclick="clearSearch()">
                <i class="material-icons">clear</i>
            </button>
        </form>
    </div>

    <div class="boutonAligne">
        <a href="?url=compte/index#form-add" class="btnBase theme">
            <i class="iconColor material-icons md-36">video_call</i>
            <span class="spanIconText">Ajouter</span>
        </a>
        <a href="?url=video/player" class="btnBase theme">
            <i class="iconColor material-icons md-36">play_circle</i>
            <span class="spanIconText">Lecteur</span>
        </a>
        <a href="?url=compte/index#form-add" class="btnBase theme">
            <i class="iconColor material-icons md-36">videocam</i>
            <span class="spanIconText">Enregistrer</span>
        </a>
    </div>

    <div class="video-grid">
        <?php if (!empty($videos)): ?>
            <?php foreach ($videos as $video): ?>
                <div class="video-item" data-video-id="<?php echo $video->getId(); ?>">
                    <div class="video-preview">
                        <video class="video-thumbnail" poster="<?php echo $video->getThumbnailPath(); ?>" preload="metadata">
                            <source src="<?php echo $video->getFullPath(); ?>" type="video/mp4">
                            Votre navigateur ne supporte pas la lecture vidéo.
                        </video>
                        <div class="video-duration"></div>
                    </div>
                    <div class="video-info">
                        <h4><?php echo htmlspecialchars($video->getTitle()); ?></h4>
                        <p><?php echo htmlspecialchars($video->getDescription()); ?></p>
                        <span class="video-date">Ajoutée le <?php echo date('d/m/Y', strtotime($video->getCreatedAt())); ?></span>
                    </div>
                    <div class="video-controls">
                        <button class="play-single btnBase vert" onclick="window.location='?url=video/player&id=<?php echo $video->getId(); ?>'">
                            <i class="material-icons">play_arrow</i>
                        </button>
                        <a href="?url=video/update/<?php echo $video->getId(); ?>#form-add" class="btnBase orange">
                            <i class="material-icons">edit</i>
                        </a>
                        <a href="?url=video/delete/<?php echo $video->getId(); ?>" class="btnBase rouge" 
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette vidéo ?');">
                            <i class="material-icons">delete</i>
                        </a>
                        <input type="checkbox" class="select-video" data-id="<?php echo $video->getId(); ?>">
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-content">Aucune vidéo n'a été ajoutée.</p>
        <?php endif; ?>
    </div>

    <form id="playlist-form" action="?url=video/player" method="GET" class="playlist-controls">
        <input type="hidden" name="url" value="video/player">
        <button type="submit" class="btnBase orange" id="play-selected" style="display: none;">
            <i class="material-icons">playlist_play</i> Lire la sélection
        </button>
    </form>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion de la recherche
    const searchInput = document.querySelector('.searchInput');
    const clearButton = document.querySelector('.clearButton');
    const videoItems = document.querySelectorAll('.video-item');

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        clearButton.style.display = searchTerm ? 'block' : 'none';

        videoItems.forEach(item => {
            const title = item.querySelector('h4').textContent.toLowerCase();
            const description = item.querySelector('p').textContent.toLowerCase();
            item.style.display = 
                title.includes(searchTerm) || description.includes(searchTerm) 
                ? 'block' 
                : 'none';
        });
    });

    // Gestion de la sélection des vidéos
    const checkboxes = document.querySelectorAll('.select-video');
    const playSelectedBtn = document.getElementById('play-selected');

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            const selectedCount = document.querySelectorAll('.select-video:checked').length;
            playSelectedBtn.style.display = selectedCount > 0 ? 'block' : 'none';

            const videoItem = checkbox.closest('.video-item');
            if (videoItem) {
                videoItem.classList.toggle('selected', checkbox.checked);
            }
        });
    });

    // Afficher la durée des vidéos
    const videoThumbnails = document.querySelectorAll('.video-thumbnail');
    videoThumbnails.forEach(video => {
        video.addEventListener('loadedmetadata', function() {
            const duration = Math.round(video.duration);
            const minutes = Math.floor(duration / 60);
            const seconds = duration % 60;
            const durationElement = this.parentElement.querySelector('.video-duration');
            if (durationElement) {
                durationElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            }
        });
    });
});
</script>
