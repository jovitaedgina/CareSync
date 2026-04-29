<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/marketplace_helpers.php';

requireLogin();

$pageTitle = 'Lacak Pesanan - CareSync';
$currentPage = 'history';
$userId = (int) (currentUser()['id'] ?? 0);
$orderId = (int) ($_GET['order_id'] ?? 0);
$orderDetail = $orderId > 0 ? getMarketplaceOrderDetail($pdo, $userId, $orderId) : null;

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
                boxShadow: { floating: \'0 20px 40px -15px rgba(29, 78, 216, 0.15)\' }
            }
        }
    }
</script>
<style>
    body { background-color: #F8FAFC; margin: 0; }
    .smooth-transition { transition: all 0.3s ease-in-out; }
    .timeline-line {
        position: absolute; top: 16px; bottom: 16px; left: 15px;
        width: 2px; background: #E2E8F0; z-index: 0;
    }
</style>
';

include __DIR__ . '/../includes/header.php';
?>

<div class="bg-slate-50 min-h-screen py-8" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (!$orderDetail): ?>
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-12 text-center">
            <i class="fa-solid fa-box-open text-5xl text-slate-200 mb-4"></i>
            <h1 class="text-2xl font-extrabold text-slate-900 m-0">Order apotek tidak ditemukan</h1>
            <p class="text-slate-500 font-medium mt-3 mb-6">Pesanan mungkin belum dibuat atau bukan milik akun pasien ini.</p>
            <a href="<?= BASE_URL ?>/pages/marketplace.php" class="inline-flex px-6 py-3 bg-primary text-white rounded-xl font-bold no-underline">Kembali ke Marketplace</a>
        </div>
        <?php else: ?>
        <nav class="flex text-sm font-medium text-slate-500 mb-8 gap-2 items-center">
            <a href="<?= BASE_URL ?>/pages/dashboard.php" class="hover:text-primary smooth-transition no-underline text-slate-500">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <a href="<?= BASE_URL ?>/pages/marketplace.php" class="hover:text-primary smooth-transition no-underline text-slate-500">Apotek</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-slate-800 font-bold truncate">Lacak Pesanan</span>
        </nav>

        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8 mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 m-0 mb-2">Lacak Pesanan</h1>
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="text-sm font-bold text-slate-600"><?= htmlspecialchars($orderDetail['order_code']) ?></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                    <span class="text-xs font-semibold text-slate-500"><?= htmlspecialchars($orderDetail['date_label']) ?></span>
                </div>
            </div>
            <div class="bg-blue-50 border border-blue-200 text-primary px-4 py-2 rounded-xl flex items-center gap-2 font-extrabold text-sm shadow-sm">
                <i class="fa-solid fa-truck-fast"></i> <?= htmlspecialchars($orderDetail['shipping_status']) ?>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_360px] gap-8 items-start">
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8 relative">
                <h3 class="font-extrabold text-lg text-slate-900 mb-8 m-0 border-b border-slate-100 pb-4">Riwayat Pengiriman</h3>

                <div class="relative">
                    <div class="timeline-line"></div>
                    <div class="flex flex-col gap-6 relative z-10">
                        <?php foreach ($orderDetail['tracking_history'] as $track): ?>
                        <div class="flex gap-4 items-start group">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 border-4 border-white shadow-sm text-primary bg-primaryLight">
                                <i class="fa-solid fa-check text-[10px]"></i>
                            </div>
                            <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 flex-1 group-hover:bg-slate-100 smooth-transition">
                                <h4 class="font-bold text-slate-800 text-sm m-0 mb-1"><?= htmlspecialchars($track['status']) ?></h4>
                                <p class="text-[11px] font-semibold text-slate-400 m-0">
                                    <i class="fa-regular fa-calendar mr-1"></i> <?= htmlspecialchars($track['date']) ?>
                                    <i class="fa-regular fa-clock ml-2 mr-1"></i> <?= htmlspecialchars($track['time']) ?>
                                    <?php if (!empty($track['location'])): ?>
                                    <span class="ml-2">| <?= htmlspecialchars($track['location']) ?></span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="w-full flex flex-col gap-6">
                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6">
                    <h3 class="font-extrabold text-base text-slate-900 mb-4 m-0 flex items-center gap-2">
                        <i class="fa-solid fa-box text-primary"></i> Informasi Kurir & Pembayaran
                    </h3>
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 space-y-4">
                        <div>
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Kurir</div>
                            <div class="font-extrabold text-slate-800 text-sm"><?= htmlspecialchars($orderDetail['courier']) ?></div>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">No. Resi</div>
                            <div class="font-extrabold text-slate-900 text-[15px] tracking-wider"><?= htmlspecialchars($orderDetail['awb']) ?></div>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Pembayaran</div>
                            <div class="font-extrabold text-slate-900 text-sm"><?= htmlspecialchars($orderDetail['payment_method']) ?> | <?= htmlspecialchars($orderDetail['payment_status']) ?></div>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total</div>
                            <div class="font-extrabold text-primary text-lg">Rp <?= number_format($orderDetail['total'], 0, ',', '.') ?></div>
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
                            <div class="font-bold text-slate-900 text-sm mb-1"><?= htmlspecialchars($orderDetail['receiver']) ?></div>
                            <div class="text-[11px] text-slate-400 font-bold mb-1"><?= htmlspecialchars($orderDetail['address_label'] ?? 'Rumah') ?></div>
                            <div class="text-xs text-slate-500 font-medium leading-relaxed"><?= htmlspecialchars($orderDetail['address']) ?></div>
                            <div class="text-xs text-slate-400 font-semibold mt-2"><?= htmlspecialchars($orderDetail['phone']) ?></div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6">
                    <h3 class="font-extrabold text-base text-slate-900 mb-4 m-0 flex items-center gap-2">
                        <i class="fa-solid fa-pills text-primary"></i> Item Pesanan
                    </h3>
                    <div class="flex flex-col gap-3">
                        <?php foreach ($orderDetail['items'] as $item): ?>
                        <div class="flex gap-3 items-center">
                            <img src="<?= htmlspecialchars($item['img']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-14 h-14 rounded-xl object-contain bg-slate-50 border border-slate-100 p-2">
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-sm text-slate-900 truncate"><?= htmlspecialchars($item['name']) ?></div>
                                <div class="text-xs text-slate-500"><?= (int) $item['qty'] ?> x Rp <?= number_format($item['price'], 0, ',', '.') ?></div>
                            </div>
                            <div class="font-extrabold text-sm text-slate-900">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <a href="<?= BASE_URL ?>/pages/marketplace.php" class="w-full bg-slate-100 text-slate-600 border-none hover:bg-slate-200 py-3.5 rounded-2xl font-extrabold active:scale-95 smooth-transition flex items-center justify-center gap-2 cursor-pointer text-sm no-underline">
                    Kembali ke Marketplace
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
