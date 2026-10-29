'use strict';

document.addEventListener('DOMContentLoaded', function () {
    // =========================================================
    // 1. PANNEAU D'AJOUT / ÉDITION & APERÇU IMAGE (photoList.php)
    // =========================================================
    const toggleBtns = document.querySelectorAll('.js-toggle-photo-editor');
    const editorBox = document.getElementById('photo-editor-box');
    const titleInput = document.getElementById('photo-title-input');
    const fileInput = document.getElementById('photo-file-input');
    const previewZone = document.getElementById('ocr-preview-zone');
    const previewImg = document.getElementById('photo-preview-img');

    if (toggleBtns.length > 0 && editorBox) {
        toggleBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                editorBox.classList.toggle('is-hidden');
                if (!editorBox.classList.contains('is-hidden') && titleInput) {
                    titleInput.focus();
                }
            });
        });
    }

    if (fileInput && previewZone && previewImg) {
        fileInput.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    previewZone.classList.remove('is-hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // =========================================================
    // 2. EXTRACTION OCR AVEC TESSERACT.JS (Français + Anglais)
    // =========================================================
    const btnRunOcr = document.getElementById('btnRunOcr');
    const ocrStatus = document.getElementById('ocr-status');
    const ocrProgress = document.getElementById('ocr-progress');
    const ocrTextarea = document.getElementById('photo-ocr-input');

    if (btnRunOcr && previewImg && ocrTextarea) {
        btnRunOcr.addEventListener('click', async function () {
            if (!previewImg.src || previewImg.src === window.location.href) {
                alert("Veuillez d'abord sélectionner une image.");
                return;
            }
            if (typeof Tesseract === 'undefined') {
                alert("La librairie OCR (Tesseract.js) n'a pas pu être chargée. Vérifiez votre connexion internet.");
                return;
            }

            try {
                btnRunOcr.disabled = true;
                ocrStatus.classList.remove('is-hidden');
                ocrProgress.classList.remove('is-hidden');
                ocrProgress.value = 0;
                ocrStatus.textContent = "Initialisation du moteur OCR...";

                const result = await Tesseract.recognize(
                    previewImg.src,
                    'fra+eng',
                    {
                        logger: m => {
                            if (m.status === 'recognizing text') {
                                const pct = Math.round((m.progress || 0) * 100);
                                ocrProgress.value = pct;
                                ocrStatus.textContent = `Lecture du texte en cours : ${pct}%`;
                            } else {
                                ocrStatus.textContent = `Étape : ${m.status}`;
                            }
                        }
                    }
                );

                const extracted = (result.data && result.data.text) ? result.data.text.trim() : '';
                if (extracted) {
                    ocrTextarea.value = extracted;
                    ocrStatus.textContent = "Texte extrait avec succès ! Vous pouvez le corriger ci-dessous.";
                } else {
                    ocrStatus.textContent = "Aucun texte lisible n'a été détecté sur cette image.";
                }
            } catch (err) {
                console.error("Erreur OCR :", err);
                ocrStatus.textContent = "Erreur lors de l'analyse OCR de l'image.";
            } finally {
                btnRunOcr.disabled = false;
                setTimeout(() => ocrProgress.classList.add('is-hidden'), 1500);
            }
        });
    }

    // =========================================================
    // 3. RECHERCHE INSTANTANÉE & SÉLECTION (photoList.php)
    // =========================================================
    const searchInput = document.getElementById('searchPhotoInput');
    const photoItems = document.querySelectorAll('#photoGrid .photo-item');
    if (searchInput && photoItems.length > 0) {
        searchInput.addEventListener('input', function () {
            const term = this.value.toLowerCase();
            photoItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(term) ? '' : 'none';
            });
        });
    }

    const checkboxes = document.querySelectorAll('.select-photo');
    const viewSelectedBtn = document.getElementById('view-selected');
    const selectedContainer = document.getElementById('selected-photos-container');

    if (checkboxes.length > 0 && viewSelectedBtn && selectedContainer) {
        checkboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                const card = this.closest('.photo-item');
                if (card) card.classList.toggle('selected', this.checked);

                selectedContainer.innerHTML = '';
                const checked = document.querySelectorAll('.select-photo:checked');
                checked.forEach(c => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = c.dataset.id;
                    selectedContainer.appendChild(input);
                });

                viewSelectedBtn.classList.toggle('is-hidden', checked.length === 0);
            });
        });
    }

    // =========================================================
    // 4. VISIONNEUSE PHOTO (photo.php)
    // =========================================================
    const currentPhoto = document.getElementById('current-photo');
    const photoTitle = document.getElementById('photo-title');
    const photoDescription = document.getElementById('photo-description');
    const photoOcrBox = document.getElementById('photo-ocr-box');
    const photoOcrText = document.getElementById('photo-ocr-text');
    const photoEditLink = document.getElementById('photo-edit-link');
    const photoConvertLink = document.getElementById('photo-convert-link');
    const thumbnails = document.querySelectorAll('.thumbnail');
    const prevButton = document.querySelector('.prev-photo');
    const nextButton = document.querySelector('.next-photo');

    let currentIndex = 0;

    function showPhoto(index) {
        if (thumbnails.length === 0 || !currentPhoto) return;
        const photo = thumbnails[index];
        if (!photo) return;

        currentPhoto.src = photo.src;
        currentPhoto.alt = photo.alt;
        if (photoTitle) photoTitle.textContent = photo.dataset.title || '';
        if (photoDescription) photoDescription.textContent = photo.dataset.description || '';

        const ocrContent = (photo.dataset.ocr || '').trim();
        if (photoOcrBox && photoOcrText) {
            if (ocrContent) {
                photoOcrText.textContent = ocrContent;
                photoOcrBox.classList.remove('is-hidden');
            } else {
                photoOcrText.textContent = '';
                photoOcrBox.classList.add('is-hidden');
            }
        }

        if (photoEditLink) {
            photoEditLink.href = `?url=photo/update/${photo.dataset.id}`;
        }
        if (photoConvertLink) {
            if (ocrContent || (photo.dataset.description || '').trim()) {
                photoConvertLink.href = `?url=photo/toTexte/${photo.dataset.id}`;
                photoConvertLink.classList.remove('is-hidden');
            } else {
                photoConvertLink.classList.add('is-hidden');
            }
        }

        thumbnails.forEach(thumb => thumb.classList.remove('active'));
        photo.classList.add('active');
    }

    if (thumbnails.length > 0 && currentPhoto) {
        showPhoto(0);

        thumbnails.forEach((thumb, index) => {
            thumb.addEventListener('click', () => {
                currentIndex = index;
                showPhoto(currentIndex);
            });
        });

        if (prevButton) {
            prevButton.addEventListener('click', () => {
                currentIndex = (currentIndex - 1 + thumbnails.length) % thumbnails.length;
                showPhoto(currentIndex);
            });
        }

        if (nextButton) {
            nextButton.addEventListener('click', () => {
                currentIndex = (currentIndex + 1) % thumbnails.length;
                showPhoto(currentIndex);
            });
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft' && prevButton) prevButton.click();
            if (e.key === 'ArrowRight' && nextButton) nextButton.click();
        });
    }
});