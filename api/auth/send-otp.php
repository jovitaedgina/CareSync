<?php
// Path: api/auth/send-otp.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
require_once '../../includes/config.php';

// Memanggil library PHPMailer yang baru saja kamu install
require_once '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data['email'] ?? '');

if (empty($email)) {
    echo json_encode(["status" => "error", "message" => "Email wajib diisi!"]);
    exit;
}

try {
    // 1. Cek apakah email terdaftar di database
    $stmt = $pdo->prepare("SELECT id, nama FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // 2. Generate 6 digit angka acak & waktu kadaluarsa (5 menit)
        $otp = sprintf("%06d", mt_rand(100000, 999999));
        $expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        // 3. Simpan OTP dan waktu kadaluarsa ke database
        $updateStmt = $pdo->prepare("UPDATE users SET otp_code = :otp, otp_expiry = :expiry WHERE id = :id");
        $updateStmt->execute([
            ':otp' => $otp,
            ':expiry' => $expiry,
            ':id' => $user['id']
        ]);

        // 4. Proses Pengiriman Email
        $mail = new PHPMailer(true);
        try {
            // Konfigurasi Server SMTP Gmail
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            
            $mail->Username   = 'caresync58@gmail.com'; 
            $mail->Password   = 'kqpxuldqwvsdinpp';

            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Pengirim & Penerima
            $mail->setFrom('caresync58@gmail.com', 'CareSync App');
            $mail->addAddress($email, $user['nama']);

            // Konten Email (Bisa dikustomisasi desainnya)
            $mail->isHTML(true);
            $mail->Subject = 'Kode OTP Login CareSync';
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; padding: 20px; color: #0F172A; background-color: #F8FAFC; border-radius: 12px;'>
                    <h2 style='color: #1D4ED8;'>Halo, {$user['nama']}!</h2>
                    <p>Seseorang mencoba masuk ke akun CareSync kamu. Berikut adalah kode OTP rahasiamu:</p>
                    <h1 style='background: #1D4ED8; color: white; padding: 12px 24px; display: inline-block; border-radius: 8px; letter-spacing: 5px;'>{$otp}</h1>
                    <p>Kode ini hanya berlaku selama <strong>5 menit</strong>. Jangan berikan kode ini ke siapa pun, termasuk admin CareSync!</p>
                </div>
            ";

            $mail->send();
            echo json_encode([
                "status" => "success", 
                "message" => "Kode OTP berhasil dikirim ke email kamu!"
            ]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => "Gagal mengirim email. Pastikan koneksi internet stabil dan sandi aplikasi benar."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Email tidak terdaftar di sistem!"]);
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>