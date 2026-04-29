<?php
header('Content-Type: application/json; charset=UTF-8');

require_once '../../../includes/config.php';
require_once '../../../includes/marketplace_helpers.php';

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Sesi login berakhir.']);
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true);
$method = trim((string) ($payload['payment_method'] ?? ''));
$userId = (int) (currentUser()['id'] ?? 0);

try {
    $payment = createMarketplacePendingPayment($pdo, $userId, $method);

    echo json_encode([
        'status' => 'success',
        'message' => 'Transaksi pembayaran berhasil dibuat.',
        'data' => $payment,
    ]);
} catch (Throwable $e) {
    http_response_code(422);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
    ]);
}