<?php
session_start();

if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "mahasiswa") {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Scan QR Absen</title>

<script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>

<style>
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #667eea, #764ba2);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    color: #fff;
}

.container {
    width: 100%;
    max-width: 420px;
    padding: 20px;
    text-align: center;
}

video {
    width: 100%;
    max-width: 300px;
    height: 300px;
    border-radius: 15px;
    border: 3px solid #fff;
    background: #000;
}

.overlay {
    position: absolute;
    width: 200px;
    height: 200px;
    border: 2px solid #6C63FF;
    border-radius: 10px;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    pointer-events: none;
}

.status {
    margin: 15px 0;
}

.result {
    margin-top: 15px;
    padding: 15px;
    border-radius: 10px;
    background: rgba(255,255,255,0.15);
}

.success {
    background: rgba(76,175,80,0.25);
    border: 1px solid #4CAF50;
}

.error {
    background: rgba(244,67,54,0.25);
    border: 1px solid #F44336;
}

button, a {
    margin-top: 10px;
    padding: 12px 20px;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    text-decoration: none;
    font-weight: 600;
}

button {
    background: rgba(255,255,255,0.2);
    color: #fff;
}

a {
    background: #6C63FF;
    color: #fff;
    display: inline-block;
}
</style>
</head>

<body>

<div class="container">
    <h2>📱 Scan QR Absen</h2>
    <p>Arahkan kamera ke QR Code</p>

    <div style="position: relative; margin: 20px auto;">
        <video id="video" autoplay playsinline muted></video>
        <div class="overlay"></div>
    </div>

    <div class="status" id="status">Membuka kamera...</div>

    <div class="result" id="result">
        Menunggu QR Code...
    </div>

    <button onclick="switchCamera()">🔄 Ganti Kamera</button><br>
    <button onclick="retryCamera()">🔁 Coba Lagi</button><br>
    <a href="dashboard.php">← Kembali</a>
</div>

<script>
const video = document.getElementById("video");
const statusEl = document.getElementById("status");
const resultEl = document.getElementById("result");

let stream = null;
let facingMode = "environment";
let scanning = false;
let interval = null;

/* =========================
   CAMERA CONTROL
========================= */
async function initCamera() {
    statusEl.textContent = "Membuka kamera...";
    resultEl.className = "result";
    resultEl.textContent = "Menunggu QR Code...";

    if (stream) {
        stream.getTracks().forEach(t => t.stop());
    }

    try {
        stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: facingMode },
            audio: false
        });

        video.srcObject = stream;
        updateMirror();

        setTimeout(startScan, 500);
        statusEl.textContent = "Kamera siap, arahkan ke QR";

    } catch (e) {
        statusEl.textContent = "Gagal mengakses kamera";
        resultEl.className = "result error";
        resultEl.textContent = e.message;
    }
}

function updateMirror() {
    video.style.transform =
        (facingMode === "user") ? "scaleX(-1)" : "scaleX(1)";
}

function switchCamera() {
    facingMode = (facingMode === "environment") ? "user" : "environment";
    initCamera();
}

function retryCamera() {
    initCamera();
}

/* =========================
   QR SCANNING
========================= */
function startScan() {
    if (scanning) return;
    scanning = true;

    interval = setInterval(() => {
        if (video.readyState !== video.HAVE_ENOUGH_DATA) return;

        const canvas = document.createElement("canvas");
        const ctx = canvas.getContext("2d");

        canvas.width = 300;
        canvas.height = 300;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const img = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(img.data, canvas.width, canvas.height);

        if (code) handleDetected(code.data);

    }, 900); // stabil untuk HP
}

function handleDetected(data) {
    clearInterval(interval);
    scanning = false;

    if (stream) {
        stream.getTracks().forEach(t => t.stop());
        stream = null;
    }

    statusEl.textContent = "Mengirim data...";
    resultEl.className = "result success";
    resultEl.innerHTML = "✅ QR terdeteksi<br><small>" + data + "</small>";

    submitAttendance(data);
}

/* =========================
   SEND TO SERVER
========================= */
function submitAttendance(token) {
    fetch("record_attendance.php", {
    method: "POST",
    headers: {
        "Content-Type": "application/x-www-form-urlencoded"
    },
    body: "token=" + encodeURIComponent(token)
})
.then(res => res.text())
.then(text => {
    console.log("RAW RESPONSE:", text);

    let data;
    try {
        data = JSON.parse(text);
    } catch (e) {
        statusEl.textContent = "Server kirim data rusak";
        return;
    }

    if (data.status === "success") {
        statusEl.textContent = "Absen berhasil";
        resultEl.innerHTML = "✅ " + data.message;
    } else {
        statusEl.textContent = "Absen gagal";
        resultEl.innerHTML = "❌ " + data.message;
    }
})
.catch(err => {
    console.error(err);
    statusEl.textContent = "Server error";
});


initCamera();
</script>

</body>
</html>
