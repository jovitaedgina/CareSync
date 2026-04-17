<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle   = 'Resep Digital — CareSync';
$currentPage = 'history'; // Bisa masuk ke menu Riwayat atau menu khusus

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
    
    /* Dekorasi Kertas Resep (Watermark) */
    .prescription-paper {
        background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
        background-size: 30px 30px;
        background-color: white;
    }
</style>
';

include __DIR__ . '/../includes/header.php';

/* * MOCK DATA RESEP DIGITAL
 * Data ini didapat dari hasil konsultasi dokter.
 */
$prescriptionData = [
    'id' => 'RSP-20260417-001',
    'date' => '17 April 2026, 10:45 WIB',
    'status' => 'Belum Ditebus',
    'doctor' => [
        'name' => 'dr. Susanti Wulandari, Sp.KK',
        'sip' => 'SIP. 123/KK/2020',
        'specialty' => 'Spesialis Kulit & Kelamin',
        'hospital' => 'CareSync Telemedicine'
    ],
    'patient' => [
        'name' => 'Jovita Edgina',
        'age' => '20 Tahun',
        'weight' => '52 kg',
        'gender' => 'Perempuan'
    ],
    'medicines' => [
        [
            'name' => 'Doxycycline 500mg',
            'type' => 'Kapsul',
            'qty' => 10,
            'dosis' => '2 x sehari 1 kapsul (Habiskan)',
            'price' => 25000,
            'icon' => 'fa-capsules'
        ],
        [
            'name' => 'Benzolac CL 5%',
            'type' => 'Salep 10gr',
            'qty' => 1,
            'dosis' => 'Dioleskan tipis pada area berjerawat 1x sehari (Malam)',
            'price' => 45000,
            'icon' => 'fa-tube'
        ],
        [
            'name' => 'Cetirizine 10mg',
            'type' => 'Tablet',
            'qty' => 5,
            'dosis' => '1 x sehari 1 tablet (Jika gatal)',
            'price' => 15000,
            'icon' => 'fa-tablets'
        ]
    ]
];

// Hitung total harga resep
$totalPrescriptionPrice = 0;
foreach ($prescriptionData['medicines'] as $med) {
    $totalPrescriptionPrice += ($med['price'] * $med['qty']);
}
?>

