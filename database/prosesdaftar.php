<?php
require_once __DIR__ . '/db.php';

if (isset($_POST['submit'])) {

    $nama     = trim($_POST['nama']);
    $status   = $_POST['status']; // dosen / mahasiswa
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $npm = null;
    $nip = null;

    if ($status === 'mahasiswa') {
        $npm = trim($_POST['npm'] ?? '');
        if ($npm === '') {
            die("❌ NPM wajib diisi untuk mahasiswa");
        }
    }

    if ($status === 'dosen') {
        $nip = trim($_POST['nip'] ?? '');
        if ($nip === '') {
            die("❌ NIP wajib diisi untuk dosen");
        }
    }

    // HASH PASSWORD
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // =============================
    // CEK USERNAME
    // =============================
    $cek = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $cek->bind_param("s", $username);
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows > 0) {
        die("❌ Username sudah digunakan");
    }

    // =============================
    // INSERT USER
    // =============================
    $stmt = $conn->prepare(
        "INSERT INTO users (username, password_hash, fullname, role, npm, nip)
         VALUES (?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "ssssss",
        $username,
        $password_hash,
        $nama,
        $status,
        $npm,
        $nip
    );

    if ($stmt->execute()) {
        header("Location: /websitephp/loginphp");
        exit;
    } else {
        echo "❌ Gagal mendaftar: " . $stmt->error;
    }
}
