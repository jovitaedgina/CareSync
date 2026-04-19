<?php
require_once __DIR__ . '/../includes/config.php';

// 1. Bersihkan session PHP
$_SESSION = [];
session_destroy();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keluar dari CareSync...</title>
</head>
<body>
    <script>
        // 2. Hapus token sesuai dengan key di app.js
        localStorage.removeItem('em_token');
        localStorage.removeItem('em_user');
        
        // 3. Gunakan replace agar tidak bisa di-back
        window.location.replace('<?= BASE_URL ?>/pages/login.php');
    </script>
</body>
</html>