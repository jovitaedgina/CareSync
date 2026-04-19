<?php
// Path: api/auth/verify-login-otp.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
require_once '../../includes/config.php';

$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data['email'] ?? '');
$otp = trim($data['otp'] ?? '');

if (empty($email) || empty($otp)) {
    echo json_encode(["status" => "error", "message" => "Email dan OTP wajib diisi!"]);
    exit;
}

try {
    // Ambil data user beserta OTP yang disimpan
    $stmt = $pdo->prepare("SELECT id, nama, email, role, otp_code, otp_expiry FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $currentTime = date('Y-m-d H:i:s');

        // Validasi 1: Apakah OTP kosong? (Artinya user belum minta OTP)
        if (empty($user['otp_code'])) {
            echo json_encode(["status" => "error", "message" => "Kode OTP tidak ditemukan, silakan minta kode baru."]);
            exit;
        }

        // Validasi 2: Apakah OTP sudah lewat 5 menit?
        if ($currentTime > $user['otp_expiry']) {
            echo json_encode(["status" => "error", "message" => "Kode OTP sudah kadaluarsa! Silakan kirim ulang."]);
            exit;
        }

        // Validasi 3: Apakah kodenya cocok?
        if ($otp === $user['otp_code']) {
            // Jika berhasil, HAPUS kode OTP dari database agar tidak bisa disalahgunakan lagi
            $clearStmt = $pdo->prepare("UPDATE users SET otp_code = NULL, otp_expiry = NULL WHERE id = :id");
            $clearStmt->execute([':id' => $user['id']]);

            // Buat token dummy untuk session
            $token = bin2hex(random_bytes(16));
            
            echo json_encode([
                "status" => "success",
                "message" => "Login berhasil",
                "data" => [
                    "token" => $token,
                    "user" => [
                        "id" => $user['id'],
                        "name" => $user['nama'],
                        "email" => $user['email'],
                        "role" => $user['role'] ?? 'user'
                    ]
                ]
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Kode OTP salah! Coba lagi."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Email tidak valid!"]);
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>