<div class="bg-slate-50 min-h-screen py-8" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <nav class="flex text-sm font-medium text-slate-500 mb-8 gap-2 items-center">
            <a href="<?= BASE_URL ?>/pages/dashboard.php" class="hover:text-primary smooth-transition no-underline text-slate-500">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <a href="<?= BASE_URL ?>/pages/history.php" class="hover:text-primary smooth-transition no-underline text-slate-500">Riwayat Medis</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-slate-800 font-bold truncate">Detail Resep</span>
        </nav>

        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 m-0">E-Resep Dokter</h1>
                <p class="text-slate-500 font-medium mt-2 text-sm">Resep digital ini diterbitkan secara resmi dari sesi konsultasi Anda.</p>
            </div>
            <div class="flex gap-3">
                <button class="bg-white px-5 py-2.5 rounded-xl border border-slate-200 shadow-sm text-sm font-bold text-slate-700 hover:text-primary hover:border-primary smooth-transition flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-download"></i> Unduh PDF
                </button>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8 items-start">
            
            <div class="w-full lg:w-2/3">
                <div class="prescription-paper rounded-[2rem] shadow-floating border border-slate-200 overflow-hidden relative">
                    
                    <div class="bg-white/90 backdrop-blur-sm border-b-2 border-slate-200 p-8">
                        <div class="flex justify-between items-start mb-6">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center text-white text-2xl shadow-md">
                                    <i class="fa-solid fa-notes-medical"></i>
                                </div>
                                <div>
                                    <h2 class="font-extrabold text-xl text-dark tracking-tight m-0">CareSync</h2>
                                    <p class="text-xs font-bold text-primary tracking-widest uppercase m-0 mt-0.5">Telemedicine</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs font-bold text-slate-400 mb-1">No. Resep</div>
                                <div class="font-extrabold text-slate-800 text-sm"><?= $prescriptionData['id'] ?></div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row justify-between gap-6 bg-slate-50 rounded-2xl p-5 border border-slate-100">
                            <div>
                                <h3 class="font-extrabold text-slate-900 text-sm m-0 mb-1"><?= $prescriptionData['doctor']['name'] ?></h3>
                                <p class="text-xs font-medium text-slate-500 m-0 mb-1"><?= $prescriptionData['doctor']['specialty'] ?></p>
                                <p class="text-[10px] font-bold text-slate-400 m-0"><?= $prescriptionData['doctor']['sip'] ?></p>
                            </div>
                            <div class="sm:text-right">
                                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tanggal Diterbitkan</div>
                                <p class="text-xs font-extrabold text-slate-800 m-0"><i class="fa-regular fa-calendar text-primary mr-1"></i> <?= $prescriptionData['date'] ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/80 backdrop-blur-sm p-8 min-h-[400px] relative">
                        
                        <i class="fa-solid fa-staff-snake text-[250px] text-slate-100 absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 opacity-30 pointer-events-none"></i>

                        <div class="mb-8 border-b border-slate-200 pb-4 relative z-10">
                            <div class="flex items-center gap-6">
                                <div class="text-sm">
                                    <span class="text-slate-400 font-medium mr-2">Pro:</span> 
                                    <span class="font-extrabold text-slate-900 text-base"><?= $prescriptionData['patient']['name'] ?></span>
                                </div>
                                <div class="w-1 h-1 bg-slate-300 rounded-full"></div>
                                <div class="text-sm font-bold text-slate-600"><?= $prescriptionData['patient']['age'] ?></div>
                                <div class="w-1 h-1 bg-slate-300 rounded-full"></div>
                                <div class="text-sm font-bold text-slate-600"><?= $prescriptionData['patient']['weight'] ?></div>
                            </div>
                        </div>

                        <div class="font-serif italic font-bold text-4xl text-slate-800 mb-6 relative z-10">R/</div>

                        <div class="flex flex-col gap-6 relative z-10 pl-4 md:pl-8">
                            <?php foreach ($prescriptionData['medicines'] as $med): ?>
                            <div class="flex items-start justify-between group">
                                <div>
                                    <h4 class="font-extrabold text-slate-900 text-lg m-0 mb-1 flex items-center gap-2">
                                        <?= $med['name'] ?> 
                                    </h4>
                                    <div class="text-sm font-semibold text-slate-700 italic m-0 mb-2">S. <?= $med['dosis'] ?></div>
                                </div>
                                <div class="font-extrabold text-slate-800 text-base bg-white border border-slate-200 px-3 py-1 rounded-lg shadow-sm">
                                    No. <?= $med['qty'] ?>
                                </div>
                            </div>
                            <hr class="border-slate-200 border-dashed">
                            <?php endforeach; ?>
                        </div>

                    </div>
                    
                    <div class="bg-white/90 backdrop-blur-sm border-t border-slate-200 p-6 text-center relative z-10">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest m-0">Tanda Tangan Elektronik Sah</p>
                        <p class="font-serif italic text-primary font-bold text-2xl m-0 mt-2"><?= $prescriptionData['doctor']['name'] ?></p>
                    </div>

                </div>
            </div>

            <div class="w-full lg:w-1/3">
                <div class="bg-white rounded-[2rem] p-6 lg:p-8 border border-slate-100 shadow-sm sticky top-28">
                    
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-extrabold text-lg text-slate-900 m-0">Tebus Resep</h3>
                        <span class="text-xs font-extrabold text-amber-600 bg-amber-50 border border-amber-200 px-3 py-1.5 rounded-lg">
                            <i class="fa-solid fa-clock mr-1"></i> <?= $prescriptionData['status'] ?>
                        </span>
                    </div>
                    
                    <div class="flex flex-col gap-4 mb-6 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                        <?php foreach ($prescriptionData['medicines'] as $med): ?>
                        <div class="flex justify-between items-start bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <div>
                                <div class="font-bold text-slate-800 text-sm line-clamp-1" title="<?= $med['name'] ?>"><?= $med['name'] ?></div>
                                <div class="text-[10px] text-slate-500 font-semibold mt-1"><?= $med['qty'] ?> x Rp <?= number_format($med['price'], 0, ',', '.') ?></div>
                            </div>
                            <div class="font-extrabold text-slate-900 text-sm">
                                Rp <?= number_format($med['price'] * $med['qty'], 0, ',', '.') ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <hr class="border-slate-100 border-dashed mb-6">
                    
                    <div class="flex justify-between items-center mb-8">
                        <div>
                            <span class="font-extrabold text-slate-900 block mb-1">Estimasi Total</span>
                            <span class="text-[10px] text-slate-400 font-semibold">*Belum termasuk ongkir</span>
                        </div>
                        <span class="font-extrabold text-primary text-2xl tracking-tight">Rp <?= number_format($totalPrescriptionPrice, 0, ',', '.') ?></span>
                    </div>

                    <div class="flex flex-col gap-3">
                        <button onclick="tebusResep()" class="w-full bg-primary text-white border-none hover:bg-blue-800 py-4 rounded-2xl font-extrabold shadow-floating active:scale-95 smooth-transition flex items-center justify-center gap-2 cursor-pointer text-base">
                            <i class="fa-solid fa-pills"></i> Tebus Semua Obat
                        </button>
                        
                        <a href="<?= BASE_URL ?>/pages/pharmacist_consultation.php" class="w-full bg-white text-primary border border-blue-200 hover:bg-blue-50 py-3.5 rounded-2xl font-bold active:scale-95 smooth-transition flex items-center justify-center text-sm no-underline cursor-pointer box-border">
                            Konsultasi Apoteker
                        </a>
                    </div>

                    <div class="mt-6 bg-blue-50 rounded-xl p-4 flex items-start gap-3 border border-blue-100">
                        <i class="fa-solid fa-circle-info text-primary mt-0.5"></i>
                        <p class="text-[11px] text-slate-600 font-medium leading-relaxed m-0 text-justify">
                            Obat dengan tanda resep dokter hanya bisa dibeli melalui platform apotek terintegrasi kami dan diawasi oleh Apoteker bersertifikat.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    // Fungsi Aksi Tebus Resep
    function tebusResep() {
        alert("Semua obat dalam e-Resep berhasil ditambahkan ke keranjang Anda!");
        
        // Simulasi penambahan badge keranjang
        const cartBadge = document.getElementById('cart-count');
        if(cartBadge) {
            cartBadge.classList.remove('hidden');
            cartBadge.textContent = parseInt(cartBadge.textContent || 0) + <?= count($prescriptionData['medicines']) ?>;
            cartBadge.classList.add('scale-150');
            setTimeout(() => cartBadge.classList.remove('scale-150'), 200);
        }

        // Redirect ke keranjang atau langsung ke pengiriman
        if(confirm("Lanjut ke halaman Keranjang Belanja?")) {
            window.location.href = '<?= BASE_URL ?>/pages/cart.php';
        }
    }
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>