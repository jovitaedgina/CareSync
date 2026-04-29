<?php
require_once '../../includes/config.php';
require_once '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

$data = jsonInput();
$email = normalizeEmail($data['email'] ?? '');
$nama = sanitizeText($data['nama'] ?? 'Calon Pengguna', 100);

if ($email === '') {
    jsonResponse(['status' => 'error', 'message' => 'Email wajib diisi!'], 422);
}

if (!isValidEmail($email)) {
    jsonResponse(['status' => 'error', 'message' => 'Format email tidak valid.'], 422);
}

try {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
    $stmt->execute([':email' => $email]);
    if ($stmt->fetch()) {
        writeAuditLog($pdo, 'auth.otp.send_register', 'failed', null, 'users', $email, [
            'email' => $email,
            'reason' => 'email_already_registered',
        ]);
        jsonResponse(['status' => 'error', 'message' => 'Email sudah terdaftar! Silakan login.'], 409);
    }

    if (SMTP_HOST === '' || SMTP_USERNAME === '' || SMTP_PASSWORD === '' || SMTP_FROM_EMAIL === '') {
        jsonResponse(['status' => 'error', 'message' => 'Layanan email belum dikonfigurasi dengan aman di server.'], 503);
    }

    $otp = sprintf('%06d', random_int(100000, 999999));
    $expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));

    $pdo->prepare('DELETE FROM otp_requests WHERE email = :email')->execute([':email' => $email]);
    $insertStmt = $pdo->prepare('INSERT INTO otp_requests (email, otp_code, otp_expiry) VALUES (:email, :otp, :expiry)');
    $insertStmt->execute([':email' => $email, ':otp' => $otp, ':expiry' => $expiry]);

    $safeName = htmlspecialchars($nama, ENT_QUOTES, 'UTF-8');
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
    $mail->addAddress($email, $nama);
    $mail->isHTML(true);
    $mail->Subject = 'Kode OTP Pendaftaran CareSync';
    $mail->Body = "
        <div style='font-family: Arial, sans-serif; padding: 20px; background-color: #F8FAFC; border-radius: 12px;'>
            <h2 style='color: #10B981;'>Selamat datang, {$safeName}!</h2>
            <p>Terima kasih telah mendaftar di CareSync. Berikut adalah kode OTP untuk memverifikasi email kamu:</p>
            <h1 style='background: #10B981; color: white; padding: 12px 24px; display: inline-block; border-radius: 8px; letter-spacing: 5px;'>{$safeOtp}</h1>
            <p>Kode ini berlaku selama <strong>5 menit</strong>.</p>
        </div>
    ";
    $mail->send();

    writeAuditLog($pdo, 'auth.otp.send_register', 'success', null, 'otp_requests', $email, [
        'email' => $email,
    ]);
    jsonResponse(['status' => 'success', 'message' => 'Kode OTP pendaftaran berhasil dikirim!']);
} catch (Throwable $e) {
    writeAuditLog($pdo, 'auth.otp.send_register', 'failed', null, 'otp_requests', $email, [
        'email' => $email,
        'reason' => 'server_error',
    ]);
    handleServerException($e, 'Gagal mengirim OTP pendaftaran.');
}
