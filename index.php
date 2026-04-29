<?php
require_once __DIR__ . '/includes/config.php';
header('Location: ' . BASE_URL . (isLoggedIn() ? dashboardPathForRole(currentUser()['role'] ?? 'user') : '/pages/login.php'));
exit;
