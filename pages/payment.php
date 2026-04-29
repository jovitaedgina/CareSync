<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/marketplace_helpers.php';

requireLogin();

$pageTitle = 'Pembayaran - CareSync';
$currentPage = 'marketplace';
$userId = (int) (currentUser()['id'] ?? 0);
$checkout = getMarketplaceCheckoutContext($pdo, $userId);
$paymentMethods = getMarketplacePaymentMethods();

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
                boxShadow: {
                    floating: \'0 20px 40px -15px rgba(29, 78, 216, 0.15)\',
                    modal: \'0 25px 50px -12px rgba(0, 0, 0, 0.25)\'
                }
            }
        }
    }
</script>
<style>
    body { background-color: #F8FAFC; margin: 0; }
    .smooth-transition { transition: all 0.3s ease-in-out; }
    .payment-radio:checked + div {
        border-color: #1D4ED8;
        background-color: #EFF6FF;
    }
    .payment-radio:checked + div .radio-circle {
        border-color: #1D4ED8;
    }
    .payment-radio:checked + div .radio-circle::after {
        transform: scale(1);
    }
    @keyframes scan {
        0%, 100% { top: 10%; }
        50% { top: 90%; }
    }
    .scan-line {
        position: absolute; left: 10%; right: 10%; height: 3px;
        background: rgba(16, 185, 129, 0.8); box-shadow: 0 0 10px rgba(16, 185, 129, 0.8);
        animation: scan 2.5s infinite linear;
    }
</style>
';

include __DIR__ . '/../includes/header.php';
?>

<div class="bg-slate-50 min-h-screen py-8" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (!$checkout || empty($checkout['shipping'])): ?>
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-12 text-center">
            <i class="fa-solid fa-wallet text-5xl text-slate-200 mb-4"></i>
            <h1 class="text-2xl font-extrabold text-slate-900 m-0">Data pembayaran belum siap</h1>
            <p class="text-slate-500 font-medium mt-3 mb-6">Silakan kembali ke halaman pengiriman dan pilih kurir terlebih dahulu.</p>
            <a href="<?= BASE_URL ?>/pages/shipping.php" class="inline-flex px-6 py-3 bg-primary text-white rounded-xl font-bold no-underline">Kembali ke Pengiriman</a>
        </div>
        <?php else: ?>
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 m-0">Pilih Pembayaran</h1>
                <p class="text-slate-500 font-medium mt-2 text-sm">Pasien melakukan pembayaran digital, lalu payment gateway memverifikasi transaksi sebelum pesanan diproses apotek.</p>
            </div>
            <div class="bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm text-sm font-bold text-slate-700 w-fit">
                Checkout siap untuk <?= count($checkout['items']) ?> produk
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8 items-start">
            <div class="w-full lg:w-2/3 flex flex-col gap-6">
                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 sm:p-8">
                    <h3 class="font-extrabold text-lg text-slate-900 mb-5 m-0 flex items-center gap-2">
                        <i class="fa-solid fa-qrcode text-primary"></i> E-Wallet & QRIS
                    </h3>
                    <div class="flex flex-col gap-3">
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="payment_method" value="QRIS" class="peer payment-radio hidden" onchange="selectPayment('QRIS')">
                            <div class="border-2 border-slate-100 rounded-2xl p-4 hover:border-blue-300 smooth-transition flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-8 bg-slate-800 text-white rounded flex items-center justify-center font-extrabold text-xs italic tracking-widest border border-slate-200">QRIS</div>
                                    <div>
                                        <h4 class="font-bold text-slate-900 text-sm m-0">QRIS (Semua E-Wallet & Bank)</h4>
                                        <p class="text-[11px] text-emerald-500 font-bold m-0 mt-0.5"><i class="fa-solid fa-bolt"></i> Terverifikasi otomatis</p>
                                    </div>
                                </div>
                                <div class="radio-circle w-5 h-5 rounded-full border-2 border-slate-300 relative flex items-center justify-center smooth-transition">
                                    <div class="w-2.5 h-2.5 bg-primary rounded-full absolute scale-0 smooth-transition origin-center"></div>
                                </div>
                            </div>
                        </label>

                        <label class="relative cursor-pointer group">
                            <input type="radio" name="payment_method" value="GoPay" class="peer payment-radio hidden" onchange="selectPayment('GoPay')">
                            <div class="border-2 border-slate-100 rounded-2xl p-4 hover:border-blue-300 smooth-transition flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-8 bg-[#00AED6] text-white rounded flex items-center justify-center font-extrabold text-[10px] border border-slate-200">gopay</div>
                                    <h4 class="font-bold text-slate-900 text-sm m-0">GoPay</h4>
                                </div>
                                <div class="radio-circle w-5 h-5 rounded-full border-2 border-slate-300 relative flex items-center justify-center smooth-transition">
                                    <div class="w-2.5 h-2.5 bg-primary rounded-full absolute scale-0 smooth-transition origin-center"></div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 sm:p-8">
                    <h3 class="font-extrabold text-lg text-slate-900 mb-5 m-0 flex items-center gap-2">
                        <i class="fa-solid fa-building-columns text-primary"></i> Transfer Virtual Account (VA)
                    </h3>
                    <div class="flex flex-col gap-3">
                        <?php foreach (['VA_BCA', 'VA_MANDIRI', 'VA_BNI', 'VA_BRI'] as $methodCode): ?>
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="payment_method" value="<?= htmlspecialchars($methodCode) ?>" class="peer payment-radio hidden" onchange="selectPayment('<?= htmlspecialchars($paymentMethods[$methodCode]['name']) ?>')">
                            <div class="border-2 border-slate-100 rounded-2xl p-4 hover:border-blue-300 smooth-transition flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-8 bg-primary text-white rounded flex items-center justify-center font-extrabold text-[10px] border border-slate-200 italic">
                                        <?= htmlspecialchars(str_replace(['VA_', 'BRI', 'BCA', 'BNI', 'MANDIRI'], ['VA ', 'BRI', 'BCA', 'BNI', 'MDR'], $methodCode)) ?>
                                    </div>
                                    <h4 class="font-bold text-slate-900 text-sm m-0"><?= htmlspecialchars($paymentMethods[$methodCode]['name']) ?></h4>
                                </div>
                                <div class="radio-circle w-5 h-5 rounded-full border-2 border-slate-300 relative flex items-center justify-center smooth-transition">
                                    <div class="w-2.5 h-2.5 bg-primary rounded-full absolute scale-0 smooth-transition origin-center"></div>
                                </div>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="w-full lg:w-1/3">
                <div class="bg-white rounded-[2rem] p-6 lg:p-8 border border-slate-100 shadow-sm sticky top-28">
                    <h3 class="font-extrabold text-lg text-slate-900 mb-6 m-0 border-b border-slate-100 pb-4">Ringkasan Pembayaran</h3>

                    <div class="flex flex-col gap-4 mb-6 text-sm">
                        <div class="flex justify-between items-center text-slate-600">
                            <span>Subtotal Barang</span>
                            <span class="font-bold text-slate-800">Rp <?= number_format($checkout['subtotal'], 0, ',', '.') ?></span>
                        </div>
                        <div class="flex justify-between items-center text-slate-600">
                            <span>Ongkos Kirim</span>
                            <span class="font-bold text-slate-800">Rp <?= number_format($checkout['shipping_price'], 0, ',', '.') ?></span>
                        </div>
                        <div class="flex justify-between items-center text-slate-600">
                            <span>Biaya Layanan</span>
                            <span class="font-bold text-slate-800">Rp <?= number_format($checkout['service_fee'], 0, ',', '.') ?></span>
                        </div>
                    </div>

                    <div class="flex justify-between items-center mb-6">
                        <span class="font-bold text-slate-600">Total Tagihan</span>
                        <span class="font-extrabold text-primary text-2xl tracking-tight">Rp <?= number_format($checkout['grand_total'], 0, ',', '.') ?></span>
                    </div>

                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 mb-8">
                        <span class="text-xs font-bold text-slate-500 block mb-1">Metode Terpilih:</span>
                        <div id="selected-method-text" class="font-extrabold text-slate-900 text-sm">Belum ada yang dipilih</div>
                    </div>

                    <button id="btn-pay" disabled onclick="processPayment()" class="w-full bg-slate-300 text-white border-none py-4 rounded-2xl font-extrabold cursor-not-allowed smooth-transition flex items-center justify-center gap-2 text-base">
                        <i class="fa-solid fa-lock"></i> Bayar Sekarang
                    </button>

                    <p class="text-[10px] text-center text-slate-400 mt-4 font-semibold">
                        Selesaikan pembayaran dalam waktu 15 menit setelah instruksi gateway dibuat.
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<div id="paymentModal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-900/70 backdrop-blur-md opacity-0 transition-opacity duration-300 p-4">
    <div id="paymentModalContent" class="bg-white w-full max-w-md rounded-[2rem] shadow-modal overflow-hidden relative transform scale-95 transition-transform duration-300 flex flex-col">
        <div class="bg-primary text-white p-6 flex justify-between items-center">
            <div>
                <h3 class="font-extrabold text-lg m-0">Menunggu Pembayaran</h3>
                <p class="text-blue-200 text-xs font-medium mt-1">Selesaikan dalam <span id="countdown" class="font-bold text-white">15:00</span></p>
            </div>
            <button onclick="closePaymentModal()" class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center text-white transition-colors border-none cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="p-6 md:p-8 flex flex-col items-center text-center" id="modal-payment-body"></div>

        <div class="p-6 bg-slate-50 border-t border-slate-100 text-center">
            <button onclick="confirmSuccess()" class="w-full bg-primary text-white border-none hover:bg-blue-800 py-3.5 rounded-xl font-extrabold shadow-md active:scale-95 smooth-transition cursor-pointer">
                Saya Sudah Bayar
            </button>
            <button onclick="closePaymentModal()" class="w-full mt-3 bg-transparent text-slate-500 border-none hover:text-slate-800 py-2 font-bold cursor-pointer text-sm">
                Tutup Instruksi
            </button>
        </div>
    </div>
</div>

<script>
const BASE_URL = '<?= BASE_URL ?>';
const totalPayment = <?= (int) ($checkout['grand_total'] ?? 0) ?>;
let selectedMethodId = '';
let selectedMethodName = '';
let activePayment = null;
let countdownInterval = null;

function formatRupiah(number) {
    return new Intl.NumberFormat('id-ID').format(number);
}

function selectPayment(methodName) {
    const radios = document.getElementsByName('payment_method');
    for (const radio of radios) {
        if (radio.checked) {
            selectedMethodId = radio.value;
            break;
        }
    }

    selectedMethodName = methodName;
    document.getElementById('selected-method-text').textContent = selectedMethodName;

    const btnPay = document.getElementById('btn-pay');
    btnPay.disabled = false;
    btnPay.classList.remove('bg-slate-300', 'cursor-not-allowed');
    btnPay.classList.add('bg-primary', 'hover:bg-blue-800', 'shadow-floating');
}

function renderPaymentInstruction(data) {
    const body = document.getElementById('modal-payment-body');
    const gateway = data.gateway;
    const order = data.order;

    if (gateway.type === 'qris') {
        body.innerHTML = `
            <div class="text-sm font-bold text-slate-500 mb-2">Order ${order.order_code}</div>
            <div class="text-3xl font-extrabold text-primary mb-6">Rp ${formatRupiah(totalPayment)}</div>
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 mb-4 relative overflow-hidden inline-block">
                <img src="${gateway.qr_url}" alt="QRIS Code" class="w-48 h-48 opacity-80 mix-blend-multiply">
                <div class="scan-line"></div>
            </div>
            <div class="text-sm font-bold text-slate-800 mb-2">Scan QR Code di aplikasi pembayaran Anda</div>
            <p class="text-xs text-slate-500">Gateway reference: <strong>${gateway.reference}</strong></p>
        `;
        return;
    }

    if (gateway.type === 'va') {
        body.innerHTML = `
            <div class="text-sm font-bold text-slate-500 mb-2">Order ${order.order_code}</div>
            <div class="text-3xl font-extrabold text-primary mb-6">Rp ${formatRupiah(totalPayment)}</div>
            <div class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-6 mb-4">
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Nomor Virtual Account</div>
                <div class="flex items-center justify-between bg-white border border-slate-300 rounded-xl p-3">
                    <span class="font-extrabold text-xl text-slate-900 tracking-widest">${gateway.va_number}</span>
                    <button onclick="navigator.clipboard.writeText('${gateway.va_number}')" class="text-primary border-none bg-transparent cursor-pointer font-bold text-sm">
                        <i class="fa-regular fa-copy"></i> Salin
                    </button>
                </div>
            </div>
            <p class="text-xs text-slate-500">Selesaikan pembayaran ${gateway.method_name} sebelum ${gateway.expires_at}.</p>
        `;
        return;
    }

    body.innerHTML = `
        <div class="text-sm font-bold text-slate-500 mb-2">Order ${order.order_code}</div>
        <div class="text-3xl font-extrabold text-primary mb-6">Rp ${formatRupiah(totalPayment)}</div>
        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center border-4 border-slate-100 mb-4 animate-pulse">
            <i class="fa-solid fa-mobile-screen text-3xl text-primary"></i>
        </div>
        <h4 class="font-bold text-slate-800 text-base mb-2">Konfirmasi di aplikasi ${gateway.method_name}</h4>
        <p class="text-xs text-slate-500">${gateway.deeplink_label}</p>
    `;
}

function openPaymentModal() {
    const modal = document.getElementById('paymentModal');
    const content = document.getElementById('paymentModalContent');

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        modal.classList.add('opacity-100');
        content.classList.remove('scale-95');
        content.classList.add('scale-100');
        document.body.style.overflow = 'hidden';
    }, 10);
}

