'use strict';

document.addEventListener('DOMContentLoaded', function() {
    const viewer = document.getElementById('photo-viewer');
    const currentPhoto = document.getElementById('current-photo');
    const photoTitle = document.getElementById('photo-title');
    const photoDescription = document.getElementById('photo-description');
    const thumbnails = document.querySelectorAll('.thumbnail');
    const prevButton = document.querySelector('.prev-photo');
    const nextButton = document.querySelector('.next-photo');
    
    let currentIndex = 0;
    
    function showPhoto(index) {
        const photo = thumbnails[index];
        currentPhoto.src = photo.src;
        currentPhoto.alt = photo.alt;
        photoTitle.textContent = photo.dataset.title;
        photoDescription.textContent = photo.dataset.description;
        
        thumbnails.forEach(thumb => thumb.classList.remove('active'));
        photo.classList.add('active');
    }
    
    if (thumbnails.length > 0) {
        showPhoto(0);
        
        thumbnails.forEach((thumb, index) => {
            thumb.addEventListener('click', () => {
                currentIndex = index;
                showPhoto(currentIndex);
            });
        });
        
        prevButton.addEventListener('click', () => {
            currentIndex = (currentIndex - 1 + thumbnails.length) % thumbnails.length;
            showPhoto(currentIndex);
        });
        
        nextButton.addEventListener('click', () => {
            currentIndex = (currentIndex + 1) % thumbnails.length;
            showPhoto(currentIndex);
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
