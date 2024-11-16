'use strict';

// Fonction pour sauvegarder et jouer les pistes sélectionnées
async function saveAndPlaySelectedTracks() {
    console.log('Fonction saveAndPlaySelectedTracks appelée');
    
    const selectedCheckboxes = document.querySelectorAll('.select-audio:checked');
    console.log('Nombre de pistes sélectionnées:', selectedCheckboxes.length);
    
    if (selectedCheckboxes.length === 0) {
        showMessage('Veuillez sélectionner au moins une piste audio.', true);
        return;
    }

    const selectedTracks = Array.from(selectedCheckboxes).map(checkbox => {
        const audioItem = checkbox.closest('.audio-item');
        return checkbox.getAttribute('data-audio-id');
    });

    try {
        const csrfTokenInput = document.querySelector('input[name="csrf_token"]');
        if (!csrfTokenInput) {
            throw new Error('Token CSRF non trouvé');
        }
        const csrfToken = csrfTokenInput.value;
        console.log('Token CSRF trouvé:', csrfToken);
        console.log('Pistes sélectionnées:', selectedTracks);

        // Vérifier qu'il y a des pistes sélectionnées
        if (selectedTracks.length === 0) {
            throw new Error('Veuillez sélectionner au moins une piste audio.');
        }

        console.log('Envoi de la requête avec les pistes:', selectedTracks);
        
        const response = await fetch('index.php?url=audio/saveSelection', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ 
                tracks: selectedTracks,
                csrf_token: csrfToken 
            })
        });

        console.log('Réponse reçue:', response.status);
        const data = await response.json();
        console.log('Données reçues:', data);

        if (data.success) {
            console.log('Redirection vers le lecteur');
            window.location.href = '?url=audio/player';
        } else {
            console.error('Erreur serveur:', data.message);
            showMessage(data.message || 'Erreur lors de la sauvegarde de la sélection', true);
        }
    } catch (error) {
        console.error('Erreur complète:', error);
        showMessage('Erreur lors de la sauvegarde de la sélection: ' + error.message, true);
    }
}

// Fonction pour afficher les messages
function showMessage(message, isError = true) {
    // Supprimer les messages précédents de même type
    const className = isError ? 'error-message' : 'success-message';
    const existingMessages = document.querySelectorAll('.' + className);
    existingMessages.forEach(msg => msg.remove());
    
    const div = document.createElement('div');
    div.className = className;
    div.textContent = message;
    
    const container = document.querySelector('.audio-section');
    if (container) {
        container.insertBefore(div, container.firstChild);
        
        // Animation de disparition
        setTimeout(() => {
            div.style.opacity = '0';
            setTimeout(() => div.remove(), 1000);
        }, 5000);
    }
}

let player = null;
let elements = null;

function initializePlayerElements() {
    player = {
        audio: new Audio(),
        currentIndex: 0,
        tracks: [],
        isPlaying: false
    };

    elements = {
        nowPlaying: document.querySelector(".now-playing"),
        trackArt: document.querySelector(".track-art"),
        trackName: document.querySelector(".track-name"),
        trackArtist: document.querySelector(".track-artist"),
        playButton: document.querySelector(".playpause-track"),
        nextButton: document.querySelector(".next-track"),
        prevButton: document.querySelector(".prev-track"),
        seekSlider: document.querySelector(".seek_slider"),
        volumeSlider: document.querySelector(".volume_slider"),
        currentTime: document.querySelector(".current-time"),
        totalDuration: document.querySelector(".total-duration")
    };
}

// Initialiser le lecteur avec les pistes
function initializeAudioPlayer(tracks) {
    if (!tracks?.length) {
        console.warn('No tracks provided');
        return;
    }

    initializePlayerElements();

    console.log('Initializing player with tracks:', tracks);
    player.tracks = tracks;
    
    // Configuration de l'audio
    player.audio.addEventListener('ended', () => playNext());
    player.audio.addEventListener('timeupdate', updateProgress);
    player.audio.addEventListener('error', (e) => {
        console.error('Audio error:', e);
        showMessage('Erreur de lecture audio');
    });
    
    // Écouteurs d'événements pour les contrôles
    elements.playButton.addEventListener('click', togglePlay);
    elements.nextButton.addEventListener('click', playNext);
    elements.prevButton.addEventListener('click', playPrevious);
    elements.seekSlider.addEventListener('change', seekTo);
    elements.volumeSlider.addEventListener('change', updateVolume);
    
    // Charger la première piste
    loadTrack(0);
}

