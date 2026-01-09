<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$nama_raw = $_SESSION['fullname'] ?? "User";
$role_raw = $_SESSION['role'] ?? "guest";

/* format khusus tampilan */
$nama_tampil = ucwords(strtolower($nama_raw));
$role_tampil = ucfirst(strtolower($role_raw));
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

  <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        height: 100vh;
        width: 100vw;
        display: flex;
        flex-direction: column;
        font-family: "Poppins", sans-serif;
        background: url('https://miro.medium.com/v2/resize:fit:1080/1*HExFW_w7riS578zus2z3bA.jpeg')
            no-repeat center center fixed;
        background-size: cover;
        color: white;
    }

    .navbar-custom {
        background: rgba(0, 0, 0, 0.4) !important;
        backdrop-filter: blur(10px);
    }

    .dashboard-box {
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 40px;
        max-width: 450px;
        margin: auto;
        text-align: center;
        box-shadow: 0 8px 20px rgba(0,0,0,0.4);
    }

    .btn-custom {
        width: 100%;
        padding: 12px;
        border-radius: 10px;
        font-weight: bold;
        margin-top: 20px;
        transition: 0.3s;
    }

    .logout-btn {
        background-color: #ff5f6d !important;
        border: none !important;
    }

    .logout-btn:hover {
        background-color: #ff3c50 !important;
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-dark navbar-custom">
    <div class="container-fluid">
      <span class="navbar-brand">
        Dashboard - <?= htmlspecialchars($nama_tampil) ?>
      </span>
      <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
  </nav>

  <div class="dashboard-box mt-5">
    <h3>Selamat datang, <?= htmlspecialchars($nama_tampil) ?>!</h3>
    <p>Anda login sebagai <strong><?= htmlspecialchars($role_tampil) ?></strong></p>

    <?php if ($role_raw === "dosen") : ?>
        <a href="generateqr.php" class="btn btn-primary btn-custom">
            Generate QR Absen
        </a>

        <a href="pilih_qr.php" class="btn btn-warning btn-custom">
            Export Absensi
        </a>

    <?php elseif ($role_raw === "mahasiswa") : ?>
        <a href="scan.php" class="btn btn-success btn-custom">
            Scan QR untuk Absen
        </a>
    <?php endif; ?>
  </div>


</body>
</html>
