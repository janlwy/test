'use strict';

'use strict';

// Éléments du lecteur audio
let playerElements = {
    now_playing: null,
    track_art: null,
    track_name: null,
    track_artist: null,
    playpause_btn: null,
    next_btn: null,
    prev_btn: null,
    seek_slider: null,
    volume_slider: null,
    curr_time: null,
    total_duration: null,
    curr_track: null
};

// État du lecteur
let playerState = {
    track_index: 0,
    isPlaying: false,
    updateTimer: null,
    track_list: []
};

// Initialisation des éléments du lecteur
function initializePlayerElements() {
    playerElements = {
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
    initializePlayerElements();
    
    if (tracks && tracks.length > 0) {
        playerState.track_list = tracks;
        loadTrack(0);
    } else {
        setDefaultPlayerState();
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
            if (Array.isArray(tracks) && tracks.length > 0) {
                track_list = tracks;
                loadTrack(0);
                console.log('Pistes chargées:', track_list);
            } else {
                console.log('Aucune piste disponible');
                showError("Aucune piste audio disponible");
            }
        } catch (error) {
            console.error('Erreur lors du chargement des pistes:', error);
            showError("Erreur lors du chargement des pistes audio");
        }
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
        console.error('Piste invalide ou index incorrect');
        showError('Piste invalide ou index incorrect');
        return;
    }

    try {
        clearInterval(playerState.updateTimer);
        resetValues();

        const track = playerState.track_list[index];
        if (!track.path) {
            console.error('Chemin de la piste manquant:', track);
            showError("Erreur: Fichier audio non trouvé");
            return;
        }

    // Vérifier l'existence du fichier avant de le charger
    const trackPath = track.path;
    fetch(trackPath, { 
        method: 'HEAD',
        cache: 'no-cache'
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Fichier non trouvé: ${trackPath}`);
            }
            return response;
        })
        .then(() => {
            console.log('Chargement de la piste:', trackPath);
            playerElements.curr_track.src = trackPath;
            
            // Ajouter des gestionnaires d'événements pour déboguer
            playerElements.curr_track.addEventListener('error', (e) => {
                console.error('Erreur de chargement audio:', e);
                console.error('Code erreur:', playerElements.curr_track.error.code);
                console.error('Message erreur:', playerElements.curr_track.error.message);
                showError("Erreur de chargement du fichier audio");
            });
    
    curr_track.addEventListener('loadeddata', () => {
        console.log('Piste audio chargée avec succès');
    });
    
    curr_track.load();

    // Update details of the track
    track_art.style.backgroundImage =
        "url(" + track_list[track_index].image + ")";
    track_name.textContent = track_list[track_index].title;
    track_artist.textContent = track_list[track_index].artist;
    now_playing.textContent =
        "Piste " + (track_index + 1) + " de " + track_list.length;

    // Set an interval of 1000 milliseconds
    // for updating the seek slider
    updateTimer = setInterval(seekUpdate, 1000);

    // Move to the next track if the current finishes playing
    curr_track.addEventListener("ended", nextTrack);
}

// Function to reset all values to their default
function resetValues() {
    try {
        if (playerElements.curr_time) {
            playerElements.curr_time.textContent = "00:00";
        }
        if (playerElements.total_duration) {
            playerElements.total_duration.textContent = "00:00";
        }
        if (playerElements.seek_slider) {
            playerElements.seek_slider.value = 0;
        }
    } catch (error) {
        console.error('Erreur lors de la réinitialisation des valeurs:', error);
        showError('Erreur lors de la réinitialisation du lecteur');
    }
}


function playpauseTrack() {
    // Switch between playing and pausing
    // depending on the current state
    if (!isPlaying) playTrack();
    else pauseTrack();
}

function playTrack() {
    // Play the loaded track
    curr_track.play();
    isPlaying = true;

    // Replace icon with the pause icon
    playpause_btn.innerHTML = '<i class="material-icons md-48">pause_circle</i>';
}

function pauseTrack() {
    // Pause the loaded track
    curr_track.pause();
    isPlaying = false;

    // Replace icon with the play icon
    playpause_btn.innerHTML = '<i class="material-icons md-48">play_circle</i>';
}

function nextTrack() {
    // Go back to the first track if the
    // current one is the last in the track list
    if (track_index < track_list.length - 1)
        track_index += 1;
    else track_index = 0;

    // Load and play the new track
    loadTrack(track_index);
    playTrack();
}

function prevTrack() {
    // Go back to the last track if the
    // current one is the first in the track list
    if (track_index > 0)
        track_index -= 1;
    else track_index = track_list.length - 1;

    // Load and play the new track
    loadTrack(track_index);
    playTrack();
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

    // Check if the current track duration is a legible number
    if (!isNaN(curr_track.duration)) {
        seekPosition = curr_track.currentTime * (100 / curr_track.duration);
        seek_slider.value = seekPosition;

        // Calculate the time left and the total duration
        let currentMinutes = Math.floor(curr_track.currentTime / 60);
        let currentSeconds = Math.floor(curr_track.currentTime - currentMinutes * 60);
        let durationMinutes = Math.floor(curr_track.duration / 60);
        let durationSeconds = Math.floor(curr_track.duration - durationMinutes * 60);

        // Add a zero to the single digit time values
        if (currentSeconds < 10) { currentSeconds = "0" + currentSeconds; }
        if (durationSeconds < 10) { durationSeconds = "0" + durationSeconds; }
        if (currentMinutes < 10) { currentMinutes = "0" + currentMinutes; }
        if (durationMinutes < 10) { durationMinutes = "0" + durationMinutes; }

        // Display the updated duration
        curr_time.textContent = currentMinutes + ":" + currentSeconds;
        total_duration.textContent = durationMinutes + ":" + durationSeconds;
    }
}
