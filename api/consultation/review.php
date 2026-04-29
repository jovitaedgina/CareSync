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

$payload = json_decode(file_get_contents('php://input'), true) ?: [];
$consultationId = (int) ($payload['consultation_id'] ?? 0);
$rating = (int) ($payload['rating'] ?? 0);
$userId = (int) (currentUser()['id'] ?? 0);

if ($consultationId <= 0 || $rating <= 0) {
    http_response_code(422);
    echo json_encode([
        'status' => 'error',
        'message' => 'Data review tidak valid.',
    ]);
    exit;
}

try {
    $consultation = saveConsultationRating($pdo, $consultationId, $userId, $rating);
    echo json_encode([
        'status' => 'success',
        'message' => 'Terima kasih, review berhasil disimpan.',
        'data' => [
            'consultation' => $consultation,
        ],
    ]);
} catch (Throwable $e) {
    http_response_code(422);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
    ]);
}
