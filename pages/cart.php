<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle   = 'Keranjang Belanja — CareSync';
$currentPage = 'marketplace'; // Tetap aktifkan menu Apotek

// Suntikkan Tailwind CSS khusus untuk halaman ini
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
    /* Checkbox Custom Styling */
    .custom-checkbox { width: 20px; height: 20px; accent-color: #1D4ED8; cursor: pointer; }
    input[type=number]::-webkit-inner-spin-button, input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
</style>
';

include __DIR__ . '/../includes/header.php';

/* * MOCK DATA KERANJANG
 * Nanti diganti dengan Query ke tabel cart/keranjang di Database
 */
$cartItems = [
    [
        'id' => 1,
        'name' => 'Blackmores Vitamin C 500mg - 60 Tablet',
        'category' => 'Vitamin & Suplemen',
        'price' => 120000,
        'qty' => 1,
        'stock' => 45,
        'icon' => 'fa-bottle-droplet',
        'badge' => 'text-emerald-600 bg-emerald-50'
    ],
    [
        'id' => 3,
        'name' => 'Sensi Masker Medis 3-Ply - Isi 50 Pcs',
        'category' => 'Alat Kesehatan',
        'price' => 35000,
        'qty' => 2,
        'stock' => 300,
        'icon' => 'fa-mask-face',
        'badge' => 'text-purple-600 bg-purple-50'
    ],
];
?>

<div class="bg-slate-50 min-h-screen py-8" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 m-0">Keranjang Belanja</h1>
            <p class="text-slate-500 font-medium mt-2 text-sm">Pastikan barang belanjaan dan resep Anda sudah benar.</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            
            <div class="w-full lg:w-2/3 flex flex-col gap-6">
                
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex items-center justify-between">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" id="selectAll" class="custom-checkbox" checked onchange="toggleSelectAll()">
                        <span class="font-extrabold text-slate-800 text-sm">Pilih Semua Item</span>
                    </label>
                    <button onclick="removeSelected()" class="text-sm font-bold text-red-500 hover:text-red-700 bg-transparent border-none cursor-pointer smooth-transition">
                        Hapus Terpilih
                    </button>
                </div>

                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden" id="cart-container">
                    
                    <?php if (count($cartItems) > 0): ?>
                        <?php foreach ($cartItems as $index => $item): ?>
                        <div class="p-6 border-b border-slate-100 last:border-b-0 flex gap-4 sm:gap-6 cart-item" data-id="<?= $item['id'] ?>" data-price="<?= $item['price'] ?>">
                            <div class="pt-2">
                                <input type="checkbox" class="custom-checkbox item-checkbox" checked onchange="calculateTotal()">
                            </div>
                            
                            <div class="w-20 h-20 sm:w-28 sm:h-28 bg-slate-50 rounded-2xl flex items-center justify-center flex-shrink-0 relative overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-tr from-slate-200 to-slate-50 opacity-40"></div>
                                <i class="fa-solid <?= $item['icon'] ?> text-4xl sm:text-5xl text-slate-300 relative z-10"></i>
                            </div>

                            <div class="flex flex-col flex-1 justify-between">
                                <div>
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider px-2 py-1 rounded w-fit mb-2 inline-block <?= $item['badge'] ?>">
                                        <?= $item['category'] ?>
                                    </span>
                                    <a href="<?= BASE_URL ?>/pages/product-detail.php?id=<?= $item['id'] ?>" class="block font-extrabold text-slate-900 text-sm sm:text-base leading-snug hover:text-primary smooth-transition no-underline m-0">
                                        <?= $item['name'] ?>
                                    </a>
                                    <div class="font-extrabold text-primary text-base sm:text-lg mt-1">
                                        Rp <?= number_format($item['price'], 0, ',', '.') ?>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between mt-4">
                                    <div class="text-xs font-bold text-slate-400 hidden sm:block">Sisa stok: <?= $item['stock'] ?></div>
                                    <div class="flex items-center gap-4 w-full sm:w-auto justify-end">
                                        <button onclick="removeRow(this)" class="w-8 h-8 rounded-lg bg-slate-50 text-slate-400 hover:bg-red-50 hover:text-red-500 flex items-center justify-center border-none cursor-pointer smooth-transition">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                        
                                        <div class="flex items-center bg-white border border-slate-200 rounded-xl p-1 shadow-sm">
                                            <button onclick="updateCartQty(this, -1, <?= $item['stock'] ?>)" class="w-8 h-8 flex items-center justify-center text-slate-500 hover:text-primary hover:bg-slate-50 rounded-lg transition-colors border-none cursor-pointer bg-transparent">
                                                <i class="fa-solid fa-minus text-xs"></i>
                                            </button>
                                            <input type="number" value="<?= $item['qty'] ?>" class="qty-input w-10 text-center bg-transparent font-extrabold text-slate-800 outline-none border-none text-sm" onchange="manualCartQty(this, <?= $item['stock'] ?>)">
                                            <button onclick="updateCartQty(this, 1, <?= $item['stock'] ?>)" class="w-8 h-8 flex items-center justify-center text-slate-500 hover:text-primary hover:bg-slate-50 rounded-lg transition-colors border-none cursor-pointer bg-transparent">
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

<script>
    // Format Rupiah
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka);
    }

    // Kalkulasi Total Harga dan Update UI Ringkasan Belanja
    function calculateTotal() {
        const items = document.querySelectorAll('.cart-item');
        let totalItems = 0;
        let totalPrice = 0;

        items.forEach(item => {
            const checkbox = item.querySelector('.item-checkbox');
            if (checkbox.checked) {
                const price = parseInt(item.getAttribute('data-price'));
                const qty = parseInt(item.querySelector('.qty-input').value);
                totalItems += qty;
                totalPrice += (price * qty);
            }
        });

        // Update Text
        document.getElementById('summary-items').textContent = `Total Harga (${totalItems} Barang)`;
        document.getElementById('summary-subtotal').textContent = `Rp ${formatRupiah(totalPrice)}`;
        document.getElementById('summary-total').textContent = `Rp ${formatRupiah(totalPrice)}`;
        
        // Update Button State
        const btnCheckout = document.getElementById('btn-checkout');
        if (totalItems === 0) {
            btnCheckout.disabled = true;
            btnCheckout.classList.replace('bg-primary', 'bg-slate-300');
            btnCheckout.classList.replace('hover:bg-blue-800', 'cursor-not-allowed');
            btnCheckout.classList.remove('shadow-floating', 'active:scale-95');
        } else {
            btnCheckout.disabled = false;
            btnCheckout.classList.replace('bg-slate-300', 'bg-primary');
            btnCheckout.classList.replace('cursor-not-allowed', 'hover:bg-blue-800');
            btnCheckout.classList.add('shadow-floating', 'active:scale-95');
        }

        // Cek Select All state
        checkSelectAllState();
    }

    // Update Kuantitas dari Tombol +/-
    function updateCartQty(btn, change, maxStock) {
        const input = btn.parentElement.querySelector('.qty-input');
        let newQty = parseInt(input.value) + change;
        
        if (newQty >= 1 && newQty <= maxStock) {
            input.value = newQty;
            calculateTotal();
        }
    }

    // Update Kuantitas Manual (Ketik)
    function manualCartQty(input, maxStock) {
        let val = parseInt(input.value);
        if (isNaN(val) || val < 1) input.value = 1;
        if (val > maxStock) input.value = maxStock;
        calculateTotal();
    }

    // Fitur Pilih Semua
    function toggleSelectAll() {
        const isChecked = document.getElementById('selectAll').checked;
        const checkboxes = document.querySelectorAll('.item-checkbox');
        checkboxes.forEach(cb => cb.checked = isChecked);
        calculateTotal();
    }

    // Cek apakah semua checkbox terpilih untuk mengatur checkbox "Pilih Semua"
    function checkSelectAllState() {
        const checkboxes = document.querySelectorAll('.item-checkbox');
        const selectAll = document.getElementById('selectAll');
        if(checkboxes.length === 0) {
            selectAll.checked = false;
            return;
        }
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        selectAll.checked = allChecked;
    }

    // Hapus satu item
    function removeRow(btn) {
        if(confirm('Hapus barang ini dari keranjang?')) {
            const item = btn.closest('.cart-item');
            item.remove();
            calculateTotal();
            checkEmptyCart();
        }
    }

    // Hapus item yang dipilih
    function removeSelected() {
        const checkboxes = document.querySelectorAll('.item-checkbox:checked');
        if (checkboxes.length === 0) {
            alert('Pilih barang yang ingin dihapus terlebih dahulu.');
            return;
        }
        
        if(confirm(`Hapus ${checkboxes.length} barang terpilih dari keranjang?`)) {
            checkboxes.forEach(cb => {
                cb.closest('.cart-item').remove();
            });
            calculateTotal();
            checkEmptyCart();
        }
    }

    // Cek apakah keranjang kosong setelah penghapusan
    function checkEmptyCart() {
        const container = document.getElementById('cart-container');
        const items = container.querySelectorAll('.cart-item');
        if (items.length === 0) {
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
            document.getElementById('selectAll').checked = false;
            document.getElementById('selectAll').disabled = true;
        }
    }

    // Aksi Lanjut ke Checkout
    function proceedToCheckout() {
        // Disini kamu kumpulkan data item yg dicentang lalu kirim ke halaman shipping.php
        window.location.href = '<?= BASE_URL ?>/pages/shipping.php'; // Hubungkan ke file pengiriman (Fig. 23)
    }

    // Kalkulasi awal saat halaman dimuat
    document.addEventListener('DOMContentLoaded', () => {
        calculateTotal();
    });
