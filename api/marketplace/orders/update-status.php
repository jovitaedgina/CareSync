<?php
header('Content-Type: application/json; charset=UTF-8');

require_once '../../../includes/config.php';
require_once '../../../includes/marketplace_helpers.php';

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Sesi login berakhir.']);
    exit;
}

$user = currentUser();
if (!userCanManageMarketplace($user)) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Anda tidak memiliki akses untuk mengelola pesanan apotek.']);
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true);
$orderId = (int) ($payload['order_id'] ?? 0);
$status = trim((string) ($payload['status'] ?? ''));

try {
    $order = updateMarketplaceOrderShippingStatus($pdo, $orderId, $status);

    echo json_encode([
        'status' => 'success',
        'message' => 'Status pengiriman berhasil diperbarui.',
        'data' => $order,
    ]);
} catch (Throwable $e) {
    http_response_code(422);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
    ]);
}
