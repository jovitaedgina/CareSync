<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
require_once '../../includes/config.php';

$data = json_decode(file_get_contents("php://input"), true);
$nama = trim($data['nama'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$gender = !empty($data['gender']) ? trim($data['gender']) : null;
$dob = !empty($data['dob']) ? trim($data['dob']) : null;
$phone = !empty($data['phone']) ? trim($data['phone']) : null;
$otp = trim($data['otp'] ?? '');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Misal variabel ID user barunya adalah $newUserId
$_SESSION['user_id'] = $newUserId;
$_SESSION['role']    = 'user';

if (empty($nama) || empty($email) || empty($password) || empty($otp)) {
    echo json_encode(["status" => "error", "message" => "Semua data dan OTP wajib diisi!"]);
    exit;
}

try {
    // 1. Cek OTP di tabel otp_requests
    $stmt = $pdo->prepare("SELECT otp_code, otp_expiry FROM otp_requests WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $otpData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$otpData) {
        echo json_encode(["status" => "error", "message" => "Sesi OTP tidak ditemukan atau email tidak valid!"]);
        exit;
    }

    $currentTime = date('Y-m-d H:i:s');
    if ($currentTime > $otpData['otp_expiry']) {
        echo json_encode(["status" => "error", "message" => "Kode OTP sudah kadaluarsa! Silakan minta ulang."]);
        exit;
    }

    if ($otp !== $otpData['otp_code']) {
        echo json_encode(["status" => "error", "message" => "Kode OTP salah!"]);
        exit;
    }

    // 2. OTP Benar -> Masukkan ke tabel users
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $insertUser = $pdo->prepare("INSERT INTO users (nama, email, password, gender, dob, phone, role) VALUES (:nama, :email, :password, :gender, :dob, :phone, 'user') RETURNING id");
    $insertUser->execute([
        ':nama' => $nama,
        ':email' => $email,
        ':password' => $hashedPassword,
        ':gender' => $gender,
        ':dob' => $dob,
        ':phone' => $phone
    ]);
    
    $newUserId = $insertUser->fetchColumn();

    // 3. Bersihkan tabel otp_requests agar rapi
    $pdo->prepare("DELETE FROM otp_requests WHERE email = :email")->execute([':email' => $email]);

    // 4. Buat Token & Auto Login
    $token = bin2hex(random_bytes(16));
    echo json_encode([
        "status" => "success",
        "message" => "Pendaftaran berhasil!",
        "data" => [
            "token" => $token,
            "user" => [
                "id" => $newUserId,
                "name" => $nama,
                "email" => $email,
                "role" => "user"
            ]
        ]
    ]);

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>