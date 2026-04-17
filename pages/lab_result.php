<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle   = 'Hasil Laboratorium — CareSync';
$currentPage = 'history'; 

// Suntikkan Tailwind CSS
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
</style>
';

include __DIR__ . '/../includes/header.php';

/* * MOCK DATA HASIL LAB */
$labData = [
    'patientName' => 'Jovita Edgina',
    'patientId' => 'RM-0912-334',
    'date' => '15 April 2026',
    'doctor' => 'dr. Ika Syafitri, Sp.PD',
    'laboratory' => 'Klinik Utama CareSync Lab',
    
    // Status flag: 0 = Normal, 1 = Tinggi (High), -1 = Rendah (Low)
    'results' => [
        'Hematologi Rutin' => [
            ['param' => 'Hemoglobin', 'value' => '13.5', 'unit' => 'g/dL', 'ref' => '12.0 - 15.0', 'flag' => 0],
            ['param' => 'Leukosit (WBC)', 'value' => '7.200', 'unit' => '/uL', 'ref' => '4.500 - 10.000', 'flag' => 0],
            ['param' => 'Trombosit (PLT)', 'value' => '145.000', 'unit' => '/uL', 'ref' => '150.000 - 450.000', 'flag' => -1],
            ['param' => 'Hematokrit', 'value' => '40', 'unit' => '%', 'ref' => '36 - 46', 'flag' => 0],
        ],
        'Kimia Klinik (Fungsi Hati & Lemak)' => [
            ['param' => 'Kolesterol Total', 'value' => '245', 'unit' => 'mg/dL', 'ref' => '< 200', 'flag' => 1],
            ['param' => 'Gula Darah Puasa', 'value' => '95', 'unit' => 'mg/dL', 'ref' => '< 100', 'flag' => 0],
            ['param' => 'Asam Urat', 'value' => '5.2', 'unit' => 'mg/dL', 'ref' => '2.4 - 5.7', 'flag' => 0],
        ]
    ]
];
?>

<div class="bg-slate-50 min-h-screen py-8" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <nav class="flex text-sm font-medium text-slate-500 mb-8 gap-2 items-center">
            <a href="<?= BASE_URL ?>/pages/dashboard.php" class="hover:text-primary smooth-transition no-underline text-slate-500">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <a href="<?= BASE_URL ?>/pages/history.php" class="hover:text-primary smooth-transition no-underline text-slate-500">Riwayat Medis</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-slate-800 font-bold truncate">Hasil Laboratorium</span>
        </nav>

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 m-0 flex items-center gap-3">
                    <i class="fa-solid fa-microscope text-primary"></i> Hasil Laboratorium
                </h1>
                <p class="text-slate-500 font-medium mt-2 text-sm">Dokumen medis resmi hasil pemeriksaan lab Anda.</p>
            </div>
            <button class="bg-white px-5 py-2.5 rounded-xl border border-slate-200 shadow-sm text-sm font-bold text-slate-700 hover:text-primary hover:border-primary smooth-transition flex items-center gap-2 cursor-pointer w-fit">
                <i class="fa-solid fa-file-pdf text-red-500"></i> Unduh PDF
            </button>
        </div>

        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Nama Pasien</span>
                    <span class="font-extrabold text-slate-900 text-base"><?= $labData['patientName'] ?></span>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">No. Rekam Medis</span>
                    <span class="font-bold text-slate-800 text-sm"><?= $labData['patientId'] ?></span>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Tanggal Pemeriksaan</span>
                    <span class="font-bold text-slate-800 text-sm"><?= $labData['date'] ?></span>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Dokter Perujuk</span>
                    <span class="font-bold text-primary text-sm"><?= $labData['doctor'] ?></span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden mb-8">
            
            <div class="bg-slate-800 text-white p-5 border-b border-slate-700 flex items-center gap-3">
                <i class="fa-solid fa-flask-vial"></i>
                <h3 class="font-extrabold text-base m-0">Rincian Pemeriksaan</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Parameter</th>
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Hasil</th>
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Nilai Rujukan</th>
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Satuan</th>
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm font-medium">
                        
                        <?php foreach ($labData['results'] as $category => $tests): ?>
                        
                        <tr class="bg-blue-50/50">
                            <td colspan="5" class="py-3 px-6 font-extrabold text-primary text-sm border-b border-slate-100">
                                <?= $category ?>
                            </td>
                        </tr>

                        <?php foreach ($tests as $test): ?>
                        <tr class="border-b border-slate-100 hover:bg-slate-50 smooth-transition">
                            <td class="py-4 px-6 text-slate-800 font-semibold"><?= $test['param'] ?></td>
                            
                            <td class="py-4 px-6 font-extrabold text-base <?= $test['flag'] === 0 ? 'text-slate-900' : ($test['flag'] === 1 ? 'text-red-500' : 'text-orange-500') ?>">
                                <?= $test['value'] ?>
                            </td>
                            
                            <td class="py-4 px-6 text-slate-500"><?= $test['ref'] ?></td>
                            <td class="py-4 px-6 text-slate-400"><?= $test['unit'] ?></td>
                            <td class="py-4 px-6 text-center">
                                <?php if ($test['flag'] === 0): ?>
                                    <span class="bg-emerald-50 text-emerald-600 border border-emerald-200 px-3 py-1 rounded-lg text-xs font-bold tracking-wider uppercase">Normal</span>
                                <?php elseif ($test['flag'] === 1): ?>
                                    <span class="bg-red-50 text-red-600 border border-red-200 px-3 py-1 rounded-lg text-xs font-extrabold tracking-wider uppercase"><i class="fa-solid fa-arrow-up mr-1"></i> Tinggi</span>
                                <?php else: ?>
                                    <span class="bg-orange-50 text-orange-600 border border-orange-200 px-3 py-1 rounded-lg text-xs font-extrabold tracking-wider uppercase"><i class="fa-solid fa-arrow-down mr-1"></i> Rendah</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php endforeach; ?>
                        
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-gradient-to-r from-primary to-blue-500 rounded-[2rem] p-8 md:p-10 shadow-floating flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden group">
            <i class="fa-solid fa-user-doctor text-white/10 text-9xl absolute -right-6 -bottom-10 group-hover:scale-110 group-hover:-rotate-12 smooth-transition duration-500"></i>
            <div class="relative z-10 text-white md:w-2/3">
                <h3 class="text-2xl font-extrabold mb-2 m-0">Butuh penjelasan medis?</h3>
                <p class="text-blue-100 text-sm leading-relaxed m-0">Bawa hasil ini dan konsultasikan langsung dengan dokter spesialis untuk mendapatkan diagnosa dan penanganan yang tepat.</p>
            </div>
            <a href="<?= BASE_URL ?>/pages/booking.php" class="relative z-10 bg-white text-primary px-8 py-3.5 rounded-full font-extrabold shadow-lg hover:bg-slate-50 hover:scale-105 active:scale-95 smooth-transition whitespace-nowrap no-underline cursor-pointer">
                Konsultasi Dokter
            </a>
        </div>

    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>