'use strict';

// Lecteur audio -----------------------------------------------------------------
// and assign them to a variable
const now_playing = document.querySelector(".now-playing");
const track_art = document.querySelector(".track-art");
const track_name = document.querySelector(".track-name");
const track_artist = document.querySelector(".track-artist");

const playpause_btn = document.querySelector(".playpause-track");
const next_btn = document.querySelector(".next-track");
const prev_btn = document.querySelector(".prev-track");

const seek_slider = document.querySelector(".seek_slider");
const volume_slider = document.querySelector(".volume_slider");
const curr_time = document.querySelector(".current-time");
const total_duration = document.querySelector(".total-duration");

// Specify globally used values
let track_index = 0;
let isPlaying = false;
let updateTimer;

// Create the audio element for the player
let curr_track = document.createElement('audio');

let track_list = [];
document.addEventListener('DOMContentLoaded', function() {
    if (typeof audios !== 'undefined') {
        track_list = audios.map(audio => ({
            id: audio.id,
            path: audio.path,
            image: audio.image,
            title: audio.title,
            artist: audio.artist
        }));
    }

    document.getElementById('play-selected').addEventListener('click', function() {
        const selectedAudios = Array.from(document.querySelectorAll('#audio-list li.selected')).map(li => li.dataset.id);
        if (selectedAudios.length > 0) {
            window.location.href = `?url=audio/index&ids=${selectedAudios.join(',')}`;
        } else {
            alert('Veuillez sélectionner au moins un enregistrement audio.');
        }
    });

    document.querySelectorAll('#audio-list li').forEach(li => {
        li.addEventListener('click', function() {
            this.classList.toggle('selected');
        });
    });
});

function loadTrack(track_index) {
    // Clear the previous seek timer
    clearInterval(updateTimer);
    resetValues();

    // Load a new track
    curr_track.src = track_list[track_index].path;
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
    // using the 'ended' event
    curr_track.addEventListener("ended", nextTrack);
}

// Function to reset all values to their default
function resetValues() {
    curr_time.textContent = "00:00";
    total_duration.textContent = "00:00";
    seek_slider.value = 0;
}

// Ensure the DOM is fully loaded before attaching event listeners
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('#audio-table tr').forEach(tr => {
        tr.addEventListener('click', function() {
            document.querySelectorAll('#audio-table tr').forEach(row => row.classList.remove('selected'));
            this.classList.add('selected');
            const audioId = this.dataset.id;
            const audioData = track_list.find(track => track.id == audioId);
            if (audioData) {
                track_index = track_list.indexOf(audioData);
                loadTrack(track_index);
                playTrack();
            }
        });
    });

    document.getElementById('play-selected').addEventListener('click', function() {
        const selectedAudio = document.querySelector('#audio-table tr.selected');
        if (selectedAudio) {
            const audioId = selectedAudio.dataset.id;
            const audioData = track_list.find(track => track.id == audioId);
            if (audioData) {
                track_index = track_list.indexOf(audioData);
                loadTrack(track_index);
                playTrack();
            }
        } else {
            alert('Veuillez sélectionner un enregistrement audio.');
        }
    });
});

// Load the first track in the tracklist
loadTrack(track_index);

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
