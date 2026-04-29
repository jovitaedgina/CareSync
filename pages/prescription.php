<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/booking_helpers.php';
require_once __DIR__ . '/../includes/marketplace_helpers.php';

requireLogin();

$pageTitle = 'E-Resep Dokter - CareSync';
$currentPage = 'history';
$user = currentUser();
$userId = (int) ($user['id'] ?? 0);
$requestedPrescriptionId = (int) ($_GET['id'] ?? $_GET['prescription_id'] ?? 0);

$prescriptions = getPatientPrescriptions($pdo, $userId);
$selectedPrescription = null;

if ($requestedPrescriptionId > 0) {
    $selectedPrescription = getPatientPrescriptionDetail($pdo, $userId, $requestedPrescriptionId);
}

if (!$selectedPrescription && $prescriptions) {
    $selectedPrescription = $prescriptions[0];
}

$orderDetail = null;
if ($selectedPrescription && (int) $selectedPrescription['order_id'] > 0) {
    $orderDetail = getMarketplaceOrderDetail($pdo, $userId, (int) $selectedPrescription['order_id']);
}

$paymentPayload = $orderDetail['payment_payload'] ?? [];
$paymentInstructions = trim((string) ($orderDetail['payment_instructions'] ?? ''));
$paymentStatus = strtolower((string) ($orderDetail['payment_status'] ?? 'pending'));
$canConfirmPayment = $orderDetail && in_array($paymentStatus, ['pending', 'menunggu', ''], true);

$extraHead = '
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: { sans: [\'"Plus Jakarta Sans"\', \'sans-serif\'] },
                colors: {
                    primary: \'#1D4ED8\',
                    primaryLight: \'#EFF6FF\',
                    accent: \'#10B981\',
                    dark: \'#0F172A\',
                    textSoft: \'#64748B\'
                },
                boxShadow: { floating: \'0 20px 40px -15px rgba(29, 78, 216, 0.15)\' }
            }
        }
    }
