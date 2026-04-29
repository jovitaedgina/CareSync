<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/marketplace_helpers.php';

requireLogin();

$pageTitle = 'Keranjang Belanja - CareSync';
$currentPage = 'marketplace';
$userId = (int) (currentUser()['id'] ?? 0);
$cartSummary = getMarketplaceCartSummary($pdo, $userId);
$cartItems = $cartSummary['items'];

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
    .custom-checkbox { width: 20px; height: 20px; accent-color: #1D4ED8; cursor: pointer; }
    input[type=number]::-webkit-inner-spin-button, input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
</style>
';

include __DIR__ . '/../includes/header.php';
?>

<div class="bg-slate-50 min-h-screen py-8" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 m-0">Keranjang Belanja</h1>
            <p class="text-slate-500 font-medium mt-2 text-sm">Hanya obat non resep atau obat resep dengan resep valid yang bisa diproses ke pembayaran digital.</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <div class="w-full lg:w-2/3 flex flex-col gap-6">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex items-center justify-between">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" id="selectAll" class="custom-checkbox" <?= $cartItems ? 'checked' : '' ?>>
                        <span class="font-extrabold text-slate-800 text-sm">Pilih Semua Item</span>
                    </label>
                    <button type="button" onclick="removeSelected()" class="text-sm font-bold text-red-500 hover:text-red-700 bg-transparent border-none cursor-pointer smooth-transition">
                        Hapus Terpilih
                    </button>
                </div>

                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden" id="cart-container">
                    <?php if ($cartItems): ?>
                        <?php foreach ($cartItems as $item): ?>
                        <div class="p-6 border-b border-slate-100 last:border-b-0 flex gap-4 sm:gap-6 cart-item" data-id="<?= (int) $item['id'] ?>" data-price="<?= (int) $item['price'] ?>">
                            <div class="pt-2">
                                <input type="checkbox" class="custom-checkbox item-checkbox" <?= !$item['requires_prescription'] || $item['has_valid_prescription'] ? 'checked' : '' ?> <?= $item['requires_prescription'] && !$item['has_valid_prescription'] ? 'disabled' : '' ?>>
                            </div>

                            <div class="w-20 h-20 sm:w-28 sm:h-28 bg-slate-50 rounded-2xl flex items-center justify-center flex-shrink-0 relative overflow-hidden border border-slate-100">
                                <img src="<?= htmlspecialchars($item['img']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="object-contain mix-blend-multiply h-full w-full p-2">
                            </div>

                            <div class="flex flex-col flex-1 justify-between">
                                <div>
                                    <div class="flex flex-wrap gap-2 items-center mb-2">
                                        <span class="text-[10px] font-extrabold uppercase tracking-wider px-2 py-1 rounded w-fit inline-block <?= htmlspecialchars($item['badge']) ?>">
                                            <?= htmlspecialchars($item['category']) ?>
                                        </span>
                                        <?php if ($item['requires_prescription']): ?>
                                        <span class="text-[10px] font-extrabold uppercase tracking-wider px-2 py-1 rounded w-fit inline-block bg-red-50 text-red-600 border border-red-100">
                                            Resep
                                        </span>
                                        <?php endif; ?>
                                    </div>

                                    <a href="<?= BASE_URL ?>/pages/product-detail.php?id=<?= (int) $item['id'] ?>" class="block font-extrabold text-slate-900 text-sm sm:text-base leading-snug hover:text-primary smooth-transition no-underline m-0">
                                        <?= htmlspecialchars($item['name']) ?>
                                    </a>
                                    <div class="font-extrabold text-primary text-base sm:text-lg mt-1">
                                        Rp <?= number_format($item['price'], 0, ',', '.') ?>
                                    </div>
                                    <?php if ($item['requires_prescription'] && !$item['has_valid_prescription']): ?>
                                    <p class="text-xs font-bold text-red-500 mt-2 mb-0">Obat ini perlu resep dokter dan tidak bisa dipilih untuk checkout.</p>
                                    <?php endif; ?>
                                </div>

                                <div class="flex items-center justify-between mt-4">
                                    <div class="text-xs font-bold text-slate-400 hidden sm:block">Sisa stok: <?= (int) $item['stock'] ?></div>
                                    <div class="flex items-center gap-4 w-full sm:w-auto justify-end">
                                        <button onclick="removeRow(<?= (int) $item['id'] ?>)" class="w-8 h-8 rounded-lg bg-slate-50 text-slate-400 hover:bg-red-50 hover:text-red-500 flex items-center justify-center border-none cursor-pointer smooth-transition">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>

                                        <div class="flex items-center bg-white border border-slate-200 rounded-xl p-1 shadow-sm">
                                            <button onclick="changeQty(<?= (int) $item['id'] ?>, -1, <?= (int) $item['stock'] ?>)" class="w-8 h-8 flex items-center justify-center text-slate-500 hover:text-primary hover:bg-slate-50 rounded-lg transition-colors border-none cursor-pointer bg-transparent">
                                                <i class="fa-solid fa-minus text-xs"></i>
                                            </button>
                                            <input type="number" value="<?= (int) $item['qty'] ?>" class="qty-input w-10 text-center bg-transparent font-extrabold text-slate-800 outline-none border-none text-sm" onchange="manualQtyChange(this, <?= (int) $item['id'] ?>, <?= (int) $item['stock'] ?>)">
                                            <button onclick="changeQty(<?= (int) $item['id'] ?>, 1, <?= (int) $item['stock'] ?>)" class="w-8 h-8 flex items-center justify-center text-slate-500 hover:text-primary hover:bg-slate-50 rounded-lg transition-colors border-none cursor-pointer bg-transparent">
                                                <i class="fa-solid fa-plus text-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="p-12 flex flex-col items-center justify-center text-center">
                            <i class="fa-solid fa-cart-shopping text-6xl text-slate-200 mb-4"></i>
                            <h3 class="font-extrabold text-slate-800 text-lg mb-2">Keranjangmu masih kosong</h3>
                            <p class="text-slate-500 text-sm mb-6">Yuk, cari obat dan vitamin untuk kebutuhan kesehatanmu!</p>
                            <a href="<?= BASE_URL ?>/pages/marketplace.php" class="bg-primary text-white px-6 py-2.5 rounded-xl font-bold shadow-md hover:bg-blue-800 smooth-transition no-underline">
                                Mulai Belanja
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="w-full lg:w-1/3">
                <div class="bg-white rounded-[2rem] p-6 lg:p-8 border border-slate-100 shadow-sm sticky top-28">
                    <h3 class="font-extrabold text-lg text-slate-900 mb-6 m-0 border-b border-slate-100 pb-4">Ringkasan Belanja</h3>

                    <div class="flex flex-col gap-4 mb-6">
                        <div class="flex justify-between items-center text-sm font-medium text-slate-600">
                            <span id="summary-items">Total Harga (0 Barang)</span>
                            <span id="summary-subtotal" class="font-bold text-slate-800">Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center text-sm font-medium text-slate-600">
                            <span>Diskon</span>
                            <span class="font-bold text-emerald-500">- Rp 0</span>
                        </div>
                    </div>

                    <hr class="border-slate-100 border-dashed mb-6">

                    <div class="flex justify-between items-center mb-8">
                        <span class="font-extrabold text-slate-900">Total Tagihan</span>
                        <span id="summary-total" class="font-extrabold text-primary text-xl">Rp 0</span>
                    </div>

                    <button id="btn-checkout" onclick="proceedToCheckout()" class="w-full bg-primary text-white border-none hover:bg-blue-800 py-4 rounded-2xl font-extrabold shadow-floating active:scale-95 smooth-transition flex items-center justify-center gap-2 cursor-pointer">
                        Lanjut ke Pengiriman <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="toast-stack-cart" style="position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:10px"></div>
<script>
const BASE_URL = '<?= BASE_URL ?>';

function formatRupiah(number) {
    return new Intl.NumberFormat('id-ID').format(number);
}

function showToast(message, type = 'info') {
    const colors = { info: '#1D4ED8', success: '#10B981', error: '#EF4444', warning: '#F59E0B' };
    const stack = document.getElementById('toast-stack-cart');
    const toast = document.createElement('div');
    toast.style.cssText = `padding:14px 16px;border-radius:16px;background:#fff;border-left:4px solid ${colors[type]};box-shadow:0 8px 30px -4px rgba(0,0,0,.15);font-size:14px;font-weight:700;color:#0F172A;min-width:260px`;
    toast.textContent = message;
    stack.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(20px)';
        toast.style.transition = '.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 2500);
}

function syncCartBadge(count) {
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

function calculateTotal() {
    const items = document.querySelectorAll('.cart-item');
    let totalItems = 0;
    let totalPrice = 0;

    items.forEach((item) => {
        const checkbox = item.querySelector('.item-checkbox');
        if (checkbox && checkbox.checked && !checkbox.disabled) {
            const price = parseInt(item.dataset.price, 10) || 0;
            const qty = parseInt(item.querySelector('.qty-input').value, 10) || 0;
            totalItems += qty;
            totalPrice += price * qty;
        }
    });

    document.getElementById('summary-items').textContent = `Total Harga (${totalItems} Barang)`;
    document.getElementById('summary-subtotal').textContent = `Rp ${formatRupiah(totalPrice)}`;
    document.getElementById('summary-total').textContent = `Rp ${formatRupiah(totalPrice)}`;

    const btn = document.getElementById('btn-checkout');
    btn.disabled = totalItems === 0;
    btn.classList.toggle('bg-slate-300', totalItems === 0);
    btn.classList.toggle('cursor-not-allowed', totalItems === 0);
    btn.classList.toggle('bg-primary', totalItems > 0);
}

async function updateCart(productId, qty) {
    const response = await fetch(`${BASE_URL}/api/marketplace/cart/update.php`, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId, qty })
    });
    const result = await response.json();
    if (!response.ok || result.status !== 'success') {
        throw new Error(result.message || 'Keranjang gagal diperbarui.');
    }
    syncCartBadge(result.data.cart_count || 0);
    return result;
}

