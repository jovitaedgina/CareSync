<?php
require_once __DIR__ . '/staff_portal_helpers.php';

requireRole('Dokter');

$current_page = basename($_SERVER['PHP_SELF']);
$staffProfile = getStaffProfile($pdo, (int) (currentUser()['id'] ?? 0));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokter Panel - CareSync</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased flex h-screen overflow-hidden">

    <aside class="w-64 bg-white shadow-lg border-r border-gray-200 flex flex-col hidden md:flex z-20">
        <div class="h-16 flex items-center px-6 border-b border-gray-100 shrink-0">
            <i class="fa-solid fa-notes-medical text-blue-600 text-2xl mr-2"></i>
            <span class="text-xl font-bold text-gray-900 tracking-tight">Care<span class="text-blue-600">Sync</span> <span class="text-xs text-gray-500 font-normal">Dokter</span></span>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <a href="dashboard.php" class="flex items-center px-4 py-3 rounded-xl transition <?= $current_page === 'dashboard.php' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' ?>">
                <i class="fa-solid fa-chart-pie w-6"></i>
                <span class="font-medium ml-3">Dashboard</span>
            </a>
            <a href="jadwal.php" class="flex items-center px-4 py-3 rounded-xl transition <?= $current_page === 'jadwal.php' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' ?>">
                <i class="fa-solid fa-calendar-week w-6"></i>
                <span class="font-medium ml-3">Jadwal Praktik</span>
            </a>
            <a href="consultation_room.php" class="flex items-center px-4 py-3 rounded-xl transition <?= $current_page === 'consultation_room.php' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' ?>">
                <i class="fa-solid fa-comment-medical w-6"></i>
                <span class="font-medium ml-3">Ruang Konsultasi</span>
            </a>
            <a href="patient_history.php" class="flex items-center px-4 py-3 rounded-xl transition <?= $current_page === 'patient_history.php' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' ?>">
                <i class="fa-solid fa-folder-open w-6"></i>
                <span class="font-medium ml-3">Rekam Medis</span>
            </a>
        </nav>

        <div class="p-4 border-t border-gray-100 shrink-0">
            <a href="<?= BASE_URL ?>/pages/logout.php" class="flex items-center px-4 py-3 text-red-500 hover:bg-red-50 hover:text-red-600 rounded-xl transition font-bold group">
                <i class="fa-solid fa-right-from-bracket w-6 group-hover:-translate-x-1 transition-transform"></i>
                <span class="ml-3">Keluar Sistem</span>
            </a>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 z-10 shrink-0">
            <div class="text-gray-500 md:hidden">
                <i class="fa-solid fa-bars text-xl cursor-pointer"></i>
            </div>

            <div class="flex items-center space-x-4 ml-auto">
                <button class="text-gray-400 hover:text-blue-600 transition relative">
                    <i class="fa-solid fa-bell text-xl"></i>
                </button>

                <div class="relative ml-2 pl-4 border-l border-gray-200" x-data="{ profileOpen: false }">
                    <button @click="profileOpen = !profileOpen" @click.away="profileOpen = false" class="flex items-center gap-3 focus:outline-none hover:bg-gray-50 py-1 px-2 rounded-lg transition">
                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold border border-blue-200 overflow-hidden">
                            <?php if (($staffProfile['photo_url'] ?? '') !== ''): ?>
                            <img src="<?= htmlspecialchars((string) $staffProfile['photo_url']) ?>" alt="Foto Profil" class="w-full h-full object-cover">
                            <?php else: ?>
                            <?= htmlspecialchars($staffProfile['initials']) ?>
                            <?php endif; ?>
                        </div>
                        <div class="hidden sm:flex flex-col text-left">
                            <span class="text-sm font-bold text-gray-800 leading-none"><?= htmlspecialchars($staffProfile['name']) ?></span>
                            <span class="text-[10px] text-gray-500 mt-1 leading-none"><?= htmlspecialchars($staffProfile['specialization'] !== '' ? $staffProfile['specialization'] : 'Dokter') ?></span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-xs text-gray-400 ml-1 transition-transform" :class="profileOpen ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="profileOpen"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-lg py-2 border border-gray-100 z-50" style="display: none;">

                        <div class="px-4 py-2 mb-2 border-b border-gray-50">
                            <p class="text-xs text-gray-500">Login sebagai</p>
                            <p class="text-sm font-bold text-gray-900 truncate"><?= htmlspecialchars($staffProfile['email']) ?></p>
                        </div>

                        <a href="profile.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                            <i class="fa-solid fa-user-gear w-5"></i> Pengaturan Profil
                        </a>

                        <div class="border-t border-gray-100 my-2"></div>

                        <a href="<?= BASE_URL ?>/pages/logout.php" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                            <i class="fa-solid fa-right-from-bracket w-5"></i> Keluar
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto bg-gray-50 relative">
