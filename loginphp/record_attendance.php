<?php
date_default_timezone_set('Asia/Jakarta');
header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/../database/db.php'; // $conn

// simple logger (file berada di folder loginphp, hapus/disable setelah debug)
function log_debug($s) {
    @file_put_contents(__DIR__ . '/debug_att.log', date('[Y-m-d H:i:s] ') . $s . PHP_EOL, FILE_APPEND);
}

try {
    // 1. login check
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["status" => "error", "message" => "User belum login"]);
        exit;
    }
    $user_id = (int) $_SESSION['user_id'];

    // 2. get token
    $token = $_POST['token'] ?? null;
    if (!$token) {
        echo json_encode(["status" => "error", "message" => "QR token tidak dikirim"]);
        exit;
    }
    $token = trim($token);

    // 3. validate token using PHP time (safe)
    $now = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("
        SELECT id, expires_at
        FROM qr_sessions
        WHERE token = ?
          AND expires_at > ?
        LIMIT 1
    ");
    if (!$stmt) {
        log_debug("Prepare failed (select qr_session): " . $conn->error);
        throw new Exception("DB error");
    }
    $stmt->bind_param("ss", $token, $now);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "QR tidak valid atau sudah kedaluwarsa"]);
        exit;
    }

    $qr = $res->fetch_assoc();
    $qr_session_id = (int) $qr['id'];

    // 4. check duplicate attendance
    $stmt = $conn->prepare("
        SELECT id FROM attendance_logs
        WHERE user_id = ? AND qr_session_id = ?
        LIMIT 1
    ");
    if (!$stmt) {
        log_debug("Prepare failed (check duplicate): " . $conn->error);
        throw new Exception("DB error");
    }
    $stmt->bind_param("ii", $user_id, $qr_session_id);
    $stmt->execute();
    $check = $stmt->get_result();

    if ($check->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Kamu sudah melakukan absensi"]);
        exit;
    }

    // 5. insert attendance — USE CORRECT COLUMN NAMES (qr_session_id, user_id)
    $stmt = $conn->prepare("
        INSERT INTO attendance_logs (qr_session_id, user_id, ip, user_agent)
        VALUES (?, ?, ?, ?)
    ");
    if (!$stmt) {
        log_debug("Prepare failed (insert): " . $conn->error);
        throw new Exception("DB error");
    }
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;
    $stmt->bind_param("iiss", $qr_session_id, $user_id, $ip, $ua);
    $ok = $stmt->execute();
    if (!$ok) {
        // log MySQL error for debug
        log_debug("Insert failed: " . $stmt->error . " | token=" . $token);
        echo json_encode(["status" => "error", "message" => "Gagal menyimpan absensi"]);
        exit;
    }

    // 6. success
    echo json_encode(["status" => "success", "message" => "Absensi berhasil dicatat"]);
    exit;

} catch (Throwable $e) {
    // log details then return generic error
    log_debug("Exception: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Server error"]);
    exit;
}
