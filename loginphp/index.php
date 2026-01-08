<?php
session_start();
include "../database/db.php"; // SESUAI STRUKTUR FOLDER

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = $_POST['username'];
    $password = $_POST['password'];

    // ambil user dari database
    $stmt = $conn->prepare(
        "SELECT id, username, password_hash, role, fullname
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
        $_SESSION['fullname'] = $user['fullname'];
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- STYLE KAMU TETAP -->
    <style>
        * {
            box-sizing: border-box;
        }

        input {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;

            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: none;
            font-size: 14px;
            line-height: 1.4;
        }

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

        @media (max-width: 480px) {
        .login-box {
            width: 100%;
            max-width: 100%;
            margin: 20px;
            padding: 30px;
        }

        h2 {
            font-size: 22px;
        }

        input, button {
            padding: 14px;
            font-size: 15px;
        }
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
