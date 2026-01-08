<?php
date_default_timezone_set('Asia/Jakarta');
header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/../database/db.php';

ob_clean(); // PENTING: bersihin output buffer

try {

    if (!isset($_SESSION['user_id'])) {
        echo json_encode([
            "status" => "error",
            "message" => "User belum login"
        ]);
        exit;
    }

    if (empty($_POST['token'])) {
        echo json_encode([
            "status" => "error",
            "message" => "QR token tidak dikirim"
        ]);
        exit;
    }

    $user_id = (int) $_SESSION['user_id'];
    $token   = trim($_POST['token']);

    $stmt = $conn->prepare("
        SELECT id 
        FROM qr_sessions 
        WHERE token = ?
        AND expires_at > NOW()
        LIMIT 1
    ");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        echo json_encode([
            "status" => "error",
            "message" => "QR tidak valid atau sudah kedaluwarsa"
        ]);
        exit;
    }

    $qr = $res->fetch_assoc();
    $qr_session_id = (int) $qr['id'];

    $stmt = $conn->prepare("
        SELECT id 
        FROM attendance_logs
        WHERE user_id = ?
        AND qr_session_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("ii", $user_id, $qr_session_id);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode([
            "status" => "error",
            "message" => "Kamu sudah absen"
        ]);
        exit;
    }

    $stmt = $conn->prepare("
        INSERT INTO attendance_logs (user_id, qr_session_id)
        VALUES (?, ?)
    ");
    $stmt->bind_param("ii", $user_id, $qr_session_id);
    $stmt->execute();

    echo json_encode([
        "status" => "success",
        "message" => "Absensi berhasil dicatat"
    ]);
    exit;

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Server error"
        // debug sengaja DIHAPUS
    ]);
    exit;
}