</script>


<script>
// Fix checkout navigation
document.addEventListener('DOMContentLoaded', function() {
    // Cari semua tombol checkout
    const btns = document.querySelectorAll('button, a');
    btns.forEach(btn => {
        const txt = btn.textContent.trim().toLowerCase();
        if (txt.includes('checkout') || txt.includes('lanjut ke pembayaran') || txt.includes('beli sekarang')) {
            if (btn.tagName === 'A') {
                btn.href = '<?= BASE_URL ?>/pages/shipping.php';
            } else {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.location.href = '<?= BASE_URL ?>/pages/shipping.php';
                });
            }
        }
    });
    
    // Sync cart badge
    const cc = parseInt(localStorage.getItem('em_cart') || '0');
    const b = document.getElementById('cart-count');
    if (b && cc > 0) { b.textContent = cc; b.classList.remove('hidden'); }
});

// Toast function
function showToast(msg, type='success') {
    let s = document.getElementById('toast-stack-cart');
    if (!s) { s = document.createElement('div'); s.id='toast-stack-cart'; s.style.cssText='position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:10px'; document.body.appendChild(s); }
    const t = document.createElement('div');
    const c = type==='success'?'#10B981':type==='error'?'#EF4444':'#1D4ED8';
    t.style.cssText=`display:flex;align-items:center;gap:10px;padding:14px 16px;border-radius:14px;background:#fff;box-shadow:0 8px 24px -4px rgba(0,0,0,0.12);font-size:14px;font-weight:600;color:#0F172A;border-left:4px solid ${c};min-width:260px`;
    t.innerHTML=msg+'<button onclick="this.parentElement.remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;color:#94a3b8;font-size:16px">×</button>';
    s.appendChild(t);
    setTimeout(()=>t.remove(), 3000);
}
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>