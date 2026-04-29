<?php
require_once '../../includes/config.php';

$data = jsonInput();
$email = normalizeEmail($data['email'] ?? '');
$password = $data['password'] ?? '';

if ($email === '' || $password === '') {
    jsonResponse(['status' => 'error', 'message' => 'Email dan password wajib diisi!'], 422);
}

if (!isValidEmail($email)) {
    jsonResponse(['status' => 'error', 'message' => 'Format email tidak valid.'], 422);
}

try {
    $stmt = $pdo->prepare('SELECT id, nama, email, password, role FROM users WHERE email = :email');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
            $rehashStmt = $pdo->prepare('UPDATE users SET password = :password WHERE id = :id');
            $rehashStmt->execute([
                ':password' => password_hash($password, PASSWORD_DEFAULT),
                ':id' => $user['id'],
            ]);
        }

        $authUser = [
            'id' => (int) $user['id'],
            'name' => $user['nama'],
            'email' => $user['email'],
            'role' => $user['role'] ?: 'user',
        ];
        $token = issueAuthToken($authUser);
        writeAuditLog($pdo, 'auth.login.password', 'success', (int) $user['id'], 'users', (string) $user['id'], [
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
    }

    writeAuditLog($pdo, 'auth.login.password', 'failed', null, 'users', $email, [
        'email' => $email,
        'reason' => 'invalid_credentials',
    ]);
    jsonResponse(['status' => 'error', 'message' => 'Email atau password salah!'], 401);
} catch (Throwable $e) {
    writeAuditLog($pdo, 'auth.login.password', 'failed', null, 'users', $email, [
        'email' => $email,
        'reason' => 'server_error',
    ]);
    handleServerException($e, 'Gagal memproses login.');
}
