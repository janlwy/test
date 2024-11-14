'use strict';

let audioPlayer = null;
let currentTrack = null;
let trackList = [];
let playerElements = null;
let playerState = {
    isPlaying: false,
    track_index: 0,
    track_list: []
};

// Initialisation des éléments du lecteur
function initializePlayerElements() {
    return {
        now_playing: document.querySelector(".now-playing"),
        track_art: document.querySelector(".track-art"),
        track_name: document.querySelector(".track-name"),
        track_artist: document.querySelector(".track-artist"),
        playpause_btn: document.querySelector(".playpause-track"),
        next_btn: document.querySelector(".next-track"),
        prev_btn: document.querySelector(".prev-track"),
        seek_slider: document.querySelector(".seek_slider"),
        volume_slider: document.querySelector(".volume_slider"),
        curr_time: document.querySelector(".current-time"),
        total_duration: document.querySelector(".total-duration"),
        curr_track: document.createElement('audio')
    };
}

function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    
    // Supprimer les messages d'erreur précédents
    const existingErrors = document.querySelectorAll('.error-message');
    existingErrors.forEach(err => err.remove());
    
    // Ajouter le nouveau message
    const playerContainer = document.querySelector('.player-container');
    if (playerContainer) {
        playerContainer.insertBefore(errorDiv, playerContainer.firstChild);
        
        // Faire disparaître le message après 5 secondes
        setTimeout(() => {
            errorDiv.style.opacity = '0';
            setTimeout(() => errorDiv.remove(), 1000);
        }, 5000);
    }
}

function initializeAudioPlayer(tracks) {
    if (!tracks || tracks.length === 0) {
        showError('Aucune piste sélectionnée');
        return;
    }
    
    trackList = tracks;
    playerState.track_list = tracks;
    playerElements = initializePlayerElements();
    
    if (!playerElements.curr_track) {
        console.error('Élément audio non initialisé');
        showError('Erreur d\'initialisation du lecteur');
        return;
    }
    
    const playerContainer = document.querySelector('.player-container');
    if (playerContainer) {
        playerElements.curr_track.controls = false;
        loadTrack(0);
        
        // Ajouter les écouteurs d'événements
        playerElements.curr_track.addEventListener('ended', () => nextTrack());
        playerElements.curr_track.addEventListener('timeupdate', seekUpdate);
        
        console.log('Lecteur audio initialisé avec', tracks.length, 'pistes');
    } else {
        console.error('Container du lecteur non trouvé');
        showError('Erreur d\'initialisation du lecteur');
    }
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

// Fonction globale pour jouer les pistes sélectionnées

document.addEventListener('DOMContentLoaded', () => {
    const audioDataElement = document.getElementById('audioData');
    if (audioDataElement && audioDataElement.dataset.audios) {
        try {
            const tracks = JSON.parse(audioDataElement.dataset.audios);
            console.log('Données audio reçues:', tracks);
            
            if (Array.isArray(tracks) && tracks.length > 0) {
                initializeAudioPlayer(tracks);
            } else {
                console.log('Aucune piste disponible');
                showError("Aucune piste audio disponible");
            }
        } catch (error) {
            console.error('Erreur lors du chargement des pistes:', error);
            showError("Erreur lors du chargement des pistes audio");
        }
    } else {
        console.log('Element audioData non trouvé ou pas de données');
    }

    // Gestionnaire pour les checkboxes
    document.querySelectorAll('.select-audio').forEach(checkbox => {
        checkbox.addEventListener('change', (e) => {
            const audioItem = e.target.closest('.audio-item');
            if (audioItem) {
                if (e.target.checked) {
                    audioItem.classList.add('selected');
                } else {
                    audioItem.classList.remove('selected');
                }
            }
        });

        // Empêcher la propagation du clic sur la checkbox
        checkbox.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    });
});

function loadTrack(index) {
    if (!playerState.track_list || !playerState.track_list[index]) {
        console.error('Piste invalide ou track_list non initialisé');
        showError('Piste invalide');
        return;
    }

    try {
        resetValues();
        playerState.track_index = index;
        currentTrack = playerState.track_list[index];
        console.log('Chargement de la piste:', currentTrack);
        
        if (!currentTrack.path) {
            throw new Error('Chemin de la piste manquant');
        }

        playerElements.curr_track.src = currentTrack.path;
        playerElements.curr_track.load();
        
        // Mettre à jour l'interface
        if (playerElements) {
            playerElements.track_name.textContent = currentTrack.title || 'Sans titre';
            playerElements.track_artist.textContent = currentTrack.artist || 'Artiste inconnu';
            playerElements.track_art.style.backgroundImage = currentTrack.image ? 
                `url('${currentTrack.image}')` : 
                "url('./Ressources/images/default-cover.png')";
            playerElements.now_playing.textContent = `Piste ${index + 1} sur ${playerState.track_list.length}`;
        }

        // Mettre à jour le volume initial
        if (playerElements.volume_slider) {
            playerElements.curr_track.volume = playerElements.volume_slider.value / 100;
        }

        playTrack();
    } catch (error) {
        console.error('Erreur lors du chargement de la piste:', error);
        showError('Erreur lors du chargement de la piste');
    }
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
        showError('Erreur lors de la réinitialisation du lecteur');
    }
}


