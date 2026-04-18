<?php
define('BASE_URL', 'http://localhost/caresync');
define('API_URL',  'http://localhost/caresync/api');
define('APP_NAME', 'CareSync');

// Mulai session kalau belum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db_host = '127.0.0.1';
$db_port = '5432';
$db_name = 'caresync_db';
$db_user = 'postgres';
$db_pass = 'password';

try {
    $dsn = "pgsql:host=$db_host;port=$db_port;dbname=$db_name";
    $pdo = new PDO($dsn, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Koneksi Database Gagal: " . $e->getMessage());
}

// Helper: cek apakah user sudah login
function isLoggedIn(): bool {
    return isset($_SESSION['user_token']) && !empty($_SESSION['user_token']);
}

// Helper: redirect kalau belum login
function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/pages/login.php');
        exit;
    }
}

// Helper: redirect kalau sudah login
function redirectIfLoggedIn(): void {
    if (isLoggedIn()) {
        header('Location: ' . BASE_URL . '/pages/dashboard.php');
        exit;
    }
}

// Helper: ambil data user dari session
function currentUser(): array {
    return $_SESSION['user'] ?? [];
}
