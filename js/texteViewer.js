'use strict';

document.addEventListener('DOMContentLoaded', function () {
    // =========================================================
    // 1. LOGIQUE DE LA PAGE LISTE & ÉDITEUR (texteList.php)
    // =========================================================
    const toggleEditorBtns = document.querySelectorAll('.js-toggle-note-editor');
    const editorBox = document.getElementById('note-editor-box');
    const noteTitleInput = document.getElementById('note-title');

    if (toggleEditorBtns.length > 0 && editorBox) {
        toggleEditorBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                const isHidden = editorBox.classList.contains('is-hidden');
                if (isHidden) {
                    editorBox.classList.remove('is-hidden');
                    if (noteTitleInput) noteTitleInput.focus();
                } else {
                    editorBox.classList.add('is-hidden');
                }
            });
        });
    }

    // Recherche instantanée dans les notes
    const searchInput = document.getElementById('searchTexteInput');
    const gridItems = document.querySelectorAll('#texteGrid .texte-item');
    if (searchInput && gridItems.length > 0) {
        searchInput.addEventListener('input', function () {
            const term = this.value.toLowerCase();
            gridItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(term) ? '' : 'none';
            });
        });
    }

    // Sélection multiple pour le mode lecture
    const checkboxes = document.querySelectorAll('.select-texte');
    const viewSelectedBtn = document.getElementById('view-selected');
    const selectedInputsContainer = document.getElementById('selected-inputs-container');

    if (checkboxes.length > 0 && viewSelectedBtn && selectedInputsContainer) {
        checkboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                const parentCard = this.closest('.texte-item');
                if (parentCard) {
                    parentCard.classList.toggle('selected', this.checked);
                }

                selectedInputsContainer.innerHTML = '';
                const checked = document.querySelectorAll('.select-texte:checked');
                checked.forEach(c => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = c.dataset.id;
                    selectedInputsContainer.appendChild(input);
                });
                viewSelectedBtn.style.display = checked.length > 0 ? 'inline-flex' : 'none';
            });
        });
    }

    // =========================================================
    // 2. LOGIQUE DE LA LISEUSE (texte.php)
    // =========================================================
    const texteTitle = document.getElementById('texte-title');
    const texteContent = document.getElementById('texte-content');
    const texteDate = document.getElementById('texte-date');
    const texteCounter = document.getElementById('texte-counter');
    const texteEditLink = document.getElementById('texte-edit-link');
    const readerItems = document.querySelectorAll('.texte-list .texte-item');
    const prevButton = document.querySelector('.prev-texte');
    const nextButton = document.querySelector('.next-texte');

    let currentIndex = 0;

    function showTexte(index) {
        if (readerItems.length === 0) return;
        const texte = readerItems[index];
        if (!texte) return;

        if (texteTitle) texteTitle.textContent = texte.dataset.title || '';
        if (texteContent) texteContent.textContent = texte.dataset.content || '';
        if (texteDate) texteDate.textContent = texte.dataset.date || '';
        if (texteCounter) texteCounter.textContent = `${index + 1} / ${readerItems.length}`;
        if (texteEditLink) texteEditLink.href = `?url=texte/update/${texte.dataset.id}`;

        readerItems.forEach(item => item.classList.remove('active'));
        texte.classList.add('active');
    }

    if (readerItems.length > 0) {
        showTexte(0);

        readerItems.forEach((item, index) => {
            item.addEventListener('click', () => {
                currentIndex = index;
                showTexte(currentIndex);
            });
        });

        if (prevButton) {
            prevButton.addEventListener('click', () => {
                currentIndex = (currentIndex - 1 + readerItems.length) % readerItems.length;
                showTexte(currentIndex);
            });
        }

        if (nextButton) {
            nextButton.addEventListener('click', () => {
                currentIndex = (currentIndex + 1) % readerItems.length;
                showTexte(currentIndex);
            });
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft' && prevButton) {
                prevButton.click();
            } else if (e.key === 'ArrowRight' && nextButton) {
                nextButton.click();
            }
        });
    }
});