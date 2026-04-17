<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle   = 'Lacak Pesanan — CareSync';
$currentPage = 'history'; 

// Suntikkan Tailwind CSS
$extraHead = '
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: { sans: [\'"Plus Jakarta Sans"\', \'sans-serif\'] },
                colors: {
                    primary: \'#1D4ED8\', primaryLight: \'#EFF6FF\',
                    accent: \'#10B981\', dark: \'#0F172A\', textSoft: \'#64748B\'
                },
                boxShadow: { \'floating\': \'0 20px 40px -15px rgba(29, 78, 216, 0.15)\' }
            }
        }
    }
</script>
<style>
    body { background-color: #F8FAFC; margin: 0; }
    .smooth-transition { transition: all 0.3s ease-in-out; }
    
    /* Custom vertical timeline */
    .timeline-line {
        position: absolute; top: 16px; bottom: 16px; left: 15px;
        width: 2px; background: #E2E8F0; z-index: 0;
    }
</style>
';

include __DIR__ . '/../includes/header.php';

/* * MOCK DATA TRACKING */
$orderDetail = [
    'id' => 'EM-20260418-5521',
    'date' => '18 April 2026, 09:15 WIB',
    'status' => 'Sedang Dikirim', // Dibuat, Diproses, Dikirim, Selesai
    'courier' => 'GoSend Instant',
    'awb' => 'GOS-8829100291',
    'receiver' => 'Jovita Edgina',
    'address' => 'Jl. Siliwangi No. 123, Kahuripan, Tawang, Tasikmalaya'
];

$trackingHistory = [
    ['status' => 'Kurir sedang menuju alamat tujuan.', 'date' => '18 Apr 2026', 'time' => '11:30', 'done' => true, 'icon' => 'fa-motorcycle', 'color' => 'text-primary bg-primaryLight'],
    ['status' => 'Pesanan telah diserahkan kepada kurir.', 'date' => '18 Apr 2026', 'time' => '11:15', 'done' => true, 'icon' => 'fa-box', 'color' => 'text-slate-500 bg-slate-100'],
    ['status' => 'Pesanan sedang disiapkan oleh Apoteker.', 'date' => '18 Apr 2026', 'time' => '09:45', 'done' => true, 'icon' => 'fa-pills', 'color' => 'text-slate-500 bg-slate-100'],
    ['status' => 'Pembayaran berhasil diverifikasi.', 'date' => '18 Apr 2026', 'time' => '09:20', 'done' => true, 'icon' => 'fa-check-double', 'color' => 'text-slate-500 bg-slate-100'],
    ['status' => 'Pesanan dibuat.', 'date' => '18 Apr 2026', 'time' => '09:15', 'done' => true, 'icon' => 'fa-clipboard-list', 'color' => 'text-slate-500 bg-slate-100'],
];
?>

<div class="bg-slate-50 min-h-screen py-8" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <nav class="flex text-sm font-medium text-slate-500 mb-8 gap-2 items-center">
            <a href="<?= BASE_URL ?>/pages/dashboard.php" class="hover:text-primary smooth-transition no-underline text-slate-500">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <a href="<?= BASE_URL ?>/pages/history.php" class="hover:text-primary smooth-transition no-underline text-slate-500">Riwayat Pesanan</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-slate-800 font-bold truncate">Lacak Pesanan</span>
        </nav>

        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8 mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 m-0 mb-2">Lacak Pesanan</h1>
                <div class="flex items-center gap-3">
                    <span class="text-sm font-bold text-slate-600"><?= $orderDetail['id'] ?></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                    <span class="text-xs font-semibold text-slate-500"><?= $orderDetail['date'] ?></span>
                </div>
            </div>
            <div class="bg-blue-50 border border-blue-200 text-primary px-4 py-2 rounded-xl flex items-center gap-2 font-extrabold text-sm shadow-sm">
                <i class="fa-solid fa-truck-fast"></i> <?= $orderDetail['status'] ?>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8 items-start">
            
            <div class="w-full lg:w-3/5 bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8 relative">
                <h3 class="font-extrabold text-lg text-slate-900 mb-8 m-0 border-b border-slate-100 pb-4">Riwayat Pengiriman</h3>
                
                <div class="relative">
                    <div class="timeline-line"></div>
                    
                    <div class="flex flex-col gap-6 relative z-10">
                        <?php foreach ($trackingHistory as $idx => $track): ?>
                        <div class="flex gap-4 items-start group">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 border-4 border-white shadow-sm <?= $track['color'] ?>">
                                <i class="fa-solid <?= $track['icon'] ?> text-[10px]"></i>
                            </div>
                            <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 flex-1 group-hover:bg-slate-100 smooth-transition">
                                <h4 class="font-bold text-slate-800 text-sm m-0 mb-1"><?= $track['status'] ?></h4>
                                <p class="text-[11px] font-semibold text-slate-400 m-0">
                                    <i class="fa-regular fa-calendar mr-1"></i> <?= $track['date'] ?> &nbsp;
                                    <i class="fa-regular fa-clock ml-1 mr-1"></i> <?= $track['time'] ?>
                                </p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="w-full lg:w-2/5 flex flex-col gap-6">
                
                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6">
                    <h3 class="font-extrabold text-base text-slate-900 mb-4 m-0 flex items-center gap-2">
                        <i class="fa-solid fa-box text-primary"></i> Informasi Kurir
                    </h3>
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Kurir</div>
                        <div class="font-extrabold text-slate-800 text-sm mb-4"><?= $orderDetail['courier'] ?></div>
                        
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">No. Resi</div>
                        <div class="flex items-center justify-between bg-white border border-slate-200 rounded-lg p-2 px-3">
                            <span class="font-extrabold text-slate-900 text-[15px] tracking-wider"><?= $orderDetail['awb'] ?></span>
                            <button onclick="navigator.clipboard.writeText('<?= $orderDetail['awb'] ?>'); alert('Resi disalin!')" class="text-primary hover:text-blue-800 border-none bg-transparent cursor-pointer font-bold text-sm">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6">
                    <h3 class="font-extrabold text-base text-slate-900 mb-4 m-0 flex items-center gap-2">
                        <i class="fa-solid fa-location-dot text-primary"></i> Alamat Tujuan
                    </h3>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-50 text-primary flex items-center justify-center flex-shrink-0 mt-1">
                            <i class="fa-regular fa-user"></i>
                        </div>
                        <div>
                            <div class="font-bold text-slate-900 text-sm mb-1"><?= $orderDetail['receiver'] ?></div>
                            <div class="text-xs text-slate-500 font-medium leading-relaxed"><?= $orderDetail['address'] ?></div>
                        </div>
                    </div>
                </div>

                <a href="<?= BASE_URL ?>/pages/history.php" class="w-full bg-slate-100 text-slate-600 border-none hover:bg-slate-200 py-3.5 rounded-2xl font-extrabold active:scale-95 smooth-transition flex items-center justify-center gap-2 cursor-pointer text-sm no-underline">
                    Kembali ke Riwayat
                </a>
            </div>

        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>