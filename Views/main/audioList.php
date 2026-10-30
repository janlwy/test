<?php

/**
 * @var \Session\SessionManager $session
 * @var \Models\Audio[] $audios
 * @var \Models\Audio|null $editAudio
 */
$session = $session ?? \Session\SessionManager::getInstance();
$audios = $audios ?? [];
$editAudio = $editAudio ?? null;

$session->ensureCsrfToken();
$csrfToken = $session->get('csrf_token');
$isEditing = ($editAudio !== null);
$formAction = $isEditing ? "?url=audio/update/" . $editAudio->getId() : "?url=audio/create";
?>

<section class="audio-section">
    <div class="searchBox">
        <input type="text" id="searchAudioInput" class="searchInput" placeholder="Rechercher un titre, un artiste ou une note vocale...">
        <button type="button" class="searchButton">
            <i class="material-icons">search</i>
        </button>
    </div>

    <div class="boutonAligne">
        <button type="button" class="btnBase theme" id="btnToggleUpload">
            <i class="iconColor material-icons md-36">library_add</i>
            <span class="spanIconText"><?php echo $isEditing ? 'Modifier Audio' : 'Importer Audio'; ?></span>
        </button>
        <button type="button" class="btnBase theme" id="btnToggleRecorder">
            <i class="iconColor material-icons md-36">mic</i>
            <span class="spanIconText">Note Vocale</span>
        </button>
        <a href="?url=audio/player" class="btnBase theme">
            <i class="iconColor material-icons md-36">radio</i>
            <span class="spanIconText">Lecteur Global</span>
        </a>
    </div>

    <!-- PANNEAU 1 : Dictaphone Web (Enregistrement Note Vocale au micro) -->
    <div id="voice-recorder-box" class="formContainer audio-panel is-hidden">
        <h3><i class="material-icons">mic</i> Enregistrer une note vocale</h3>
        <form id="voiceRecorderForm" method="POST" action="?url=audio/create" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="artiste" value="Note vocale">
            <input type="file" name="path" id="recordedAudioFile" class="is-hidden">

            <div class="audio-field">
                <label for="voice-title"><b>Titre de la note vocale</b></label>
                <input type="text" id="voice-title" name="title" class="inputModal" required maxlength="100"
                    placeholder="Ex: Mémo réunion, Idée chanson, Rappel...">
            </div>

            <div class="recorder-controls">
                <div id="recording-timer" class="recording-timer">00:00</div>
                <div id="recording-status" class="recording-status">Prêt à enregistrer</div>

                <div class="boutonAligne">
                    <button type="button" id="btnStartRecord" class="btnBase rouge">
                        <i class="material-icons">fiber_manual_record</i>
                        <span class="spanIconText">Démarrer</span>
                    </button>
                    <button type="button" id="btnStopRecord" class="btnBase orange is-hidden" disabled>
                        <i class="material-icons">stop</i>
                        <span class="spanIconText">Arrêter</span>
                    </button>
                </div>

                <audio id="recordedAudioPreview" controls class="audio-preview is-hidden"></audio>
            </div>

            <div class="panel-actions">
                <button type="button" class="btnBase gris" id="btnCancelRecorder">Fermer</button>
                <button type="submit" class="btnBase vert" id="btnSaveVoiceNote" disabled>
                    <i class="material-icons">save</i>
                    <span class="spanIconText">Sauvegarder la note vocale</span>
                </button>
            </div>
        </form>
    </div>

    <!-- PANNEAU 2 : Import / Modification de fichier Audio ou Musique -->
    <div id="audio-upload-box" class="formContainer audio-panel <?php echo $isEditing ? '' : 'is-hidden'; ?>">
        <h3><?php echo $isEditing ? 'Modifier la piste audio' : 'Importer un fichier audio / musique'; ?></h3>
        <form method="POST" action="<?php echo $formAction; ?>" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

            <div class="audio-field">
                <label for="audio-title"><b>Titre</b></label>
                <input type="text" id="audio-title" name="title" class="inputModal" required maxlength="100"
                    placeholder="Titre du morceau ou de l'enregistrement"
                    value="<?php echo $isEditing ? htmlspecialchars($editAudio->getTitle(), ENT_QUOTES, 'UTF-8') : ''; ?>">
            </div>

            <div class="audio-field">
                <label for="audio-artist"><b>Artiste / Catégorie</b></label>
                <input type="text" id="audio-artist" name="artiste" class="inputModal" maxlength="100"
                    placeholder="Nom de l'artiste ou 'Note vocale'"
                    value="<?php echo $isEditing ? htmlspecialchars($editAudio->getArtist(), ENT_QUOTES, 'UTF-8') : ''; ?>">
            </div>

            <div class="audio-field">
                <label for="audio-image"><b>Pochette (optionnelle)</b></label>
                <input type="file" id="audio-image" name="image" class="inputModal" accept="image/jpeg,image/png,image/webp">
            </div>

            <?php if (!$isEditing): ?>
                <div class="audio-field">
                    <label for="audio-file"><b>Fichier Audio (MP3, M4A, WAV, OGG, WEBM)</b></label>
                    <input type="file" id="audio-file" name="path" class="inputModal" required
                        accept="audio/mpeg,audio/mp4,audio/wav,audio/x-m4a,audio/aac,audio/ogg,audio/webm,video/mp4">
                </div>
            <?php endif; ?>

            <div class="panel-actions">
                <?php if ($isEditing): ?>
                    <a href="?url=audio/list" class="btnBase gris">Annuler</a>
                <?php else: ?>
                    <button type="button" class="btnBase gris" id="btnCancelUpload">Fermer</button>
                <?php endif; ?>
                <button type="submit" class="btnBase vert">
                    <i class="material-icons">save</i>
                    <span class="spanIconText">Enregistrer</span>
                </button>
            </div>
        </form>
    </div>

    <!-- GRILLE DES AUDIOS -->
    <div class="audio-grid" id="audioGrid">
        <?php if (!empty($audios)): ?>
            <?php foreach ($audios as $audio): ?>
                <div class="audio-item" data-audio-id="<?php echo $audio->getId(); ?>">
                    <div class="audio-media">
                        <img src="<?php echo htmlspecialchars($audio->getFullImagePath(), ENT_QUOTES, 'UTF-8'); ?>" alt="Pochette" class="audio-cover">
                        <input type="checkbox" class="select-track" data-id="<?php echo $audio->getId(); ?>" title="Ajouter à la lecture groupée">
                    </div>
                    <div class="audio-info">
                        <h4><?php echo htmlspecialchars($audio->getTitle(), ENT_QUOTES, 'UTF-8'); ?></h4>
                        <p><?php echo htmlspecialchars($audio->getArtist(), ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <div class="audio-controls">
                        <a href="?url=audio/player&id=<?php echo $audio->getId(); ?>" class="btnBase vert" title="Lire">
                            <i class="material-icons">play_arrow</i>
                        </a>
                        <a href="?url=audio/update/<?php echo $audio->getId(); ?>" class="btnBase orange" title="Modifier">
                            <i class="material-icons">edit</i>
                        </a>
                        <a href="?url=audio/delete/<?php echo $audio->getId(); ?>" class="btnBase rouge" title="Supprimer"
                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet audio ?');">
                            <i class="material-icons">delete</i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-content">Aucun enregistrement audio pour le moment. Importez un fichier ou enregistrez une note vocale !</p>
        <?php endif; ?>
    </div>

    <form id="playlist-form" action="index.php" method="GET" class="playlist-controls">
        <input type="hidden" name="url" value="audio/player">
        <div id="selected-tracks-container"></div>
        <button type="submit" class="btnBase orange is-hidden" id="play-selected">
            <i class="material-icons">playlist_play</i>
            <span class="spanIconText">Lire la sélection</span>
        </button>
    </form>
</section>