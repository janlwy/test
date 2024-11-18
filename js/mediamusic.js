'use strict';

document.addEventListener('DOMContentLoaded', () => {
    const playlistForm = document.getElementById('playlist-form');
    const playSelectedBtn = document.getElementById('play-selected');
    
    // Gérer la sélection des pistes
    document.querySelectorAll('.select-track').forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            const selectedCount = document.querySelectorAll('.select-track:checked').length;
            playSelectedBtn.style.display = selectedCount > 0 ? 'block' : 'none';
            
            // Mettre à jour le formulaire
            if (selectedCount > 0) {
                const selectedIds = Array.from(document.querySelectorAll('.select-track:checked'))
                    .map(cb => cb.dataset.id);
                
                // Supprimer les anciens inputs
                playlistForm.querySelectorAll('input[name="ids[]"]').forEach(input => input.remove());
                
                // Ajouter les nouveaux inputs
                selectedIds.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    playlistForm.appendChild(input);
                });
            }
        });
    });

    setTimeout(() => {
        const playerData = document.getElementById('player-data');
        if (playerData && playerData.dataset.tracks) {
            try {
                const tracks = JSON.parse(playerData.dataset.tracks);
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
    player.initialize(tracks.map(track => ({
        ...track,
        fullPath: track.fullPath || track.path,
        fullImage: track.fullImage || track.image
    })));
}

// Fonction pour ajouter les champs cachés au formulaire
function addSelectedTracksToForm() {
    const form = document.getElementById('playlist-form');
    if (!form) {
        console.error('Formulaire de playlist non trouvé');
        return false;
    }

    const selectedCheckboxes = document.querySelectorAll('.select-track:checked');
    console.log('Checkboxes sélectionnées:', selectedCheckboxes.length);
    
    // Supprimer les anciens champs cachés s'il y en a
    const oldInputs = form.querySelectorAll('input[name="tracks[]"]');
    oldInputs.forEach(input => input.remove());
    
    // Ajouter les nouveaux champs cachés
    selectedCheckboxes.forEach(checkbox => {
        const audioId = checkbox.getAttribute('data-audio-id');
        console.log('Ajout de la piste:', audioId);
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'tracks[]';
        input.value = audioId;
        form.appendChild(input);
    });
    
    const hasSelectedTracks = selectedCheckboxes.length > 0;
    console.log('Nombre total de pistes sélectionnées:', selectedCheckboxes.length);
    return hasSelectedTracks;
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
    const checkboxes = document.querySelectorAll('.select-track');
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
            
            // Mettre à jour l'affichage du bouton de lecture
            const selectedCount = document.querySelectorAll('.select-track:checked').length;
            const playSelectedBtn = document.getElementById('play-selected');
            if (playSelectedBtn) {
                playSelectedBtn.style.display = selectedCount > 0 ? 'block' : 'none';
            }
        });

        checkbox.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    });

    // Gestionnaire pour le bouton de lecture
    const playSelectedButton = document.getElementById('play-selected');
    const form = document.getElementById('playlist-form');
    
    if (playSelectedButton && form) {
        console.log('Bouton de lecture et formulaire trouvés');
        form.addEventListener('submit', (e) => {
            e.preventDefault(); // Empêcher la soumission par défaut
            
            const hasSelectedTracks = addSelectedTracksToForm();
            console.log('Pistes sélectionnées:', hasSelectedTracks);
            
            if (!hasSelectedTracks) {
                showMessage('Veuillez sélectionner au moins une piste audio.', true);
                return;
            }
            
            // Debug des données du formulaire avant soumission
            const formData = new FormData(form);
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }
            
            // Soumettre le formulaire
            form.submit();
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
            playButton: document.querySelector(".playpause-track i"),
            nextButton: document.querySelector(".next-track i"),
            prevButton: document.querySelector(".prev-track i"),
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

        this.tracks = tracks;
        this.currentIndex = 0;
        this.isPlaying = false;

        // Initialiser le volume
        if (this.elements.volumeSlider) {
            this.audio.volume = this.elements.volumeSlider.value / 100;
        }

        // Charger la première piste
        this.loadTrack(0);
        
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
        try {
            const track = this.tracks[index];
            if (!track?.fullPath) {
                throw new Error('Piste invalide ou chemin manquant');
            }

            // Réinitialiser l'audio
            this.audio.pause();
            this.isPlaying = false;
            
            // Mettre à jour l'index et la source
            this.currentIndex = index;
            this.audio.src = track.fullPath;
            
            // Charger et préparer l'audio
            this.audio.load();
            this.updateInterface(track, index);

            // Lecture automatique si nécessaire
            const audioData = document.getElementById('audioData');
            if (audioData?.dataset.autoplay === 'true') {
                this.playTrack();
            }
        } catch (error) {
            console.error('Erreur de chargement:', error);
            showMessage('Erreur lors du chargement de la piste: ' + error.message, true);
        }

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
        if (!this.audio.src) {
            showMessage('Aucune piste sélectionnée', true);
            return;
        }

        try {
            await this.audio.play();
            this.isPlaying = true;
            this.elements.playButton.innerHTML = '<i class="material-icons md-48">pause_circle</i>';
            
            // Sauvegarder l'état de lecture
            const state = {
                currentTrack: this.currentIndex,
                isPlaying: true,
                volume: this.audio.volume,
                position: this.audio.currentTime
            };
            localStorage.setItem('audioPlayerState', JSON.stringify(state));
            
        } catch (error) {
            console.error('Erreur de lecture:', error);
            this.isPlaying = false;
            this.elements.playButton.innerHTML = '<i class="material-icons md-48">play_circle</i>';
            showMessage('Erreur de lecture: ' + error.message, true);
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
