'use strict';

document.addEventListener('DOMContentLoaded', function() {
    const viewer = document.getElementById('texte-viewer');
    const texteTitle = document.getElementById('texte-title');
    const texteContent = document.getElementById('texte-content');
    const texteDate = document.getElementById('texte-date');
    const texteItems = document.querySelectorAll('.texte-item');
    const prevButton = document.querySelector('.prev-texte');
    const nextButton = document.querySelector('.next-texte');
    
    let currentIndex = 0;
    
    function showTexte(index) {
        const texte = texteItems[index];
        texteTitle.textContent = texte.dataset.title;
        texteContent.innerHTML = texte.dataset.content.replace(/\n/g, '<br>');
        texteDate.textContent = texte.dataset.date;
        
        texteItems.forEach(item => item.classList.remove('active'));
        texte.classList.add('active');
    }
    
    if (texteItems.length > 0) {
        showPhoto(0);
        
        texteItems.forEach((item, index) => {
            item.addEventListener('click', () => {
                currentIndex = index;
                showTexte(currentIndex);
            });
        });
        
        prevButton.addEventListener('click', () => {
            currentIndex = (currentIndex - 1 + texteItems.length) % texteItems.length;
            showTexte(currentIndex);
        });
        
        nextButton.addEventListener('click', () => {
            currentIndex = (currentIndex + 1) % texteItems.length;
            showTexte(currentIndex);
        });
        
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                prevButton.click();
            } else if (e.key === 'ArrowRight') {
                nextButton.click();
            }
        });
    }
});
