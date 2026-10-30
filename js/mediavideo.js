'use strict';

document.addEventListener('DOMContentLoaded', () => {
    // =========================================================
    // 1. PANNEAUX IMPORT & WEBCAM (videoList.php)
    // =========================================================
    const uploadBox = document.getElementById('video-upload-box');
    const recorderBox = document.getElementById('video-recorder-box');
    const btnToggleUpload = document.getElementById('btnToggleVideoUpload');
    const btnToggleRecorder = document.getElementById('btnToggleVideoRecorder');
    const btnCancelUpload = document.getElementById('btnCancelVideoUpload');
    const btnCancelRecorder = document.getElementById('btnCancelVideoRecorder');

    let webcamStream = null;

    function stopWebcamStream() {
        if (webcamStream) {
            webcamStream.getTracks().forEach(track => track.stop());
            webcamStream = null;
        }
    }

    function openVideoUpload() {
        stopWebcamStream();
        if (recorderBox) recorderBox.classList.add('is-hidden');
        if (uploadBox) {
            uploadBox.classList.toggle('is-hidden');
            const input = document.getElementById('video-title-input');
            if (!uploadBox.classList.contains('is-hidden') && input) input.focus();
        }
    }

    function openVideoRecorder() {
        if (uploadBox) uploadBox.classList.add('is-hidden');
        if (recorderBox) {
            const willOpen = recorderBox.classList.contains('is-hidden');
            recorderBox.classList.toggle('is-hidden');
            if (willOpen) {
                const titleInput = document.getElementById('webcam-title');
                if (titleInput && !titleInput.value) {
                    const now = new Date();
                    titleInput.value = `Note vidéo du ${now.toLocaleDateString('fr-FR')} à ${now.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })}`;
                }
            } else {
                stopWebcamStream();
            }
        }
    }

    if (btnToggleUpload) btnToggleUpload.addEventListener('click', openVideoUpload);
    if (btnToggleRecorder) btnToggleRecorder.addEventListener('click', openVideoRecorder);
    if (btnCancelUpload && uploadBox) {
        btnCancelUpload.addEventListener('click', () => uploadBox.classList.add('is-hidden'));
    }
    if (btnCancelRecorder && recorderBox) {
        btnCancelRecorder.addEventListener('click', () => {
            stopWebcamStream();
            recorderBox.classList.add('is-hidden');
        });
    }

    if (window.location.hash === '#record') openVideoRecorder();
    if (window.location.hash === '#upload') openVideoUpload();

    // =========================================================
    // 2. ENREGISTREMENT WEBCAM (MediaRecorder)
    // =========================================================
    const btnStart = document.getElementById('btnStartVideoRecord');
    const btnStop = document.getElementById('btnStopVideoRecord');
    const btnSave = document.getElementById('btnSaveVideoNote');
    const webcamPreview = document.getElementById('webcamPreview');
    const timerDisplay = document.getElementById('video-recording-timer');
    const recordedVideoFile = document.getElementById('recordedVideoFile');

    let videoRecorder = null;
    let videoChunks = [];
    let timerInterval = null;
    let seconds = 0;

    if (btnStart && btnStop && webcamPreview) {
        btnStart.addEventListener('click', async () => {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                alert("Votre navigateur ne supporte pas la capture vidéo.");
                return;
            }
            try {
                webcamStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
                webcamPreview.srcObject = webcamStream;
                webcamPreview.muted = true;
                webcamPreview.play();

                const mimeType = MediaRecorder.isTypeSupported('video/webm') ? 'video/webm' : '';
                videoRecorder = mimeType ? new MediaRecorder(webcamStream, { mimeType }) : new MediaRecorder(webcamStream);
                videoChunks = [];

                videoRecorder.addEventListener('dataavailable', e => {
                    if (e.data && e.data.size > 0) videoChunks.push(e.data);
                });

                videoRecorder.addEventListener('stop', () => {
                    stopWebcamStream();
                    clearInterval(timerInterval);
                    if (timerDisplay) timerDisplay.classList.remove('is-recording');

                    const finalMime = videoRecorder.mimeType || 'video/webm';
                    const blob = new Blob(videoChunks, { type: finalMime });
                    webcamPreview.srcObject = null;
                    webcamPreview.src = URL.createObjectURL(blob);
                    webcamPreview.muted = false;
                    webcamPreview.controls = true;

                    const file = new File([blob], `webcam_${Date.now()}.webm`, { type: 'video/webm' });
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    recordedVideoFile.files = dt.files;

                    if (btnSave) btnSave.disabled = false;
                });

                videoRecorder.start();
                seconds = 0;
                if (timerDisplay) {
                    timerDisplay.textContent = "00:00";
                    timerDisplay.classList.add('is-recording');
                }
                timerInterval = setInterval(() => {
                    seconds++;
                    const m = Math.floor(seconds / 60).toString().padStart(2, '0');
                    const s = (seconds % 60).toString().padStart(2, '0');
                    if (timerDisplay) timerDisplay.textContent = `${m}:${s}`;
                }, 1000);

                btnStart.classList.add('is-hidden');
                btnStop.classList.remove('is-hidden');
                btnStop.disabled = false;
                if (btnSave) btnSave.disabled = true;
            } catch (err) {
                console.error("Erreur caméra :", err);
                alert("Impossible d'accéder à la caméra ou au microphone.");
            }
        });

        btnStop.addEventListener('click', () => {
            if (videoRecorder && videoRecorder.state !== 'inactive') {
                videoRecorder.stop();
                btnStop.classList.add('is-hidden');
                btnStop.disabled = true;
                btnStart.classList.remove('is-hidden');
            }
        });
    }

    // =========================================================
    // 3. RECHERCHE, DURÉE & SÉLECTION (videoList.php)
    // =========================================================
    const searchInput = document.getElementById('searchVideoInput');
    const videoItems = document.querySelectorAll('#videoGrid .video-item');
    if (searchInput && videoItems.length > 0) {
        searchInput.addEventListener('input', function () {
            const term = this.value.toLowerCase();
            videoItems.forEach(item => {
                item.style.display = item.textContent.toLowerCase().includes(term) ? '' : 'none';
            });
        });
    }

    document.querySelectorAll('.video-thumbnail').forEach(vid => {
        vid.addEventListener('loadedmetadata', function () {
            if (!isFinite(this.duration)) return;
            const d = Math.round(this.duration);
            const m = Math.floor(d / 60);
            const s = (d % 60).toString().padStart(2, '0');
            const badge = this.parentElement.querySelector('.video-duration');
            if (badge) badge.textContent = `${m}:${s}`;
        });
    });

    const checkboxes = document.querySelectorAll('.select-video');
    const playSelectedBtn = document.getElementById('play-selected-videos');
    const selectedContainer = document.getElementById('selected-videos-container');

    if (checkboxes.length > 0 && playSelectedBtn && selectedContainer) {
        checkboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                const card = this.closest('.video-item');
                if (card) card.classList.toggle('selected', this.checked);

                selectedContainer.innerHTML = '';
                const checked = document.querySelectorAll('.select-video:checked');
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
    // 4. LECTEUR VIDÉO & PLAYLIST (video.php)
    // =========================================================
    const playerData = document.getElementById('video-player-data');
    const videoPlayer = document.getElementById('video-player');
    const videoTitle = document.getElementById('video-title');
    const videoDesc = document.getElementById('video-description');
    const playlistItems = document.querySelectorAll('.playlist-item');

    if (playerData && videoPlayer && playerData.dataset.videos) {
        try {
            const videos = JSON.parse(playerData.dataset.videos);
            if (Array.isArray(videos) && videos.length > 0) {
                function loadVideoItem(index) {
                    const v = videos[index];
                    if (!v) return;
                    videoPlayer.pause();
                    videoPlayer.src = v.path;
                    if (v.thumbnail) videoPlayer.poster = v.thumbnail;
                    videoPlayer.load();
                    if (videoTitle) videoTitle.textContent = v.title || '';
                    if (videoDesc) videoDesc.textContent = v.description || '';

                    playlistItems.forEach((item, idx) => {
                        item.classList.toggle('active', idx === index);
                    });
                }

                loadVideoItem(0);

                playlistItems.forEach((item, index) => {
                    item.addEventListener('click', () => loadVideoItem(index));
                });
            }
        } catch (e) {
            console.error("Erreur d'initialisation du lecteur vidéo :", e);
        }
    }
});