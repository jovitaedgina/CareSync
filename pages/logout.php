<?php
require_once __DIR__ . '/../includes/config.php';

clearAuthCookie();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keluar dari CareSync...</title>
</head>
<body>
    <script>
        // Hapus token frontend lalu paksa ke halaman login
        localStorage.removeItem('em_token');
        localStorage.removeItem('em_user');

        window.location.replace('<?= BASE_URL ?>/pages/login.php');
    </script>
</body>
</html>
