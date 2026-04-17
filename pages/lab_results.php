<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle   = 'Detail Hasil Lab — CareSync';
$currentPage = 'profile'; // Arahkan active state ke menu profil/dashboard

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
                boxShadow: { \'sm\': \'0 2px 8px rgba(0,0,0,0.04)\', \'floating\': \'0 20px 40px -15px rgba(29, 78, 216, 0.15)\' }
            }
        }
    }
</script>
<style>
    body { background-color: #F8FAFC; margin: 0; }
    .smooth-transition { transition: all 0.3s ease-in-out; }
    
    /* Dekorasi Background Laporan Medis */
    .medical-pattern {
        background-color: #1D4ED8;
        background-image: url("data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'#ffffff\' fill-opacity=\'0.05\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
</style>
';

include __DIR__ . '/../includes/header.php';

/* * MOCK DATA HASIL LAB PASIEN */
$labDocument = [
    'doc_no' => 'LAB-20260415-0921',
    'date' => '15 April 2026, 08:30 WIB',
    'patient_name' => 'Jovita Edgina',
    'patient_dob' => '12 Mei 2005 (20 Tahun)',
    'patient_gender' => 'Perempuan',
    'doctor_ref' => 'dr. Ika Syafitri, Sp.PD',
    'lab_facility' => 'Klinik Utama CareSync Lab, Tasikmalaya',
    
    // Hasil: flag 0 = Normal, 1 = Tinggi (High), -1 = Rendah (Low)
    'test_groups' => [
        [
            'group_name' => 'Hematologi Rutin',
            'tests' => [
                ['name' => 'Hemoglobin', 'result' => '13.5', 'unit' => 'g/dL', 'ref' => '12.0 - 15.0', 'flag' => 0],
                ['name' => 'Leukosit (WBC)', 'result' => '7.200', 'unit' => '/uL', 'ref' => '4.500 - 10.000', 'flag' => 0],
                ['name' => 'Trombosit (PLT)', 'result' => '145.000', 'unit' => '/uL', 'ref' => '150.000 - 450.000', 'flag' => -1],
                ['name' => 'Hematokrit', 'result' => '40', 'unit' => '%', 'ref' => '36 - 46', 'flag' => 0],
            ]
        ],
        [
            'group_name' => 'Kimia Klinik (Fungsi Lemak & Gula)',
            'tests' => [
                ['name' => 'Kolesterol Total', 'result' => '245', 'unit' => 'mg/dL', 'ref' => '< 200', 'flag' => 1],
                ['name' => 'Trigliserida', 'result' => '160', 'unit' => 'mg/dL', 'ref' => '< 150', 'flag' => 1],
                ['name' => 'Gula Darah Puasa', 'result' => '95', 'unit' => 'mg/dL', 'ref' => '70 - 100', 'flag' => 0],
                ['name' => 'Asam Urat', 'result' => '5.2', 'unit' => 'mg/dL', 'ref' => '2.4 - 5.7', 'flag' => 0],
            ]
        ]
    ],
    'conclusion' => 'Secara umum hasil hematologi dalam batas normal, namun terdapat sedikit penurunan pada Trombosit. Pada panel Kimia Klinik, ditemukan peningkatan kadar Kolesterol Total dan Trigliserida. Disarankan untuk mengurangi konsumsi makanan berlemak tinggi, berolahraga teratur, dan berkonsultasi kembali dengan dokter spesialis penyakit dalam.'
];
?>

<div class="bg-slate-50 min-h-screen py-8" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <nav class="flex text-sm font-medium text-slate-500 mb-8 gap-2 items-center">
            <a href="<?= BASE_URL ?>/pages/profile.php" class="hover:text-primary smooth-transition no-underline text-slate-500 flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Profil
            </a>
            <div class="w-px h-4 bg-slate-300 mx-2"></div>
            <span class="text-slate-800 font-bold truncate">Detail Hasil Laboratorium</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 m-0">Laporan Hasil Lab</h1>
                <p class="text-slate-500 font-medium mt-2 text-sm">Dokumen resmi pemeriksaan medis elektronik Anda.</p>
            </div>
            <button class="bg-white border border-slate-200 text-slate-700 px-5 py-2.5 rounded-xl font-bold shadow-sm hover:text-primary hover:border-primary active:scale-95 smooth-transition flex items-center gap-2 cursor-pointer w-fit">
                <i class="fa-solid fa-file-pdf text-red-500"></i> Unduh PDF
            </button>
        </div>

        <div class="bg-white rounded-[2rem] shadow-floating border border-slate-100 overflow-hidden mb-8 relative">
            
            <div class="medical-pattern p-8 sm:p-10 text-white relative">
                <i class="fa-solid fa-microscope text-[150px] absolute -right-10 -top-10 opacity-10 transform -rotate-12 pointer-events-none"></i>
                
                <div class="flex flex-col md:flex-row justify-between items-start gap-8 relative z-10">
                    <div class="flex-1">
                        <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm border border-white/30 text-white text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-widest mb-4">
                            Dokumen Rahasia
                        </div>
                        <h2 class="font-extrabold text-3xl m-0 mb-1"><?= $labDocument['patient_name'] ?></h2>
                        <p class="text-blue-100 font-medium m-0"><?= $labDocument['patient_gender'] ?> • <?= $labDocument['patient_dob'] ?></p>
                    </div>
                    
                    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 md:text-right w-full md:w-auto">
                        <div class="text-[10px] text-blue-200 uppercase tracking-widest font-bold mb-1">No. Registrasi Lab</div>
                        <div class="font-extrabold text-white text-base mb-3"><?= $labDocument['doc_no'] ?></div>
                        
                        <div class="text-[10px] text-blue-200 uppercase tracking-widest font-bold mb-1">Tanggal Pemeriksaan</div>
                        <div class="font-bold text-white text-sm"><i class="fa-regular fa-calendar-check mr-1"></i> <?= $labDocument['date'] ?></div>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50/50 border-b border-slate-100 p-6 sm:px-10 flex flex-col sm:flex-row justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white text-primary flex items-center justify-center shadow-sm"><i class="fa-regular fa-hospital text-lg"></i></div>
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Fasilitas Pemeriksa</div>
                        <div class="font-extrabold text-slate-800 text-sm"><?= $labDocument['lab_facility'] ?></div>
                    </div>
                </div>
                <div class="flex items-center gap-3 sm:text-right flex-row-reverse sm:flex-row">
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Dokter Perujuk</div>
                        <div class="font-extrabold text-slate-800 text-sm"><?= $labDocument['doctor_ref'] ?></div>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-white text-primary flex items-center justify-center shadow-sm"><i class="fa-solid fa-user-doctor text-lg"></i></div>
                </div>
            </div>

            <div class="p-6 sm:p-10">
                <div class="overflow-x-auto no-scrollbar">
                    <table class="w-full text-left border-collapse min-w-[600px]">
                        <thead>
                            <tr class="border-b-2 border-slate-200">
                                <th class="pb-3 text-xs font-extrabold text-slate-400 uppercase tracking-wider w-1/3">Parameter Uji</th>
                                <th class="pb-3 text-xs font-extrabold text-slate-400 uppercase tracking-wider w-1/4">Hasil</th>
                                <th class="pb-3 text-xs font-extrabold text-slate-400 uppercase tracking-wider w-1/4">Nilai Rujukan</th>
                                <th class="pb-3 text-xs font-extrabold text-slate-400 uppercase tracking-wider text-right w-1/6">Status</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            <?php foreach ($labDocument['test_groups'] as $group): ?>
                            
                            <tr>
                                <td colspan="4" class="pt-8 pb-3 border-b border-slate-100">
                                    <h3 class="font-extrabold text-slate-800 text-base m-0 flex items-center gap-2">
                                        <i class="fa-solid fa-vial-circle-check text-primary"></i> <?= $group['group_name'] ?>
                                    </h3>
                                </td>
                            </tr>

                            <?php foreach ($group['tests'] as $test): ?>
                            <tr class="border-b border-slate-50 hover:bg-slate-50/80 smooth-transition group">
                                <td class="py-4 font-bold text-slate-700 text-sm pl-2">
                                    <?= $test['name'] ?>
                                </td>
                                
                                <td class="py-4">
                                    <?php 
                                        $resultColor = 'text-slate-800'; // Normal
                                        if ($test['flag'] === 1) $resultColor = 'text-red-600'; // Tinggi
                                        if ($test['flag'] === -1) $resultColor = 'text-orange-500'; // Rendah
                                    ?>
                                    <div class="font-extrabold text-lg <?= $resultColor ?> m-0 flex items-baseline gap-1">
                                        <?= $test['result'] ?> 
                                        <span class="text-xs font-semibold text-slate-400"><?= $test['unit'] ?></span>
                                    </div>
                                </td>
                                
                                <td class="py-4 text-sm font-medium text-slate-500">
                                    <?= $test['ref'] ?> <span class="text-[10px]"><?= $test['unit'] ?></span>
                                </td>
                                
                                <td class="py-4 text-right pr-2">
                                    <?php if ($test['flag'] === 0): ?>
                                        <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-600 border border-emerald-200 px-2.5 py-1 rounded-lg text-[10px] font-extrabold tracking-wider uppercase shadow-sm">
                                            <i class="fa-solid fa-check"></i> Normal
                                        </span>
                                    <?php elseif ($test['flag'] === 1): ?>
                                        <span class="inline-flex items-center gap-1.5 bg-red-50 text-red-600 border border-red-200 px-2.5 py-1 rounded-lg text-[10px] font-extrabold tracking-wider uppercase shadow-sm animate-pulse">
                                            <i class="fa-solid fa-arrow-up"></i> Tinggi
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 bg-orange-50 text-orange-600 border border-orange-200 px-2.5 py-1 rounded-lg text-[10px] font-extrabold tracking-wider uppercase shadow-sm">
                                            <i class="fa-solid fa-arrow-down"></i> Rendah
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="p-6 sm:p-10 pt-0">
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1.5 h-full bg-primary"></div>
                    <h4 class="font-extrabold text-slate-800 text-sm uppercase tracking-wider mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-user-doctor text-primary"></i> Catatan & Kesimpulan Medis
                    </h4>
                    <p class="text-slate-600 text-sm leading-relaxed font-medium m-0 text-justify">
                        <?= $labDocument['conclusion'] ?>
                    </p>
                </div>
            </div>

            <div class="bg-white border-t border-slate-100 p-8 flex justify-end">
                <div class="text-center w-48">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest m-0 mb-2">Penanggung Jawab Lab</p>
                    <div class="font-serif italic text-primary font-bold text-2xl my-3">dr. Ika Syafitri</div>
                    <div class="w-full h-px bg-slate-200 mb-2"></div>
                    <p class="text-xs font-bold text-slate-800 m-0">dr. Ika Syafitri, Sp.PD</p>
                    <p class="text-[10px] font-medium text-slate-500 m-0">SIP. 445/123/SIP/2022</p>
                </div>
            </div>

        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 flex flex-col sm:flex-row items-center justify-between gap-6 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-blue-50 text-primary rounded-full flex items-center justify-center text-2xl flex-shrink-0">
                    <i class="fa-solid fa-comment-medical"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-900 text-base m-0 mb-1">Punya Pertanyaan Terkait Hasil Lab Ini?</h3>
                    <p class="text-xs font-medium text-slate-500 m-0">Diskusikan langsung dengan dokter agar mendapatkan penanganan yang akurat.</p>
                </div>
            </div>
            <a href="<?= BASE_URL ?>/pages/booking.php" class="w-full sm:w-auto bg-primary text-white px-6 py-3 rounded-xl font-bold shadow-md hover:bg-blue-800 active:scale-95 smooth-transition text-sm text-center no-underline whitespace-nowrap">
                Konsultasi Sekarang
            </a>
        </div>

    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>