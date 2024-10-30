'use strict';

class AudioRecorder {
    constructor() {
        this.mediaRecorder = null;
        this.audioChunks = [];
        this.isRecording = false;
        this.stream = null;
        this.audioContext = null;
        this.analyser = null;
        this.source = null;
    }

    async startRecording() {
        try {
            this.stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            this.mediaRecorder = new MediaRecorder(this.stream);
            this.audioChunks = [];

            this.mediaRecorder.ondataavailable = (event) => {
                this.audioChunks.push(event.data);
            };

            this.mediaRecorder.onstop = () => {
                const audioBlob = new Blob(this.audioChunks, { type: 'audio/wav' });
                this.showAudioEditor(audioBlob);
            };

            // Configuration de l'analyseur audio pour la visualisation
            this.audioContext = new AudioContext();
            this.analyser = this.audioContext.createAnalyser();
            this.source = this.audioContext.createMediaStreamSource(this.stream);
            this.source.connect(this.analyser);
            this.visualize();

            this.mediaRecorder.start();
            this.isRecording = true;
            
            // Mettre à jour l'interface
            document.getElementById('recordButton').innerHTML = '<i class="material-icons">stop</i>';
            document.getElementById('recordButton').classList.add('recording');
        } catch (err) {
            console.error('Erreur lors de l\'enregistrement:', err);
            alert('Erreur lors de l\'accès au microphone. Veuillez vérifier les permissions.');
        }
    }

    stopRecording() {
        if (this.mediaRecorder && this.isRecording) {
            this.mediaRecorder.stop();
            this.stream.getTracks().forEach(track => track.stop());
            this.isRecording = false;
            
            // Réinitialiser l'interface
            document.getElementById('recordButton').innerHTML = '<i class="material-icons">mic</i>';
            document.getElementById('recordButton').classList.remove('recording');
        }
    }

    visualize() {
        const canvas = document.getElementById('visualizer');
        const canvasCtx = canvas.getContext('2d');
        const WIDTH = canvas.width;
        const HEIGHT = canvas.height;

        this.analyser.fftSize = 2048;
        const bufferLength = this.analyser.frequencyBinCount;
        const dataArray = new Uint8Array(bufferLength);

        canvasCtx.clearRect(0, 0, WIDTH, HEIGHT);

        const draw = () => {
            if (!this.isRecording) return;

            requestAnimationFrame(draw);
            this.analyser.getByteTimeDomainData(dataArray);
            canvasCtx.fillStyle = 'rgb(200, 200, 200)';
            canvasCtx.fillRect(0, 0, WIDTH, HEIGHT);
            canvasCtx.lineWidth = 2;
            canvasCtx.strokeStyle = 'rgb(0, 0, 0)';
            canvasCtx.beginPath();

            const sliceWidth = WIDTH * 1.0 / bufferLength;
            let x = 0;

            for (let i = 0; i < bufferLength; i++) {
                const v = dataArray[i] / 128.0;
                const y = v * HEIGHT / 2;

                if (i === 0) {
                    canvasCtx.moveTo(x, y);
                } else {
                    canvasCtx.lineTo(x, y);
                }

                x += sliceWidth;
            }

            canvasCtx.lineTo(canvas.width, canvas.height / 2);
            canvasCtx.stroke();
        };

        draw();
    }

    showAudioEditor(audioBlob) {
        const audioUrl = URL.createObjectURL(audioBlob);
        const editorHtml = `
            <div class="audio-editor">
                <audio id="recordedAudio" controls src="${audioUrl}"></audio>
                <div class="editor-controls">
                    <button onclick="audioRecorder.saveRecording()" class="btnBase vert">
                        <i class="material-icons">save</i> Enregistrer
                    </button>
                    <button onclick="audioRecorder.discardRecording()" class="btnBase rouge">
                        <i class="material-icons">delete</i> Annuler
                    </button>
                </div>
            </div>
        `;
        document.getElementById('audioEditor').innerHTML = editorHtml;
    }

    async saveRecording() {
        const audio = document.getElementById('recordedAudio');
        const formData = new FormData();
        
        // Créer un nom de fichier unique
        const fileName = `record_${Date.now()}.wav`;
        const audioBlob = await fetch(audio.src).then(r => r.blob());
        
        formData.append('title', 'Enregistrement ' + new Date().toLocaleString());
        formData.append('artiste', 'Utilisateur');
        formData.append('path', new File([audioBlob], fileName, { type: 'audio/wav' }));
        
        try {
            const response = await fetch('?url=audio/create', {
                method: 'POST',
                body: formData
            });
            
            if (response.ok) {
                alert('Enregistrement sauvegardé avec succès!');
                window.location.href = '?url=audio/list';
            } else {
                throw new Error('Erreur lors de la sauvegarde');
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Erreur lors de la sauvegarde de l\'enregistrement');
        }
    }

    discardRecording() {
        document.getElementById('audioEditor').innerHTML = '';
        document.getElementById('recordButton').innerHTML = '<i class="material-icons">mic</i>';
    }
}

const audioRecorder = new AudioRecorder();

function toggleRecording() {
    if (!audioRecorder.isRecording) {
        audioRecorder.startRecording();
    } else {
        audioRecorder.stopRecording();
    }
}
