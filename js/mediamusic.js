'use strict';

// Initialisation du lecteur audio au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
    const audioData = document.getElementById('audioData');
    if (audioData && audioData.dataset.audios) {
        try {
            const tracks = JSON.parse(audioData.dataset.audios);
            if (Array.isArray(tracks) && tracks.length > 0) {
                console.log('Initializing audio player with tracks:', tracks);
                initializeAudioPlayer(tracks);
            } else {
                console.warn('No audio tracks available');
            }
        } catch (error) {
            console.error('Error parsing audio data:', error);
        }
    }
});


class AudioPlayer {
    constructor() {
        this.audio = new Audio();
        this.currentIndex = 0;
        this.tracks = [];
        this.isPlaying = false;
        this.initializeElements();
        this.setupEventListeners();
    }

    initializeElements() {
        this.elements = {
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

    setupEventListeners() {
        this.audio.addEventListener('ended', () => this.playNext());
        this.audio.addEventListener('timeupdate', () => this.updateProgress());
        this.audio.addEventListener('error', () => showMessage('Erreur de lecture audio'));
        
        this.elements.playButton.addEventListener('click', () => this.togglePlay());
        this.elements.nextButton.addEventListener('click', () => this.playNext());
        this.elements.prevButton.addEventListener('click', () => this.playPrevious());
        this.elements.seekSlider.addEventListener('change', () => this.seekTo());
        this.elements.volumeSlider.addEventListener('change', () => this.updateVolume());
    }

    initialize(tracks) {
        if (!tracks?.length) return;
        this.tracks = tracks;
        this.loadTrack(0);
        
        // Vérifier si l'autoplay est activé
        const audioData = document.getElementById('audioData');
        if (audioData && audioData.dataset.autoplay === 'true') {
            this.playTrack();
        }
    }
}

const player = new AudioPlayer();

function initializeAudioPlayer(tracks) {
    player.initialize(tracks);
}

// Fonction pour sauvegarder et jouer les pistes sélectionnées
async function saveAndPlaySelectedTracks() {
    console.log('Fonction saveAndPlaySelectedTracks appelée');
    
    const selectedCheckboxes = document.querySelectorAll('.select-audio:checked');
    console.log('Nombre de pistes sélectionnées:', selectedCheckboxes.length);
    
    if (selectedCheckboxes.length === 0) {
        throw new Error('Veuillez sélectionner au moins une piste audio.');
    }

    // Récupérer les données audio complètes depuis l'élément audioData
    const audioDataElement = document.getElementById('audioData');
    if (!audioDataElement || !audioDataElement.dataset.audios) {
        throw new Error('Données audio non disponibles');
    }

    const allAudios = JSON.parse(audioDataElement.dataset.audios);
    const selectedIds = Array.from(selectedCheckboxes).map(checkbox => checkbox.getAttribute('data-audio-id'));
    
    const selectedTracks = selectedIds.map(id => {
        const audioData = allAudios.find(audio => audio.id === id);
        if (!audioData) {
            throw new Error(`Données audio non trouvées pour l'ID: ${id}`);
        }
        console.log('Piste sélectionnée:', audioData);
        return audioData;
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
        playSelectedButton.addEventListener('click', async (e) => {
            e.preventDefault();
            console.log('Bouton de lecture cliqué');
            const selectedCheckboxes = document.querySelectorAll('.select-audio:checked');
            
            if (selectedCheckboxes.length === 0) {
                showMessage('Veuillez sélectionner au moins une piste audio.', true);
                return;
            }
            
            try {
                await saveAndPlaySelectedTracks();
            } catch (error) {
                console.error('Erreur lors de la lecture:', error);
                showMessage('Erreur lors de la lecture: ' + error.message, true);
            }
        });
    } else {
        console.error('Bouton de lecture non trouvé');
    }
});

class AudioPlayer {
    loadTrack(index) {
        const track = this.tracks[index];
        if (!track?.fullPath) {
            console.error('Piste invalide:', track);
            return;
        }

        this.currentIndex = index;
        this.audio.src = track.fullPath;
        this.audio.load();

        console.log('Chargement de la piste:', {
            index: index,
            track: track,
            src: this.audio.src
        });

        this.updateInterface(track, index);
        this.playTrack();
    }

    updateInterface(track, index) {
        this.elements.trackName.textContent = track.title || 'Sans titre';
        this.elements.trackArtist.textContent = track.artist || 'Artiste inconnu';
        this.elements.trackArt.style.backgroundImage = track.fullImage ? 
            `url('${track.fullImage}')` : 
            "url('./Ressources/images/default-cover.png')";
        this.elements.nowPlaying.textContent = `Piste ${index + 1} sur ${this.tracks.length}`;
        
        console.log('Mise à jour interface:', {
            title: track.title,
            artist: track.artist,
            image: track.fullImage,
            index: index
        });
        
        this.elements.seekSlider.value = 0;
        this.elements.currentTime.textContent = "00:00";
        this.elements.totalDuration.textContent = "00:00";
    }

    togglePlay() {
        this.isPlaying ? this.pauseTrack() : this.playTrack();
    }

    async playTrack() {
        try {
            console.log('Tentative de lecture de:', this.audio.src);
            
            // Vérifier si le fichier existe avant la lecture
            const response = await fetch(this.audio.src);
            if (!response.ok) {
                throw new Error(`Fichier non trouvé ou inaccessible (${response.status})`);
            }
            
            // Vérifier le type MIME de la réponse
            const contentType = response.headers.get('content-type');
            console.log('Type MIME détecté:', contentType);
            
            if (!contentType || !contentType.includes('audio/')) {
                throw new Error(`Type de fichier non supporté: ${contentType}`);
            }

            await this.audio.play();
            this.isPlaying = true;
            this.elements.playButton.innerHTML = '<i class="material-icons md-48">pause_circle</i>';
            
        } catch (error) {
            console.error('Erreur de lecture:', error);
            this.elements.playButton.innerHTML = '<i class="material-icons md-48">play_circle</i>';
            this.isPlaying = false;
            
            // Afficher l'erreur à l'utilisateur
            const errorMessage = document.createElement('div');
            errorMessage.className = 'error-message';
            errorMessage.textContent = `Erreur de lecture: ${error.message}`;
            this.elements.trackName.parentNode.insertBefore(errorMessage, this.elements.trackName.nextSibling);
            
            // Retirer le message d'erreur après 5 secondes
            setTimeout(() => errorMessage.remove(), 5000);
        }
    }

    pauseTrack() {
        this.audio.pause();
        this.isPlaying = false;
        this.elements.playButton.innerHTML = '<i class="material-icons md-48">play_circle</i>';
    }

    playNext() {
        this.loadTrack((this.currentIndex + 1) % this.tracks.length);
    }

    playPrevious() {
        this.loadTrack(this.currentIndex > 0 ? this.currentIndex - 1 : this.tracks.length - 1);
    }

    updateProgress() {
        if (isNaN(this.audio.duration)) return;

        const seekPosition = this.audio.currentTime * (100 / this.audio.duration);
        this.elements.seekSlider.value = seekPosition;
        this.elements.currentTime.textContent = this.formatTime(this.audio.currentTime);
        this.elements.totalDuration.textContent = this.formatTime(this.audio.duration);
    }

    formatTime(time) {
        const minutes = Math.floor(time / 60);
        const seconds = Math.floor(time % 60);
        return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }

    seekTo() {
        const time = this.audio.duration * (this.elements.seekSlider.value / 100);
        if (!isNaN(time)) {
            this.audio.currentTime = time;
        }
    }

    updateVolume() {
        this.audio.volume = this.elements.volumeSlider.value / 100;
    }
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
