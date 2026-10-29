'use strict';

document.addEventListener('DOMContentLoaded', () => {
    // =========================================================
    // 1. GESTION DES PANNEAUX (Import Fichier & Dictaphone)
    // =========================================================
    const uploadBox = document.getElementById('audio-upload-box');
    const recorderBox = document.getElementById('voice-recorder-box');
    const btnToggleUpload = document.getElementById('btnToggleUpload');
    const btnToggleRecorder = document.getElementById('btnToggleRecorder');
    const btnCancelUpload = document.getElementById('btnCancelUpload');
    const btnCancelRecorder = document.getElementById('btnCancelRecorder');

    function openUploadPanel() {
        if (recorderBox) recorderBox.classList.add('is-hidden');
        if (uploadBox) {
            uploadBox.classList.toggle('is-hidden');
            const titleInput = document.getElementById('audio-title');
            if (!uploadBox.classList.contains('is-hidden') && titleInput) {
                titleInput.focus();
            }
        }
    }

    function openRecorderPanel() {
        if (uploadBox) uploadBox.classList.add('is-hidden');
        if (recorderBox) {
            recorderBox.classList.toggle('is-hidden');
            const voiceTitle = document.getElementById('voice-title');
            if (!recorderBox.classList.contains('is-hidden') && voiceTitle) {
                if (!voiceTitle.value) {
                    const now = new Date();
                    voiceTitle.value = `Note vocale du ${now.toLocaleDateString('fr-FR')} à ${now.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })}`;
                }
                voiceTitle.focus();
            }
        }
    }

    if (btnToggleUpload) btnToggleUpload.addEventListener('click', openUploadPanel);
    if (btnToggleRecorder) btnToggleRecorder.addEventListener('click', openRecorderPanel);
    if (btnCancelUpload && uploadBox) {
        btnCancelUpload.addEventListener('click', () => uploadBox.classList.add('is-hidden'));
    }
    if (btnCancelRecorder && recorderBox) {
        btnCancelRecorder.addEventListener('click', () => recorderBox.classList.add('is-hidden'));
    }

    // Ouverture automatique via le hash URL (#record ou #upload depuis le lecteur)
    if (window.location.hash === '#record') openRecorderPanel();
    if (window.location.hash === '#upload') openUploadPanel();

    // =========================================================
    // 2. DICTAPHONE WEB (MediaRecorder API)
    // =========================================================
    const btnStartRecord = document.getElementById('btnStartRecord');
    const btnStopRecord = document.getElementById('btnStopRecord');
    const btnSaveVoiceNote = document.getElementById('btnSaveVoiceNote');
    const recordingTimer = document.getElementById('recording-timer');
    const recordingStatus = document.getElementById('recording-status');
    const recordedAudioPreview = document.getElementById('recordedAudioPreview');
    const recordedAudioFile = document.getElementById('recordedAudioFile');

    let mediaRecorder = null;
    let audioChunks = [];
    let timerInterval = null;
    let secondsElapsed = 0;

    function formatTimer(sec) {
        const m = Math.floor(sec / 60).toString().padStart(2, '0');
        const s = (sec % 60).toString().padStart(2, '0');
        return `${m}:${s}`;
    }

    if (btnStartRecord && btnStopRecord) {
        btnStartRecord.addEventListener('click', async () => {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                alert("Votre navigateur ne supporte pas l'enregistrement vocal par microphone.");
                return;
            }

            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                const mimeType = MediaRecorder.isTypeSupported('audio/webm')
                    ? 'audio/webm'
                    : (MediaRecorder.isTypeSupported('audio/ogg') ? 'audio/ogg' : '');

                mediaRecorder = mimeType ? new MediaRecorder(stream, { mimeType }) : new MediaRecorder(stream);
                audioChunks = [];

                mediaRecorder.addEventListener('dataavailable', event => {
                    if (event.data && event.data.size > 0) {
                        audioChunks.push(event.data);
                    }
                });

                mediaRecorder.addEventListener('stop', () => {
                    stream.getTracks().forEach(track => track.stop());
                    clearInterval(timerInterval);
                    recordingTimer.classList.remove('is-recording');

                    const finalMime = mediaRecorder.mimeType || 'audio/webm';
                    const ext = finalMime.includes('ogg') ? 'ogg' : 'webm';
                    const audioBlob = new Blob(audioChunks, { type: finalMime });
                    const audioUrl = URL.createObjectURL(audioBlob);

                    if (recordedAudioPreview) {
                        recordedAudioPreview.src = audioUrl;
                        recordedAudioPreview.classList.remove('is-hidden');
                    }

                    // Injecter le Blob dans le champ <input type="file"> pour l'envoi standard POST
                    const file = new File([audioBlob], `note_vocale_${Date.now()}.${ext}`, { type: finalMime });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    recordedAudioFile.files = dataTransfer.files;

                    if (btnSaveVoiceNote) btnSaveVoiceNote.disabled = false;
                    if (recordingStatus) recordingStatus.textContent = "Enregistrement terminé. Vous pouvez l'écouter ou le sauvegarder.";
                });

                mediaRecorder.start();
                secondsElapsed = 0;
                recordingTimer.textContent = "00:00";
                recordingTimer.classList.add('is-recording');
                recordingStatus.textContent = "Enregistrement en cours... Parlez dans votre micro.";

                timerInterval = setInterval(() => {
                    secondsElapsed++;
                    recordingTimer.textContent = formatTimer(secondsElapsed);
                }, 1000);

                btnStartRecord.classList.add('is-hidden');
                btnStopRecord.classList.remove('is-hidden');
                btnStopRecord.disabled = false;
                if (btnSaveVoiceNote) btnSaveVoiceNote.disabled = true;
                if (recordedAudioPreview) recordedAudioPreview.classList.add('is-hidden');
            } catch (err) {
                console.error("Erreur d'accès au microphone :", err);
                alert("Impossible d'accéder au microphone. Vérifiez les permissions de votre navigateur.");
            }
        });

        btnStopRecord.addEventListener('click', () => {
            if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                mediaRecorder.stop();
                btnStopRecord.classList.add('is-hidden');
                btnStopRecord.disabled = true;
                btnStartRecord.classList.remove('is-hidden');
            }
        });
    }

    // =========================================================
    // 3. RECHERCHE INSTANTANÉE & SÉLECTION DE PLAYLIST
    // =========================================================
    const searchInput = document.getElementById('searchAudioInput');
    const audioItems = document.querySelectorAll('#audioGrid .audio-item');
    if (searchInput && audioItems.length > 0) {
        searchInput.addEventListener('input', function () {
            const term = this.value.toLowerCase();
            audioItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(term) ? '' : 'none';
            });
        });
    }

    const checkboxes = document.querySelectorAll('.select-track');
    const playSelectedBtn = document.getElementById('play-selected');
    const selectedContainer = document.getElementById('selected-tracks-container');

    if (checkboxes.length > 0 && playSelectedBtn && selectedContainer) {
        checkboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                const card = this.closest('.audio-item');
                if (card) card.classList.toggle('selected', this.checked);

                selectedContainer.innerHTML = '';
                const checked = document.querySelectorAll('.select-track:checked');
                checked.forEach(c => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = c.dataset.id;
                    selectedContainer.appendChild(input);
                });

                playSelectedBtn.classList.toggle('is-hidden', checked.length === 0);
            });
        });
    }

    // =========================================================
    // 4. INITIALISATION DU LECTEUR AUDIO (audio.php)
    // =========================================================
    const playerData = document.getElementById('player-data');
    if (playerData && playerData.dataset.tracks) {
        try {
            const tracks = JSON.parse(playerData.dataset.tracks);
            if (Array.isArray(tracks) && tracks.length > 0) {
                const player = new AudioPlayer();
                player.initialize(tracks);
            }
        } catch (error) {
            console.error('Erreur lors du décodage des pistes audio :', error);
        }
    }
});

