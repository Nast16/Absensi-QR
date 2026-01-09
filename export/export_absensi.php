<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dosen') {
    die("Akses ditolak");
}

require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$qr_session_id = $_GET['qr_id'] ?? null;

if (!$qr_session_id) {
    die("QR Session tidak ditemukan");
}


// ==========================
// AMBIL DATA ABSENSI
// ==========================
$stmt = $conn->prepare("
    SELECT 
        u.fullname,
        u.npm,
        a.timestamp
    FROM attendance_logs a
    JOIN users u ON a.user_id = u.id
    WHERE a.qr_session_id = ?
    ORDER BY u.fullname ASC
");
$stmt->bind_param("i", $qr_session_id);
$stmt->execute();
$result = $stmt->get_result();

// ==========================
// BUAT EXCEL
// ==========================
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Absensi");

// HEADER
$sheet->setCellValue('A1', 'No');
$sheet->setCellValue('B1', 'Nama Mahasiswa');
$sheet->setCellValue('C1', 'NPM');
$sheet->setCellValue('D1', 'Waktu Absensi');

$row = 2;
$no = 1;

while ($data = $result->fetch_assoc()) {
    $sheet->setCellValue("A{$row}", $no++);
    $sheet->setCellValue("B{$row}", $data['fullname']);
    $sheet->setCellValue("C{$row}", $data['npm']);
    $sheet->setCellValue("D{$row}", $data['timestamp']);
    $row++;
}

// AUTO WIDTH
foreach (range('A','D') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// ==========================
// DOWNLOAD FILE
// ==========================
$filename = "Absensi_QR_{$qr_session_id}.xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