</script>
<style>
    body { background-color: #F8FAFC; margin: 0; }
    .smooth-transition { transition: all 0.3s ease-in-out; }
    .prescription-paper {
        background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
        background-size: 28px 28px;
        background-color: white;
    }
</style>
';

include __DIR__ . '/../includes/header.php';
?>

<div class="bg-slate-50 min-h-screen py-8" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex text-sm font-medium text-slate-500 mb-8 gap-2 items-center">
            <a href="<?= BASE_URL ?>/pages/dashboard.php" class="hover:text-primary smooth-transition no-underline text-slate-500">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <a href="<?= BASE_URL ?>/pages/history.php" class="hover:text-primary smooth-transition no-underline text-slate-500">Riwayat Medis</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-slate-800 font-bold truncate">E-Resep</span>
        </nav>

        <?php if (!$selectedPrescription): ?>
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 p-12 text-center">
                <div class="w-20 h-20 rounded-full bg-primaryLight text-primary flex items-center justify-center mx-auto mb-5 text-3xl">
                    <i class="fa-solid fa-file-prescription"></i>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 m-0 mb-2">Belum ada e-resep aktif</h1>
                <p class="text-slate-500 font-medium max-w-lg mx-auto m-0 mb-6">Setelah dokter menuliskan resep dari sesi konsultasi, detail obat dan status tebusannya akan muncul di sini.</p>
                <a href="<?= BASE_URL ?>/pages/history.php" class="inline-flex items-center gap-2 bg-primary text-white px-6 py-3 rounded-xl font-bold shadow-md hover:bg-blue-800 smooth-transition no-underline">
                    <i class="fa-solid fa-clock-rotate-left"></i> Buka Riwayat Konsultasi
                </a>
            </div>
        <?php else: ?>
            <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 m-0">E-Resep Dokter</h1>
                    <p class="text-slate-500 font-medium mt-2 text-sm">Resep ini terbit dari sesi konsultasi Anda dan otomatis tersambung ke alur pemesanan apotek CareSync.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="<?= BASE_URL ?>/pages/consultation.php?consultation_id=<?= (int) $selectedPrescription['consultation_id'] ?>" class="bg-white px-5 py-2.5 rounded-xl border border-slate-200 shadow-sm text-sm font-bold text-slate-700 hover:text-primary hover:border-primary smooth-transition flex items-center gap-2 no-underline">
                        <i class="fa-solid fa-comments"></i> Buka Konsultasi
                    </a>
                    <?php if ($orderDetail): ?>
                        <a href="<?= BASE_URL ?>/pages/tracking.php?order_id=<?= (int) $orderDetail['id'] ?>" class="bg-primary text-white px-5 py-2.5 rounded-xl shadow-sm text-sm font-bold hover:bg-blue-800 smooth-transition flex items-center gap-2 no-underline">
                            <i class="fa-solid fa-truck-fast"></i> Lacak Pesanan
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-[280px_minmax(0,1fr)_360px] gap-8 items-start">
                <aside class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden xl:sticky xl:top-28">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Daftar Resep</div>
                        <div class="text-lg font-extrabold text-slate-900 mt-1"><?= count($prescriptions) ?> resep</div>
                    </div>
                    <div class="p-3 flex flex-col gap-3 max-h-[75vh] overflow-y-auto">
                        <?php foreach ($prescriptions as $prescription): ?>
                            <?php $isActive = (int) $prescription['id'] === (int) $selectedPrescription['id']; ?>
                            <a href="<?= BASE_URL ?>/pages/prescription.php?id=<?= (int) $prescription['id'] ?>" class="block rounded-2xl border <?= $isActive ? 'border-blue-200 bg-blue-50' : 'border-slate-200 bg-white hover:border-blue-200 hover:bg-slate-50' ?> p-4 no-underline smooth-transition">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="text-xs font-bold uppercase tracking-wider text-slate-400"><?= htmlspecialchars($prescription['code']) ?></div>
                                        <div class="text-sm font-extrabold text-slate-900 mt-1"><?= htmlspecialchars($prescription['doctor_name']) ?></div>
                                        <div class="text-xs font-semibold text-primary mt-1"><?= htmlspecialchars($prescription['specialization']) ?></div>
                                    </div>
                                    <span class="text-[10px] font-bold px-2 py-1 rounded-full border <?= $prescription['status_tone'] === 'blue' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-amber-50 text-amber-700 border-amber-200' ?>">
                                        <?= htmlspecialchars($prescription['status_label']) ?>
                                    </span>
                                </div>
                                <div class="text-xs text-slate-500 font-medium mt-3"><?= htmlspecialchars($prescription['issued_label']) ?></div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </aside>

                <div class="prescription-paper rounded-[2rem] shadow-floating border border-slate-200 overflow-hidden relative">
                    <div class="bg-white/90 backdrop-blur-sm border-b-2 border-slate-200 p-8">
                        <div class="flex justify-between items-start mb-6 gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center text-white text-2xl shadow-md">
                                    <i class="fa-solid fa-notes-medical"></i>
                                </div>
                                <div>
                                    <h2 class="font-extrabold text-xl text-dark tracking-tight m-0">CareSync</h2>
                                    <p class="text-xs font-bold text-primary tracking-widest uppercase m-0 mt-0.5">Telemedicine Prescription</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs font-bold text-slate-400 mb-1">No. Resep</div>
                                <div class="font-extrabold text-slate-800 text-sm"><?= htmlspecialchars($selectedPrescription['code']) ?></div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row justify-between gap-6 bg-slate-50 rounded-2xl p-5 border border-slate-100">
                            <div>
                                <h3 class="font-extrabold text-slate-900 text-sm m-0 mb-1"><?= htmlspecialchars($selectedPrescription['doctor_name']) ?></h3>
                                <p class="text-xs font-medium text-slate-500 m-0 mb-1"><?= htmlspecialchars($selectedPrescription['specialization']) ?></p>
                                <p class="text-[10px] font-bold text-slate-400 m-0">Konsultasi #CS-<?= (int) $selectedPrescription['consultation_id'] ?></p>
                            </div>
                            <div class="sm:text-right">
                                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tanggal Diterbitkan</div>
                                <p class="text-xs font-extrabold text-slate-800 m-0"><i class="fa-regular fa-calendar text-primary mr-1"></i> <?= htmlspecialchars($selectedPrescription['issued_label']) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/80 backdrop-blur-sm p-8 min-h-[400px] relative">
                        <i class="fa-solid fa-staff-snake text-[220px] text-slate-100 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 opacity-30 pointer-events-none"></i>

                        <div class="mb-6 border-b border-slate-200 pb-4 relative z-10">
                            <div class="flex flex-wrap items-center gap-4">
                                <div class="text-sm">
                                    <span class="text-slate-400 font-medium mr-2">Pro:</span>
                                    <span class="font-extrabold text-slate-900 text-base"><?= htmlspecialchars($user['name'] ?? 'Pasien CareSync') ?></span>
                                </div>
                                <div class="w-1 h-1 bg-slate-300 rounded-full"></div>
                                <div class="text-sm font-bold text-slate-600"><?= htmlspecialchars($selectedPrescription['consultation_status']) ?></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8 relative z-10">
                            <div class="bg-slate-50 rounded-2xl border border-slate-100 p-4">
                                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Diagnosis</div>
                                <div class="text-sm font-semibold text-slate-800 leading-relaxed"><?= htmlspecialchars($selectedPrescription['diagnosis']) ?></div>
                            </div>
                            <div class="bg-slate-50 rounded-2xl border border-slate-100 p-4">
                                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Catatan Dokter</div>
                                <div class="text-sm font-semibold text-slate-800 leading-relaxed"><?= htmlspecialchars($selectedPrescription['notes'] !== '' ? $selectedPrescription['notes'] : 'Tidak ada catatan tambahan.') ?></div>
                            </div>
                        </div>

                        <div class="font-serif italic font-bold text-4xl text-slate-800 mb-6 relative z-10">R/</div>

                        <div class="flex flex-col gap-6 relative z-10 pl-2 md:pl-8">
                            <?php foreach ($selectedPrescription['items'] as $item): ?>
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <h4 class="font-extrabold text-slate-900 text-lg m-0 mb-1"><?= htmlspecialchars($item['name']) ?></h4>
                                        <div class="text-sm font-semibold text-slate-700 italic m-0 mb-2">S. <?= htmlspecialchars($item['instruction'] !== '' ? $item['instruction'] : 'Ikuti anjuran dokter') ?></div>
                                        <div class="text-xs text-slate-500 font-medium"><?= htmlspecialchars($item['dose'] !== '' ? $item['dose'] : 'Dosis mengikuti item resep') ?></div>
                                    </div>
                                    <div class="font-extrabold text-slate-800 text-base bg-white border border-slate-200 px-3 py-1 rounded-lg shadow-sm">
                                        No. <?= (int) $item['qty'] ?>
                                    </div>
                                </div>
                                <hr class="border-slate-200 border-dashed">
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="bg-white/90 backdrop-blur-sm border-t border-slate-200 p-6 text-center relative z-10">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest m-0">Diterbitkan dari konsultasi digital CareSync</p>
                        <p class="font-serif italic text-primary font-bold text-2xl m-0 mt-2"><?= htmlspecialchars($selectedPrescription['doctor_name']) ?></p>
                    </div>
                </div>

                <aside class="bg-white rounded-[2rem] p-6 lg:p-8 border border-slate-100 shadow-sm xl:sticky xl:top-28">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-extrabold text-lg text-slate-900 m-0">Status Tebusan</h3>
                        <span class="text-xs font-extrabold px-3 py-1.5 rounded-lg border <?= $selectedPrescription['status_tone'] === 'blue' ? 'text-blue-700 bg-blue-50 border-blue-200' : 'text-amber-700 bg-amber-50 border-amber-200' ?>">
                            <?= htmlspecialchars($selectedPrescription['status_label']) ?>
                        </span>
                    </div>

                    <div class="flex flex-col gap-4 mb-6 max-h-[300px] overflow-y-auto pr-2">
                        <?php foreach ($selectedPrescription['items'] as $item): ?>
                            <div class="flex justify-between items-start bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <div>
                                    <div class="font-bold text-slate-800 text-sm"><?= htmlspecialchars($item['name']) ?></div>
                                    <div class="text-[10px] text-slate-500 font-semibold mt-1"><?= (int) $item['qty'] ?> x Rp <?= number_format((int) $item['price'], 0, ',', '.') ?></div>
                                    <div class="text-[10px] text-slate-400 font-medium mt-1">Stok apotek: <?= (int) $item['stock'] ?></div>
                                </div>
                                <div class="font-extrabold text-slate-900 text-sm">Rp <?= number_format((int) $item['subtotal'], 0, ',', '.') ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <hr class="border-slate-100 border-dashed mb-6">

                    <div class="space-y-3 mb-6 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-medium">Subtotal Obat</span>
                            <span class="font-bold text-slate-900">Rp <?= number_format((int) $selectedPrescription['subtotal'], 0, ',', '.') ?></span>
                        </div>
                        <?php if ($orderDetail): ?>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500 font-medium">Status Bayar</span>
                                <span class="font-bold <?= $paymentStatus === 'paid' ? 'text-emerald-600' : 'text-amber-600' ?>"><?= htmlspecialchars($orderDetail['payment_status']) ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500 font-medium">Status Pengiriman</span>
                                <span class="font-bold text-slate-900"><?= htmlspecialchars($orderDetail['shipping_status']) ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500 font-medium">Total Pesanan</span>
                                <span class="font-bold text-primary">Rp <?= number_format((int) $orderDetail['total'], 0, ',', '.') ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="flex flex-col gap-3">
                        <?php if ($orderDetail): ?>
                            <?php if ($canConfirmPayment): ?>
                                <button type="button" id="open-payment-modal" class="w-full bg-primary text-white border-none hover:bg-blue-800 py-4 rounded-2xl font-extrabold shadow-floating active:scale-95 smooth-transition flex items-center justify-center gap-2 cursor-pointer text-base">
                                    <i class="fa-solid fa-qrcode"></i> Lihat Instruksi Pembayaran
                                </button>
                            <?php endif; ?>
                            <a href="<?= BASE_URL ?>/pages/tracking.php?order_id=<?= (int) $orderDetail['id'] ?>" class="w-full bg-white text-primary border border-blue-200 hover:bg-blue-50 py-3.5 rounded-2xl font-bold active:scale-95 smooth-transition flex items-center justify-center text-sm no-underline cursor-pointer box-border">
                                Lacak Pesanan Apotek
                            </a>
                        <?php else: ?>
                            <div class="bg-amber-50 rounded-xl p-4 border border-amber-200 text-sm font-medium text-amber-800">
                                Pesanan apotek untuk resep ini belum terbentuk. Simpan ulang resep dari sisi dokter jika item obat baru saja diperbarui.
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($orderDetail): ?>
                        <div class="mt-6 bg-blue-50 rounded-xl p-4 flex items-start gap-3 border border-blue-100">
                            <i class="fa-solid fa-circle-info text-primary mt-0.5"></i>
                            <p class="text-[11px] text-slate-600 font-medium leading-relaxed m-0 text-justify">
                                Resep ini sudah tersambung ke pesanan apotek <strong><?= htmlspecialchars($orderDetail['order_code']) ?></strong>. Apoteker akan memproses obat setelah pembayaran tervalidasi.
                            </p>
                        </div>
                    <?php endif; ?>
                </aside>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($orderDetail && $canConfirmPayment): ?>
    <div id="payment-modal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-900/70 backdrop-blur-sm p-4">
        <div class="bg-white w-full max-w-md rounded-[2rem] shadow-xl overflow-hidden">
            <div class="bg-primary text-white p-6 flex items-center justify-between">
                <div>
                    <div class="text-xs uppercase tracking-wider font-bold text-blue-100">Pembayaran Resep</div>
                    <div class="text-lg font-extrabold mt-1"><?= htmlspecialchars($orderDetail['order_code']) ?></div>
                </div>
                <button type="button" id="close-payment-modal" class="w-10 h-10 rounded-full bg-white/15 text-white border-none cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="p-6 text-center">
                <div class="text-sm font-bold text-slate-500 mb-2"><?= htmlspecialchars($orderDetail['payment_method'] ?: 'Pembayaran Digital') ?></div>
                <div class="text-3xl font-extrabold text-primary mb-6">Rp <?= number_format((int) $orderDetail['total'], 0, ',', '.') ?></div>
                <?php if (($paymentPayload['type'] ?? '') === 'qris' && !empty($paymentPayload['qr_url'])): ?>
                    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 mb-4 inline-block">
                        <img src="<?= htmlspecialchars($paymentPayload['qr_url']) ?>" alt="QRIS Code" class="w-56 h-56 opacity-90 mix-blend-multiply">
                    </div>
                <?php elseif (!empty($orderDetail['payment_code'])): ?>
                    <div class="bg-slate-50 rounded-2xl border border-slate-200 p-4 mb-4">
                        <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Kode Pembayaran</div>
                        <div class="text-xl font-extrabold text-slate-900"><?= htmlspecialchars($orderDetail['payment_code']) ?></div>
                    </div>
                <?php endif; ?>

                <?php if ($paymentInstructions !== ''): ?>
                    <div class="bg-slate-50 rounded-2xl border border-slate-200 p-4 text-left text-sm font-medium text-slate-700 whitespace-pre-line"><?= htmlspecialchars($paymentInstructions) ?></div>
                <?php endif; ?>
            </div>
            <div class="p-6 bg-slate-50 border-t border-slate-100 flex flex-col gap-3">
                <button type="button" id="confirm-payment" class="w-full bg-primary text-white border-none hover:bg-blue-800 py-3.5 rounded-xl font-extrabold shadow-md cursor-pointer">
                    Saya Sudah Bayar
                </button>
                <button type="button" id="dismiss-payment-modal" class="w-full bg-transparent text-slate-500 border-none py-2 font-bold cursor-pointer text-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
    const BASE_URL = '<?= BASE_URL ?>';
    const paymentModal = document.getElementById('payment-modal');

    function togglePaymentModal(show) {
        paymentModal.classList.toggle('hidden', !show);
        paymentModal.classList.toggle('flex', show);
        document.body.style.overflow = show ? 'hidden' : '';
    }

    async function confirmPayment() {
        const button = document.getElementById('confirm-payment');
        button.disabled = true;
        button.textContent = 'Memproses...';

        try {
            const response = await fetch(`${BASE_URL}/api/marketplace/payment/confirm.php`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    order_id: <?= (int) $orderDetail['id'] ?>
                })
            });
            const result = await response.json();

            if (!response.ok || result.status !== 'success') {
                throw new Error(result.message || 'Pembayaran gagal dikonfirmasi.');
            }

            window.location.href = `${BASE_URL}/pages/tracking.php?order_id=<?= (int) $orderDetail['id'] ?>`;
        } catch (error) {
            alert(error.message || 'Pembayaran gagal dikonfirmasi.');
            button.disabled = false;
            button.textContent = 'Saya Sudah Bayar';
        }
    }

    document.getElementById('open-payment-modal')?.addEventListener('click', () => togglePaymentModal(true));
    document.getElementById('close-payment-modal')?.addEventListener('click', () => togglePaymentModal(false));
    document.getElementById('dismiss-payment-modal')?.addEventListener('click', () => togglePaymentModal(false));
    document.getElementById('confirm-payment')?.addEventListener('click', confirmPayment);
    paymentModal?.addEventListener('click', (event) => {
        if (event.target === paymentModal) {
            togglePaymentModal(false);
        }
    });
    </script>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
