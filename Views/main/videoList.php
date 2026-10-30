<?php

/**
 * @var \Session\SessionManager $session
 * @var \Models\Video[] $videos
 * @var \Models\Video|null $editVideo
 */
$session = $session ?? \Session\SessionManager::getInstance();
$videos = $videos ?? [];
$editVideo = $editVideo ?? null;

$session->ensureCsrfToken();
$csrfToken = $session->get('csrf_token');
$isEditing = ($editVideo !== null);
$formAction = $isEditing ? "?url=video/update/" . $editVideo->getId() : "?url=video/create";
?>

<section class="video-section">
    <div class="searchBox">
        <input type="text" id="searchVideoInput" class="searchInput" placeholder="Rechercher une vidéo...">
        <button type="button" class="searchButton">
            <i class="material-icons">search</i>
        </button>
    </div>

    <div class="boutonAligne">
        <button type="button" class="btnBase theme" id="btnToggleVideoUpload">
            <i class="iconColor material-icons md-36">video_call</i>
            <span class="spanIconText"><?php echo $isEditing ? 'Modifier la vidéo' : 'Importer Vidéo'; ?></span>
        </button>
        <button type="button" class="btnBase theme" id="btnToggleVideoRecorder">
            <i class="iconColor material-icons md-36">videocam</i>
            <span class="spanIconText">Enregistrer Webcam</span>
        </button>
        <a href="?url=video/player" class="btnBase theme">
            <i class="iconColor material-icons md-36">play_circle</i>
            <span class="spanIconText">Lecteur Global</span>
        </a>
    </div>

    <!-- PANNEAU 1 : Enregistreur Vidéo Webcam -->
    <div id="video-recorder-box" class="formContainer video-panel is-hidden">
        <h3><i class="material-icons">videocam</i> Enregistrer une note vidéo</h3>
        <form method="POST" action="?url=video/create" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="file" name="video_file" id="recordedVideoFile" class="is-hidden">

            <div class="video-field">
                <label for="webcam-title"><b>Titre de la note vidéo</b></label>
                <input type="text" id="webcam-title" name="title" class="inputModal" required maxlength="255"
                    placeholder="Ex: Démonstration, Mémo vidéo...">
            </div>

            <div class="video-field">
                <label for="webcam-desc"><b>Description (optionnelle)</b></label>
                <input type="text" id="webcam-desc" name="description" class="inputModal" maxlength="500"
                    placeholder="Notes complémentaires...">
            </div>

            <div class="webcam-stage">
                <video id="webcamPreview" autoplay muted playsinline class="webcam-video"></video>
                <div id="video-recording-timer" class="recording-timer">00:00</div>
                <div class="boutonAligne">
                    <button type="button" id="btnStartVideoRecord" class="btnBase rouge">
                        <i class="material-icons">fiber_manual_record</i>
                        <span class="spanIconText">Démarrer la capture</span>
                    </button>
                    <button type="button" id="btnStopVideoRecord" class="btnBase orange is-hidden" disabled>
                        <i class="material-icons">stop</i>
                        <span class="spanIconText">Arrêter</span>
                    </button>
                </div>
            </div>

            <div class="panel-actions">
                <button type="button" class="btnBase gris" id="btnCancelVideoRecorder">Fermer</button>
                <button type="submit" class="btnBase vert" id="btnSaveVideoNote" disabled>
                    <i class="material-icons">save</i>
                    <span class="spanIconText">Sauvegarder la vidéo</span>
                </button>
            </div>
        </form>
    </div>

    <!-- PANNEAU 2 : Import / Édition de fichier Vidéo -->
    <div id="video-upload-box" class="formContainer video-panel <?php echo $isEditing ? '' : 'is-hidden'; ?>">
        <h3><?php echo $isEditing ? 'Modifier la vidéo' : 'Importer un fichier vidéo'; ?></h3>
        <form method="POST" action="<?php echo $formAction; ?>" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

            <div class="video-field">
                <label for="video-title-input"><b>Titre</b></label>
                <input type="text" id="video-title-input" name="title" class="inputModal" required maxlength="255"
                    placeholder="Titre de la vidéo"
                    value="<?php echo $isEditing ? htmlspecialchars($editVideo->getTitle(), ENT_QUOTES, 'UTF-8') : ''; ?>">
            </div>

            <div class="video-field">
                <label for="video-desc-input"><b>Description</b></label>
                <textarea id="video-desc-input" name="description" rows="3" class="video-textarea"
                    placeholder="Description de la vidéo..."><?php echo $isEditing ? htmlspecialchars($editVideo->getDescription() ?? '', ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
            </div>

            <div class="video-field">
                <label for="video-thumb-input"><b>Miniature (optionnelle - JPG, PNG, WEBP)</b></label>
                <input type="file" id="video-thumb-input" name="thumbnail" class="inputModal" accept="image/jpeg,image/png,image/webp">
            </div>

            <?php if (!$isEditing): ?>
                <div class="video-field">
                    <label for="video-file-input"><b>Fichier Vidéo (MP4, WEBM, OGG, MOV - Max 100 Mo)</b></label>
                    <input type="file" id="video-file-input" name="video_file" class="inputModal" required
                        accept="video/mp4,video/webm,video/ogg,video/quicktime">
                </div>
            <?php endif; ?>

            <div class="panel-actions">
                <?php if ($isEditing): ?>
                    <a href="?url=video/list" class="btnBase gris">Annuler</a>
                <?php else: ?>
                    <button type="button" class="btnBase gris" id="btnCancelVideoUpload">Fermer</button>
                <?php endif; ?>
                <button type="submit" class="btnBase vert">
                    <i class="material-icons">save</i>
                    <span class="spanIconText">Enregistrer</span>
                </button>
            </div>
        </form>
    </div>

    <!-- GRILLE DES VIDÉOS -->
    <div class="video-grid" id="videoGrid">
        <?php if (!empty($videos)): ?>
            <?php foreach ($videos as $video): ?>
                <div class="video-item" data-video-id="<?php echo $video->getId(); ?>">
                    <div class="video-preview">
                        <video class="video-thumbnail" poster="<?php echo htmlspecialchars($video->getThumbnailPath(), ENT_QUOTES, 'UTF-8'); ?>" preload="metadata">
                            <source src="<?php echo htmlspecialchars($video->getFullPath(), ENT_QUOTES, 'UTF-8'); ?>">
                        </video>
                        <div class="video-duration"></div>
                        <input type="checkbox" class="select-video" data-id="<?php echo $video->getId(); ?>" title="Sélectionner">
                    </div>
                    <div class="video-info">
                        <h4><?php echo htmlspecialchars($video->getTitle(), ENT_QUOTES, 'UTF-8'); ?></h4>
                        <p><?php echo htmlspecialchars($video->getDescription() ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                        <span class="video-date">Ajoutée le <?php echo $video->getCreatedAt() ? date('d/m/Y', strtotime($video->getCreatedAt())) : 'Date inconnue'; ?></span>
                    </div>
                    <div class="video-controls">
                        <a href="?url=video/player&id=<?php echo $video->getId(); ?>" class="btnBase vert" title="Lire">
                            <i class="material-icons">play_arrow</i>
                        </a>
                        <a href="?url=video/update/<?php echo $video->getId(); ?>" class="btnBase orange" title="Modifier">
                            <i class="material-icons">edit</i>
                        </a>
                        <a href="?url=video/delete/<?php echo $video->getId(); ?>" class="btnBase rouge" title="Supprimer"
                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette vidéo ?');">
                            <i class="material-icons">delete</i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-content">Aucune vidéo n'a été ajoutée. Importez un fichier ou enregistrez une note webcam !</p>
        <?php endif; ?>
    </div>

    <form id="video-playlist-form" action="index.php" method="GET" class="playlist-controls">
        <input type="hidden" name="url" value="video/player">
        <div id="selected-videos-container"></div>
        <button type="submit" class="btnBase orange is-hidden" id="play-selected-videos">
            <i class="material-icons">playlist_play</i>
            <span class="spanIconText">Lire la sélection</span>
        </button>
    </form>
</section>