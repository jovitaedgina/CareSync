<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/marketplace_helpers.php';

requireLogin();

$pageTitle = 'Pengiriman & Ekspedisi - CareSync';
$currentPage = 'marketplace';
$userId = (int) (currentUser()['id'] ?? 0);
$checkout = getMarketplaceCheckoutContext($pdo, $userId);
$couriers = [];
$shippingProviderError = '';
$missingAddressFields = [];
$shippingMeta = [
    'is_live' => false,
    'warning' => '',
    'status' => 'idle',
    'status_label' => 'Menunggu',
    'debug_message' => '',
];

if ($checkout) {
    $missingAddressFields = $checkout['missing_address_fields'] ?? [];
    try {
        if (!$missingAddressFields) {
            $shippingMeta = getMarketplaceShippingOptions($checkout);
            $couriers = $shippingMeta['couriers'] ?? [];
        }
    } catch (Throwable $e) {
        $shippingProviderError = $e->getMessage();
    }
}

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
    .courier-radio:checked + div {
        border-color: #1D4ED8;
        background-color: #EFF6FF;
    }
    .courier-radio:checked + div .check-icon {
        opacity: 1;
        transform: scale(1);
    }
</style>
';

include __DIR__ . '/../includes/header.php';
?>

<div class="bg-slate-50 min-h-screen py-8" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (!$checkout): ?>
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-12 text-center">
            <i class="fa-solid fa-truck-fast text-5xl text-slate-200 mb-4"></i>
            <h1 class="text-2xl font-extrabold text-slate-900 m-0">Belum ada checkout aktif</h1>
            <p class="text-slate-500 font-medium mt-3 mb-6">Pilih produk dari keranjang terlebih dahulu sebelum menentukan pengiriman.</p>
            <a href="<?= BASE_URL ?>/pages/cart.php" class="inline-flex px-6 py-3 bg-primary text-white rounded-xl font-bold no-underline">Kembali ke Keranjang</a>
        </div>
        <?php else: ?>
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 m-0">Pengiriman</h1>
            <p class="text-slate-500 font-medium mt-2 text-sm">Pilih alamat dan metode pengiriman untuk pesanan apotek Anda.</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <div class="w-full lg:w-2/3 flex flex-col gap-6">
                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 sm:p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-extrabold text-lg text-slate-900 m-0 flex items-center gap-2">
                            <i class="fa-solid fa-location-dot text-primary"></i> Alamat Pengiriman
                        </h3>
                    </div>

                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1.5 h-full bg-primary"></div>
                        <div class="flex flex-wrap items-center gap-3 mb-2">
                            <span class="font-extrabold text-slate-900 text-base"><?= htmlspecialchars($checkout['address']['name']) ?></span>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider px-2 py-1 rounded-md bg-slate-200 text-slate-600">
                                <?= htmlspecialchars($checkout['address']['label']) ?>
                            </span>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider px-2 py-1 rounded-md bg-blue-50 text-primary border border-blue-100">
                                Zona <?= htmlspecialchars($checkout['address']['zone_label']) ?>
                            </span>
                        </div>
                        <div class="text-slate-600 text-sm font-medium mb-1"><?= htmlspecialchars($checkout['address']['phone']) ?></div>
                        <div class="text-slate-600 text-sm leading-relaxed mb-3"><?= htmlspecialchars($checkout['address']['address']) ?></div>
                        <div class="text-[11px] font-bold text-slate-400 mb-3">
                            <?= htmlspecialchars($checkout['address']['village'] ?: '-') ?> / <?= htmlspecialchars($checkout['address']['district'] ?: '-') ?> / <?= htmlspecialchars($checkout['address']['city'] ?: '-') ?> / <?= htmlspecialchars($checkout['address']['province'] ?: '-') ?> / <?= htmlspecialchars($checkout['address']['postal_code'] ?: '-') ?>
                        </div>
                        <div class="text-xs font-bold text-slate-400 flex items-start gap-1">
                            <i class="fa-solid fa-thumbtack mt-0.5"></i> Catatan: <?= htmlspecialchars($checkout['address']['note']) ?>
                        </div>
                        <a href="<?= BASE_URL ?>/pages/profile.php" class="inline-flex items-center gap-2 text-xs font-bold text-primary mt-4 no-underline">
                            <i class="fa-solid fa-pen"></i> Ubah alamat di profil
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 sm:p-8">
                    <h3 class="font-extrabold text-lg text-slate-900 mb-6 m-0 flex items-center gap-2">
                        <i class="fa-solid fa-box-open text-primary"></i> Barang yang Dibeli
                    </h3>

                    <div class="flex flex-col gap-4">
                        <?php foreach ($checkout['items'] as $item): ?>
                        <div class="flex gap-4 items-center pb-4 border-b border-slate-50 last:border-0 last:pb-0">
                            <div class="w-16 h-16 bg-slate-50 rounded-xl flex items-center justify-center flex-shrink-0 relative overflow-hidden border border-slate-100">
                                <img src="<?= htmlspecialchars($item['img']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="object-contain mix-blend-multiply h-full w-full p-2">
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-slate-900 text-sm m-0 line-clamp-1"><?= htmlspecialchars($item['name']) ?></h4>
                                <div class="text-xs text-slate-500 mt-1"><?= (int) $item['qty'] ?> Barang x Rp <?= number_format($item['price'], 0, ',', '.') ?></div>
                            </div>
                            <div class="font-extrabold text-slate-900 text-sm">
                                Rp <?= number_format($item['subtotal'], 0, ',', '.') ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 sm:p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-extrabold text-lg text-slate-900 m-0 flex items-center gap-2">
                            <i class="fa-solid fa-truck-fast text-primary"></i> Pilih Ekspedisi
                        </h3>
                        <span class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-lg">Total Berat: <?= (int) $checkout['total_weight'] ?> gr | Zona <?= htmlspecialchars($checkout['address']['zone_label']) ?></span>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <?php if (!$missingAddressFields): ?>
                        <?php
                            $statusTone = 'bg-slate-100 text-slate-600 border-slate-200';
                            if (($shippingMeta['status'] ?? '') === 'live') {
                                $statusTone = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                            } elseif (in_array(($shippingMeta['status'] ?? ''), ['invalid_prefix', 'address_not_specific'], true)) {
                                $statusTone = 'bg-amber-50 text-amber-700 border-amber-200';
                            } elseif (in_array(($shippingMeta['status'] ?? ''), ['missing_api_key', 'live_failed'], true)) {
                                $statusTone = 'bg-sky-50 text-sky-700 border-sky-200';
                            }
                        ?>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-1 rounded-full border <?= $statusTone ?>">
                            <?= htmlspecialchars($shippingMeta['status_label'] ?? 'Status Ongkir') ?>
                        </span>
                        <?php endif; ?>
                    </div>

                    <?php if (($shippingMeta['warning'] ?? '') !== '' && !$missingAddressFields): ?>
                    <div class="mb-5 bg-sky-50 border border-sky-200 text-sky-800 rounded-2xl p-4 text-sm font-semibold">
                        <?= htmlspecialchars($shippingMeta['warning']) ?>
                        <div class="mt-2 text-xs font-medium text-sky-700">Pilihan ekspedisi di bawah tetap bisa digunakan untuk melanjutkan checkout dengan ongkir simulasi.</div>
                    </div>
                    <?php endif; ?>

                    <?php if ($missingAddressFields): ?>
                    <div class="mb-5 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl p-4 text-sm font-semibold">
                        Lengkapi <?= htmlspecialchars(implode(', ', $missingAddressFields)) ?> di profil pasien agar ongkir live dan pilihan kurir bisa dimunculkan.
                        <a href="<?= BASE_URL ?>/pages/profile.php" class="inline-flex items-center gap-2 text-xs font-bold text-rose-700 underline ml-1">
                            Buka profil
                        </a>
                    </div>
                    <?php endif; ?>

                    <?php if ($shippingProviderError !== ''): ?>
                    <div class="mb-5 bg-amber-50 border border-amber-200 text-amber-700 rounded-2xl p-4 text-sm font-semibold">
                        <?= htmlspecialchars($shippingProviderError) ?>
                    </div>
                    <?php endif; ?>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php foreach ($couriers as $courier): ?>
                        <?php $selected = ($checkout['shipping']['id'] ?? '') === $courier['id']; ?>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="courier" value="<?= htmlspecialchars($courier['id']) ?>" class="peer courier-radio hidden" onchange="updateShippingCost(this)" <?= $selected ? 'checked' : '' ?>>
                            <div class="border-2 border-slate-100 rounded-2xl p-4 hover:border-blue-300 smooth-transition h-full flex flex-col relative overflow-hidden">
                                <div class="check-icon absolute top-4 right-4 w-6 h-6 bg-primary rounded-full text-white flex items-center justify-center opacity-0 scale-50 smooth-transition shadow-sm">
                                    <i class="fa-solid fa-check text-xs"></i>
                                </div>

                                <div class="w-10 h-10 rounded-full flex items-center justify-center mb-3 <?= htmlspecialchars($courier['color']) ?>">
                                    <i class="fa-solid <?= htmlspecialchars($courier['icon']) ?> text-sm"></i>
                                </div>
                                <h4 class="font-extrabold text-slate-900 text-[15px] m-0 mb-1"><?= htmlspecialchars($courier['name']) ?></h4>
                                <div class="text-xs font-semibold text-slate-500 mb-3 flex items-center gap-1">
                                    <i class="fa-regular fa-clock"></i> Estimasi: <?= htmlspecialchars($courier['etd']) ?>
                                </div>
                                <div class="text-[11px] font-bold text-slate-400 mb-2">
                                    <?= htmlspecialchars($courier['provider']) ?> | <?= htmlspecialchars($courier['service']) ?> | <?= (int) $courier['weight_kg'] ?> kg dihitung
                                </div>
                                <div class="mt-auto font-extrabold text-primary text-base">
                                    Rp <?= number_format($courier['price'], 0, ',', '.') ?>
                                </div>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>

                    <?php if (!$couriers && !$missingAddressFields): ?>
                    <div class="mt-5 text-sm text-slate-500 font-medium">
                        Belum ada layanan yang bisa ditampilkan untuk alamat ini.
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="w-full lg:w-1/3">
                <div class="bg-white rounded-[2rem] p-6 lg:p-8 border border-slate-100 shadow-sm sticky top-28">
                    <h3 class="font-extrabold text-lg text-slate-900 mb-6 m-0 border-b border-slate-100 pb-4">Ringkasan Belanja</h3>

                    <div class="flex flex-col gap-4 mb-6">
                        <div class="flex justify-between items-center text-sm font-medium text-slate-600">
                            <span>Total Harga Barang</span>
                            <span class="font-bold text-slate-800">Rp <?= number_format($checkout['subtotal'], 0, ',', '.') ?></span>
                        </div>
                        <div class="flex justify-between items-center text-sm font-medium text-slate-600">
                            <span>Total Ongkos Kirim</span>
                            <span id="summary-shipping" class="font-bold text-slate-800">Rp <?= number_format($checkout['shipping_price'], 0, ',', '.') ?></span>
                        </div>
                        <div class="flex justify-between items-center text-sm font-medium text-slate-600">
                            <span>Biaya Layanan & Asuransi</span>
                            <span class="font-bold text-slate-800">Rp <?= number_format($checkout['service_fee'], 0, ',', '.') ?></span>
                        </div>
                    </div>

                    <hr class="border-slate-100 border-dashed mb-6">

                    <div class="flex justify-between items-center mb-8">
                        <span class="font-extrabold text-slate-900">Total Tagihan</span>
                        <span id="summary-total" class="font-extrabold text-primary text-2xl tracking-tight">Rp <?= number_format($checkout['grand_total'], 0, ',', '.') ?></span>
                    </div>

                    <button onclick="proceedToPayment()" class="w-full bg-primary text-white border-none hover:bg-blue-800 py-4 rounded-2xl font-extrabold shadow-floating active:scale-95 smooth-transition flex items-center justify-center gap-2 cursor-pointer text-base">
                        Pilih Pembayaran <i class="fa-solid fa-shield-halved"></i>
                    </button>

                    <p class="text-[10px] text-center text-slate-400 mt-4 font-semibold">
                        <i class="fa-solid fa-lock text-slate-300"></i> Transaksi Anda dilindungi enkripsi SSL 256-bit.
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
const BASE_URL = '<?= BASE_URL ?>';
const subtotal = <?= (int) ($checkout['subtotal'] ?? 0) ?>;
const serviceFee = <?= (int) ($checkout['service_fee'] ?? 2000) ?>;

