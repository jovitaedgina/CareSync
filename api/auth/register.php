<?php
require_once '../../includes/config.php';

$data = jsonInput();
$nama = sanitizeText($data['nama'] ?? '', 100);
$email = normalizeEmail($data['email'] ?? '');
$password = $data['password'] ?? '';
$gender = !empty($data['gender']) ? sanitizeText($data['gender'], 20) : null;
$dob = !empty($data['dob']) ? trim((string) $data['dob']) : null;
$phone = !empty($data['phone']) ? preg_replace('/[^\d+]/', '', (string) $data['phone']) : null;
$otp = trim($data['otp'] ?? '');

if ($nama === '' || $email === '' || $password === '' || $otp === '') {
    jsonResponse(['status' => 'error', 'message' => 'Semua data dan OTP wajib diisi!'], 422);
}

if (!isValidEmail($email)) {
    jsonResponse(['status' => 'error', 'message' => 'Format email tidak valid.'], 422);
}

if (!isValidOtp($otp)) {
    jsonResponse(['status' => 'error', 'message' => 'Format OTP tidak valid.'], 422);
}

$passwordError = validatePasswordStrength($password);
if ($passwordError !== null) {
    jsonResponse(['status' => 'error', 'message' => $passwordError], 422);
}

try {
    $stmt = $pdo->prepare('SELECT otp_code, otp_expiry FROM otp_requests WHERE email = :email');
    $stmt->execute([':email' => $email]);
    $otpData = $stmt->fetch();

    if (!$otpData) {
        writeAuditLog($pdo, 'auth.register', 'failed', null, 'users', $email, [
            'email' => $email,
            'reason' => 'otp_session_missing',
        ]);
        jsonResponse(['status' => 'error', 'message' => 'Sesi OTP tidak ditemukan atau email tidak valid!'], 422);
    }

    $currentTime = date('Y-m-d H:i:s');
    if ($currentTime > $otpData['otp_expiry']) {
        writeAuditLog($pdo, 'auth.register', 'failed', null, 'users', $email, [
            'email' => $email,
            'reason' => 'otp_expired',
        ]);
        jsonResponse(['status' => 'error', 'message' => 'Kode OTP sudah kadaluarsa! Silakan minta ulang.'], 422);
    }

    if ($otp !== $otpData['otp_code']) {
        writeAuditLog($pdo, 'auth.register', 'failed', null, 'users', $email, [
            'email' => $email,
            'reason' => 'otp_mismatch',
        ]);
        jsonResponse(['status' => 'error', 'message' => 'Kode OTP salah!'], 422);
    }

    $existsStmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
    $existsStmt->execute([':email' => $email]);
    if ($existsStmt->fetchColumn()) {
        jsonResponse(['status' => 'error', 'message' => 'Email sudah terdaftar! Silakan login.'], 409);
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $insertUser = $pdo->prepare(
        "INSERT INTO users (nama, email, password, gender, dob, phone, role)
         VALUES (:nama, :email, :password, :gender, :dob, :phone, 'user')
         RETURNING id"
    );
    $insertUser->execute([
        ':nama' => $nama,
        ':email' => $email,
        ':password' => $hashedPassword,
        ':gender' => $gender,
        ':dob' => $dob,
        ':phone' => $phone,
    ]);
    $newUserId = (int) $insertUser->fetchColumn();

    $pdo->prepare('DELETE FROM otp_requests WHERE email = :email')->execute([':email' => $email]);

    $authUser = [
        'id' => $newUserId,
        'name' => $nama,
        'email' => $email,
        'role' => 'user',
    ];
    $token = issueAuthToken($authUser);
    writeAuditLog($pdo, 'auth.register', 'success', $newUserId, 'users', (string) $newUserId, [
        'email' => $email,
    ]);

    jsonResponse([
        'status' => 'success',
        'message' => 'Pendaftaran berhasil!',
        'data' => [
            'token' => $token,
            'user' => $authUser,
        ],
    ], 201);
} catch (Throwable $e) {
    writeAuditLog($pdo, 'auth.register', 'failed', null, 'users', $email, [
        'email' => $email,
        'reason' => 'server_error',
    ]);
    handleServerException($e, 'Pendaftaran gagal diproses.');
}
