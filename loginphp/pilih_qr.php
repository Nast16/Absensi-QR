<?php
session_start();
require_once "../database/db.php";

/* =====================
   SECURITY CHECK
===================== */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dosen') {
    header("Location: dashboard.php");
    exit;
}

/* =====================
   AMBIL DATA QR SESSION
===================== */
$stmt = $conn->prepare("
    SELECT 
        q.id,
        q.created_at,
        q.expires_at,
        COUNT(a.id) AS total_absen
    FROM qr_sessions q
    LEFT JOIN attendance_logs a 
        ON a.qr_session_id = q.id
    GROUP BY q.id
    ORDER BY q.created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pilih Sesi QR</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #1d2671, #c33764);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    color: white;
}

.card {
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(12px);
    padding: 30px;
    width: 100%;
    max-width: 700px;
    border-radius: 18px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.35);
}

h2 {
    text-align: center;
    margin-bottom: 25px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 12px;
    text-align: center;
}

th {
    background: rgba(0,0,0,0.35);
}

tr:nth-child(even) {
    background: rgba(255,255,255,0.08);
}

.btn {
    padding: 8px 14px;
    border-radius: 10px;
    background: #ffc107;
    color: #000;
    font-weight: 600;
    text-decoration: none;
}

.btn:hover {
    background: #ffb300;
}

.back {
    display: inline-block;
    margin-top: 20px;
    color: white;
    text-decoration: none;
    font-size: 14px;
}
</style>
</head>

<body>

<div class="card">
    <h2>📋 Pilih Sesi Absensi</h2>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Waktu Dibuat</th>
                <th>Kadaluarsa</th>
                <th>Total Hadir</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php $no = 1; ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= date("d M Y H:i", strtotime($row['created_at'])) ?></td>
                    <td><?= date("d M Y H:i", strtotime($row['expires_at'])) ?></td>
                    <td><?= $row['total_absen'] ?> orang</td>
                    <td>
                        <a href="../export/export_absensi.php?qr_id=<?= $row['id'] ?>"
                        class="btn">
                        Export Absensi
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">Belum ada sesi QR</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <a href="dashboard.php" class="back">← Kembali ke Dashboard</a>
</div>

</body>
</html>
