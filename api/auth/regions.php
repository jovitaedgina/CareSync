<?php
header('Content-Type: application/json; charset=UTF-8');

require_once '../../includes/config.php';
require_once '../../includes/marketplace_helpers.php';

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'Sesi berakhir, silakan login kembali.',
    ]);
    exit;
}

if (BINDERBYTE_API_KEY === '') {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'API key Binderbyte belum diatur.',
    ]);
    exit;
}

$level = strtolower(trim((string) ($_GET['level'] ?? 'provinces')));
$parentId = trim((string) ($_GET['parent_id'] ?? ''));

if ($level === 'postal_code') {
    $province = trim((string) ($_GET['province'] ?? ''));
    $city = trim((string) ($_GET['city'] ?? ''));
    $district = trim((string) ($_GET['district'] ?? ''));
    $village = trim((string) ($_GET['village'] ?? ''));

    if (RAJAONGKIR_API_KEY === '') {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'API key RajaOngkir belum diatur.',
        ]);
        exit;
    }

    if ($province === '' || $city === '') {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Provinsi dan kota wajib diisi untuk mencari kode pos.',
        ]);
        exit;
    }

    try {
        $matchedLocation = searchRajaOngkirCity($city, $province, $district, $village);
        $postalCode = trim((string) ($matchedLocation['zip_code'] ?? ''));

        echo json_encode([
            'status' => 'success',
            'postal_code' => $postalCode,
            'location' => $matchedLocation,
        ]);
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage(),
        ]);
    }
    exit;
}

$endpoints = [
    'provinces' => [
        'url' => 'https://api.binderbyte.com/wilayah/provinsi?api_key=' . rawurlencode(BINDERBYTE_API_KEY),
        'parent_key' => null,
    ],
    'cities' => [
        'url' => 'https://api.binderbyte.com/wilayah/kabupaten?api_key=' . rawurlencode(BINDERBYTE_API_KEY),
        'parent_key' => 'id_provinsi',
    ],
    'districts' => [
        'url' => 'https://api.binderbyte.com/wilayah/kecamatan?api_key=' . rawurlencode(BINDERBYTE_API_KEY),
        'parent_key' => 'id_kabupaten',
    ],
    'villages' => [
        'url' => 'https://api.binderbyte.com/wilayah/kelurahan?api_key=' . rawurlencode(BINDERBYTE_API_KEY),
        'parent_key' => 'id_kecamatan',
    ],
];

if (!isset($endpoints[$level])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Level wilayah tidak valid.',
    ]);
    exit;
}

$config = $endpoints[$level];
$url = $config['url'];

if ($config['parent_key'] !== null) {
    if ($parentId === '') {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'parent_id wajib diisi.',
        ]);
        exit;
    }

    $url .= '&' . $config['parent_key'] . '=' . rawurlencode($parentId);
}

try {
    $response = marketplaceHttpGetJson($url);
    $items = $response['data']['value'] ?? [];

    if (!is_array($items)) {
        throw new RuntimeException('Format data wilayah tidak valid.');
    }

    $normalized = [];
    foreach ($items as $item) {
        if (!is_array($item)) {
            continue;
        }

        $normalized[] = [
            'id' => (string) ($item['id'] ?? ''),
            'name' => trim((string) ($item['name'] ?? '')),
        ];
    }

    echo json_encode([
        'status' => 'success',
        'items' => array_values(array_filter($normalized, static function (array $item): bool {
            return $item['id'] !== '' && $item['name'] !== '';
        })),
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
    ]);
}
