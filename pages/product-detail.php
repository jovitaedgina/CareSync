<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/marketplace_helpers.php';

requireLogin();

$pageTitle = 'Detail Produk - CareSync';
$currentPage = 'marketplace';
$productId = (int) ($_GET['id'] ?? 1);
$userId = (int) (currentUser()['id'] ?? 0);
$product = findMarketplaceProduct($pdo, $productId) ?? findMarketplaceProduct($pdo, 1);
$hasPrescription = $product ? userHasPrescriptionForProduct($pdo, $userId, $product) : false;
$prescriptionLocked = $product ? ($product['requires_prescription'] && !$hasPrescription) : false;

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
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
    @keyframes slideIn{from{opacity:0;transform:translateX(20px)}to{opacity:1;transform:translateX(0)}}
</style>
';

include __DIR__ . '/../includes/header.php';
?>

<?php if (!$product): ?>
<div class="max-w-4xl mx-auto px-4 py-16 text-center">
    <h1 class="text-2xl font-extrabold text-slate-900">Produk tidak ditemukan</h1>
    <a href="<?= BASE_URL ?>/pages/marketplace.php" class="inline-flex mt-6 px-6 py-3 bg-primary text-white rounded-xl font-bold no-underline">Kembali ke Marketplace</a>
</div>
<?php else: ?>
<div class="bg-slate-50 min-h-screen py-8" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex text-sm font-medium text-slate-500 mb-8 gap-2 items-center">
            <a href="<?= BASE_URL ?>/pages/dashboard.php" class="hover:text-primary smooth-transition no-underline text-slate-500">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <a href="<?= BASE_URL ?>/pages/marketplace.php" class="hover:text-primary smooth-transition no-underline text-slate-500">Obat & Vitamin</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-slate-800 font-bold truncate max-w-[200px] sm:max-w-none"><?= htmlspecialchars($product['name']) ?></span>
        </nav>

        <div class="flex flex-col lg:flex-row gap-10 items-start">
            <div class="w-full lg:w-1/3 flex flex-col gap-6 lg:sticky lg:top-28">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 flex items-center justify-center relative aspect-square overflow-hidden group">
                    <div class="absolute inset-0 bg-slate-50/50 rounded-3xl m-2"></div>
                    <img src="<?= htmlspecialchars($product['img']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="object-contain mix-blend-multiply h-full w-full relative z-10 group-hover:scale-110 smooth-transition">

                    <span class="absolute top-6 left-6 z-20 text-[10px] font-extrabold uppercase tracking-wider px-3 py-1.5 rounded-full bg-white text-slate-700 shadow-sm border border-slate-200">
                        <i class="fa-solid fa-shield-halved text-primary mr-1"></i> Asli
                    </span>
                </div>

                <div>
                    <div class="flex flex-wrap gap-2 mb-3">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider px-3 py-1.5 rounded-full <?= htmlspecialchars($product['badge']) ?>">
                            <?= htmlspecialchars($product['category']) ?>
                        </span>
                        <?php if ($product['requires_prescription']): ?>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider px-3 py-1.5 rounded-full bg-red-50 text-red-600 border border-red-100">
                            Wajib Resep Dokter
                        </span>
                        <?php endif; ?>
                    </div>

                    <h1 class="text-2xl font-extrabold text-slate-900 leading-snug mb-3">
                        <?= htmlspecialchars($product['name']) ?>
                    </h1>

                    <div class="text-sm font-semibold text-slate-500 mb-2 border-b border-slate-200 pb-4">
                        Stok tersedia: <?= (int) $product['stock'] ?> item
                    </div>

                    <div class="text-3xl font-extrabold text-slate-900 tracking-tight">
                        Rp <span id="display-price"><?= number_format($product['price'], 0, ',', '.') ?></span>
                    </div>
                    <div class="text-xs font-semibold text-slate-400 mt-1">Per <?= htmlspecialchars(explode('@', $product['kemasan'])[0] ?? 'Kemasan') ?></div>
                </div>

                <?php if ($prescriptionLocked): ?>
                <div class="bg-red-50 border border-red-100 rounded-2xl p-4 text-sm text-red-700 font-semibold leading-relaxed">
                    Produk ini tetap ditampilkan di marketplace, tetapi tidak bisa dibeli tanpa resep dokter yang valid. Silakan konsultasi atau buka e-resep Anda terlebih dahulu.
                </div>
                <?php endif; ?>

                <div class="flex flex-col gap-4 mt-2">
                    <div class="flex items-center gap-4">
                        <span class="text-sm font-bold text-slate-700">Jumlah:</span>
                        <div class="flex items-center bg-white border border-slate-200 rounded-xl p-1 shadow-sm w-fit">
                            <button onclick="updateQty(-1)" class="w-8 h-8 flex items-center justify-center text-slate-500 hover:text-primary hover:bg-slate-50 rounded-lg transition-colors border-none cursor-pointer bg-transparent">
                                <i class="fa-solid fa-minus"></i>
                            </button>
                            <input type="number" id="product-qty" value="1" min="1" max="<?= (int) $product['stock'] ?>" class="w-12 text-center bg-transparent font-bold text-slate-800 outline-none border-none text-sm" onchange="manualQtyChange(this)">
                            <button onclick="updateQty(1)" class="w-8 h-8 flex items-center justify-center text-slate-500 hover:text-primary hover:bg-slate-50 rounded-lg transition-colors border-none cursor-pointer bg-transparent">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <button id="add-cart-btn" onclick="addToCart()" class="w-full <?= $prescriptionLocked ? 'bg-slate-300 cursor-not-allowed' : 'bg-primary hover:bg-blue-800 cursor-pointer' ?> text-white border-none py-3.5 rounded-xl font-bold shadow-md shadow-blue-200 active:scale-95 smooth-transition flex items-center justify-center gap-2 text-[15px]" <?= $prescriptionLocked ? 'disabled' : '' ?>>
                        <?= $prescriptionLocked ? 'Butuh Resep Dokter' : 'Tambah ke Keranjang' ?>
                    </button>
                </div>
            </div>

            <div class="w-full lg:w-2/3 flex flex-col gap-6">
                <div class="bg-[#F0FAFA] border border-[#A6E1D8] rounded-2xl p-6">
                    <h4 class="font-extrabold text-slate-800 text-[15px] mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-notes-medical text-primary"></i> Deskripsi & Manfaat
                    </h4>
                    <p class="text-slate-700 text-[13px] font-medium leading-relaxed m-0 text-justify">
                        <?= htmlspecialchars($product['description']) ?>
                    </p>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <h4 class="font-extrabold text-slate-800 text-[15px] mb-5 flex items-center gap-2">
                        <i class="fa-solid fa-prescription-bottle text-primary"></i> Dosis & Aturan Pakai
                    </h4>
                    <div class="flex flex-col gap-5">
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Dosis</span>
                            <p class="text-slate-700 text-[13px] font-semibold m-0 leading-relaxed"><?= htmlspecialchars($product['dosis']) ?></p>
                        </div>
                        <hr class="border-slate-100 border-dashed">
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Aturan Pakai</span>
                            <p class="text-slate-700 text-[13px] font-semibold m-0 leading-relaxed"><?= htmlspecialchars($product['aturan_pakai']) ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <h4 class="font-extrabold text-slate-800 text-[15px] mb-5 flex items-center gap-2">
                        <i class="fa-solid fa-shield-virus text-primary"></i> Informasi Keamanan
                    </h4>
                    <div class="flex flex-col gap-5">
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Kategori Kehamilan</span>
                            <p class="text-slate-700 text-[13px] font-semibold m-0 leading-relaxed"><?= htmlspecialchars($product['kategori_kehamilan']) ?></p>
                        </div>
                        <hr class="border-slate-100 border-dashed">
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Perhatian Khusus</span>
                            <p class="text-slate-700 text-[13px] font-semibold m-0 leading-relaxed"><?= htmlspecialchars($product['perhatian']) ?></p>
                        </div>
                        <hr class="border-slate-100 border-dashed">
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Kontraindikasi</span>
                            <p class="text-slate-700 text-[13px] font-semibold m-0 leading-relaxed"><?= htmlspecialchars($product['kontraindikasi']) ?></p>
                        </div>
                        <hr class="border-slate-100 border-dashed">
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Efek Samping</span>
                            <p class="text-slate-700 text-[13px] font-semibold m-0 leading-relaxed"><?= htmlspecialchars($product['efek_samping']) ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <h4 class="font-extrabold text-slate-800 text-[15px] mb-5 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-primary"></i> Detail Produk
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Indikasi Umum</span>
                            <p class="text-slate-700 text-[13px] font-semibold m-0"><?= htmlspecialchars($product['indikasi']) ?></p>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Komposisi</span>
                            <p class="text-slate-700 text-[13px] font-semibold m-0"><?= htmlspecialchars($product['komposisi']) ?></p>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Golongan Produk</span>
                            <p class="text-slate-700 text-[13px] font-semibold m-0"><?= htmlspecialchars($product['golongan']) ?></p>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Kemasan</span>
                            <p class="text-slate-700 text-[13px] font-semibold m-0"><?= htmlspecialchars($product['kemasan']) ?></p>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Manufaktur</span>
                            <p class="text-slate-700 text-[13px] font-semibold m-0"><?= htmlspecialchars($product['manufaktur']) ?></p>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">No. Registrasi</span>
                            <p class="text-slate-700 text-[13px] font-semibold m-0"><?= htmlspecialchars($product['no_registrasi']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="toast-stack" style="position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:10px"></div>
<script>
const BASE_URL = '<?= BASE_URL ?>';
const basePrice = <?= (int) $product['price'] ?>;
const maxStock = <?= (int) $product['stock'] ?>;
const prescriptionLocked = <?= $prescriptionLocked ? 'true' : 'false' ?>;
const qtyInput = document.getElementById('product-qty');
const priceDisplay = document.getElementById('display-price');

function showToast(msg, type = 'info', dur = 3200) {
    const colors = { info: '#1D4ED8', success: '#10B981', error: '#EF4444', warning: '#F59E0B' };
    const icons = { info: 'fa-circle-info', success: 'fa-circle-check', error: 'fa-circle-xmark', warning: 'fa-triangle-exclamation' };
    const iconBgs = { info: '#EFF6FF', success: '#ECFDF5', error: '#FEF2F2', warning: '#FFFBEB' };
    const stack = document.getElementById('toast-stack');
    const toast = document.createElement('div');
    toast.style.cssText = `display:flex;align-items:center;gap:12px;padding:14px 18px;border-radius:16px;background:#fff;box-shadow:0 8px 30px -4px rgba(0,0,0,0.15);min-width:280px;font-size:14px;font-weight:600;color:#0F172A;border-left:4px solid ${colors[type]};animation:slideIn .3s ease`;
    toast.innerHTML = `<div style="width:34px;height:34px;border-radius:10px;background:${iconBgs[type]};color:${colors[type]};display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fa-solid ${icons[type]}"></i></div><div style="flex:1">${msg}</div><button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:18px;padding:0;line-height:1">x</button>`;
    stack.appendChild(toast);
    setTimeout(() => {
        toast.style.transition = 'all .3s';
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(20px)';
        setTimeout(() => toast.remove(), 300);
    }, dur);
}

function formatRupiah(value) {
    return new Intl.NumberFormat('id-ID').format(value);
}

function updatePrice() {
    const qty = parseInt(qtyInput.value, 10) || 1;
    priceDisplay.textContent = formatRupiah(basePrice * qty);
}

function updateQty(change) {
    let currentQty = parseInt(qtyInput.value, 10) || 1;
    let newQty = currentQty + change;
    if (newQty < 1) newQty = 1;
    if (newQty > maxStock) {
        newQty = maxStock;
        showToast('Maksimal pembelian ' + maxStock + ' item.', 'warning');
    }
    qtyInput.value = newQty;
    updatePrice();
}

function manualQtyChange(input) {
    let value = parseInt(input.value, 10);
    if (isNaN(value) || value < 1) value = 1;
    if (value > maxStock) {
        value = maxStock;
        showToast('Maksimal pembelian ' + maxStock + ' item.', 'warning');
    }
    input.value = value;
    updatePrice();
}

function syncCart(count) {
    const badge = document.getElementById('cart-count');
    if (!badge) return;
    if (count > 0) {
        badge.textContent = count;
        badge.classList.remove('hidden');
    } else {
        badge.textContent = '0';
        badge.classList.add('hidden');
    }
}

async function addToCart() {
    if (prescriptionLocked) {
        showToast('Produk ini hanya bisa dibeli dengan resep dokter yang valid.', 'warning');
        return;
    }

    const qty = parseInt(qtyInput.value, 10) || 1;

    try {
        const response = await fetch(`${BASE_URL}/api/marketplace/cart/add.php`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: <?= (int) $product['id'] ?>, qty })
        });
        const result = await response.json();
        if (!response.ok || result.status !== 'success') {
            throw new Error(result.message || 'Produk gagal ditambahkan ke keranjang.');
        }

        syncCart(result.data.cart_count || 0);
        showToast(`<b>${qty}x <?= htmlspecialchars($product['name'], ENT_QUOTES) ?></b> berhasil ditambahkan ke keranjang!`, 'success');
    } catch (error) {
        showToast(error.message, 'warning');
    }
}
</script>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
