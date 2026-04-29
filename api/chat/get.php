<?php
header('Content-Type: application/json; charset=UTF-8');

require_once '../../includes/config.php';
require_once '../../includes/booking_helpers.php';

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'Sesi berakhir, silakan login kembali.',
    ]);
    exit;
}

$userId = (int) (currentUser()['id'] ?? 0);
$consultationId = (int) ($_GET['consultation_id'] ?? $_GET['id_konsultasi'] ?? 0);
$lastId = (int) ($_GET['last_id'] ?? 0);

if ($consultationId <= 0) {
    http_response_code(422);
    echo json_encode([
        'status' => 'error',
        'message' => 'ID sesi konsultasi diperlukan.',
    ]);
    exit;
}

$consultation = findAccessibleConsultation($pdo, $consultationId, $userId);
if (!$consultation) {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'message' => 'Anda tidak memiliki akses ke sesi konsultasi ini.',
    ]);
    exit;
}

echo json_encode([
    'status' => 'success',
    'data' => [
        'consultation' => $consultation,
        'messages' => getConsultationMessages($pdo, $consultationId, max(0, $lastId)),
    ],
]);
