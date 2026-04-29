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
$orderId = (int) ($payload['order_id'] ?? 0);
$userId = (int) (currentUser()['id'] ?? 0);

try {
    $order = confirmMarketplacePayment($pdo, $userId, $orderId);

    echo json_encode([
        'status' => 'success',
        'message' => 'Pembayaran digital berhasil dikonfirmasi.',
        'data' => $order,
    ]);
} catch (Throwable $e) {
    http_response_code(422);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
    ]);
}