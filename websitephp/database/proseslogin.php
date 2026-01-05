<?php
session_start();
require_once __DIR__ . '/db.php';


$username = $_POST['username'];
$password = $_POST['password'];

// gunakan prepared statement
$stmt = $conn->prepare(
    "SELECT id, username, password_hash, fullname, role 
     FROM users WHERE username = ?"
);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$data = $result->fetch_assoc();

if ($data && password_verify($password, $data['password_hash'])) {

    $_SESSION['user_id'] = $data['id'];
    $_SESSION['username'] = $data['username'];
    $_SESSION['nama']     = $data['fullname'];
    $_SESSION['role']     = $data['role'];

    header("Location: dashboard.php");
    exit();
} else {
    echo "❌ Username atau password salah";
}
