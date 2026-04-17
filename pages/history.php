<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle   = 'Riwayat Konsultasi — CareSync';
$currentPage = 'history';

// Suntikkan Tailwind CSS dan Konfigurasi Tema CareSync
$extraHead = '
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: { sans: [\'"Plus Jakarta Sans"\', \'sans-serif\'] },
                colors: {
                    primary: \'#1D4ED8\', primaryLight: \'#EFF6FF\',
                    accent: \'#10B981\', dark: \'#0F172A\', textSoft: \'#64748B\',
                    warning: \'#F59E0B\', warningLight: \'#FEF3C7\',
                    danger: \'#EF4444\', dangerLight: \'#FEE2E2\'
                },
                boxShadow: { \'floating\': \'0 20px 40px -15px rgba(29, 78, 216, 0.15)\' }
            }
        }
    }
</script>
<style>
    body { background-color: #F8FAFC; margin: 0; }
    .smooth-transition { transition: all 0.3s ease-in-out; }
    /* Sembunyikan scrollbar untuk UI yang lebih bersih */
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
';

include __DIR__ . '/../includes/header.php';
?>

<div class="bg-slate-50 min-h-screen py-10" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 m-0">Rekam Medis</h1>
                <p class="text-slate-500 font-medium mt-2 m-0">Jovita Edgina · Pasien Umum · #MED-8819</p>
            </div>
            <button onclick="window.print()" class="bg-white border border-slate-200 text-slate-600 hover:text-primary hover:border-primary px-5 py-2.5 rounded-xl font-bold text-sm shadow-sm flex items-center gap-2 smooth-transition cursor-pointer active:scale-95">
                <i class="fa-solid fa-print"></i> Simpan PDF
            </button>
        </div>

        <div class="flex flex-col lg:flex-row gap-8 items-start">
            
            <div class="w-full lg:w-3/5 xl:w-2/3">
                
                <div class="flex overflow-x-auto no-scrollbar gap-2 mb-6 border-b border-slate-200 pb-2">
                    <button class="filter-btn active whitespace-nowrap px-4 py-2 rounded-full text-sm font-bold bg-dark text-white smooth-transition cursor-pointer border-none" onclick="filterHistory(this,'all')">Semua</button>
                    <button class="filter-btn whitespace-nowrap px-4 py-2 rounded-full text-sm font-bold bg-transparent text-slate-500 hover:text-dark smooth-transition cursor-pointer border-none" onclick="filterHistory(this,'selesai')">Selesai</button>
                    <button class="filter-btn whitespace-nowrap px-4 py-2 rounded-full text-sm font-bold bg-transparent text-slate-500 hover:text-dark smooth-transition cursor-pointer border-none" onclick="filterHistory(this,'aktif')">Aktif</button>
                    <button class="filter-btn whitespace-nowrap px-4 py-2 rounded-full text-sm font-bold bg-transparent text-slate-500 hover:text-dark smooth-transition cursor-pointer border-none" onclick="filterHistory(this,'resep')">Ada Resep</button>
                </div>

                <div id="history-list" class="flex flex-col gap-4">
                    <?php
                    $histories = [
                        [1, 'dr. Susanti Wulandari, Sp.KK', 'Spesialis Kulit & Kelamin', '24 Nov 2024', 'selesai', 'Jerawat meradang dan gatal di pipi kanan', 'Acne Vulgaris Grade II', true,  'SW', 'bg-blue-500'],
                        [2, 'dr. Budi Santoso, Sp.M',        'Spesialis Mata',            '15 Okt 2024', 'selesai', 'Mata merah dan gatal sejak 3 hari',      'Konjungtivitis Alergi',  false, 'BS', 'bg-teal-500'],
                        [3, 'dr. Fenny Nurmahdi',             'Dokter Umum',               '2 Sep 2024',  'selesai', 'Demam tinggi dan batuk kering',           'ISPA',                   true,  'FN', 'bg-purple-500'],
                        [4, 'dr. Ika Syafitri, Sp.PD',        'Penyakit Dalam',            '10 Agu 2024', 'selesai', 'Kontrol rutin tekanan darah',             'Hipertensi Stage 1',     true,  'IS', 'bg-amber-500'],
                    ];
                    foreach ($histories as $i => [$id,$name,$spec,$date,$status,$complaint,$diagnosis,$hasResep,$init,$colBg]):
                        $isActive = ($i === 0);
                    ?>
                    <div class="history-card bg-white p-5 rounded-2xl border <?= $isActive ? 'border-primary bg-primaryLight' : 'border-slate-200' ?> hover:border-primary cursor-pointer smooth-transition flex gap-4 items-start group" 
                         data-status="<?= $status ?>" data-resep="<?= $hasResep ? '1' : '0' ?>"
                         onclick="selectHistory(this, <?= $id ?>)">
                        
                        <div class="w-12 h-12 rounded-xl <?= $colBg ?> flex items-center justify-center text-sm font-extrabold text-white flex-shrink-0 shadow-sm group-hover:scale-105 smooth-transition">
                            <?= $init ?>
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start gap-2 mb-1">
                                <h4 class="font-extrabold text-slate-900 text-[15px] m-0 truncate"><?= $name ?></h4>
                                <span class="text-[11px] font-semibold text-slate-400 whitespace-nowrap"><?= $date ?></span>
                            </div>
                            <div class="text-xs font-semibold text-primary mb-3"><?= $spec ?></div>
                            <div class="text-sm text-slate-600 mb-3 truncate">Keluhan: <span class="font-medium"><?= $complaint ?></span></div>
                            
                            <div class="flex flex-wrap gap-2 items-center">
                                <span class="bg-slate-100 text-slate-600 text-[10px] font-bold px-2.5 py-1 rounded-md border border-slate-200">
                                    <i class="fa-solid fa-stethoscope mr-1"></i> <?= $diagnosis ?>
                                </span>
                                <?php if ($hasResep): ?>
                                <span class="bg-emerald-50 text-emerald-600 border border-emerald-200 text-[10px] font-bold px-2.5 py-1 rounded-md">
                                    <i class="fa-solid fa-pills mr-1"></i> Ada Resep
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="w-full lg:w-2/5 xl:w-1/3 lg:sticky lg:top-28">
                <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden">
                    
                    <div class="bg-gradient-to-br from-primary to-blue-400 p-6 flex gap-4 items-center relative overflow-hidden">
                        <i class="fa-solid fa-heart-pulse text-white/10 text-6xl absolute -right-2 -bottom-2"></i>
                        <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm border border-white/30 flex items-center justify-center text-xl font-extrabold text-white flex-shrink-0 shadow-inner z-10">
                            JE
                        </div>
                        <div class="relative z-10 text-white">
                            <h3 class="font-extrabold text-lg m-0 mb-0.5">Jovita Edgina</h3>
                            <p class="text-xs text-blue-100 m-0 mb-2 font-medium">Pasien Umum · #MED-8819</p>
                            <div class="flex gap-3 text-[11px] font-semibold text-white/80">
                                <span class="flex items-center gap-1"><i class="fa-solid fa-cake-candles"></i> 20 Thn</span>
                                <span class="flex items-center gap-1"><i class="fa-solid fa-venus"></i> Perempuan</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 border-b border-slate-100">
                        <div class="grid grid-cols-3 gap-3 mb-5">
                            <div class="bg-slate-50 rounded-xl p-3 text-center border border-slate-100">
                                <div class="text-lg mb-1">🫀</div>
                                <div class="text-sm font-extrabold text-slate-900">120/80</div>
                                <div class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mt-1">Tekanan Darah</div>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-3 text-center border border-slate-100">
                                <div class="text-lg mb-1">🩸</div>
                                <div class="text-sm font-extrabold text-slate-900">95 <span class="text-[10px]">mg/dL</span></div>
                                <div class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mt-1">Gula Darah</div>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-3 text-center border border-slate-100">
                                <div class="text-lg mb-1">⚡</div>
                                <div class="text-sm font-extrabold text-slate-900">180</div>
                                <div class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mt-1">Kolesterol</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 bg-warningLight text-warning px-3 py-2.5 rounded-xl text-xs font-bold mb-2">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <span>Alergi Aspirin</span>
                            <span class="ml-auto bg-warning/20 text-warning px-2 py-0.5 rounded text-[9px] uppercase tracking-wider">Tinggi</span>
                        </div>
                        <div class="flex items-center gap-2 bg-dangerLight text-danger px-3 py-2.5 rounded-xl text-xs font-bold">
                            <i class="fa-solid fa-notes-medical"></i>
                            <span>Riwayat Asma</span>
                            <span class="ml-auto bg-danger/20 text-danger px-2 py-0.5 rounded text-[9px] uppercase tracking-wider">Sedang</span>
                        </div>
                    </div>

                    <div class="flex border-b border-slate-100 bg-slate-50/50">
                        <button class="detail-tab flex-1 py-3.5 text-center text-xs font-extrabold cursor-pointer border-b-2 border-primary text-primary smooth-transition bg-transparent" onclick="switchTab(this,'tab-riwayat')">Riwayat</button>
                        <button class="detail-tab flex-1 py-3.5 text-center text-xs font-extrabold cursor-pointer border-b-2 border-transparent text-slate-400 hover:text-slate-700 smooth-transition bg-transparent" onclick="switchTab(this,'tab-resep')">Resep</button>
                        <button class="detail-tab flex-1 py-3.5 text-center text-xs font-extrabold cursor-pointer border-b-2 border-transparent text-slate-400 hover:text-slate-700 smooth-transition bg-transparent" onclick="switchTab(this,'tab-lab')">Hasil Lab</button>
                    </div>

                    <div class="p-6 h-[400px] overflow-y-auto no-scrollbar">

                        <div class="detail-section block" id="tab-riwayat">
                            <?php
                            $visits = [
                                ['Konsultasi Kulit Wajah', '24 Nov 2024', 'dr. Susanti Wulandari', 'Jerawat meradang dan gatal di pipi kanan', 'Acne Vulgaris Grade II'],
                                ['Pemeriksaan Umum (Flu)', '15 Okt 2024', 'dr. Budi Santoso',      'Demam dan batuk 3 hari',                   'ISPA Ringan'],
                            ];
                            foreach ($visits as $idx => [$title,$date,$doc,$keluhan,$diag]):
                            ?>
                            <div class="relative pl-6 pb-6 <?= $idx === count($visits)-1 ? '' : 'border-l-2 border-slate-100' ?> ml-2">
                                <div class="absolute w-3 h-3 bg-primary rounded-full -left-[7px] top-1 ring-4 ring-white"></div>
                                <div class="flex justify-between items-start mb-1">
                                    <h5 class="font-extrabold text-sm text-slate-900 m-0"><?= $title ?></h5>
                                </div>
                                <div class="text-[11px] font-semibold text-slate-400 mb-3"><?= $date ?> · <?= $doc ?></div>
                                <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                                    <div class="text-xs text-slate-600 mb-1"><span class="font-bold text-slate-700">Keluhan:</span> <?= $keluhan ?></div>
                                    <div class="text-xs text-primary"><span class="font-bold">Diagnosis:</span> <?= $diag ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="detail-section hidden" id="tab-resep">
                            <div class="border-2 border-dashed border-slate-200 rounded-2xl p-5 relative bg-white">
                                <i class="fa-solid fa-prescription absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-[80px] text-slate-50 opacity-50 pointer-events-none"></i>
                                
                                <div class="flex justify-between items-start mb-4 pb-4 border-b border-slate-100 relative z-10">
                                    <div>
                                        <div class="font-extrabold text-primary text-sm tracking-tight">CareSync E-Rx</div>
                                        <div class="text-[9px] font-bold text-slate-400 mt-0.5">RP/CSYNC/012345</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-xs text-slate-800">dr. Susanti Wulandari</div>
                                        <div class="text-[9px] font-medium text-slate-400 mt-0.5">Jakarta, 24 Nov 2024</div>
                                    </div>
                                </div>
                                
                                <div class="text-[10px] font-bold text-slate-500 mb-4 relative z-10 uppercase tracking-wider">
                                    Untuk: Jovita Edgina (20 Thn)
                                </div>
                                
                                <div class="relative z-10 space-y-3">
                                    <div class="pb-3 border-b border-slate-50">
                                        <div class="font-extrabold text-sm text-slate-800"><span class="text-primary italic mr-1">R/</span> Clindamycin 300mg Caps</div>
                                        <div class="text-[11px] font-medium text-slate-500 mt-1"><i class="fa-solid fa-arrow-turn-up fa-rotate-90 text-slate-300 mr-1"></i> No. XV · 1 dd 1 caps (malam hari)</div>
                                    </div>
                                    <div class="pb-3 border-b border-slate-50">
                                        <div class="font-extrabold text-sm text-slate-800"><span class="text-primary italic mr-1">R/</span> Benzoilac 5% Gel Tube No. 1</div>
                                        <div class="text-[11px] font-medium text-slate-500 mt-1"><i class="fa-solid fa-arrow-turn-up fa-rotate-90 text-slate-300 mr-1"></i> S u.e (Oles tipis malam hari)</div>
                                    </div>
                                </div>
                                
                                <div class="mt-6 pt-4 border-t border-slate-100 text-[10px] font-bold text-slate-400 relative z-10 text-center italic">
                                    "Dokumen ini valid dan diterbitkan secara digital"
                                </div>
                            </div>

                            <a href="<?= BASE_URL ?>/pages/marketplace.php" class="mt-6 w-full bg-primary text-white border-none hover:bg-blue-800 py-3.5 rounded-xl font-bold shadow-md shadow-blue-200 active:scale-95 smooth-transition flex items-center justify-center gap-2 cursor-pointer text-sm no-underline">
                                <i class="fa-solid fa-cart-shopping"></i> Tebus Resep Ini
                            </a>
                        </div>

                        <div class="detail-section hidden" id="tab-lab">
                            <div class="flex flex-col items-center justify-center text-center py-10">
                                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-4xl mb-4 border border-slate-100 shadow-inner text-slate-300">
                                    <i class="fa-solid fa-microscope"></i>
                                </div>
                                <h5 class="font-extrabold text-slate-800 text-sm mb-2 m-0">Belum Ada Data Lab</h5>
                                <p class="text-xs text-slate-500 max-w-[200px] leading-relaxed m-0">Hasil pemeriksaan laboratorium atau rontgen akan muncul di sini secara otomatis jika tersedia.</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
// Filter Kategori Riwayat
function filterHistory(btn, filter) {
    // Styling Button Tab Kiri
    document.querySelectorAll('.filter-btn').forEach(b => {
        b.className = "filter-btn whitespace-nowrap px-4 py-2 rounded-full text-sm font-bold bg-transparent text-slate-500 hover:text-dark smooth-transition cursor-pointer border-none";
    });
    btn.className = "filter-btn active whitespace-nowrap px-4 py-2 rounded-full text-sm font-bold bg-dark text-white smooth-transition cursor-pointer border-none";
    
    // Logika Show/Hide Card
    document.querySelectorAll('.history-card').forEach(c => {
        const show = filter === 'all' 
            || (filter === 'resep' && c.getAttribute('data-resep') === '1') 
            || c.getAttribute('data-status') === filter;
        
        c.style.display = show ? 'flex' : 'none';
    });
}

// Select Card History
function selectHistory(el, id) {
    document.querySelectorAll('.history-card').forEach(c => {
        c.classList.remove('border-primary', 'bg-primaryLight');
        c.classList.add('border-slate-200');
    });
    el.classList.remove('border-slate-200');
    el.classList.add('border-primary', 'bg-primaryLight');
    
    // (Opsional) Di tahap selanjutnya, fungsi ini bisa fetch data API backend berdasarkan ID
}

// Switch Tabs Kanan (Detail, Resep, Lab)
function switchTab(el, tabId) {
    // Styling Nav Tabs Kanan
    document.querySelectorAll('.detail-tab').forEach(t => {
        t.className = "detail-tab flex-1 py-3.5 text-center text-xs font-extrabold cursor-pointer border-b-2 border-transparent text-slate-400 hover:text-slate-700 smooth-transition bg-transparent";
    });
    el.className = "detail-tab active flex-1 py-3.5 text-center text-xs font-extrabold cursor-pointer border-b-2 border-primary text-primary smooth-transition bg-transparent";
    
    // Hide all sections, show active
    document.querySelectorAll('.detail-section').forEach(s => {
        s.classList.remove('block');
        s.classList.add('hidden');
    });
    document.getElementById(tabId).classList.remove('hidden');
    document.getElementById(tabId).classList.add('block');
}
</script>