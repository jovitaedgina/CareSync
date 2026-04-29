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
    echo json_encode(['status' => 'error', 'message' => 'Anda tidak memiliki akses untuk mengelola stok obat.']);
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true);
$productId = (int) ($payload['product_id'] ?? 0);
$stock = isset($payload['stock']) ? (int) $payload['stock'] : -1;
$price = isset($payload['price']) && $payload['price'] !== '' ? (int) $payload['price'] : null;

try {
    $product = updateMarketplaceProductStock($pdo, $productId, $stock, $price);

    echo json_encode([
        'status' => 'success',
        'message' => 'Stok obat berhasil diperbarui.',
        'data' => $product,
    ]);
} catch (Throwable $e) {
    http_response_code(422);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
    ]);
}
