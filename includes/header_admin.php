<?php
require_once __DIR__ . '/staff_portal_helpers.php';

requireRole('Admin');

$current_page = basename($_SERVER['PHP_SELF']);
$staffProfile = getStaffProfile($pdo, (int) (currentUser()['id'] ?? 0));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - CareSync</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased flex h-screen overflow-hidden">

    <aside class="w-64 bg-[#0f172a] shadow-xl flex flex-col hidden md:flex z-20 text-slate-300">
        <div class="h-16 flex items-center px-6 border-b border-slate-800 shrink-0 bg-[#0b1120]">
            <i class="fa-solid fa-notes-medical text-indigo-500 text-2xl mr-2"></i>
            <span class="text-xl font-bold text-white tracking-tight">Care<span class="text-indigo-500">Sync</span></span>
            <span class="ml-2 text-[10px] font-bold bg-indigo-500 text-white px-1.5 py-0.5 rounded uppercase">Admin</span>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <p class="px-4 text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-2">Menu Utama</p>
            <a href="dashboard.php" class="flex items-center px-4 py-3 rounded-xl transition <?= $current_page === 'dashboard.php' ? 'bg-indigo-600 text-white shadow-md' : 'hover:bg-slate-800 hover:text-white' ?>">
                <i class="fa-solid fa-chart-line w-6"></i>
                <span class="font-medium ml-3">Dashboard Overview</span>
            </a>

            <p class="px-4 text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-2 mt-6">Data Master</p>
            <a href="manage_staff.php" class="flex items-center px-4 py-3 rounded-xl transition <?= $current_page === 'manage_staff.php' ? 'bg-indigo-600 text-white shadow-md' : 'hover:bg-slate-800 hover:text-white' ?>">
                <i class="fa-solid fa-user-doctor w-6"></i>
                <span class="font-medium ml-3">Manajemen Staf</span>
            </a>
            <a href="patient_data.php" class="flex items-center px-4 py-3 rounded-xl transition <?= $current_page === 'patient_data.php' ? 'bg-indigo-600 text-white shadow-md' : 'hover:bg-slate-800 hover:text-white' ?>">
                <i class="fa-solid fa-users w-6"></i>
                <span class="font-medium ml-3">Database Pasien</span>
            </a>

            <p class="px-4 text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-2 mt-6">Laporan</p>
            <a href="reports.php" class="flex items-center px-4 py-3 rounded-xl transition <?= $current_page === 'reports.php' ? 'bg-indigo-600 text-white shadow-md' : 'hover:bg-slate-800 hover:text-white' ?>">
                <i class="fa-solid fa-file-invoice-dollar w-6"></i>
                <span class="font-medium ml-3">Laporan Keuangan</span>
            </a>
        </nav>

        <div class="p-4 border-t border-slate-800 shrink-0 bg-[#0b1120]">
            <a href="<?= BASE_URL ?>/pages/logout.php" class="flex items-center px-4 py-3 text-red-400 hover:bg-red-500/10 hover:text-red-300 rounded-xl transition font-bold group">
                <i class="fa-solid fa-right-from-bracket w-6 group-hover:-translate-x-1 transition-transform"></i>
                <span class="ml-3">Keluar Sistem</span>
            </a>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 z-10 shrink-0">
            <div class="text-slate-500 md:hidden">
                <i class="fa-solid fa-bars text-xl cursor-pointer"></i>
            </div>

            <div class="flex items-center space-x-4 ml-auto">
                <button class="text-slate-400 hover:text-indigo-600 transition relative">
                    <i class="fa-solid fa-bell text-xl"></i>
                </button>

                <div class="relative ml-2 pl-4 border-l border-slate-200" x-data="{ profileOpen: false }">
                    <button @click="profileOpen = !profileOpen" @click.away="profileOpen = false" class="flex items-center gap-3 focus:outline-none hover:bg-slate-50 py-1 px-2 rounded-lg transition">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold border border-indigo-200"><?= htmlspecialchars($staffProfile['initials']) ?></div>
                        <div class="hidden sm:flex flex-col text-left">
                            <span class="text-sm font-bold text-slate-800 leading-none"><?= htmlspecialchars($staffProfile['name']) ?></span>
                            <span class="text-[10px] text-slate-500 mt-1 leading-none">Administrator</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-xs text-slate-400 ml-1 transition-transform" :class="profileOpen ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="profileOpen" style="display: none;" class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-lg py-2 border border-slate-100 z-50">
                        <div class="px-4 py-2 mb-2 border-b border-slate-50">
                            <p class="text-xs text-slate-500">Login sebagai</p>
                            <p class="text-sm font-bold text-slate-900 truncate"><?= htmlspecialchars($staffProfile['email']) ?></p>
                        </div>
                        <a href="<?= BASE_URL ?>/pages/logout.php" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                            <i class="fa-solid fa-right-from-bracket w-5"></i> Keluar
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto bg-slate-50 relative">
