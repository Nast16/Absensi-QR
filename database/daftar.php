<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Pendaftaran Kampus</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #1d2671, #c33764);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            padding: 35px;
            width: 100%;
            max-width: 420px;
            border-radius: 18px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
            color: white;
        }

        .card h2 {
            text-align: center;
            margin-bottom: 25px;
            font-weight: 600;
        }

        label {
            font-size: 14px;
            font-weight: 500;
        }

        input, select {
            width: 100%;
            padding: 12px;
            margin-top: 6px;
            margin-bottom: 18px;
            border-radius: 10px;
            border: none;
            outline: none;
            font-size: 14px;
        }

        input:focus, select:focus {
            box-shadow: 0 0 0 2px rgba(255,255,255,0.6);
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            background: linear-gradient(135deg, #00c6ff, #0072ff);
            color: white;
            cursor: pointer;
            transition: 0.3s ease;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.4);
        }

        .footer-text {
            text-align: center;
            font-size: 12px;
            margin-top: 20px;
            opacity: 0.8;
        }
    </style>
</head>

<body>

    <div class="card">
        <h2>🎓 Pendaftaran Pengguna Kampus</h2>

        <form action="prosesdaftar.php" method="POST">

            <label>Nama Lengkap</label>
            <input type="text" name="nama" placeholder="Masukkan nama lengkap" required>

            <label>Status</label>
            <select name="status" required>
                <option value="">-- Pilih Status --</option>
                <option value="dosen">Dosen</option>
                <option value="mahasiswa">Mahasiswa</option>
            </select>

            <label>Username</label>
            <input type="text" name="username" placeholder="Buat username" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Buat password" required>

            <button type="submit" name="submit">Daftar Sekarang</button>
        </form>

        <div class="footer-text">
            © 2025 Sistem Informasi Kampus
        </div>
    </div>

</body>
</html>
