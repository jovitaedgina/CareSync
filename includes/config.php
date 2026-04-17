<?php
define('BASE_URL', 'http://localhost/caresync');
define('API_URL',  'http://localhost/caresync/api');
define('APP_NAME', 'CareSync');

// Mulai session kalau belum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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
