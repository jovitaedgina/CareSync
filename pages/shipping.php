<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle   = 'Pengiriman & Ekspedisi — CareSync';
$currentPage = 'marketplace'; // Tetap di lingkup apotek

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
    
    /* Styling khusus untuk Radio Button Ekspedisi agar terlihat seperti Card */
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

/* * MOCK DATA CHECKOUT
 * Nanti data ini diambil dari session/database (cart user, alamat user)
 */
$userAddress = [
    'label' => 'Rumah',
    'name' => 'Jovita Edgina',
    'phone' => '0812-3456-7890',
    'address' => 'Jl. Siliwangi No. 123, Kahuripan, Tawang, Kota Tasikmalaya, Jawa Barat 46115',
    'note' => 'Pagar warna hitam, rumah cat putih.'
];

$checkoutItems = [
    [
        'name' => 'Blackmores Vitamin C 500mg - 60 Tablet',
        'price' => 120000,
        'qty' => 1,
        'icon' => 'fa-bottle-droplet',
        'weight' => 200 // gram
    ],
    [
        'name' => 'Sensi Masker Medis 3-Ply - Isi 50 Pcs',
        'price' => 35000,
        'qty' => 2,
        'icon' => 'fa-mask-face',
        'weight' => 300 // gram
    ],
];

// Kalkulasi subtotal barang
$subtotal = 0;
$totalWeight = 0;
foreach ($checkoutItems as $item) {
    $subtotal += ($item['price'] * $item['qty']);
    $totalWeight += ($item['weight'] * $item['qty']);
}

// Mock Data Ekspedisi
$couriers = [
    ['id' => 'instan1', 'name' => 'GoSend Instant', 'etd' => '1 - 3 Jam', 'price' => 15000, 'icon' => 'fa-motorcycle', 'color' => 'text-emerald-500 bg-emerald-50'],
    ['id' => 'instan2', 'name' => 'GrabExpress', 'etd' => '1 - 3 Jam', 'price' => 16000, 'icon' => 'fa-motorcycle', 'color' => 'text-emerald-500 bg-emerald-50'],
    ['id' => 'reg1', 'name' => 'JNE Reguler', 'etd' => '2 - 3 Hari', 'price' => 12000, 'icon' => 'fa-truck-fast', 'color' => 'text-blue-500 bg-blue-50'],
    ['id' => 'reg2', 'name' => 'J&T Express', 'etd' => '2 - 3 Hari', 'price' => 11000, 'icon' => 'fa-truck-fast', 'color' => 'text-red-500 bg-red-50'],
];
?>

