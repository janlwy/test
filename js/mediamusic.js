'use strict';

// Initialisation du lecteur audio au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
    // Vérifier si nous sommes sur la page du lecteur audio
    const isPlayerPage = window.location.href.includes('audio/player');
    if (!isPlayerPage) {
        return; // Ne pas initialiser le lecteur sur les autres pages
    }

    setTimeout(() => {
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
    }, 100);
});


let player;

function initializeAudioPlayer(tracks) {
    if (!player) {
        player = new AudioPlayer();
    }
    player.initialize(tracks);
}

// Fonction pour sauvegarder et jouer les pistes sélectionnées
async function saveAndPlaySelectedTracks() {
    try {
        console.log('Fonction saveAndPlaySelectedTracks appelée');
        
        const selectedCheckboxes = document.querySelectorAll('.select-audio:checked');
        console.log('Nombre de pistes sélectionnées:', selectedCheckboxes.length);
        
        if (selectedCheckboxes.length === 0) {
            showMessage('Veuillez sélectionner au moins une piste audio.', true);
            return;
        }

        const selectedIds = Array.from(selectedCheckboxes).map(checkbox => {
            const id = parseInt(checkbox.getAttribute('data-audio-id'));
            if (!id || isNaN(id)) {
                throw new Error('ID de piste invalide détecté');
            }
            console.log('ID sélectionné:', id);
            return id;
        });

    try {
        const audioListData = document.getElementById('audioList-data');
        if (!audioListData) {
            throw new Error('Élément audioList-data non trouvé');
        }
        const csrfToken = audioListData.dataset.csrfToken;
        if (!csrfToken) {
            throw new Error('Token CSRF non trouvé dans les données audio');
        }
        console.log('Token CSRF trouvé:', csrfToken);

        const selectedIds = Array.from(selectedCheckboxes).map(checkbox => {
            const id = checkbox.getAttribute('data-audio-id');
            console.log('ID extrait de checkbox:', id);
            return parseInt(id) || null;
        }).filter(id => id !== null);

        if (selectedIds.length === 0) {
            throw new Error('IDs de pistes invalides');
        }

        console.log('Envoi de la requête avec les pistes:', selectedIds);
        
        const response = await fetch('?url=audio/saveSelection', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ 
                tracks: selectedIds,
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
            const errorMsg = data.message || 'Erreur lors de la sauvegarde de la sélection';
            console.error('Erreur serveur:', errorMsg);
            showMessage(errorMsg, true);
            throw new Error(errorMsg);
        }
    } catch (error) {
        console.error('Erreur complète:', error);
        showMessage('Erreur lors de la sauvegarde de la sélection: ' + error.message, true);
    }
}

function showMessage(message, isError = false) {
    const messageDiv = document.createElement('div');
    messageDiv.className = isError ? 'error-message' : 'success-message';
    messageDiv.textContent = message;
    document.querySelector('section').insertBefore(messageDiv, document.querySelector('section').firstChild);
    setTimeout(() => messageDiv.remove(), 5000);
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM chargé, initialisation...');

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
            e.stopPropagation();
            console.log('Bouton de lecture cliqué');
            
            const selectedCheckboxes = document.querySelectorAll('.select-audio:checked');
            console.log('Checkboxes sélectionnées:', selectedCheckboxes.length);
            
            if (selectedCheckboxes.length === 0) {
                showMessage('Veuillez sélectionner au moins une piste audio.', true);
                return;
            }
            
            try {
                const result = await saveAndPlaySelectedTracks();
                console.log('Résultat saveAndPlaySelectedTracks:', result);
                if (result && result.success) {
                    window.location.href = '?url=audio/player';
                }
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
    constructor() {
        this.audio = new Audio();
        this.currentIndex = 0;
        this.tracks = [];
        this.isPlaying = false;
        this.initializeElements();
        this.setupEventListeners();
    }

    initializeElements() {
        // Vérifier si nous sommes sur la page du lecteur audio
        if (!window.location.href.includes('audio/player')) {
            console.log('Page non-lecteur, initialisation annulée');
            return;
        }

        // Attendre que le DOM soit complètement chargé
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.initializeElements());
            return;
        }

        const playerContainer = document.querySelector('#player-container');
        if (!playerContainer) {
            console.error('Container du lecteur non trouvé');
            return;
        }

        // Créer les éléments s'ils n'existent pas
        if (!document.getElementById('now-playing')) {
            const nowPlaying = document.createElement('div');
            nowPlaying.id = 'now-playing';
            nowPlaying.className = 'now-playing';
            playerContainer.appendChild(nowPlaying);
        }

        if (!document.getElementById('track-art')) {
            const trackArt = document.createElement('div');
            trackArt.id = 'track-art';
            trackArt.className = 'track-art';
            playerContainer.appendChild(trackArt);
        }

        if (!document.getElementById('track-name')) {
            const trackName = document.createElement('div');
            trackName.id = 'track-name';
            trackName.className = 'track-name';
            playerContainer.appendChild(trackName);
        }

        if (!document.getElementById('track-artist')) {
            const trackArtist = document.createElement('div');
            trackArtist.id = 'track-artist';
            trackArtist.className = 'track-artist';
            playerContainer.appendChild(trackArtist);
        }

        // Initialiser les références aux éléments
        this.elements = {
            nowPlaying: document.getElementById("now-playing"),
            trackArt: document.getElementById("track-art"),
            trackName: document.getElementById("track-name"),
            trackArtist: document.getElementById("track-artist"),
            playButton: document.querySelector(".playpause-track"),
            nextButton: document.querySelector(".next-track"),
            prevButton: document.querySelector(".prev-track"),
            seekSlider: document.querySelector(".seek_slider"),
            volumeSlider: document.querySelector(".volume_slider"),
            currentTime: document.querySelector(".current-time"),
            totalDuration: document.querySelector(".total-duration")
        };

        // Vérifier que tous les éléments ont été créés
        Object.entries(this.elements).forEach(([key, element]) => {
            if (!element) {
                console.error(`Élément ${key} non trouvé ou non créé`);
            }
        });
    }

    setupEventListeners() {
        this.audio.addEventListener('ended', () => this.playNext());
        this.audio.addEventListener('timeupdate', () => this.updateProgress());
        this.audio.addEventListener('error', (e) => {
            console.error('Erreur audio:', e);
            showMessage('Erreur de lecture audio: ' + (e.message || 'Erreur inconnue'));
        });
        
        if (this.elements.playButton) {
            this.elements.playButton.addEventListener('click', () => this.togglePlay());
        }
        if (this.elements.nextButton) {
            this.elements.nextButton.addEventListener('click', () => this.playNext());
        }
        if (this.elements.prevButton) {
            this.elements.prevButton.addEventListener('click', () => this.playPrevious());
        }
        if (this.elements.seekSlider) {
            this.elements.seekSlider.addEventListener('change', () => this.seekTo());
        }
        if (this.elements.volumeSlider) {
            this.elements.volumeSlider.addEventListener('change', () => this.updateVolume());
        }
    }

    initialize(tracks) {
        if (!tracks?.length) {
            console.warn('Aucune piste à initialiser');
            return;
        }
        
        // Réinitialiser les éléments pour s'assurer qu'ils sont à jour
        this.initializeElements();
        
        // Vérification plus détaillée des éléments
        const elementCheck = {
            trackName: this.elements.trackName,
            trackArtist: this.elements.trackArtist,
            trackArt: this.elements.trackArt,
            nowPlaying: this.elements.nowPlaying
        };
        
        const missingElements = Object.entries(elementCheck)
            .filter(([key, element]) => !element)
            .map(([key]) => key);
        
        if (missingElements.length > 0) {
            console.error('Éléments manquants dans le DOM:', missingElements);
            console.log('État actuel des éléments:', elementCheck);
            return;
        }
        
        console.log('Initialisation avec les pistes:', tracks);
        this.tracks = tracks;
        
        try {
            this.loadTrack(0);
            
            const audioData = document.getElementById('audioData');
            if (audioData?.dataset.autoplay === 'true') {
                this.playTrack();
            }
        } catch (error) {
            console.error('Erreur lors de l\'initialisation:', error);
        }
    }

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
        // Vérifier que tous les éléments existent avant de les mettre à jour
        if (this.elements.trackName) {
            this.elements.trackName.textContent = track.title || 'Sans titre';
        }
        if (this.elements.trackArtist) {
            this.elements.trackArtist.textContent = track.artist || 'Artiste inconnu';
        }
        if (this.elements.trackArt) {
            this.elements.trackArt.style.backgroundImage = track.fullImage ? 
                `url('${track.fullImage}')` : 
                "url('./Ressources/images/default-cover.png')";
        }
        if (this.elements.nowPlaying) {
            this.elements.nowPlaying.textContent = `Piste ${index + 1} sur ${this.tracks.length}`;
        }
        
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
document.addEventListener('DOMContentLoaded', initializeAudioContent);

function initializeAudioContent() {
    const isPlayerPage = window.location.href.includes('audio/player');
    const audioListData = document.getElementById('audioList-data');
    const audioData = document.getElementById('audioData');
    
    try {
        if (isPlayerPage && audioData?.dataset.audios) {
            const tracks = JSON.parse(audioData.dataset.audios);
            if (!tracks?.length) {
                showMessage('Aucune piste disponible');
                return;
            }
            console.log('Initialisation du lecteur avec', tracks.length, 'pistes');
            initializeAudioPlayer(tracks);
        } else if (audioListData?.dataset.audios) {
            const tracks = JSON.parse(audioListData.dataset.audios);
            if (!tracks?.length) {
                showMessage('Aucune piste disponible');
            }
        }
    } catch (error) {
        console.error('Erreur de chargement des pistes:', error);
        showMessage('Erreur de chargement des pistes');
    }
}
