<?php
require_once __DIR__ . '/db.php';

if (isset($_POST['submit'])) {

    $nama     = $_POST['nama'];
    $status   = $_POST['status'];   // dosen / mahasiswa
    $username = $_POST['username'];
    $password = $_POST['password'];

    // HASH PASSWORD
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // =============================
    // CEK USERNAME SUDAH ADA
    // =============================
    $cek = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $cek->bind_param("s", $username);
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows > 0) {
        echo "❌ Username sudah digunakan!<br>";
        echo "<a href='daftar.php'>Kembali</a>";
        exit;
    }

    // =============================
    // INSERT KE DATABASE
    // =============================
    $stmt = $conn->prepare(
        "INSERT INTO users (username, password_hash, fullname, role)
         VALUES (?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "ssss",
        $username,
        $password_hash,
        $nama,
        $status
    );

    if ($stmt->execute()) {
        header("Location: /websitephp/loginphp/index.php");
        exit;
    } else {
        echo "❌ Gagal mendaftar: " . $stmt->error;
    }
}
?>
