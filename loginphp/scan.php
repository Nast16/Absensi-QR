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
*{box-sizing:border-box;margin:0;padding:0}
body{
    font-family:Poppins,sans-serif;
    background:linear-gradient(135deg,#667eea,#764ba2);
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    color:#fff;
}
.container{width:100%;max-width:420px;padding:20px;text-align:center}
video{
    width:100%;
    max-width:300px;
    height:300px;
    border-radius:15px;
    border:3px solid #fff;
    background:#000;
}
.overlay{
    position:absolute;
    width:200px;height:200px;
    border:2px solid #6C63FF;
    border-radius:10px;
    top:50%;left:50%;
    transform:translate(-50%,-50%);
    pointer-events:none;
}
.status{margin:15px 0}
.result{
    margin-top:15px;
    padding:15px;
    border-radius:10px;
    background:rgba(255,255,255,.15)
}
.success{background:rgba(76,175,80,.25);border:1px solid #4CAF50}
.error{background:rgba(244,67,54,.25);border:1px solid #F44336}
button,a{
    margin-top:10px;
    padding:12px 20px;
    border-radius:10px;
    border:none;
    cursor:pointer;
    text-decoration:none;
    font-weight:600;
}
button{background:rgba(255,255,255,.2);color:#fff}
a{background:#6C63FF;color:#fff;display:inline-block}
</style>
</head>

<body>
<div class="container">
    <h2>📱 Scan QR Absen</h2>
    <p>Tap tombol di bawah untuk membuka kamera</p>

    <div style="position:relative;margin:20px auto;">
        <video id="video" playsinline muted></video>
        <div class="overlay"></div>
    </div>

    <div class="status" id="status">Kamera belum aktif</div>
    <div class="result" id="result">Menunggu...</div>

    <button onclick="startCamera()">📷 Buka Kamera</button><br>
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

/* ================= CAMERA ================= */
async function startCamera(){
    if(stream) return;
    await initCamera();
}

async function initCamera(){
    statusEl.textContent = "Membuka kamera...";
    resultEl.className = "result";
    resultEl.textContent = "Menunggu QR Code...";
    scanning = false;

    if(interval) clearInterval(interval);
    if(stream){
        stream.getTracks().forEach(t=>t.stop());
        stream=null;
    }

    try{
        stream = await navigator.mediaDevices.getUserMedia({
            video:{facingMode:{ideal:facingMode}},
            audio:false
        });

        video.srcObject = stream;
        video.onloadedmetadata = () => {
            video.play();
            updateMirror();
            statusEl.textContent = "Kamera aktif, arahkan ke QR";
            startScan();
        };
    }catch(e){
        statusEl.textContent = "Gagal membuka kamera";
        resultEl.className = "result error";
        resultEl.textContent = e.name + ": " + e.message;
    }
}

function updateMirror(){
    video.style.transform =
        (facingMode==="user") ? "scaleX(-1)" : "scaleX(1)";
}

function switchCamera(){
    facingMode = (facingMode==="environment")?"user":"environment";
    retryCamera();
}

function retryCamera(){
    if(stream){
        stream.getTracks().forEach(t=>t.stop());
        stream=null;
    }
    startCamera();
}

/* ================= SCAN ================= */
function startScan(){
    if(scanning) return;
    scanning = true;

    interval = setInterval(()=>{
        if(video.readyState !== video.HAVE_ENOUGH_DATA) return;

        const canvas = document.createElement("canvas");
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        const ctx = canvas.getContext("2d");
        ctx.drawImage(video,0,0,canvas.width,canvas.height);

        const img = ctx.getImageData(0,0,canvas.width,canvas.height);
        const code = jsQR(img.data,canvas.width,canvas.height);

        if(code) handleDetected(code.data);
    },800);
}

function handleDetected(data){
    clearInterval(interval);
    scanning=false;

    if(stream){
        stream.getTracks().forEach(t=>t.stop());
        stream=null;
    }

    statusEl.textContent = "Mengirim data...";
    resultEl.className = "result success";
    resultEl.innerHTML = "✅ QR terdeteksi<br><small>"+data+"</small>";

    submitAttendance(data);
}

/* ================= SEND ================= */
function submitAttendance(token){
    fetch("record_attendance.php",{
        method:"POST",
        headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:"token="+encodeURIComponent(token)
    })
    .then(r=>r.json())
    .then(d=>{
        if(d.status==="success"){
            statusEl.textContent="Absen berhasil";
            resultEl.innerHTML="✅ "+d.message;
        }else{
            statusEl.textContent="Absen gagal";
            resultEl.innerHTML="❌ "+d.message;
        }
    })
    .catch(()=>{
        statusEl.textContent="Server error";
    });
}
</script>
</body>
</html>
