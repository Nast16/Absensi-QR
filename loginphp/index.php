<?php
session_start();
include "../database/db.php"; // SESUAI STRUKTUR FOLDER

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = $_POST['username'];
    $password = $_POST['password'];

    // ambil user dari database
    $stmt = $conn->prepare(
        "SELECT id, username, password_hash, role 
         FROM users 
         WHERE username = ?"
    );
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // cek password
    if ($user && password_verify($password, $user['password_hash'])) {

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // redirect setelah login
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <!-- STYLE KAMU TETAP -->
    <style>
        body {
            background: url('https://miro.medium.com/v2/resize:fit:1080/1*HExFW_w7riS578zus2z3bA.jpeg')
                no-repeat center center fixed;
            background-size: cover;
            font-family: Poppins, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: white;
        }
        .login-box {
            background: rgba(0, 0, 0, 0.55);
            padding: 35px;
            width: 350px;
            border-radius: 20px;
            backdrop-filter: blur(8px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.4);
            text-align: center;
        }
        input, button {
            width: 100%;
            padding: 12px;
            border: none;
            margin: 10px 0;
            border-radius: 10px;
        }
        button {
            background: #6C63FF;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background: #554EF1;
        }
        .error {
            background: rgba(255, 0, 0, 0.4);
            padding: 8px;
            border-radius: 8px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <form action="" method="POST" class="login-box">
        <h2>Login</h2>

        <?php if ($error != "") : ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">LOGIN</button>
    </form>
</body>
</html>