function changeQty(productId, delta, maxStock) {
    const row = document.querySelector(`.cart-item[data-id="${productId}"]`);
    const input = row.querySelector('.qty-input');
    let value = (parseInt(input.value, 10) || 1) + delta;
    if (value < 1) value = 1;
    if (value > maxStock) {
        value = maxStock;
        showToast('Maksimal pembelian sesuai stok.', 'warning');
    }
    input.value = value;
    persistQty(productId, value);
}

function manualQtyChange(input, productId, maxStock) {
    let value = parseInt(input.value, 10);
    if (isNaN(value) || value < 1) value = 1;
    if (value > maxStock) value = maxStock;
    input.value = value;
    persistQty(productId, value);
}

async function persistQty(productId, qty) {
    try {
        await updateCart(productId, qty);
        calculateTotal();
    } catch (error) {
        showToast(error.message, 'warning');
        window.location.reload();
    }
}

async function removeRow(productId) {
    try {
        const response = await fetch(`${BASE_URL}/api/marketplace/cart/remove.php`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId })
        });
        const result = await response.json();
        if (!response.ok || result.status !== 'success') {
            throw new Error(result.message || 'Produk gagal dihapus dari keranjang.');
        }
        const row = document.querySelector(`.cart-item[data-id="${productId}"]`);
        if (row) row.remove();
        calculateTotal();
        checkEmptyCart();
        syncCartBadge(result.data.cart_count || 0);
    } catch (error) {
        showToast(error.message, 'warning');
    }
}