function formatRupiah(number) {
    return new Intl.NumberFormat('id-ID').format(number);
}

async function updateShippingCost(radioElement) {
    try {
        const response = await fetch(`${BASE_URL}/api/marketplace/checkout/shipping.php`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ courier_id: radioElement.value })
        });
        const result = await response.json();
        if (!response.ok || result.status !== 'success') {
            throw new Error(result.message || 'Kurir gagal dipilih.');
        }

        const shippingCost = Number(result.data.shipping_price || 0);
        const total = Number(result.data.grand_total || 0);
        document.getElementById('summary-shipping').textContent = `Rp ${formatRupiah(shippingCost)}`;
        document.getElementById('summary-total').textContent = `Rp ${formatRupiah(total)}`;
    } catch (error) {
        alert(error.message);
    }
}

function proceedToPayment() {
    const selectedCourier = document.querySelector('input[name="courier"]:checked');
    if (!selectedCourier) {
        alert('Mohon pilih metode pengiriman terlebih dahulu.');
        return;
    }
    window.location.href = '<?= BASE_URL ?>/pages/payment.php';
}

document.addEventListener('DOMContentLoaded', () => {
    const selectedCourier = document.querySelector('input[name="courier"]:checked');
    if (selectedCourier) {
        updateShippingCost(selectedCourier);
    }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
