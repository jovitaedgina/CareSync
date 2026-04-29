<?php
require_once '../../includes/config.php';
require_once '../../includes/marketplace_helpers.php';

if (!isLoggedIn()) {
    jsonResponse(['status' => 'error', 'message' => 'Sesi berakhir, silakan login kembali.'], 401);
}

$loggedInUser = currentUser();
$userId = (int) ($loggedInUser['id'] ?? 0);

if ($userId <= 0) {
    jsonResponse(['status' => 'error', 'message' => 'Token tidak valid, silakan login kembali.'], 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['status' => 'error', 'message' => 'Metode request tidak valid.'], 405);
}

if (!isset($_FILES['photo']) || !is_array($_FILES['photo'])) {
    jsonResponse(['status' => 'error', 'message' => 'File foto belum dipilih.'], 422);
}

$file = $_FILES['photo'];
if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    jsonResponse(['status' => 'error', 'message' => 'Upload foto gagal diproses.'], 422);
}

$tmpPath = (string) ($file['tmp_name'] ?? '');
if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
    jsonResponse(['status' => 'error', 'message' => 'File upload tidak valid.'], 422);
}

$imageInfo = @getimagesize($tmpPath);
if (!is_array($imageInfo)) {
    jsonResponse(['status' => 'error', 'message' => 'File harus berupa gambar yang valid.'], 422);
}

$mimeType = strtolower((string) ($imageInfo['mime'] ?? ''));
$allowedMimeTypes = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
];

if (!isset($allowedMimeTypes[$mimeType])) {
    jsonResponse(['status' => 'error', 'message' => 'Format foto harus JPG, PNG, atau WEBP.'], 422);
}

$maxBytes = 2 * 1024 * 1024;
if ((int) ($file['size'] ?? 0) > $maxBytes) {
    jsonResponse(['status' => 'error', 'message' => 'Ukuran foto maksimal 2 MB.'], 422);
}

ensureUserProfilePhotoSchema($pdo);

$uploadDir = dirname(__DIR__, 2) . '/uploads/profile_photos';
if (!is_dir($uploadDir) && !@mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
    jsonResponse(['status' => 'error', 'message' => 'Folder upload foto tidak dapat dibuat.'], 500);
}

try {
    $stmt = $pdo->prepare('SELECT profile_photo FROM users WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $userId]);
    $existingPhoto = trim((string) $stmt->fetchColumn());

    $extension = $allowedMimeTypes[$mimeType];
    $fileName = 'user_' . $userId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
    $targetRelativePath = 'uploads/profile_photos/' . $fileName;
    $targetAbsolutePath = $uploadDir . '/' . $fileName;

    if (!move_uploaded_file($tmpPath, $targetAbsolutePath)) {
        throw new RuntimeException('File foto gagal disimpan ke server.');
    }

    $updateStmt = $pdo->prepare('UPDATE users SET profile_photo = :profile_photo WHERE id = :id');
    $updateStmt->execute([
        ':profile_photo' => $targetRelativePath,
        ':id' => $userId,
    ]);

    if ($existingPhoto !== '' && !preg_match('#^https?://#i', $existingPhoto)) {
        $oldAbsolutePath = dirname(__DIR__, 2) . '/' . ltrim(str_replace('\\', '/', $existingPhoto), '/');
        if (is_file($oldAbsolutePath) && realpath(dirname($oldAbsolutePath)) === realpath($uploadDir)) {
            @unlink($oldAbsolutePath);
        }
    }

    jsonResponse([
        'status' => 'success',
        'message' => 'Foto profil berhasil diperbarui.',
        'photo_url' => getUserProfilePhotoUrl($targetRelativePath),
    ]);
} catch (Throwable $e) {
    handleServerException($e, 'Foto profil gagal diperbarui.');
}