// Fonction pour sauvegarder et jouer les pistes sélectionnées
async function saveAndPlaySelectedTracks() {
    console.log('Fonction saveAndPlaySelectedTracks appelée');
    
    const selectedCheckboxes = document.querySelectorAll('.select-audio:checked');
    console.log('Nombre de pistes sélectionnées:', selectedCheckboxes.length);
    
    if (selectedCheckboxes.length === 0) {
        showMessage('Veuillez sélectionner au moins une piste audio.', true);
        return;
    }

    const selectedTracks = Array.from(selectedCheckboxes).map(checkbox => {
        const audioItem = checkbox.closest('.audio-item');
        const track = {
            id: checkbox.getAttribute('data-audio-id'),
            path: checkbox.getAttribute('data-audio-path'),
            title: audioItem.querySelector('h4').textContent.trim(),
            artist: audioItem.querySelector('p').textContent.trim(),
            image: audioItem.querySelector('.photoAudio').src
        };
        console.log('Piste sélectionnée:', track);
        return track;
    });

    try {
        const csrfTokenInput = document.querySelector('input[name="csrf_token"]');
        if (!csrfTokenInput) {
            throw new Error('Token CSRF non trouvé');
        }
        const csrfToken = csrfTokenInput.value;
        console.log('Token CSRF trouvé:', csrfToken);

        console.log('Envoi de la requête avec les pistes:', selectedTracks.map(track => track.id));
        
        const response = await fetch('index.php?url=audio/saveSelection', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ 
                tracks: selectedTracks.map(track => track.id),
                csrf_token: csrfToken 
            })
        });

        console.log('Réponse reçue:', response.status);
        const data = await response.json();
        console.log('Données reçues:', data);

        if (data.success) {
            console.log('Redirection vers le lecteur');
            window.location.href = '?url=audio/player';
        } else {
            console.error('Erreur serveur:', data.message);
            showMessage(data.message || 'Erreur lors de la sauvegarde de la sélection', true);
        }
    } catch (error) {
        console.error('Erreur complète:', error);
        showMessage('Erreur lors de la sauvegarde de la sélection: ' + error.message, true);
    }
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM chargé, initialisation...');
    
    const audioDataElement = document.getElementById('audioData');
    if (audioDataElement && audioDataElement.dataset.audios) {
        try {
            const tracks = JSON.parse(audioDataElement.dataset.audios);
            console.log('Données audio reçues:', tracks);
            
            if (Array.isArray(tracks) && tracks.length > 0) {
                initializeAudioPlayer(tracks);
            } else {
                console.log('Aucune piste disponible');
                showMessage("Aucune piste audio disponible", true);
            }
        } catch (error) {
            console.error('Erreur lors du chargement des pistes:', error);
            showMessage("Erreur lors du chargement des pistes audio", true);
        }
    }

    // Gestionnaire pour les checkboxes
    const checkboxes = document.querySelectorAll('.select-audio');
    console.log('Nombre de checkboxes trouvées:', checkboxes.length);
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', (e) => {
            console.log('Checkbox changée:', e.target.checked);
            const audioItem = e.target.closest('.audio-item');
            if (audioItem) {
                if (e.target.checked) {
                    audioItem.classList.add('selected');
                } else {
                    audioItem.classList.remove('selected');
                }
            }
        });

        checkbox.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    });

    // Gestionnaire pour le bouton de lecture
    const playSelectedButton = document.getElementById('play-selected');
    if (playSelectedButton) {
        console.log('Bouton de lecture trouvé');
        playSelectedButton.addEventListener('click', () => {
            console.log('Bouton de lecture cliqué');
            saveAndPlaySelectedTracks();
        });
    } else {
        console.error('Bouton de lecture non trouvé');
    }
});

// Charger une piste
function loadTrack(index) {
    const track = player.tracks[index];
    if (!track?.path) {
        showMessage('Piste invalide');
        return;
    }

    player.currentIndex = index;
    player.audio.src = track.path;
    player.audio.load();

    // Mettre à jour l'interface
    elements.trackName.textContent = track.title || 'Sans titre';
    elements.trackArtist.textContent = track.artist || 'Artiste inconnu';
    elements.trackArt.style.backgroundImage = track.image ? 
        `url('${track.image}')` : 
        "url('./Ressources/images/default-cover.png')";
    elements.nowPlaying.textContent = `Piste ${index + 1} sur ${player.tracks.length}`;
    
    // Réinitialiser les contrôles
    elements.seekSlider.value = 0;
    elements.currentTime.textContent = "00:00";
    elements.totalDuration.textContent = "00:00";
    
    // Lecture automatique
    playTrack();
}

// Contrôles de lecture
function togglePlay() {
    player.isPlaying ? pauseTrack() : playTrack();
}

function playTrack() {
    player.audio.play()
        .then(() => {
            player.isPlaying = true;
            elements.playButton.innerHTML = '<i class="material-icons md-48">pause_circle</i>';
        })
        .catch(error => {
            console.error('Erreur de lecture:', error);
            showMessage('Erreur de lecture: ' + error.message);
        });
}

function pauseTrack() {
    player.audio.pause();
    player.isPlaying = false;
    elements.playButton.innerHTML = '<i class="material-icons md-48">play_circle</i>';
}

// Navigation entre les pistes
function playNext() {
    const nextIndex = (player.currentIndex + 1) % player.tracks.length;
    loadTrack(nextIndex);
}

function playPrevious() {
    const prevIndex = player.currentIndex > 0 ? player.currentIndex - 1 : player.tracks.length - 1;
    loadTrack(prevIndex);
}

// Mise à jour de la progression
function updateProgress() {
    if (isNaN(player.audio.duration)) return;

    const seekPosition = player.audio.currentTime * (100 / player.audio.duration);
    elements.seekSlider.value = seekPosition;

    elements.currentTime.textContent = formatTime(player.audio.currentTime);
    elements.totalDuration.textContent = formatTime(player.audio.duration);
}

function formatTime(time) {
    const minutes = Math.floor(time / 60);
    const seconds = Math.floor(time % 60);
    return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
}

// Contrôles du volume et de la progression
function seekTo() {
    const time = player.audio.duration * (elements.seekSlider.value / 100);
    if (!isNaN(time)) {
        player.audio.currentTime = time;
    }
}

function updateVolume() {
    player.audio.volume = elements.volumeSlider.value / 100;
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
    const audioData = document.getElementById('audioData');
    if (audioData?.dataset.audios) {
        try {
            const tracks = JSON.parse(audioData.dataset.audios);
            if (tracks.length) {
                initializeAudioPlayer(tracks);
            } else {
                showMessage('Aucune piste disponible');
            }
        } catch (error) {
            showMessage('Erreur de chargement des pistes');
        }
    }
});
