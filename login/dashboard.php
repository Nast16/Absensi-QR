<?php
session_start();

// jika belum login, kembalikan ke halaman login
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// jika logout ditekan
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard">
<div class="dash-container">
    <h1>Selamat Datang, <?php echo $_SESSION['user']; ?>!</h1>
    <p>Kamu berhasil login ke sistem sederhana ini 🎉</p>
    <a href="?logout=true" class="logout-btn">Logout</a>
</div>
</body>
</html>
