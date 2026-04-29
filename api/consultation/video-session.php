<?php
header('Access-Control-Allow-Origin: *');
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

$payload = json_decode(file_get_contents('php://input'), true);
$consultationId = (int) ($payload['idKonsultasi'] ?? $payload['consultation_id'] ?? 0);
$action = strtolower(trim((string) ($payload['action'] ?? 'start')));
$user = currentUser();
$userId = (int) ($user['id'] ?? 0);

if ($consultationId <= 0) {
    http_response_code(422);
    echo json_encode([
        'status' => 'error',
        'message' => 'ID konsultasi wajib dikirim.',
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

if (isConsultationClosed($consultation) && in_array($action, ['start', 'end'], true)) {
    http_response_code(422);
    echo json_encode([
        'status' => 'error',
        'message' => 'Sesi konsultasi ini sudah selesai dan tidak bisa diubah lagi.',
    ]);
    exit;
}

if ($action === 'start') {
    if (!userHasRole('Dokter', $user)) {
        http_response_code(403);
        echo json_encode([
            'status' => 'error',
            'message' => 'Hanya dokter yang dapat memulai video call.',
        ]);
        exit;
    }
    markConsultationAsStarted($pdo, $consultationId);
    ensureConsultationLifecycleMessage(
        $pdo,
        $consultationId,
        (int) ($consultation['doctor_user_id'] ?? 0),
        'SESI KONSULTASI DIMULAI (' . date('H:i') . ' WIB)'
    );
} elseif ($action === 'end') {
    if (!userHasRole('Dokter', $user)) {
        http_response_code(403);
        echo json_encode([
            'status' => 'error',
            'message' => 'Hanya dokter yang dapat mengakhiri video call.',
        ]);
        exit;
    }
    markConsultationVideoEnded($pdo, $consultationId);
    ensureConsultationLifecycleMessage(
        $pdo,
        $consultationId,
        (int) ($consultation['doctor_user_id'] ?? 0),
        'VIDEO CALL SELESAI (' . date('H:i') . ' WIB). Diagnosis sekarang bisa diisi.'
    );
}

$updatedConsultation = findAccessibleConsultation($pdo, $consultationId, $userId) ?? $consultation;

echo json_encode([
    'status' => 'success',
    'message' => $action === 'start'
        ? 'Status video call berhasil diperbarui.'
        : ($action === 'end' ? 'Video call selesai dan diagnosis sudah bisa diisi.' : 'Status video call berhasil diambil.'),
    'data' => [
        'consultation' => $updatedConsultation,
    ],
]);
