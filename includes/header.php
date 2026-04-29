<?php
// includes/header.php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/marketplace_helpers.php';

$headerUser = currentUser();
$headerUserName = htmlspecialchars($headerUser['name'] ?? $headerUser['nama'] ?? 'Pengguna');
$headerUserRole = $headerUser['role'] ?? 'user';
$headerUserRoleLabel = $headerUserRole === 'user' ? 'Pasien' : ucfirst($headerUserRole);
$headerCartCount = getMarketplaceCartCount();
$headerUserPhoto = '';

if (isLoggedIn()) {
    ensureUserProfilePhotoSchema($pdo);
    $headerPhotoStmt = $pdo->prepare('SELECT profile_photo FROM users WHERE id = :id LIMIT 1');
    $headerPhotoStmt->execute([':id' => (int) ($headerUser['id'] ?? 0)]);
    $headerUserPhoto = getUserProfilePhotoUrl((string) $headerPhotoStmt->fetchColumn());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php if (isLoggedIn()): ?>
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <?php endif; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= (isset($pageTitle)) ? $pageTitle : APP_NAME . ' — Koneksi Kesehatan Terpadu'; ?></title>
    
    <link rel="icon" type="image/png" href="https://cdn-icons-png.flaticon.com/512/2382/2382461.png">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        primary: '#1D4ED8',
                        primaryLight: '#EFF6FF',
                        accent: '#10B981',
                        dark: '#0F172A',
                        textSoft: '#64748B'
                    },
                    boxShadow: {
                        'soft': '0 10px 40px -10px rgba(0,0,0,0.06)',
                        'floating': '0 20px 40px -15px rgba(29, 78, 216, 0.25)',
                    }
                }
            }
        }
    </script>
    
    <style>
        [x-cloak] { display: none !important; }
        .smooth-transition { transition: all 0.3s ease-in-out; }
        /* Sembunyikan scrollbar bawaan di dropdown notifikasi */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    
    <?php if (isLoggedIn()): ?>
    <script>
        window.onpageshow = function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        };
    </script>
    <?php endif; ?>

    <?= (isset($extraHead)) ? $extraHead : ''; ?>
</head>
<body class="bg-slate-50 text-dark antialiased">

