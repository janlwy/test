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

// État global du lecteur
const player = {
    audio: new Audio(),
    currentIndex: 0,
    tracks: [],
    isPlaying: false
};

// Éléments de l'interface
const elements = {
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

// Afficher un message
function showMessage(message, isError = true) {
    // Supprimer les messages précédents de même type
    const className = isError ? 'error-message' : 'success-message';
    const existingMessages = document.querySelectorAll('.' + className);
    existingMessages.forEach(msg => msg.remove());
    
    const div = document.createElement('div');
    div.className = className;
    div.textContent = message;
    
    const playerContainer = document.querySelector('.player-container');
    if (playerContainer) {
        playerContainer.insertBefore(div, playerContainer.firstChild);
        
        // Animation de disparition
        setTimeout(() => {
            div.style.opacity = '0';
            setTimeout(() => div.remove(), 1000);
        }, 5000);
    }
}

// Initialiser le lecteur avec les pistes
function initializeAudioPlayer(tracks) {
    if (!tracks?.length) {
        showMessage('Aucune piste sélectionnée');
        return;
    }

    player.tracks = tracks;
    player.audio.addEventListener('ended', () => playNext());
    player.audio.addEventListener('timeupdate', updateProgress);
    
    loadTrack(0);
    updateControls();
}

function setDefaultPlayerState() {
    const elements = playerElements;
    elements.now_playing.textContent = "Aucune piste sélectionnée";
    elements.track_name.textContent = "Sélectionnez des pistes audio";
    elements.track_artist.textContent = "depuis la liste";
    elements.track_art.style.backgroundImage = "url('./Ressources/images/default-cover.png')";
    elements.playpause_btn.innerHTML = '<i class="material-icons md-48">play_circle</i>';
    
    // Désactiver les contrôles
    [
        elements.playpause_btn,
        elements.next_btn,
        elements.prev_btn,
        elements.seek_slider,
        elements.volume_slider
    ].forEach(element => {
        if (element) element.disabled = true;
    });
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
    
    playTrack();
}

// Function to reset all values to their default
function resetValues() {
    try {
        if (!playerElements) {
            throw new Error('playerElements non initialisé');
        }
        
        const elements = ['curr_time', 'total_duration', 'seek_slider'];
        elements.forEach(element => {
            if (playerElements[element]) {
                if (element === 'seek_slider') {
                    playerElements[element].value = 0;
                } else {
                    playerElements[element].textContent = "00:00";
                }
            }
        });
    } catch (error) {
        console.error('Erreur lors de la réinitialisation des valeurs:', error);
        showMessage('Erreur lors de la réinitialisation du lecteur', true);
    }
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
        .catch(error => showMessage('Erreur de lecture'));
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

function seekTo() {
    // Calculate the seek position by the
    // percentage of the seek slider
    // and get the relative duration to the track
    let seekto = curr_track.duration * (seek_slider.value / 100);

    // Set the current track position to the calculated seek position
    curr_track.currentTime = seekto;
}

function setVolume() {
    // Set the volume according to the
    // percentage of the volume slider set
    curr_track.volume = volume_slider.value / 100;
}

// Mise à jour de la progression
function updateProgress() {
    if (isNaN(player.audio.duration)) return;

    const seekPosition = player.audio.currentTime * (100 / player.audio.duration);
    elements.seekSlider.value = seekPosition;

    function formatTime(time) {
        const minutes = Math.floor(time / 60);
        const seconds = Math.floor(time % 60);
        return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }

    elements.currentTime.textContent = formatTime(player.audio.currentTime);
    elements.totalDuration.textContent = formatTime(player.audio.duration);
}

// Contrôles du volume et de la progression
function updateVolume() {
    player.audio.volume = elements.volumeSlider.value / 100;
}

function seekTo() {
    const time = player.audio.duration * (elements.seekSlider.value / 100);
    player.audio.currentTime = time;
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
