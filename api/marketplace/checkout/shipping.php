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
$courierId = trim((string) ($payload['courier_id'] ?? ''));
$userId = (int) (currentUser()['id'] ?? 0);

try {
    $context = setMarketplaceCheckoutShipping($pdo, $userId, $courierId);

    echo json_encode([
        'status' => 'success',
        'message' => 'Pengiriman berhasil dipilih.',
        'data' => $context,
    ]);
} catch (Throwable $e) {
    http_response_code(422);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
    ]);
}
