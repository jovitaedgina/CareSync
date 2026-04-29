<?php
require_once '../../includes/config.php';
require_once '../../includes/staff_portal_helpers.php';

requireRole('Dokter');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['status' => 'error', 'message' => 'Metode request tidak valid.'], 405);
}

$data = jsonInput();
$userId = (int) (currentUser()['id'] ?? 0);

if ($userId <= 0) {
    jsonResponse(['status' => 'error', 'message' => 'Sesi login tidak valid.'], 401);
}

$doctor = findDoctorByUserId($pdo, $userId);
if (!$doctor) {
    jsonResponse(['status' => 'error', 'message' => 'Profil dokter tidak ditemukan.'], 404);
}

$nama = sanitizeText($data['nama'] ?? '', 100);
$email = strtolower(trim((string) ($data['email'] ?? '')));
$phone = preg_replace('/[^\d+]/', '', (string) ($data['phone'] ?? ''));
$spesialisasi = sanitizeText($data['spesialisasi'] ?? '', 50);
$nomorStr = sanitizeText($data['nomor_str'] ?? '', 50);

if ($nama === '' || $email === '' || $phone === '' || $spesialisasi === '' || $nomorStr === '') {
    jsonResponse(['status' => 'error', 'message' => 'Semua field profil dokter wajib diisi.'], 422);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['status' => 'error', 'message' => 'Format email tidak valid.'], 422);
}

try {
    $emailCheckStmt = $pdo->prepare('SELECT id FROM users WHERE email = :email AND id <> :id LIMIT 1');
    $emailCheckStmt->execute([
        ':email' => $email,
        ':id' => $userId,
    ]);
    if ($emailCheckStmt->fetch()) {
        jsonResponse(['status' => 'error', 'message' => 'Email sudah digunakan akun lain.'], 409);
    }

    $licenseCheckStmt = $pdo->prepare('SELECT idDokter FROM Dokter WHERE nomorSTR = :nomor_str AND id_user <> :id_user LIMIT 1');
    $licenseCheckStmt->execute([
        ':nomor_str' => $nomorStr,
        ':id_user' => $userId,
    ]);
    if ($licenseCheckStmt->fetch()) {
        jsonResponse(['status' => 'error', 'message' => 'Nomor STR sudah digunakan dokter lain.'], 409);
    }

    $pdo->beginTransaction();

    $userStmt = $pdo->prepare(
        'UPDATE users
         SET nama = :nama, email = :email, phone = :phone
         WHERE id = :id'
    );
    $userStmt->execute([
        ':nama' => $nama,
        ':email' => $email,
        ':phone' => $phone,
        ':id' => $userId,
    ]);

    $doctorStmt = $pdo->prepare(
        'UPDATE Dokter
         SET spesialisasi = :spesialisasi, nomorSTR = :nomor_str
         WHERE id_user = :id_user'
    );
    $doctorStmt->execute([
        ':spesialisasi' => $spesialisasi,
        ':nomor_str' => $nomorStr,
        ':id_user' => $userId,
    ]);

    $pdo->commit();

    writeAuditLog($pdo, 'doctor.profile.update', 'success', $userId, 'users', (string) $userId, [
        'updated_fields' => ['nama', 'email', 'phone', 'spesialisasi', 'nomorSTR'],
    ]);

    jsonResponse([
        'status' => 'success',
        'message' => 'Profil dokter berhasil diperbarui.',
    ]);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    writeAuditLog($pdo, 'doctor.profile.update', 'failed', $userId, 'users', (string) $userId, [
        'reason' => 'server_error',
    ]);

    handleServerException($e, 'Profil dokter gagal diperbarui.');
}