function playpauseTrack() {
    if (!playerState.isPlaying) {
        playTrack();
    } else {
        pauseTrack();
    }
}

function playTrack() {
    try {
        playerElements.curr_track.play();
        playerState.isPlaying = true;
        playerElements.playpause_btn.innerHTML = '<i class="material-icons md-48">pause_circle</i>';
    } catch (error) {
        console.error('Erreur lors de la lecture:', error);
        showError('Erreur lors de la lecture de la piste');
    }
}

function pauseTrack() {
    try {
        playerElements.curr_track.pause();
        playerState.isPlaying = false;
        playerElements.playpause_btn.innerHTML = '<i class="material-icons md-48">play_circle</i>';
    } catch (error) {
        console.error('Erreur lors de la pause:', error);
        showError('Erreur lors de la mise en pause');
    }
}

function nextTrack() {
    try {
        if (playerState.track_index < playerState.track_list.length - 1) {
            playerState.track_index += 1;
        } else {
            playerState.track_index = 0;
        }
        loadTrack(playerState.track_index);
        playTrack();
    } catch (error) {
        console.error('Erreur lors du passage à la piste suivante:', error);
        showError('Erreur lors du changement de piste');
    }
}

function prevTrack() {
    try {
        if (playerState.track_index > 0) {
            playerState.track_index -= 1;
        } else {
            playerState.track_index = playerState.track_list.length - 1;
        }
        loadTrack(playerState.track_index);
        playTrack();
    } catch (error) {
        console.error('Erreur lors du passage à la piste précédente:', error);
        showError('Erreur lors du changement de piste');
    }
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

function seekUpdate() {
    let seekPosition = 0;

    try {
        // Check if the current track duration is a legible number
        if (!isNaN(playerElements.curr_track.duration)) {
            seekPosition = playerElements.curr_track.currentTime * (100 / playerElements.curr_track.duration);
            playerElements.seek_slider.value = seekPosition;

            // Calculate the time left and the total duration
            let currentMinutes = Math.floor(playerElements.curr_track.currentTime / 60);
            let currentSeconds = Math.floor(playerElements.curr_track.currentTime - currentMinutes * 60);
            let durationMinutes = Math.floor(playerElements.curr_track.duration / 60);
            let durationSeconds = Math.floor(playerElements.curr_track.duration - durationMinutes * 60);

            // Add a zero to the single digit time values
            if (currentSeconds < 10) { currentSeconds = "0" + currentSeconds; }
            if (durationSeconds < 10) { durationSeconds = "0" + durationSeconds; }
            if (currentMinutes < 10) { currentMinutes = "0" + currentMinutes; }
            if (durationMinutes < 10) { durationMinutes = "0" + durationMinutes; }

            // Display the updated duration
            playerElements.curr_time.textContent = currentMinutes + ":" + currentSeconds;
            playerElements.total_duration.textContent = durationMinutes + ":" + durationSeconds;
            return true;
        }
        return false;
    } catch (error) {
        console.error('Erreur lors de la mise à jour du temps:', error);
        // Ne pas afficher d'erreur visuelle ici car cette fonction est appelée fréquemment
        return false;
    }
}
