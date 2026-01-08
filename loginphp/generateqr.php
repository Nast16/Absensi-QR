<?php
date_default_timezone_set('Asia/Jakarta');
session_start();

if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "dosen") {
    header("Location: index.php");
    exit();
}

require_once __DIR__ . '/../database/db.php';
include_once "phpqrcode/qrlib.php";

$baseUrl = "https://grover-maritime-foresakenly.ngrok-free.dev"; // GANTI JIKA NGROK RESTART

/* =============================
   CONFIG
============================= */
$qrFolder = "qrcodes/";
$expireMinutes = 5;
$expiresAt = date("Y-m-d H:i:s", time() + ($expireMinutes * 60));

if (!file_exists($qrFolder)) {
    mkdir($qrFolder, 0755, true);
}

/* =============================
   GENERATE TOKEN
============================= */
$token = bin2hex(random_bytes(16)); // token aman & random
$expiresAt = date("Y-m-d H:i:s", time() + ($expireMinutes * 60));

/* =============================
   SIMPAN KE DB
============================= */
$stmt = $conn->prepare("
    INSERT INTO qr_sessions (token, expires_at)
    VALUES (?, ?)
");
$stmt->bind_param("ss", $token, $expiresAt);


if (!$stmt->execute()) {
    $error = "Gagal menyimpan QR ke database.";
}

/* =============================
   GENERATE QR IMAGE
============================= */
$fileName = $qrFolder . "absen_" . date('Ymd_His') . ".png";

$scanUrl = $baseUrl . "/websitephp/loginphp/scan.php?token=" . $token;

QRcode::png($token, $fileName, 'H', 8, 2);



if (!file_exists($fileName)) {
    $error = "Gagal membuat file QR Code.";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Generate QR Absen</title>
    <style>
        body {
            font-family: Poppins, sans-serif;
            text-align: center;
            padding-top: 40px;
            background: #f2f2f2;
        }
        img {
            margin-top: 20px;
            border: 8px solid #333;
            background: white;
            border-radius: 10px;
            padding: 10px;
        }
        a {
            text-decoration: none;
            display: inline-block;
            margin-top: 25px;
            padding: 12px 20px;
            background: #6C63FF;
            color: white;
            border-radius: 10px;
        }
        .info-box {
            background: white;
            padding: 15px;
            border-radius: 10px;
            margin: 20px auto;
            max-width: 520px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .error {
            background: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 10px;
            margin: 20px auto;
            max-width: 500px;
        }
    </style>
</head>
<body>

<h2>Generate QR Code Absen</h2>

<?php if (isset($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php else: ?>
    <div class="info-box">
        <p><strong>Token Absen:</strong></p>
        <code><?= htmlspecialchars($token) ?></code>
        <p style="margin-top:10px;">
            <small>Berlaku sampai: <?= $expiresAt ?></small>
        </p>
    </div>

    <img src="<?= $fileName ?>" width="300" alt="QR Code Absen">
<?php endif; ?>

<br>
<a href="dashboard.php">Kembali ke Dashboard</a>

</body>
</html>
