<section class="audio-section">


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

    <div class="audio-grid">
        <?php foreach ($audios as $audio): ?>
            <div class="audio-item" data-audio-id="<?php echo $audio->getId(); ?>">
                <img src="<?php echo $audio->getFullImagePath(); ?>" alt="Pochette" class="audio-cover" style="max-width: 150px; height: auto;">
                <div class="audio-info">
                    <h4><?php echo htmlspecialchars($audio->getTitle()); ?></h4>
                    <p><?php echo htmlspecialchars($audio->getArtist()); ?></p>
                </div>
                <div class="audio-controls">
                    <button class="play-single btnBase vert" onclick="window.location='?url=audio/player&id=<?php echo $audio->getId(); ?>'">
                        <i class="material-icons">play_arrow</i>
                    </button>
                    <a href="?url=audio/update/<?php echo $audio->getId(); ?>#form-add" class="btnBase orange">
                        <i class="material-icons">edit</i>
                    </a>
                    <a href="?url=audio/delete/<?php echo $audio->getId(); ?>" class="btnBase rouge" 
                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet audio ?');">
                        <i class="material-icons">delete</i>
                    </a>
                    <input type="checkbox" class="select-track" data-id="<?php echo $audio->getId(); ?>">
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <form id="playlist-form" action="?url=audio/player" method="GET" class="playlist-controls">
        <input type="hidden" name="url" value="audio/player">
        <button type="submit" class="btnBase orange" id="play-selected" style="display: none;">
            <i class="material-icons">playlist_play</i> Lire la sélection
        </button>
    </form>
</section>
