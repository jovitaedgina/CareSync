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
$productId = (int) ($payload['product_id'] ?? 0);

if ($productId <= 0) {
    http_response_code(422);
    echo json_encode(['status' => 'error', 'message' => 'Produk tidak valid.']);
    exit;
}

$result = removeMarketplaceCartItem($productId);

echo json_encode([
    'status' => 'success',
    'message' => 'Produk berhasil dihapus dari keranjang.',
    'data' => $result,
]);
