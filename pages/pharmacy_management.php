<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/marketplace_helpers.php';

requireLogin();

$user = currentUser();
if (!userCanManageMarketplace($user)) {
    header('Location: ' . BASE_URL . '/pages/dashboard.php');
    exit;
}

$pageTitle = 'Manajemen Apotek - CareSync';
$currentPage = 'marketplace';
$products = getMarketplaceProducts($pdo);
$orders = getMarketplaceOrdersForManagement($pdo);

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
    .smooth-transition { transition: all .25s ease-in-out; }
</style>
';

include __DIR__ . '/../includes/header.php';
?>

<div class="bg-slate-50 min-h-screen py-8" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col lg:flex-row lg:items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 m-0">Manajemen Apotek</h1>
                <p class="text-slate-500 font-medium mt-2 text-sm">Apoteker dapat mengatur stok obat dan mendorong status pengiriman pasien secara bertahap.</p>
            </div>
            <div class="bg-white px-4 py-3 rounded-2xl border border-slate-200 shadow-sm text-sm font-bold text-slate-700">
                <?= count($products) ?> produk • <?= count($orders) ?> pesanan
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1.2fr)_minmax(0,1fr)] gap-8">
            <section class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-6 sm:p-8">
                <div class="flex items-center justify-between gap-4 mb-6 border-b border-slate-100 pb-4">
                    <h2 class="text-lg font-extrabold text-slate-900 m-0">Stok Obat</h2>
                    <div class="text-xs font-bold text-slate-500">Update stok dan harga jual marketplace</div>
                </div>

                <div class="space-y-4">
                    <?php foreach ($products as $product): ?>
                    <div class="border border-slate-100 rounded-2xl p-4 flex flex-col lg:flex-row gap-4 lg:items-center" data-stock-card="<?= (int) $product['id'] ?>">
                        <div class="flex items-center gap-4 flex-1 min-w-0">
                            <img src="<?= htmlspecialchars($product['img']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-16 h-16 rounded-2xl bg-slate-50 border border-slate-100 p-2 object-contain">
                            <div class="min-w-0">
                                <div class="font-extrabold text-slate-900 text-sm truncate"><?= htmlspecialchars($product['name']) ?></div>
                                <div class="text-xs text-slate-500 mt-1"><?= htmlspecialchars($product['category']) ?> • DB ID <?= (int) $product['db_id'] ?></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 items-end lg:w-[420px]">
                            <label class="text-xs font-bold text-slate-500">
                                Stok
                                <input type="number" min="0" value="<?= (int) $product['stock'] ?>" class="stock-input mt-1 w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 font-bold text-slate-800 outline-none">
                            </label>
                            <label class="text-xs font-bold text-slate-500 col-span-1 sm:col-span-2">
                                Harga
                                <input type="number" min="0" value="<?= (int) $product['price'] ?>" class="price-input mt-1 w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 font-bold text-slate-800 outline-none">
                            </label>
                            <button type="button" onclick="saveStock(<?= (int) $product['id'] ?>)" class="bg-primary text-white border-none rounded-xl px-4 py-3 font-extrabold cursor-pointer hover:bg-blue-800 smooth-transition">
                                Simpan
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-6 sm:p-8">
                <div class="flex items-center justify-between gap-4 mb-6 border-b border-slate-100 pb-4">
                    <h2 class="text-lg font-extrabold text-slate-900 m-0">Status Pesanan</h2>
                    <div class="text-xs font-bold text-slate-500">Urutan: dikemas → menunggu kurir → dikirim → terkirim → selesai</div>
                </div>

                <div class="space-y-4">
                    <?php foreach ($orders as $order): ?>
                    <?php $allowed = getMarketplaceAllowedNextStatuses($order); ?>
                    <div class="border border-slate-100 rounded-2xl p-4" data-order-card="<?= (int) $order['id'] ?>">
                        <div class="flex flex-col gap-3">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="font-extrabold text-slate-900 text-sm"><?= htmlspecialchars($order['order_code']) ?></div>
                                    <div class="text-xs text-slate-500 mt-1"><?= htmlspecialchars($order['patient_name']) ?> • <?= htmlspecialchars($order['date_label']) ?></div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pembayaran</div>
                                    <div class="font-extrabold text-slate-800 text-sm"><?= htmlspecialchars($order['payment_status']) ?></div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Status</div>
                                    <div class="font-extrabold text-slate-900 current-status"><?= htmlspecialchars($order['shipping_status']) ?></div>
                                </div>
                                <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kurir</div>
                                    <div class="font-extrabold text-slate-900"><?= htmlspecialchars($order['courier']) ?></div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-3 items-center">
                                <select class="order-status-select w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-800 outline-none" <?= $allowed ? '' : 'disabled' ?>>
                                    <?php if (!$allowed): ?>
                                    <option value="">Tidak ada aksi lanjutan</option>
                                    <?php else: ?>
                                    <option value="">Pilih status berikutnya</option>
                                    <?php foreach ($allowed as $status): ?>
                                    <option value="<?= htmlspecialchars($status) ?>"><?= htmlspecialchars($status) ?></option>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <a href="<?= BASE_URL ?>/pages/tracking.php?order_id=<?= (int) $order['id'] ?>" class="w-full sm:w-auto text-center bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-xl px-4 py-3 font-extrabold no-underline smooth-transition">Tracking</a>
                                <button type="button" onclick="saveOrderStatus(<?= (int) $order['id'] ?>)" class="w-full sm:w-auto bg-primary text-white border-none rounded-xl px-4 py-3 font-extrabold cursor-pointer hover:bg-blue-800 smooth-transition" <?= $allowed ? '' : 'disabled' ?>>
                                    Update
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
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
        if (!response.ok || result.status !== 'success') {
            throw new Error(result.message || 'Stok gagal diperbarui.');
        }
        showToast('Stok obat berhasil diperbarui.', 'success');
    } catch (error) {
        showToast(error.message, 'warning');
    }
}

async function saveOrderStatus(orderId) {
    const card = document.querySelector(`[data-order-card="${orderId}"]`);
    const select = card.querySelector('.order-status-select');
    const status = select.value;

    if (!status) {
        showToast('Pilih status pengiriman berikutnya terlebih dahulu.', 'warning');
        return;
    }

    try {
        const response = await fetch(`${BASE_URL}/api/marketplace/orders/update-status.php`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order_id: orderId, status })
        });
        const result = await response.json();
        if (!response.ok || result.status !== 'success') {
            throw new Error(result.message || 'Status pesanan gagal diperbarui.');
        }
        showToast('Status pesanan berhasil diperbarui.', 'success');
        setTimeout(() => window.location.reload(), 600);
    } catch (error) {
        showToast(error.message, 'warning');
    }
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
