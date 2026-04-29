<?php
require_once '../../includes/config.php';
require_once '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

$data = jsonInput();
$email = normalizeEmail($data['email'] ?? '');

if ($email === '') {
    jsonResponse(['status' => 'error', 'message' => 'Email wajib diisi!'], 422);
}

if (!isValidEmail($email)) {
    jsonResponse(['status' => 'error', 'message' => 'Format email tidak valid.'], 422);
}

try {
    $stmt = $pdo->prepare('SELECT id, nama FROM users WHERE email = :email');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();
    
    if (!$user) {
        writeAuditLog($pdo, 'auth.otp.send_login', 'failed', null, 'users', $email, [
            'email' => $email,
            'reason' => 'email_not_found',
        ]);
        jsonResponse(['status' => 'error', 'message' => 'Email tidak terdaftar di sistem!'], 404);
    }

    if (SMTP_HOST === '' || SMTP_USERNAME === '' || SMTP_PASSWORD === '' || SMTP_FROM_EMAIL === '') {
        jsonResponse(['status' => 'error', 'message' => 'Layanan email belum dikonfigurasi dengan aman di server.'], 503);
    }

    $otp = sprintf('%06d', random_int(100000, 999999));
    $expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));

    $updateStmt = $pdo->prepare('UPDATE users SET otp_code = :otp, otp_expiry = :expiry WHERE id = :id');
    $updateStmt->execute([
        ':otp' => $otp,
        ':expiry' => $expiry,
        ':id' => $user['id'],
    ]);

    $safeName = htmlspecialchars((string) $user['nama'], ENT_QUOTES, 'UTF-8');
    $safeOtp = htmlspecialchars($otp, ENT_QUOTES, 'UTF-8');

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USERNAME;
    $mail->Password = SMTP_PASSWORD;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = SMTP_PORT;
    $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
    $mail->addAddress($email, (string) $user['nama']);
    $mail->isHTML(true);
    $mail->Subject = 'Kode OTP Login CareSync';
    $mail->Body = "
        <div style='font-family: Arial, sans-serif; padding: 20px; color: #0F172A; background-color: #F8FAFC; border-radius: 12px;'>
            <h2 style='color: #1D4ED8;'>Halo, {$safeName}!</h2>
            <p>Seseorang mencoba masuk ke akun CareSync kamu. Berikut adalah kode OTP rahasiamu:</p>
            <h1 style='background: #1D4ED8; color: white; padding: 12px 24px; display: inline-block; border-radius: 8px; letter-spacing: 5px;'>{$safeOtp}</h1>
            <p>Kode ini hanya berlaku selama <strong>5 menit</strong>. Jangan berikan kode ini ke siapa pun, termasuk admin CareSync.</p>
        </div>
    ";
    $mail->send();

    writeAuditLog($pdo, 'auth.otp.send_login', 'success', (int) $user['id'], 'users', (string) $user['id'], [
        'email' => $email,
    ]);
    jsonResponse([
        'status' => 'success',
        'message' => 'Kode OTP berhasil dikirim ke email kamu!',
    ]);
} catch (Throwable $e) {
    writeAuditLog($pdo, 'auth.otp.send_login', 'failed', null, 'users', $email, [
        'email' => $email,
        'reason' => 'server_error',
    ]);
    handleServerException($e, 'Gagal mengirim OTP login.');
}
