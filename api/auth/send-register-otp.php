<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
require_once '../../includes/config.php';
require_once '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data['email'] ?? '');
$nama = trim($data['nama'] ?? 'Calon Pengguna');

if (empty($email)) {
    echo json_encode(["status" => "error", "message" => "Email wajib diisi!"]);
    exit;
}

try {
    // 1. Pastikan email BELUM terdaftar di tabel users
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    if ($stmt->fetch()) {
        echo json_encode(["status" => "error", "message" => "Email sudah terdaftar! Silakan login."]);
        exit;
    }

    // 2. Generate OTP & Waktu Kadaluarsa
    $otp = sprintf("%06d", mt_rand(100000, 999999));
    $expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));

    // 3. Hapus request OTP lama (jika ada) lalu masukkan yang baru
    $pdo->prepare("DELETE FROM otp_requests WHERE email = :email")->execute([':email' => $email]);
    $insertStmt = $pdo->prepare("INSERT INTO otp_requests (email, otp_code, otp_expiry) VALUES (:email, :otp, :expiry)");
    $insertStmt->execute([':email' => $email, ':otp' => $otp, ':expiry' => $expiry]);

    // 4. Kirim Email via PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        
        $mail->Username   = 'caresync58@gmail.com'; 
        $mail->Password   = 'kqpxuldqwvsdinpp'; 

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('caresync58@gmail.com', 'CareSync App');
        $mail->addAddress($email, $nama);

        $mail->isHTML(true);
        $mail->Subject = 'Kode OTP Pendaftaran CareSync';
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; padding: 20px; background-color: #F8FAFC; border-radius: 12px;'>
                <h2 style='color: #10B981;'>Selamat datang, {$nama}!</h2>
                <p>Terima kasih telah mendaftar di CareSync. Berikut adalah kode OTP untuk memverifikasi email kamu:</p>
                <h1 style='background: #10B981; color: white; padding: 12px 24px; display: inline-block; border-radius: 8px; letter-spacing: 5px;'>{$otp}</h1>
                <p>Kode ini berlaku selama <strong>5 menit</strong>.</p>
            </div>
        ";

        $mail->send();
        echo json_encode(["status" => "success", "message" => "Kode OTP pendaftaran berhasil dikirim!"]);
} catch (Exception $e) {
        // Ini akan memunculkan alasan pasti kenapa Gmail menolak
        echo json_encode(["status" => "error", "message" => "Error: " . $mail->ErrorInfo]);
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>