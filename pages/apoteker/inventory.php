<?php
require_once '../../includes/staff_portal_helpers.php';

$products = getMarketplaceProducts($pdo);
$totalProducts = count($products);
$criticalStock = count(array_filter($products, static fn(array $product): bool => (int) $product['stock'] <= 10));
$prescriptionProducts = count(array_filter($products, static fn(array $product): bool => stripos((string) $product['category'], 'Resep') !== false));

include '../../includes/header_apoteker.php';
?>

<div class="p-6 sm:p-8 max-w-7xl mx-auto">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Manajemen Stok Obat</h1>
            <p class="text-gray-500 mt-1">Daftar obat di halaman ini memakai data produk marketplace yang sama dengan sisi pasien.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-teal-50 flex items-center justify-center text-teal-600 text-xl"><i class="fa-solid fa-boxes-stacked"></i></div>
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Item Obat</p>
                <h3 class="text-2xl font-bold text-gray-900"><?= number_format($totalProducts, 0, ',', '.') ?></h3>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 border-l-4 border-l-red-500">
            <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-red-600 text-xl"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Stok Kritis</p>
                <h3 class="text-2xl font-bold text-red-600"><?= number_format($criticalStock, 0, ',', '.') ?></h3>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 border-l-4 border-l-blue-500">
            <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 text-xl"><i class="fa-solid fa-file-prescription"></i></div>
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Obat Resep</p>
                <h3 class="text-2xl font-bold text-gray-900"><?= number_format($prescriptionProducts, 0, ',', '.') ?></h3>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Stok & Harga Produk</h2>
                <p class="text-sm text-gray-500 mt-1">Perubahan stok dan harga akan tersimpan langsung ke tabel `Obat`.</p>
            </div>
            <a href="<?= BASE_URL ?>/pages/pharmacy_management.php" class="text-sm font-bold text-teal-600 hover:text-teal-700 no-underline">Buka manajemen lengkap</a>
        </div>

        <div class="divide-y divide-gray-100">
            <?php foreach ($products as $product): ?>
                <div class="p-5 flex flex-col lg:flex-row gap-4 lg:items-center" data-stock-card="<?= (int) $product['id'] ?>">
                    <div class="flex items-center gap-4 flex-1 min-w-0">
                        <img src="<?= htmlspecialchars($product['img']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-16 h-16 rounded-2xl bg-slate-50 border border-slate-100 p-2 object-contain">
                        <div class="min-w-0">
                            <div class="font-extrabold text-slate-900 text-sm truncate"><?= htmlspecialchars($product['name']) ?></div>
                            <div class="text-xs text-slate-500 mt-1"><?= htmlspecialchars($product['category']) ?> • DB ID <?= (int) $product['db_id'] ?></div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 items-end lg:w-[440px]">
                        <label class="text-xs font-bold text-slate-500">
                            Stok
                            <input type="number" min="0" value="<?= (int) $product['stock'] ?>" class="stock-input mt-1 w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 font-bold text-slate-800 outline-none">
                        </label>
                        <label class="text-xs font-bold text-slate-500 col-span-1 sm:col-span-2">
                            Harga
                            <input type="number" min="0" value="<?= (int) $product['price'] ?>" class="price-input mt-1 w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 font-bold text-slate-800 outline-none">
                        </label>
                        <button type="button" onclick="saveStock(<?= (int) $product['id'] ?>)" class="bg-teal-600 text-white border-none rounded-xl px-4 py-3 font-extrabold cursor-pointer hover:bg-teal-700 transition">
                            Simpan
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div id="pharmacy-toast" style="position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:10px"></div>
<script>
const BASE_URL = '<?= BASE_URL ?>';
function showToast(message, type = 'info') {
    const colors = { info: '#1D4ED8', success: '#10B981', error: '#EF4444', warning: '#F59E0B' };
    const stack = document.getElementById('pharmacy-toast');
    const toast = document.createElement('div');
    toast.style.cssText = `padding:14px 16px;border-radius:16px;background:#fff;border-left:4px solid ${colors[type]};box-shadow:0 8px 30px -4px rgba(0,0,0,.15);font-size:14px;font-weight:700;color:#0F172A;min-width:280px`;
    toast.textContent = message;
    stack.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(20px)';
        toast.style.transition = '.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 2600);
}
async function saveStock(productId) {
    const card = document.querySelector(`[data-stock-card="${productId}"]`);
    const stock = Number(card.querySelector('.stock-input').value || 0);
    const price = Number(card.querySelector('.price-input').value || 0);
    try {
        const response = await fetch(`${BASE_URL}/api/marketplace/stock/update.php`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId, stock, price })
        });
        const result = await response.json();
        if (!response.ok || result.status !== 'success') throw new Error(result.message || 'Stok gagal diperbarui.');
        showToast('Stok obat berhasil diperbarui.', 'success');
    } catch (error) {
        showToast(error.message, 'warning');
    }
}
</script>
