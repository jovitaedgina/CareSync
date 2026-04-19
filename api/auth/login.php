<?php
// Path: api/auth/login.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../../includes/config.php';

// 1. Pastikan session dimulai (aman digunakan agar tidak bentrok jika di config.php sudah ada)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

// Validasi input kosong
if (empty($email) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Email dan password wajib diisi!"]);
    exit;
}

try {
    // Cari user berdasarkan email
    $stmt = $pdo->prepare("SELECT id, nama, email, password, role FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifikasi keberadaan user dan kecocokan password hasil hash
    if ($user && password_verify($password, $user['password'])) {
        // Buat token dummy untuk session frontend
        $token = bin2hex(random_bytes(16));

        // 2. ---> KUNCI UTAMA: SIMPAN DATA KE SESSION PHP SISI SERVER <---
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role']    = $user['role'];
        $_SESSION['nama']    = $user['nama'];
        // -----------------------------------------------------------------

        echo json_encode([
            "status" => "success",
            "message" => "Login berhasil",
            "data" => [
                "token" => $token,
                "user" => [
                    "id" => $user['id'],
                    "name" => $user['nama'],
                    "email" => $user['email'],
                    "role" => $user['role']
                ]
            ]
        ]);
    } else {
        // Jika email tidak ada atau password salah
        echo json_encode(["status" => "error", "message" => "Email atau password salah!"]);
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>