<nav class="bg-white border-b border-slate-100 sticky top-0 z-50 shadow-sm" x-data="{ mobMenu: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            
            <div class="flex items-center gap-10">
                <a href="<?= BASE_URL ?>/pages/dashboard.php" class="flex items-center gap-2.5 no-underline group flex-shrink-0">
                    <div class="flex items-center justify-center w-11 h-11 bg-primary rounded-xl group-hover:scale-105 group-hover:rotate-3 smooth-transition shadow-soft">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.0002 21.35L10.5502 20.03C5.4002 15.36 2.0002 12.28 2.0002 8.5C2.0002 5.42 4.4202 3 7.5002 3C9.2402 3 10.9102 3.81 12.0002 5.09C13.0902 3.81 14.7602 3 16.5002 3C19.5802 3 22.0002 5.42 22.0002 8.5C22.0002 12.28 18.6002 15.36 13.4502 20.04L12.0002 21.35Z" fill="white"/>
                            <path d="M12 17L14 15M12 17L10 15M12 17V11M8 11V13M16 11V13" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="12" cy="17" r="6" stroke="#10B981" stroke-width="2"/>
                        </svg>
                    </div>
                    <span class="text-2xl font-extrabold text-dark tracking-tighter m-0">
                        Care<span class="text-primary">Sync</span>
                    </span>
                </a>

                <div class="hidden md:flex items-center gap-1">
                    <?php
                    $navs = [
                        ['name' => 'Beranda', 'url' => '/pages/dashboard.php', 'icon' => 'fa-house', 'id' => 'dashboard'],
                        ['name' => 'Konsultasi', 'url' => '/pages/booking.php', 'icon' => 'fa-stethoscope', 'id' => 'booking'],
                        ['name' => 'Apotek', 'url' => '/pages/marketplace.php', 'icon' => 'fa-pills', 'id' => 'marketplace'],
                        ['name' => 'Riwayat', 'url' => '/pages/history.php', 'icon' => 'fa-clock-rotate-left', 'id' => 'history'],
                    ];
                    foreach ($navs as $nav):
                        $isActive = (isset($currentPage) && $currentPage == $nav['id']);
                    ?>
                    <a href="<?= BASE_URL . $nav['url'] ?>" class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl text-sm font-bold no-underline smooth-transition 
                        <?= $isActive ? 'bg-primaryLight text-primary' : 'text-slate-600 hover:bg-slate-50 hover:text-dark' ?>">
                        <i class="fa-solid <?= $nav['icon'] ?> text-xs <?= $isActive ? 'text-primary' : 'text-slate-400 group-hover:text-primary' ?>"></i>
                        <?= $nav['name'] ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="flex items-center gap-4">
                
                <a href="<?= BASE_URL ?>/pages/cart.php" class="relative w-11 h-11 flex items-center justify-center bg-slate-50 rounded-xl text-slate-500 hover:bg-slate-100 hover:text-primary smooth-transition no-underline border border-slate-200">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span id="cart-count" class="absolute -top-1.5 -right-1.5 bg-accent text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full <?= $headerCartCount > 0 ? '' : 'hidden' ?>"><?= $headerCartCount ?></span>
                </a>

                <div class="relative" x-data="{ openNotif: false }" @click.away="openNotif = false">
                    <button @click="openNotif = !openNotif" class="relative w-11 h-11 flex items-center justify-center bg-slate-50 rounded-xl text-slate-500 hover:bg-slate-100 hover:text-primary smooth-transition border border-slate-200 cursor-pointer">
                        <i class="fa-solid fa-bell"></i>
                        <span class="absolute top-2.5 right-3.5 w-2.5 h-2.5 bg-red-500 border-2 border-slate-50 rounded-full" x-show="!openNotif"></span>
                    </button>
                    
                    <div x-show="openNotif" x-cloak x-transition.origin.top.right class="absolute right-0 mt-3 w-80 sm:w-96 bg-white rounded-2xl shadow-floating border border-slate-100 overflow-hidden z-50">
                        <div class="p-4 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
                            <h4 class="font-extrabold text-sm text-dark m-0">Notifikasi</h4>
                            <button class="text-[10px] font-bold text-primary hover:text-blue-800 bg-transparent border-none cursor-pointer smooth-transition">Tandai dibaca</button>
                        </div>
                        
                        <div class="max-h-80 overflow-y-auto no-scrollbar">
                            <a href="#" class="flex gap-4 p-4 border-b border-slate-50 hover:bg-slate-50 smooth-transition no-underline group cursor-pointer">
                                <div class="w-10 h-10 rounded-full bg-blue-50 text-primary flex items-center justify-center flex-shrink-0 group-hover:scale-110 smooth-transition mt-1">
                                    <i class="fa-solid fa-calendar-check"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-dark m-0 mb-1 leading-tight group-hover:text-primary smooth-transition">Pengingat Konsultasi</p>
                                    <p class="text-xs text-textSoft m-0 leading-relaxed">Jadwal Anda dengan dr. Susanti Wulandari besok pukul 10:00 WIB.</p>
                                    <span class="text-[10px] font-semibold text-slate-400 mt-2 block">10 menit yang lalu</span>
                                </div>
                            </a>
                            
                            <a href="<?= BASE_URL ?>/pages/history.php" class="flex gap-4 p-4 border-b border-slate-50 hover:bg-slate-50 smooth-transition no-underline group cursor-pointer">
                                <div class="w-10 h-10 rounded-full bg-emerald-50 text-accent flex items-center justify-center flex-shrink-0 group-hover:scale-110 smooth-transition mt-1">
                                    <i class="fa-solid fa-pills"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-dark m-0 mb-1 leading-tight group-hover:text-primary smooth-transition">Resep Diproses</p>
                                    <p class="text-xs text-textSoft m-0 leading-relaxed">Pesanan obat Anda (RP/CSYNC/012345) sedang disiapkan oleh apoteker.</p>
                                    <span class="text-[10px] font-semibold text-slate-400 mt-2 block">2 jam yang lalu</span>
                                </div>
                            </a>
                        </div>
                        
                        <div class="p-3 text-center border-t border-slate-50 bg-slate-50/50">
                            <a href="#" class="text-xs font-bold text-slate-500 hover:text-primary no-underline smooth-transition">Lihat Semua Notifikasi</a>
                        </div>
                    </div>
                </div>

                <div class="hidden md:block relative" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open" class="flex items-center gap-3 p-1.5 pr-3 bg-slate-50 rounded-full border border-slate-200 hover:bg-slate-100 smooth-transition cursor-pointer">
                        <?php if ($headerUserPhoto !== ''): ?>
                        <img src="<?= htmlspecialchars($headerUserPhoto) ?>" alt="Foto Profil" class="w-9 h-9 rounded-full object-cover border-2 border-white shadow-sm">
                        <?php else: ?>
                        <div class="w-9 h-9 rounded-full bg-primary text-white border-2 border-white shadow-sm flex items-center justify-center text-xs font-extrabold">
                            <?= htmlspecialchars(getMarketplaceProfileInitials((string) ($headerUser['name'] ?? $headerUser['nama'] ?? 'Pengguna'))) ?>
                        </div>
                        <?php endif; ?>
                        <div class="text-left">
                            <span class="block text-xs font-bold text-dark m-0 leading-tight"><?= $headerUserName ?></span>
                            <span class="block text-[11px] font-medium text-textSoft m-0"><?= htmlspecialchars($headerUserRoleLabel) ?></span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-xs text-slate-400 ml-1 smooth-transition" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-cloak x-transition.origin.top.right class="absolute right-0 mt-3 w-48 bg-white rounded-2xl shadow-floating border border-slate-100 p-2 z-50">
                        <a href="<?= BASE_URL ?>/pages/profile.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-dark no-underline smooth-transition">
                            <i class="fa-solid fa-user-circle w-4"></i> Profil Saya
                        </a>
                        <?php if (userCanManageMarketplace($headerUser)): ?>
                        <a href="<?= BASE_URL ?>/pages/pharmacy_management.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-dark no-underline smooth-transition">
                            <i class="fa-solid fa-warehouse w-4"></i> Manajemen Apotek
                        </a>
                        <?php endif; ?>
                        <a href="<?= BASE_URL ?>/pages/logout.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-red-600 hover:bg-red-50 no-underline smooth-transition">
                            <i class="fa-solid fa-right-from-bracket w-4"></i> Keluar
                        </a>
                    </div>
                </div>

                <button @click="openMob()" class="md:hidden flex-none w-11 h-11 flex items-center justify-center bg-slate-50 rounded-xl text-dark border border-slate-200 cursor-pointer">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
            
        </div>
    </div>
</nav>
