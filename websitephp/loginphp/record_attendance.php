<?php
date_default_timezone_set('Asia/Jakarta');
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../database/db.php'; // $conn (mysqli)

try {

    /* ===============================
       1. VALIDASI LOGIN
    =============================== */
    if (!isset($_SESSION['user_id'])) {
        echo json_encode([
            "status" => "error",
            "message" => "User belum login"
        ]);
        exit;
    }

    $user_id = (int) $_SESSION['user_id'];

    /* ===============================
       2. VALIDASI INPUT QR TOKEN
    =============================== */
    if (!isset($_POST['token']) || trim($_POST['token']) === '') {
        echo json_encode([
            "status" => "error",
            "message" => "QR token tidak dikirim"
        ]);
        exit;
    }

    $token = trim($_POST['token']);

    /* ===============================
       3. VALIDASI QR SESSION (PAKAI WAKTU PHP)
    =============================== */
    $now = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("
        SELECT id, expires_at
        FROM qr_sessions
        WHERE token = ?
          AND expires_at > ?
        LIMIT 1
    ");
    $stmt->bind_param("ss", $token, $now);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode([
            "status" => "error",
            "message" => "QR tidak valid atau sudah kedaluwarsa"
        ]);
        exit;
    }

    $qr_session = $result->fetch_assoc();
    $qr_session_id = (int) $qr_session['id'];

    /* ===============================
       4. CEK DOUBLE ABSENSI
    =============================== */
    $stmt = $conn->prepare("
        SELECT id
        FROM attendance_logs
        WHERE user_id = ?
          AND qr_session_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("ii", $user_id, $qr_session_id);
    $stmt->execute();
    $check = $stmt->get_result();

    if ($check->num_rows > 0) {
        echo json_encode([
            "status" => "error",
            "message" => "Kamu sudah melakukan absensi"
        ]);
        exit;
    }

    /* ===============================
       5. SIMPAN ABSENSI
    =============================== */
    $stmt = $conn->prepare("
        INSERT INTO attendance_logs (user_id, qr_session_id, created_at)
        VALUES (?, ?, NOW())
    ");
    $stmt->bind_param("ii", $user_id, $qr_session_id);
    $stmt->execute();

    /* ===============================
       6. RESPONSE SUKSES
    =============================== */
    echo json_encode([
        "status" => "success",
        "message" => "Absensi berhasil dicatat"
    ]);
    exit;

} catch (Throwable $e) {

    echo json_encode([
        "status" => "error",
        "message" => "Server error",
        "debug" => $e->getMessage() // hapus saat production
    ]);
    exit;
}
