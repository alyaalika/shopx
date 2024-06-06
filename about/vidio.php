<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Card with Overlay Text</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="card">
        <div class="card-header">
            <h2>Video Title</h2>
        </div>
        <div class="video-container">
            <video id="videoPlayer" controls>
                <source src="../video/videoplayback.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <div class="text-overlay" id="textOverlay">
                This is a sample text overlay.
            </div>
            <div class="controls">
                <button id="playPauseBtn">Play</button>
                <button id="muteUnmuteBtn">Mute</button>
                <button id="fullscreenBtn">Fullscreen</button>
                <div class="progress-bar" id="progressBar">
                    <div class="progress" id="progress"></div>
                </div>
                <span id="currentTime">0:00</span> / <span id="totalTime">0:00</span>
            </div>
        </div>
        <div class="card-footer">
            <p>Video description goes here. This is an example description for the video.</p>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
    const videoPlayer = document.getElementById('videoPlayer');
    const textOverlay = document.getElementById('textOverlay');
    const playPauseBtn = document.getElementById('playPauseBtn');
    const muteUnmuteBtn = document.getElementById('muteUnmuteBtn');
    const fullscreenBtn = document.getElementById('fullscreenBtn');
    const progressBar = document.getElementById('progressBar');
    const progress = document.getElementById('progress');
    const currentTimeDisplay = document.getElementById('currentTime');
    const totalTimeDisplay = document.getElementById('totalTime');

    // Update text overlay based on video current time
    videoPlayer.addEventListener('timeupdate', () => {
        const currentTime = videoPlayer.currentTime;

        if (currentTime >= 0 && currentTime < 5) {
            textOverlay.textContent = 'This is the beginning of the video.';
        } else if (currentTime >= 5 && currentTime < 10) {
            textOverlay.textContent = 'We are 5 seconds into the video.';
        } else if (currentTime >= 10 && currentTime < 15) {
            textOverlay.textContent = 'We are 10 seconds into the video.';
        } else {
            textOverlay.textContent = 'Enjoy the video!';
        }

        // Update progress bar and current time display
        const progressPercentage = (currentTime / videoPlayer.duration) * 100;
        progress.style.width = `${progressPercentage}%`;
        currentTimeDisplay.textContent = formatTime(currentTime);
    });

    // Update total time display when metadata is loaded
    videoPlayer.addEventListener('loadedmetadata', () => {
        totalTimeDisplay.textContent = formatTime(videoPlayer.duration);
    });

    // Play/Pause button functionality
    playPauseBtn.addEventListener('click', () => {
        if (videoPlayer.paused) {
            videoPlayer.play();
            playPauseBtn.textContent = 'Pause';
        } else {
            videoPlayer.pause();
            playPauseBtn.textContent = 'Play';
        }
    });

    // Mute/Unmute button functionality
    muteUnmuteBtn.addEventListener('click', () => {
        if (videoPlayer.muted) {
            videoPlayer.muted = false;
            muteUnmuteBtn.textContent = 'Mute';
        } else {
            videoPlayer.muted = true;
            muteUnmuteBtn.textContent = 'Unmute';
        }
    });

    // Fullscreen button functionality
    fullscreenBtn.addEventListener('click', () => {
        if (videoPlayer.requestFullscreen) {
            videoPlayer.requestFullscreen();
        } else if (videoPlayer.mozRequestFullScreen) { // Firefox
            videoPlayer.mozRequestFullScreen();
        } else if (videoPlayer.webkitRequestFullscreen) { // Chrome, Safari and Opera
            videoPlayer.webkitRequestFullscreen();
        } else if (videoPlayer.msRequestFullscreen) { // IE/Edge
            videoPlayer.msRequestFullscreen();
        }
    });

    // Progress bar click functionality
    progressBar.addEventListener('click', (e) => {
        const newTime = (e.offsetX / progressBar.offsetWidth) * videoPlayer.duration;
        videoPlayer.currentTime = newTime;
    });

    // Format time helper function
    function formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return `${minutes}:${secs < 10 ? '0' : ''}${secs}`;
    }
});

    </script>
</body>
</html>