<div class="bg-slate-50 min-h-screen py-8" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 m-0">Pengiriman</h1>
            <p class="text-slate-500 font-medium mt-2 text-sm">Pilih alamat dan metode pengiriman untuk pesanan Anda.</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            
            <div class="w-full lg:w-2/3 flex flex-col gap-6">
                
                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 sm:p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-extrabold text-lg text-slate-900 m-0 flex items-center gap-2">
                            <i class="fa-solid fa-location-dot text-primary"></i> Alamat Pengiriman
                        </h3>
                        <button class="text-sm font-bold text-primary hover:text-blue-800 bg-transparent border-none cursor-pointer smooth-transition">
                            Ubah Alamat
                        </button>
                    </div>
                    
                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1.5 h-full bg-primary"></div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="font-extrabold text-slate-900 text-base"><?= $userAddress['name'] ?></span>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider px-2 py-1 rounded-md bg-slate-200 text-slate-600">
                                <?= $userAddress['label'] ?>
                            </span>
                        </div>
                        <div class="text-slate-600 text-sm font-medium mb-1"><?= $userAddress['phone'] ?></div>
                        <div class="text-slate-600 text-sm leading-relaxed mb-3"><?= $userAddress['address'] ?></div>
                        <div class="text-xs font-bold text-slate-400 flex items-start gap-1">
                            <i class="fa-solid fa-thumbtack mt-0.5"></i> Catatan: <?= $userAddress['note'] ?>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 sm:p-8">
                    <h3 class="font-extrabold text-lg text-slate-900 mb-6 m-0 flex items-center gap-2">
                        <i class="fa-solid fa-box-open text-primary"></i> Barang yang Dibeli
                    </h3>
                    
                    <div class="flex flex-col gap-4">
                        <?php foreach ($checkoutItems as $item): ?>
                        <div class="flex gap-4 items-center pb-4 border-b border-slate-50 last:border-0 last:pb-0">
                            <div class="w-16 h-16 bg-slate-50 rounded-xl flex items-center justify-center flex-shrink-0 relative overflow-hidden border border-slate-100">
                                <i class="fa-solid <?= $item['icon'] ?> text-2xl text-slate-300"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-slate-900 text-sm m-0 line-clamp-1"><?= $item['name'] ?></h4>
                                <div class="text-xs text-slate-500 mt-1"><?= $item['qty'] ?> Barang x Rp <?= number_format($item['price'], 0, ',', '.') ?></div>
                            </div>
                            <div class="font-extrabold text-slate-900 text-sm">
                                Rp <?= number_format($item['price'] * $item['qty'], 0, ',', '.') ?>
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
                        <span class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-lg">Total Berat: <?= $totalWeight ?>gr</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php foreach ($couriers as $index => $c): ?>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="courier" value="<?= $c['price'] ?>" class="peer courier-radio hidden" onchange="updateShippingCost(this)" <?= $index === 0 ? 'checked' : '' ?>>
                            
                            <div class="border-2 border-slate-100 rounded-2xl p-4 hover:border-blue-300 smooth-transition h-full flex flex-col relative overflow-hidden">
                                
                                <div class="check-icon absolute top-4 right-4 w-6 h-6 bg-primary rounded-full text-white flex items-center justify-center opacity-0 scale-50 smooth-transition shadow-sm">
                                    <i class="fa-solid fa-check text-xs"></i>
                                </div>

                                <div class="w-10 h-10 rounded-full flex items-center justify-center mb-3 <?= $c['color'] ?>">
                                    <i class="fa-solid <?= $c['icon'] ?> text-sm"></i>
                                </div>
                                <h4 class="font-extrabold text-slate-900 text-[15px] m-0 mb-1"><?= $c['name'] ?></h4>
                                <div class="text-xs font-semibold text-slate-500 mb-3 flex items-center gap-1">
                                    <i class="fa-regular fa-clock"></i> Estimasi: <?= $c['etd'] ?>
                                </div>
                                <div class="mt-auto font-extrabold text-primary text-base">
                                    Rp <?= number_format($c['price'], 0, ',', '.') ?>
                                </div>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>

            <div class="w-full lg:w-1/3">
                <div class="bg-white rounded-[2rem] p-6 lg:p-8 border border-slate-100 shadow-sm sticky top-28">
                    <h3 class="font-extrabold text-lg text-slate-900 mb-6 m-0 border-b border-slate-100 pb-4">Ringkasan Belanja</h3>
                    
                    <div class="flex flex-col gap-4 mb-6">
                        <div class="flex justify-between items-center text-sm font-medium text-slate-600">
                            <span>Total Harga Barang</span>
                            <span class="font-bold text-slate-800">Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
                        </div>
                        <div class="flex justify-between items-center text-sm font-medium text-slate-600">
                            <span>Total Ongkos Kirim</span>
                            <span id="summary-shipping" class="font-bold text-slate-800">Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center text-sm font-medium text-slate-600">
                            <span>Biaya Layanan & Asuransi</span>
                            <span class="font-bold text-slate-800">Rp 2.000</span>
                        </div>
                    </div>
                    
                    <hr class="border-slate-100 border-dashed mb-6">
                    
                    <div class="flex justify-between items-center mb-8">
                        <span class="font-extrabold text-slate-900">Total Tagihan</span>
                        <span id="summary-total" class="font-extrabold text-primary text-2xl tracking-tight">Rp 0</span>
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
    </div>
</div>

<script>
    // Data Dasar PHP dilempar ke JS
    const subtotal = <?= $subtotal ?>;
    const serviceFee = 2000;

    // Format Rupiah
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka);
    }

    // Update Harga Ongkir dan Total Tagihan
    function updateShippingCost(radioElement) {
        const shippingCost = parseInt(radioElement.value);
        const grandTotal = subtotal + shippingCost + serviceFee;

        // Update UI Ringkasan
        document.getElementById('summary-shipping').textContent = `Rp ${formatRupiah(shippingCost)}`;
        document.getElementById('summary-total').textContent = `Rp ${formatRupiah(grandTotal)}`;
    }

    // Jalankan kalkulasi pertama kali saat halaman dimuat (agar default radio button terpilih langsung dihitung)
    document.addEventListener('DOMContentLoaded', () => {
        const defaultSelectedCourier = document.querySelector('input[name="courier"]:checked');
        if(defaultSelectedCourier) {
            updateShippingCost(defaultSelectedCourier);
        }
    });

    // Lanjut ke Pembayaran
    function proceedToPayment() {
        // Cek apakah user sudah milih kurir
        const selectedCourier = document.querySelector('input[name="courier"]:checked');
        if (!selectedCourier) {
            alert('Mohon pilih metode pengiriman (Ekspedisi) terlebih dahulu.');
            return;
        }

        // Pindah ke halaman payment (Fig. 24)
        window.location.href = '<?= BASE_URL ?>/pages/payment.php';
    }
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>