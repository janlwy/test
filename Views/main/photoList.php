<?php
$session->ensureCsrfToken();
$csrfToken = $session->get('csrf_token');
$isEditing = isset($editPhoto) && $editPhoto !== null;
$formAction = $isEditing ? "?url=photo/update/" . $editPhoto->getId() : "?url=photo/create";
?>

<section class="photo-section">
    <div class="searchBox">
        <input type="text" id="searchPhotoInput" class="searchInput" placeholder="Rechercher par titre, description ou texte OCR...">
        <button type="button" class="searchButton">
            <i class="material-icons">search</i>
        </button>
    </div>

    <div class="boutonAligne">
        <button type="button" class="btnBase theme js-toggle-photo-editor">
            <i class="iconColor material-icons md-36">add_a_photo</i>
            <span class="spanIconText"><?php echo $isEditing ? 'Modifier la photo' : 'Ajouter une photo'; ?></span>
        </button>
        <a href="?url=photo/viewer" class="btnBase theme">
            <i class="iconColor material-icons md-36">photo_library</i>
            <span class="spanIconText">Visionneuse</span>
        </a>
    </div>

    <!-- Formulaire d'ajout / édition de photo avec OCR intégré -->
    <div id="photo-editor-box" class="formContainer photo-panel <?php echo $isEditing ? '' : 'is-hidden'; ?>">
        <h3><?php echo $isEditing ? 'Modifier la photo / note scannée' : 'Ajouter une photo ou scanner une note (OCR)'; ?></h3>
        <form method="POST" action="<?php echo $formAction; ?>" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

            <div class="photo-field">
                <label for="photo-title-input"><b>Titre</b></label>
                <input type="text" id="photo-title-input" name="title" class="inputModal" required maxlength="255"
                    placeholder="Ex: Tableau blanc réunion, Page de carnet, Facture..."
                    value="<?php echo $isEditing ? htmlspecialchars($editPhoto->getTitle(), ENT_QUOTES, 'UTF-8') : ''; ?>">
            </div>

            <div class="photo-field">
                <label for="photo-file-input"><b>Image (JPG, PNG, WEBP - Appareil photo ou Fichier)</b></label>
                <input type="file" id="photo-file-input" name="photo_file" class="inputModal"
                    <?php echo $isEditing ? '' : 'required'; ?>
                    accept="image/jpeg,image/png,image/webp" capture="environment">
            </div>

            <!-- Zone d'aperçu + Bouton OCR -->
            <div id="ocr-preview-zone" class="ocr-preview-zone <?php echo $isEditing ? '' : 'is-hidden'; ?>">
                <img id="photo-preview-img" class="photo-preview-img"
                    src="<?php echo $isEditing ? htmlspecialchars($editPhoto->getFullPath(), ENT_QUOTES, 'UTF-8') : ''; ?>"
                    alt="Aperçu">
                <div class="ocr-actions">
                    <button type="button" id="btnRunOcr" class="btnBase blue">
                        <i class="material-icons">document_scanner</i>
                        <span class="spanIconText">Extraire le texte de l'image (OCR)</span>
                    </button>
                    <div id="ocr-status" class="ocr-status is-hidden"></div>
                    <progress id="ocr-progress" class="ocr-progress is-hidden" value="0" max="100"></progress>
                </div>
            </div>

            <div class="photo-field">
                <label for="photo-description-input"><b>Description / Commentaire</b></label>
                <textarea id="photo-description-input" name="description" rows="3" class="photo-textarea"
                    placeholder="Description courte de la photo..."><?php echo $isEditing ? htmlspecialchars($editPhoto->getDescription() ?? '', ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
            </div>

            <div class="photo-field">
                <label for="photo-ocr-input"><b>Texte extrait de la photo (OCR)</b></label>
                <textarea id="photo-ocr-input" name="ocr_text" rows="6" class="photo-textarea"
                    placeholder="Cliquez sur 'Extraire le texte (OCR)' ci-dessus ou saisissez la transcription ici..."><?php echo $isEditing ? htmlspecialchars($editPhoto->getOcrText() ?? '', ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
            </div>

            <div class="panel-actions">
                <?php if ($isEditing): ?>
                    <a href="?url=photo/list" class="btnBase gris">Annuler</a>
                <?php else: ?>
                    <button type="button" class="btnBase gris js-toggle-photo-editor">Fermer</button>
                <?php endif; ?>
                <button type="submit" class="btnBase vert">
                    <i class="material-icons">save</i>
                    <span class="spanIconText">Enregistrer</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Grille des photos -->
    <div class="photo-grid" id="photoGrid">
        <?php if (!empty($photos)): ?>
            <?php foreach ($photos as $photo): ?>
                <div class="photo-item" data-photo-id="<?php echo $photo->getId(); ?>">
                    <div class="photo-media">
                        <img src="<?php echo htmlspecialchars($photo->getFullPath(), ENT_QUOTES, 'UTF-8'); ?>"
                            alt="<?php echo htmlspecialchars($photo->getTitle(), ENT_QUOTES, 'UTF-8'); ?>"
                            class="photo-thumbnail">
                        <input type="checkbox" class="select-photo" data-id="<?php echo $photo->getId(); ?>" title="Sélectionner pour la visionneuse">
                    </div>
                    <div class="photo-info">
                        <h4><?php echo htmlspecialchars($photo->getTitle(), ENT_QUOTES, 'UTF-8'); ?></h4>
                        <?php if (!empty($photo->getDescription())): ?>
                            <p><?php echo htmlspecialchars(mb_substr($photo->getDescription(), 0, 100), ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($photo->getOcrText())): ?>
                            <div class="ocr-badge" title="<?php echo htmlspecialchars($photo->getOcrText(), ENT_QUOTES, 'UTF-8'); ?>">
                                <i class="material-icons md-16">text_snippet</i>
                                <span>OCR : <?php echo htmlspecialchars(mb_substr($photo->getOcrText(), 0, 70), ENT_QUOTES, 'UTF-8'); ?>...</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="photo-controls">
                        <a href="?url=photo/viewer&id=<?php echo $photo->getId(); ?>" class="btnBase vert" title="Voir en grand">
                            <i class="material-icons">visibility</i>
                        </a>
                        <?php if (!empty($photo->getOcrText()) || !empty($photo->getDescription())): ?>
                            <a href="?url=photo/toTexte/<?php echo $photo->getId(); ?>" class="btnBase blue" title="Convertir en Note Texte">
                                <i class="material-icons">note_add</i>
                            </a>
                        <?php endif; ?>
                        <a href="?url=photo/update/<?php echo $photo->getId(); ?>" class="btnBase orange" title="Modifier / OCR">
                            <i class="material-icons">edit</i>
                        </a>
                        <a href="?url=photo/delete/<?php echo $photo->getId(); ?>" class="btnBase rouge" title="Supprimer"
                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette photo ?');">
                            <i class="material-icons">delete</i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-content">Aucune photo n'a été ajoutée. Cliquez sur "Ajouter une photo" pour commencer !</p>
        <?php endif; ?>
    </div>

    <form id="slideshow-form" action="index.php" method="GET" class="slideshow-controls">
        <input type="hidden" name="url" value="photo/viewer">
        <div id="selected-photos-container"></div>
        <button type="submit" class="btnBase orange is-hidden" id="view-selected">
            <i class="material-icons">slideshow</i>
            <span class="spanIconText">Voir la sélection</span>
        </button>
    </form>
</section>

<!-- Librairie Tesseract.js (OCR côté client) + Script Photo -->
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
<script src="js/photoViewer.js"></script>