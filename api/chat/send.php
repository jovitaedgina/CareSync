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
$consultationId = (int) ($payload['consultation_id'] ?? $payload['idKonsultasi'] ?? 0);
$messageBody = trim((string) ($payload['message'] ?? $payload['isiPesan'] ?? ''));
$user = currentUser();
$userId = (int) ($user['id'] ?? 0);

if ($consultationId <= 0 || $userId <= 0 || $messageBody === '') {
    http_response_code(422);
    echo json_encode([
        'status' => 'error',
        'message' => 'Pesan tidak valid.',
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

if (isConsultationClosed($consultation)) {
    http_response_code(422);
    echo json_encode([
        'status' => 'error',
        'message' => 'Sesi konsultasi sudah selesai. Chat tidak bisa dikirim lagi.',
    ]);
    exit;
}

if (!empty($consultation['chat_expired'])) {
    http_response_code(422);
    echo json_encode([
        'status' => 'error',
        'message' => $consultation['chat_retention_notice'] ?? 'Riwayat chat konsultasi ini sudah melewati masa simpan 24 jam.',
    ]);
    exit;
}

try {
    $insert = $pdo->prepare(
        'INSERT INTO PesanChat (idKonsultasi, idPengirim, isiPesan, waktu)
         VALUES (:idKonsultasi, :idPengirim, :isiPesan, CURRENT_TIMESTAMP)
         RETURNING idPesan'
    );
    $insert->execute([
        ':idKonsultasi' => $consultationId,
        ':idPengirim' => $userId,
        ':isiPesan' => $messageBody,
    ]);

    $messageId = (int) $insert->fetchColumn();
    $messages = getConsultationMessages($pdo, $consultationId, max(0, $messageId - 1));

    echo json_encode([
        'status' => 'success',
        'message' => 'Pesan berhasil dikirim.',
        'data' => $messages[0] ?? null,
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Pesan gagal dikirim.',
    ]);
}