async function removeSelected() {
    const selectedRows = [...document.querySelectorAll('.item-checkbox:checked')].map((checkbox) => checkbox.closest('.cart-item'));
    if (!selectedRows.length) {
        showToast('Pilih item yang ingin dihapus terlebih dahulu.', 'warning');
        return;
    }

    try {
        let cartCount = 0;
        for (const row of selectedRows) {
            const response = await fetch(`${BASE_URL}/api/marketplace/cart/remove.php`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: Number(row.dataset.id) })
            });
            const result = await response.json();
            if (!response.ok || result.status !== 'success') {
                throw new Error(result.message || 'Produk gagal dihapus dari keranjang.');
            }
            cartCount = Number(result.data.cart_count || 0);
        }

        selectedRows.forEach((row) => row.remove());
        calculateTotal();
        checkEmptyCart();
        syncCartBadge(cartCount);
        showToast('Item terpilih berhasil dihapus.', 'success');
    } catch (error) {
        showToast(error.message, 'warning');
    }
}

function checkEmptyCart() {
    const container = document.getElementById('cart-container');
    if (container.querySelectorAll('.cart-item').length) {
        return;
    }

    container.innerHTML = `
        <div class="p-12 flex flex-col items-center justify-center text-center">
            <i class="fa-solid fa-cart-shopping text-6xl text-slate-200 mb-4"></i>
            <h3 class="font-extrabold text-slate-800 text-lg mb-2">Keranjangmu masih kosong</h3>
            <p class="text-slate-500 text-sm mb-6">Yuk, cari obat dan vitamin untuk kebutuhan kesehatanmu!</p>
            <a href="<?= BASE_URL ?>/pages/marketplace.php" class="bg-primary text-white px-6 py-2.5 rounded-xl font-bold shadow-md hover:bg-blue-800 smooth-transition no-underline">
                Mulai Belanja
            </a>
        </div>
    `;
}

async function proceedToCheckout() {
    const selectedIds = [...document.querySelectorAll('.item-checkbox:checked')]
        .filter((checkbox) => !checkbox.disabled)
        .map((checkbox) => Number(checkbox.closest('.cart-item').dataset.id));

    if (!selectedIds.length) {
        showToast('Pilih minimal satu produk valid untuk checkout.', 'warning');
        return;
    }

    try {
        const response = await fetch(`${BASE_URL}/api/marketplace/cart/checkout.php`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_ids: selectedIds })
        });
        const result = await response.json();
        if (!response.ok || result.status !== 'success') {
            throw new Error(result.message || 'Checkout gagal disiapkan.');
        }
        window.location.href = '<?= BASE_URL ?>/pages/shipping.php';
    } catch (error) {
        showToast(error.message, 'warning');
    }
}

document.getElementById('selectAll')?.addEventListener('change', function () {
    document.querySelectorAll('.item-checkbox').forEach((checkbox) => {
        if (!checkbox.disabled) {
            checkbox.checked = this.checked;
        }
    });
    calculateTotal();
});

document.querySelectorAll('.item-checkbox').forEach((checkbox) => {
    checkbox.addEventListener('change', calculateTotal);
});

document.addEventListener('DOMContentLoaded', calculateTotal);
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