class AudioPlayer {
    constructor() {
        this.audio = new Audio();
        this.currentIndex = 0;
        this.tracks = [];
        this.isPlaying = false;

        this.elements = {
            nowPlaying: document.getElementById("now-playing"),
            trackArt: document.getElementById("track-art"),
            trackName: document.getElementById("track-name"),
            trackArtist: document.getElementById("track-artist"),
            playButtonIcon: document.querySelector(".playpause-track i"),
            playButtonWrapper: document.querySelector(".playpause-track"),
            nextButton: document.querySelector(".next-track"),
            prevButton: document.querySelector(".prev-track"),
            seekSlider: document.querySelector(".seek_slider"),
            volumeSlider: document.querySelector(".volume_slider"),
            currentTime: document.querySelector(".current-time"),
            totalDuration: document.querySelector(".total-duration")
        };

        this.setupEventListeners();
    }

    setupEventListeners() {
        // À la fin d'une piste, on arrête la lecture sans enchaîner automatiquement
        this.audio.addEventListener('ended', () => {
            this.isPlaying = false;
            this.audio.currentTime = 0;
            if (this.elements.seekSlider) this.elements.seekSlider.value = 0;
            if (this.elements.currentTime) this.elements.currentTime.textContent = "00:00";
            if (this.elements.playButtonIcon) {
                this.elements.playButtonIcon.textContent = 'play_circle';
            }
        });
        this.audio.addEventListener('timeupdate', () => this.updateProgress());
        this.audio.addEventListener('loadedmetadata', () => this.updateProgress());

        if (this.elements.playButtonWrapper) {
            this.elements.playButtonWrapper.addEventListener('click', () => this.togglePlay());
        }
        if (this.elements.nextButton) {
            this.elements.nextButton.addEventListener('click', () => this.playNext());
        }
        if (this.elements.prevButton) {
            this.elements.prevButton.addEventListener('click', () => this.playPrevious());
        }
        if (this.elements.seekSlider) {
            this.elements.seekSlider.addEventListener('input', () => this.seekTo());
        }
        if (this.elements.volumeSlider) {
            this.elements.volumeSlider.addEventListener('input', () => this.updateVolume());
        }
    }

