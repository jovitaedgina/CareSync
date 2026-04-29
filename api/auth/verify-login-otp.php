<?php
require_once '../../includes/config.php';

$data = jsonInput();
$email = normalizeEmail($data['email'] ?? '');
$otp = trim($data['otp'] ?? '');

if ($email === '' || $otp === '') {
    jsonResponse(['status' => 'error', 'message' => 'Email dan OTP wajib diisi!'], 422);
}

if (!isValidEmail($email) || !isValidOtp($otp)) {
    jsonResponse(['status' => 'error', 'message' => 'Email atau OTP tidak valid.'], 422);
}

try {
    $stmt = $pdo->prepare('SELECT id, nama, email, role, otp_code, otp_expiry FROM users WHERE email = :email');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if (!$user) {
        writeAuditLog($pdo, 'auth.login.otp', 'failed', null, 'users', $email, [
            'email' => $email,
            'reason' => 'email_not_found',
        ]);
        jsonResponse(['status' => 'error', 'message' => 'Email tidak valid!'], 404);
    }

    $currentTime = date('Y-m-d H:i:s');
    if (empty($user['otp_code'])) {
        jsonResponse(['status' => 'error', 'message' => 'Kode OTP tidak ditemukan, silakan minta kode baru.'], 422);
    }

    if ($currentTime > $user['otp_expiry']) {
        writeAuditLog($pdo, 'auth.login.otp', 'failed', (int) $user['id'], 'users', (string) $user['id'], [
            'email' => $email,
            'reason' => 'otp_expired',
        ]);
        jsonResponse(['status' => 'error', 'message' => 'Kode OTP sudah kadaluarsa! Silakan kirim ulang.'], 422);
    }

    if (!hash_equals((string) $user['otp_code'], $otp)) {
        writeAuditLog($pdo, 'auth.login.otp', 'failed', (int) $user['id'], 'users', (string) $user['id'], [
            'email' => $email,
            'reason' => 'otp_mismatch',
        ]);
        jsonResponse(['status' => 'error', 'message' => 'Kode OTP salah! Coba lagi.'], 401);
    }

    $clearStmt = $pdo->prepare('UPDATE users SET otp_code = NULL, otp_expiry = NULL WHERE id = :id');
    $clearStmt->execute([':id' => $user['id']]);

    $authUser = [
        'id' => (int) $user['id'],
        'name' => $user['nama'],
        'email' => $user['email'],
        'role' => $user['role'] ?? 'user',
    ];
    $token = issueAuthToken($authUser);
    writeAuditLog($pdo, 'auth.login.otp', 'success', (int) $user['id'], 'users', (string) $user['id'], [
        'email' => $email,
    ]);

    jsonResponse([
        'status' => 'success',
        'message' => 'Login berhasil',
        'data' => [
            'token' => $token,
            'user' => $authUser,
        ],
    ]);
} catch (Throwable $e) {
    writeAuditLog($pdo, 'auth.login.otp', 'failed', null, 'users', $email, [
        'email' => $email,
        'reason' => 'server_error',
    ]);
    handleServerException($e, 'Verifikasi OTP gagal diproses.');
}
