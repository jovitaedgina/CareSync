<?php

function loadEnvFile(string $path): void
{
    static $loaded = [];

    if (isset($loaded[$path]) || !is_file($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = array_map('trim', explode('=', $line, 2));
        $value = trim($value, "\"'");

        if ($key !== '' && getenv($key) === false) {
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
        }
    }

    $loaded[$path] = true;
}

loadEnvFile(dirname(__DIR__) . '/.env');

define('BASE_URL', 'http://localhost/caresync');
define('API_URL', 'http://localhost/caresync/api');
define('APP_NAME', 'CareSync');
define('JITSI_BASE_URL', rtrim((string) (getenv('JITSI_BASE_URL') ?: 'https://meet.jit.si'), '/'));
define('JITSI_ROOM_PREFIX', trim((string) (getenv('JITSI_ROOM_PREFIX') ?: 'caresync-consultation')));
define('CARESYNC_JWT_SECRET', (string) (getenv('CARESYNC_JWT_SECRET') ?: 'caresync-local-secret'));
define('MARKETPLACE_SHIPPING_PROVIDER', strtolower(trim((string) (getenv('MARKETPLACE_SHIPPING_PROVIDER') ?: 'binderbyte'))));
define('BINDERBYTE_API_KEY', trim((string) (getenv('BINDERBYTE_API_KEY') ?: '')));
define('RAJAONGKIR_API_KEY', trim((string) (getenv('RAJAONGKIR_API_KEY') ?: '')));
define('MARKETPLACE_ORIGIN_CITY', trim((string) (getenv('MARKETPLACE_ORIGIN_CITY') ?: 'Tasikmalaya')));
define('MARKETPLACE_ORIGIN_PROVINCE', trim((string) (getenv('MARKETPLACE_ORIGIN_PROVINCE') ?: 'Jawa Barat')));
define('MARKETPLACE_ORIGIN_DISTRICT', trim((string) (getenv('MARKETPLACE_ORIGIN_DISTRICT') ?: '')));
define('MARKETPLACE_ORIGIN_BINDERBYTE_PREFIX', trim((string) (getenv('MARKETPLACE_ORIGIN_BINDERBYTE_PREFIX') ?: '')));

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
    die('Koneksi Database Gagal: ' . $e->getMessage());
}

function jsonInput(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || trim($raw) === '') {
        return [];
    }

    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function jsonResponse(array $payload, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($payload);
    exit;
}

function sanitizeText(?string $value, int $maxLength = 255): string
{
    $value = trim((string) $value);
    $value = preg_replace('/\s+/', ' ', $value) ?? '';

    if ($maxLength > 0) {
        $value = mb_substr($value, 0, $maxLength);
    }

    return $value;
}

function normalizeEmail(?string $email): string
{
    return strtolower(trim((string) $email));
}

function isValidEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function isValidOtp(string $otp): bool
{
    return preg_match('/^\d{6}$/', $otp) === 1;
}

function validatePasswordStrength(string $password): ?string
{
    if (strlen($password) < 8) {
        return 'Password minimal 8 karakter.';
    }

    if (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/\d/', $password)) {
        return 'Password harus mengandung huruf besar, huruf kecil, dan angka.';
    }

    return null;
}

function clearAuthCookie(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            (bool) $params['secure'],
            (bool) $params['httponly']
        );
    }

    session_destroy();
}

function issueAuthToken(array $user): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    session_regenerate_id(true);

    $token = bin2hex(random_bytes(32));
    $_SESSION['user_token'] = $token;
    $_SESSION['user'] = [
        'id' => (int) ($user['id'] ?? 0),
        'name' => (string) ($user['name'] ?? ''),
        'email' => (string) ($user['email'] ?? ''),
        'role' => (string) ($user['role'] ?? 'user'),
    ];

    return $token;
}

function isLoggedIn(): bool
{
    return !empty($_SESSION['user_token']) && !empty($_SESSION['user']);
}

function currentUser(): array
{
    return $_SESSION['user'] ?? [];
}

function normalizeUserRole(?string $role): string
{
    return strtolower(trim((string) $role));
}

function dashboardPathForRole(?string $role): string
{
    return match (normalizeUserRole($role)) {
        'admin' => '/pages/admin/dashboard.php',
        'apoteker', 'pharmacist' => '/pages/apoteker/dashboard.php',
        'dokter', 'doctor' => '/pages/dokter/dashboard.php',
        default => '/pages/dashboard.php',
    };
}

function redirectToRoleDashboard(?string $role = null): void
{
    $role = $role ?? (currentUser()['role'] ?? 'user');
    header('Location: ' . BASE_URL . dashboardPathForRole($role));
    exit;
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/pages/login.php');
        exit;
    }
}

function redirectIfLoggedIn(): void
{
    if (isLoggedIn()) {
        redirectToRoleDashboard();
    }
}

function userHasRole(string|array $roles, ?array $user = null): bool
{
    $user = $user ?? currentUser();
    $currentRole = normalizeUserRole($user['role'] ?? '');
    $roles = is_array($roles) ? $roles : [$roles];

    foreach ($roles as $role) {
        if ($currentRole === normalizeUserRole($role)) {
            return true;
        }
    }

    return false;
}

function requireRole(string|array $roles): void
{
    requireLogin();

    if (!userHasRole($roles)) {
        redirectToRoleDashboard();
    }
}

function writeAuditLog(
    PDO $pdo,
    string $eventType,
    string $status = 'success',
    ?int $userId = null,
    ?string $entityType = null,
    ?string $entityId = null,
    array $metadata = []
): void {
    try {
        $stmt = $pdo->prepare(
            'INSERT INTO audit_logs (user_id, event_type, entity_type, entity_id, status, ip_address, user_agent, metadata)
             VALUES (:user_id, :event_type, :entity_type, :entity_id, :status, :ip_address, :user_agent, :metadata)'
        );
        $stmt->execute([
            ':user_id' => $userId,
            ':event_type' => $eventType,
            ':entity_type' => $entityType,
            ':entity_id' => $entityId,
            ':status' => $status,
            ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            ':metadata' => !empty($metadata) ? json_encode($metadata) : null,
        ]);
    } catch (Throwable $e) {
    }
}

function handleServerException(Throwable $e, string $message = 'Terjadi kesalahan pada server.'): void
{
    error_log($e->getMessage());
    jsonResponse([
        'status' => 'error',
        'message' => $message,
    ], 500);
}