function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    const content = document.getElementById('paymentModalContent');

    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0');
    content.classList.remove('scale-100');
    content.classList.add('scale-95');
    document.body.style.overflow = '';

    setTimeout(() => {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }, 300);
}

function startTimer(expiresAt) {
    if (countdownInterval) {
        clearInterval(countdownInterval);
    }

    const countdownElement = document.getElementById('countdown');
    const deadline = new Date(expiresAt.replace(' ', 'T'));

    countdownInterval = setInterval(() => {
        const remainingMs = deadline.getTime() - Date.now();
        if (remainingMs <= 0) {
            countdownElement.textContent = '00:00';
            clearInterval(countdownInterval);
            return;
        }

        const totalSeconds = Math.floor(remainingMs / 1000);
        const minutes = String(Math.floor(totalSeconds / 60)).padStart(2, '0');
        const seconds = String(totalSeconds % 60).padStart(2, '0');
        countdownElement.textContent = `${minutes}:${seconds}`;
    }, 1000);
}

async function processPayment() {
    if (!selectedMethodId) {
        return;
    }

    try {
        const response = await fetch(`${BASE_URL}/api/marketplace/payment/create.php`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ payment_method: selectedMethodId })
        });
        const result = await response.json();
        if (!response.ok || result.status !== 'success') {
            throw new Error(result.message || 'Instruksi pembayaran gagal dibuat.');
        }

        activePayment = result.data;
        renderPaymentInstruction(result.data);
        openPaymentModal();
        startTimer(result.data.gateway.expires_at);
    } catch (error) {
        alert(error.message);
    }
}

async function confirmSuccess() {
    if (!activePayment || !activePayment.order) {
        alert('Instruksi pembayaran belum dibuat.');
        return;
    }

    try {
        const response = await fetch(`${BASE_URL}/api/marketplace/payment/confirm.php`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order_id: activePayment.order.id })
        });
        const result = await response.json();
        if (!response.ok || result.status !== 'success') {
            throw new Error(result.message || 'Pembayaran gagal dikonfirmasi.');
        }

        window.location.href = `${BASE_URL}/pages/tracking.php?order_id=${result.data.id}`;
    } catch (error) {
        alert(error.message);
    }
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