    initialize(tracks) {
        this.tracks = tracks;
        if (this.elements.volumeSlider) {
            this.audio.volume = this.elements.volumeSlider.value / 100;
        }
        this.loadTrack(0, false);
    }

    loadTrack(index, autoPlay = false) {
        const track = this.tracks[index];
        if (!track) return;

        this.audio.pause();
        this.isPlaying = false;
        this.currentIndex = index;
        this.audio.src = track.fullPath;
        this.audio.load();

        this.updateInterface(track, index);
        if (autoPlay) {
            this.playTrack();
        }
    }

    updateInterface(track, index) {
        if (this.elements.trackName) this.elements.trackName.textContent = track.title || 'Sans titre';
        if (this.elements.trackArtist) this.elements.trackArtist.textContent = track.artist || 'Note vocale';
        if (this.elements.trackArt) {
            this.elements.trackArt.style.backgroundImage = `url("${track.fullImage}")`;
        }
        if (this.elements.nowPlaying) {
            this.elements.nowPlaying.textContent = `Piste ${index + 1} sur ${this.tracks.length}`;
        }
        if (this.elements.seekSlider) this.elements.seekSlider.value = 0;
        if (this.elements.currentTime) this.elements.currentTime.textContent = "00:00";
        if (this.elements.totalDuration) this.elements.totalDuration.textContent = "00:00";
        if (this.elements.playButtonIcon) this.elements.playButtonIcon.textContent = 'play_circle';
    }

    togglePlay() {
        this.isPlaying ? this.pauseTrack() : this.playTrack();
    }

    async playTrack() {
        try {
            await this.audio.play();
            this.isPlaying = true;
            if (this.elements.playButtonIcon) {
                this.elements.playButtonIcon.textContent = 'pause_circle';
            }
        } catch (err) {
            console.warn("Lecture automatique en attente d'interaction :", err);
            this.isPlaying = false;
            if (this.elements.playButtonIcon) {
                this.elements.playButtonIcon.textContent = 'play_circle';
            }
        }
    }

    pauseTrack() {
        this.audio.pause();
        this.isPlaying = false;
        if (this.elements.playButtonIcon) {
            this.elements.playButtonIcon.textContent = 'play_circle';
        }
    }

    playNext() {
        this.loadTrack((this.currentIndex + 1) % this.tracks.length, false);
    }

    playPrevious() {
        const prevIndex = this.currentIndex > 0 ? this.currentIndex - 1 : this.tracks.length - 1;
        this.loadTrack(prevIndex, false);
    }

    updateProgress() {
        if (!isFinite(this.audio.duration) || this.audio.duration <= 0) return;
        const seekPosition = (this.audio.currentTime / this.audio.duration) * 100;
        if (this.elements.seekSlider) this.elements.seekSlider.value = seekPosition;
        if (this.elements.currentTime) this.elements.currentTime.textContent = this.formatTime(this.audio.currentTime);
        if (this.elements.totalDuration) this.elements.totalDuration.textContent = this.formatTime(this.audio.duration);
    }

    formatTime(time) {
        const minutes = Math.floor(time / 60).toString().padStart(2, '0');
        const seconds = Math.floor(time % 60).toString().padStart(2, '0');
        return `${minutes}:${seconds}`;
    }

    seekTo() {
        if (!isFinite(this.audio.duration)) return;
        this.audio.currentTime = this.audio.duration * (this.elements.seekSlider.value / 100);
    }

    updateVolume() {
        this.audio.volume = this.elements.volumeSlider.value / 100;
    }
}