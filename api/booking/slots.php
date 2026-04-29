<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

require_once '../../includes/config.php';
require_once '../../includes/booking_helpers.php';

if (!isLoggedIn()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Sesi berakhir, silakan login kembali.',
    ]);
    exit;
}

$doctorId = (int) ($_GET['doctor_id'] ?? 0);
$date = trim($_GET['date'] ?? '');

if ($doctorId <= 0 || $date === '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Dokter dan tanggal wajib dipilih.',
    ]);
    exit;
}

$doctor = findBookingDoctor($pdo, $doctorId);
if (!$doctor) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Dokter tidak ditemukan.',
    ]);
    exit;
}

$slots = getAvailableBookingSlots($pdo, $doctorId, $date);

echo json_encode([
    'status' => 'success',
    'data' => [
        'doctor' => $doctor,
        'slots' => $slots,
    ],
]);
