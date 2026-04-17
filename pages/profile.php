<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle   = 'Dashboard Akun — CareSync';
$currentPage = 'profile'; 

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
                boxShadow: { \'sm\': \'0 2px 8px rgba(0,0,0,0.04)\', \'floating\': \'0 20px 40px -15px rgba(29, 78, 216, 0.15)\' }
            }
        }
    }
</script>
<style>
    body { background-color: #F8FAFC; margin: 0; }
    .smooth-transition { transition: all 0.2s ease-in-out; }
    
    /* Tab System */
    .tab-content { display: none; animation: fadeIn 0.3s ease; }
    .tab-content.active { display: block; }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Pattern Background for Profile Header */
    .profile-header-pattern {
        background-color: #0F172A;
        background-image: url("data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'#ffffff\' fill-opacity=\'0.05\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    
    /* Custom Scrollbar for Midtrans Mockup */
    .midtrans-scroll::-webkit-scrollbar { width: 4px; }
    .midtrans-scroll::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
    .midtrans-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
</style>
';

include __DIR__ . '/../includes/header.php';

/* * MOCK DATA PENGGUNA */
$user = [
    'name' => 'Jovita Edgina',
    'email' => 'jovita.edgina@email.com',
    'gender' => 'Perempuan',
    'dob' => '12 Mei 2005',
    'phone' => '0812-3456-7890',
    'blood' => 'O+',
    'weight' => '52 kg',
    'height' => '160 cm',
    'address' => 'Jl. Siliwangi No. 123, Kahuripan, Tawang, Kota Tasikmalaya'
];
?>

<div class="bg-slate-50 min-h-screen py-8" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row gap-8 items-start">
            
            <div class="w-full md:w-[280px] lg:w-[320px] bg-white rounded-2xl shadow-sm border border-slate-100 flex-shrink-0 sticky top-28 overflow-hidden">
                
                <div class="profile-header-pattern h-28 w-full relative"></div>
                <div class="px-6 pb-4 text-center relative -mt-12 border-b border-slate-100">
                    <div class="w-24 h-24 bg-slate-200 border-4 border-white rounded-full mx-auto mb-3 flex items-center justify-center text-slate-400 text-3xl font-extrabold shadow-sm relative">
                        JE
                        <div class="absolute bottom-1 right-1 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full" title="Online"></div>
                    </div>
                    <h3 class="font-extrabold text-slate-900 text-base m-0 leading-tight"><?= $user['name'] ?></h3>
                    <p class="text-xs text-slate-500 mb-2 truncate"><?= $user['email'] ?></p>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider"><?= $user['gender'] ?> • TL : <?= $user['dob'] ?></p>
                </div>

                <div class="p-4 flex flex-col gap-1 pb-6">
                    
                    <div class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mt-2 mb-1 px-4">Menu Utama</div>
                    <button onclick="switchTab('konsultasi', 'Konsultasi Medis', this)" class="menu-btn w-full text-left px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 flex items-center gap-3 smooth-transition border-none cursor-pointer bg-transparent">
                        <i class="fa-solid fa-stethoscope w-4 text-center"></i> Konsultasi
                    </button>
                    <button onclick="switchTab('resep', 'Tebus Resep', this)" class="menu-btn w-full text-left px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 flex items-center gap-3 smooth-transition border-none cursor-pointer bg-transparent">
                        <i class="fa-solid fa-file-prescription w-4 text-center"></i> Tebus Resep
                    </button>

                    <div class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mt-4 mb-1 px-4">Transaksi</div>
                    <button onclick="switchTab('pembayaran', 'Pembayaran', this)" class="menu-btn active w-full text-left px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-primary flex items-center gap-3 smooth-transition border-none cursor-pointer shadow-md shadow-blue-200">
                        <i class="fa-solid fa-wallet w-4 text-center"></i> Pembayaran
                    </button>
                    <button onclick="switchTab('pesanan', 'Riwayat Pemesanan', this)" class="menu-btn w-full text-left px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 flex items-center gap-3 smooth-transition border-none cursor-pointer bg-transparent">
                        <i class="fa-solid fa-box-open w-4 text-center"></i> Pemesanan
                    </button>

                    <div class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mt-4 mb-1 px-4">Profil</div>
                    <button onclick="switchTab('akun', 'Profil Saya', this)" class="menu-btn w-full text-left px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 flex items-center gap-3 smooth-transition border-none cursor-pointer bg-transparent">
                        <i class="fa-regular fa-user w-4 text-center"></i> Profil Saya
                    </button>
                    <button onclick="switchTab('medis', 'Rekam Medis', this)" class="menu-btn w-full text-left px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 flex items-center gap-3 smooth-transition border-none cursor-pointer bg-transparent">
                        <i class="fa-solid fa-notes-medical w-4 text-center"></i> Rekam Medis
                    </button>

                    <div class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mt-4 mb-1 px-4">Akses</div>
                    <button class="w-full text-left px-4 py-2.5 rounded-xl text-sm font-bold text-slate-500 hover:text-red-500 hover:bg-red-50 flex items-center gap-3 smooth-transition border-none cursor-pointer bg-transparent">
                        <i class="fa-solid fa-arrow-right-from-bracket w-4 text-center"></i> Keluar
                    </button>

                </div>
            </div>

            <div class="flex-1 min-w-0">
                <h2 id="page-title" class="text-2xl font-extrabold text-slate-900 mb-6 m-0 border-b border-slate-200 pb-4">Pembayaran</h2>
                
                <div id="tab-pembayaran" class="tab-content active">
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm">
                        <div class="flex justify-between items-center text-[11px] text-slate-500 font-bold mb-6">
                            <span>INV-202604185332385 &nbsp; <i class="fa-solid fa-clock text-slate-300 mx-1"></i> 18 April 2026 14:30</span>
                            <button class="border border-primary text-primary bg-white px-4 py-1.5 rounded-lg hover:bg-blue-50 cursor-pointer smooth-transition font-bold">Tutup</button>
                        </div>
                        <div class="mb-6 text-sm font-semibold text-slate-700">
                            <div class="mb-1">Order ID : <span class="font-bold text-slate-900">snap-939698D812BE</span></div>
                            <div class="mb-1">Merchant : -</div>
                            <div>Jumlah Tagihan: <span class="font-bold text-slate-900">Rp 207.000,00</span></div>
                        </div>

                        <div class="bg-[#EEF2FF] rounded-2xl p-4 sm:p-8 flex items-center justify-center relative overflow-hidden">
                            <div class="absolute -right-10 top-6 bg-yellow-400 text-yellow-900 font-extrabold text-[10px] uppercase tracking-widest py-1 w-40 text-center transform rotate-45 shadow-sm">TEST</div>
                            
                            <div class="bg-white w-full max-w-sm rounded-xl shadow-[0_10px_30px_-10px_rgba(0,0,0,0.1)] overflow-hidden flex flex-col h-[400px]">
                                <div class="bg-[#0F172A] p-5 text-white">
                                    <div class="text-[11px] font-bold uppercase tracking-wider mb-2 text-slate-300">CareSync Telemedicine</div>
                                    <div class="font-extrabold text-2xl flex items-center gap-2 mb-1">
                                        Rp207.000 <i class="fa-regular fa-copy text-sm text-slate-400 cursor-pointer hover:text-white"></i>
                                    </div>
                                    <div class="text-[10px] text-slate-400 flex justify-between items-center">
                                        <span>Order ID #snap-939698D8...</span>
                                        <span class="text-blue-300 font-bold cursor-pointer">Details ▼</span>
                                    </div>
                                </div>
                                
                                <div class="flex-1 overflow-y-auto p-4 midtrans-scroll bg-slate-50">
                                    <div class="text-center text-[10px] font-bold text-slate-400 mb-3">Choose within 23:59:09</div>
                                    
                                    <div class="text-xs font-bold text-slate-500 mb-2 mt-4">Recommended payment method</div>
                                    <div class="bg-white border border-slate-200 rounded-lg p-3 flex justify-between items-center cursor-pointer hover:border-blue-400 mb-4 shadow-sm">
                                        <div>
                                            <div class="font-bold text-sm text-slate-800 mb-1">GoPay/GoPay Later</div>
                                            <div class="flex gap-2"><div class="bg-blue-500 w-6 h-3 rounded-sm"></div><div class="bg-slate-300 w-6 h-3 rounded-sm"></div></div>
                                        </div>
                                        <i class="fa-solid fa-chevron-right text-slate-400 text-xs"></i>
                                    </div>

                                    <div class="text-xs font-bold text-slate-500 mb-2 mt-4">Virtual account</div>
                                    <div class="bg-white border border-slate-200 rounded-lg p-3 flex justify-between items-center cursor-pointer hover:border-blue-400 mb-4 shadow-sm">
                                        <div class="flex gap-2">
                                            <div class="bg-[#0066AE] w-8 h-4 rounded-sm text-white text-[8px] font-bold flex items-center justify-center">BCA</div>
                                            <div class="bg-[#00529C] w-8 h-4 rounded-sm text-white text-[8px] font-bold flex items-center justify-center">BRI</div>
                                            <div class="bg-[#005E6A] w-8 h-4 rounded-sm text-white text-[8px] font-bold flex items-center justify-center">BNI</div>
                                            <div class="text-xs font-bold text-slate-400 ml-1">+4</div>
                                        </div>
                                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i>
                                    </div>
                                    
                                    <div class="text-xs font-bold text-slate-500 mb-2 mt-4">Credit/debit card</div>
                                    <div class="bg-white border border-slate-200 rounded-lg p-3 flex justify-between items-center cursor-pointer hover:border-blue-400 shadow-sm">
                                        <div class="flex gap-2 text-slate-800 font-bold italic text-xs items-center">
                                            VISA <span class="w-2 h-2 rounded-full bg-red-500 mix-blend-multiply relative"><span class="absolute w-2 h-2 rounded-full bg-yellow-500 -left-1 opacity-80"></span></span>
                                        </div>
                                        <i class="fa-solid fa-chevron-right text-slate-400 text-xs"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 bg-slate-50 border border-slate-200 rounded-xl p-5">
                            <div class="flex justify-between items-start mb-4 border-b border-slate-200 pb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-primaryLight text-primary rounded-full flex items-center justify-center text-lg"><i class="fa-solid fa-pills"></i></div>
                                    <div>
                                        <h4 class="font-extrabold text-slate-800 text-sm m-0">Apotek CareSync Pusat</h4>
                                        <div class="text-[10px] text-slate-500">Tasikmalaya, Jawa Barat</div>
                                    </div>
                                </div>
                                <div class="font-extrabold text-sm text-slate-800">Rp 207.000</div>
                            </div>
                            <h5 class="text-xs font-bold text-slate-500 mb-3 m-0">Daftar Barang</h5>
                            <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-slate-100 shadow-sm mb-4">
                                <div class="flex gap-3 items-center">
                                    <div class="w-10 h-10 bg-yellow-50 rounded flex items-center justify-center text-yellow-500 text-lg"><i class="fa-solid fa-capsules"></i></div>
                                    <div>
                                        <div class="font-bold text-slate-800 text-sm">Blackmores Vitamin C 500mg</div>
                                        <div class="text-[10px] text-slate-500">Kapsul • 1 Qty</div>
                                    </div>
                                </div>
                                <div class="font-bold text-slate-800 text-sm">Rp 120.000</div>
                            </div>
                            <div class="text-xs font-bold text-slate-600 bg-white border border-slate-200 px-3 py-2 rounded-lg w-fit">
                                Kurir : GoSend - Instant
                            </div>
                        </div>
                    </div>
                </div>

                <div id="tab-akun" class="tab-content">
                    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-100 shadow-sm">
                        <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-100">
                            <h3 class="font-extrabold text-lg text-slate-900 m-0"><i class="fa-regular fa-id-badge text-primary mr-2"></i> Data Diri Pasien</h3>
                            <button class="text-sm font-bold text-primary hover:text-blue-800 bg-primaryLight px-4 py-2 rounded-lg border-none cursor-pointer smooth-transition">Edit Data</button>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div><label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Nama Lengkap</label>
                            <input type="text" value="<?= $user['name'] ?>" readonly class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 font-semibold text-slate-700 outline-none text-sm"></div>
                            
                            <div><label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Email</label>
                            <input type="text" value="<?= $user['email'] ?>" readonly class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 font-semibold text-slate-700 outline-none text-sm"></div>
                            
                            <div><label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Nomor Telepon</label>
                            <input type="text" value="<?= $user['phone'] ?>" readonly class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 font-semibold text-slate-700 outline-none text-sm"></div>
                            
                            <div><label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Tanggal Lahir</label>
                            <input type="text" value="<?= $user['dob'] ?>" readonly class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 font-semibold text-slate-700 outline-none text-sm"></div>
                        </div>
                        
                        <div><label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Alamat Pengiriman Utama</label>
                        <textarea readonly class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-semibold text-slate-700 outline-none resize-none h-20 text-sm"><?= $user['address'] ?></textarea></div>
                    </div>
                </div>

                <div id="tab-medis" class="tab-content">
                    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-100 shadow-sm">
                        <h3 class="font-extrabold text-lg text-slate-900 mb-6 m-0 border-b border-slate-100 pb-4">Indikator Vital</h3>
                        
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
                            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 text-center">
                                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Gol. Darah</div>
                                <div class="font-extrabold text-red-500 text-xl"><?= $user['blood'] ?></div>
                            </div>
                            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 text-center">
                                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Berat Badan</div>
                                <div class="font-extrabold text-slate-800 text-xl"><?= $user['weight'] ?></div>
                            </div>
                            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 text-center">
                                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tinggi Badan</div>
                                <div class="font-extrabold text-slate-800 text-xl"><?= $user['height'] ?></div>
                            </div>
                            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 text-center">
                                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Alergi</div>
                                <div class="font-extrabold text-slate-800 text-base mt-1">Tidak Ada</div>
                            </div>
                        </div>

                        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white text-primary rounded-full flex items-center justify-center text-xl shadow-sm"><i class="fa-solid fa-flask"></i></div>
                                <div>
                                    <div class="font-bold text-slate-900 text-sm">Pemeriksaan Laboratorium</div>
                                    <div class="text-xs text-slate-500">15 Apr 2026 • Klinik CareSync</div>
                                </div>
                            </div>
                            <a href="<?= BASE_URL ?>/pages/lab_results.php" class="bg-primary text-white font-bold px-4 py-2 rounded-lg shadow-sm hover:bg-blue-800 no-underline text-xs smooth-transition">Lihat Hasil</a>
                        </div>
                    </div>
                </div>

                <div id="tab-konsultasi" class="tab-content">
                    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-100 shadow-sm">
                        <div class="flex justify-between items-center mb-6 border-b border-slate-100 pb-4">
                            <h3 class="font-extrabold text-lg text-slate-900 m-0"><i class="fa-solid fa-user-doctor text-primary mr-2"></i> Riwayat Konsultasi</h3>
                        </div>

                        <div class="flex flex-col gap-5">
                            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:border-blue-300 smooth-transition">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 bg-indigo-500 text-white rounded-full flex items-center justify-center font-extrabold text-lg">SW</div>
                                        <div>
                                            <h4 class="font-extrabold text-slate-900 text-sm m-0">dr. Susanti Wulandari, Sp.KK</h4>
                                            <div class="text-[11px] text-slate-500 font-medium">Spesialis Kulit & Kelamin</div>
                                        </div>
                                    </div>
                                    <span class="bg-emerald-50 text-emerald-600 text-[10px] font-extrabold px-2 py-1 rounded uppercase tracking-wider border border-emerald-100">SELESAI</span>
                                </div>
                                <div class="pl-15 ml-1 mb-4 border-l-2 border-slate-100">
                                    <div class="pl-3 text-xs text-slate-600 font-medium mb-1"><i class="fa-regular fa-calendar text-slate-400 mr-1"></i> 17 Apr 2026, 10:45 WIB</div>
                                    <div class="pl-3 text-xs text-slate-600"><span class="font-bold">Keluhan:</span> Jerawat meradang dan gatal.</div>
                                </div>
                                <div class="flex gap-3 mt-4 border-t border-slate-100 pt-4">
                                    <a href="<?= BASE_URL ?>/pages/prescription.php" class="flex-1 bg-primaryLight text-primary border border-blue-200 hover:bg-blue-100 font-bold px-4 py-2.5 rounded-xl text-center text-xs sm:text-sm no-underline smooth-transition">
                                        <i class="fa-solid fa-file-prescription mr-1"></i> Lihat E-Resep
                                    </a>
                                    <a href="<?= BASE_URL ?>/pages/consultation.php?doctor=1" class="flex-1 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-primary font-bold px-4 py-2.5 rounded-xl text-center text-xs sm:text-sm no-underline smooth-transition">
                                        Chat Ulang
                                    </a>
                                </div>
                            </div>

                            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 shadow-sm opacity-80">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 bg-amber-500 text-white rounded-full flex items-center justify-center font-extrabold text-lg">IS</div>
                                        <div>
                                            <h4 class="font-extrabold text-slate-900 text-sm m-0">dr. Ika Syafitri, Sp.PD</h4>
                                            <div class="text-[11px] text-slate-500 font-medium">Spesialis Penyakit Dalam</div>
                                        </div>
                                    </div>
                                    <span class="bg-slate-200 text-slate-600 text-[10px] font-extrabold px-2 py-1 rounded uppercase tracking-wider border border-slate-300">ARSIP</span>
                                </div>
                                <div class="pl-15 ml-1 mb-4 border-l-2 border-slate-200">
                                    <div class="pl-3 text-xs text-slate-500 font-medium mb-1"><i class="fa-regular fa-calendar text-slate-400 mr-1"></i> 10 Jan 2026, 09:15 WIB</div>
                                    <div class="pl-3 text-xs text-slate-500"><span class="font-bold">Keluhan:</span> Asam lambung tinggi.</div>
                                </div>
                                <div class="flex gap-3 mt-4 border-t border-slate-200 pt-4">
                                    <a href="<?= BASE_URL ?>/pages/booking.php" class="w-full bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 font-bold px-4 py-2.5 rounded-xl text-center text-xs sm:text-sm no-underline smooth-transition">
                                        Buat Janji Baru
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="tab-resep" class="tab-content">
                    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-100 shadow-sm">
                        <div class="flex justify-between items-center mb-6 border-b border-slate-100 pb-4">
                            <h3 class="font-extrabold text-lg text-slate-900 m-0"><i class="fa-solid fa-file-prescription text-primary mr-2"></i> Resep Digital</h3>
                        </div>

                        <div class="flex flex-col gap-6">
                            <div class="border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden group">
                                <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-primary group-hover:w-2 smooth-transition"></div>
                                
                                <div class="flex flex-col sm:flex-row justify-between items-start mb-5 border-b border-slate-100 pb-4 ml-3">
                                    <div>
                                        <h2 class="font-extrabold text-lg text-primary m-0">caresync</h2>
                                        <div class="text-[10px] text-slate-400 font-bold tracking-wider">SIP: 123/DU/SIP/2023</div>
                                    </div>
                                    <div class="sm:text-right mt-2 sm:mt-0">
                                        <div class="font-extrabold text-slate-900 text-sm">dr. Susanti Wulandari, Sp.KK</div>
                                        <div class="text-[10px] text-slate-500 font-medium">17 Apr 2026</div>
                                    </div>
                                </div>

                                <div class="mb-5 ml-3 space-y-3">
                                    <div class="flex items-start gap-2">
                                        <span class="font-serif font-bold text-base text-slate-800 italic">R/</span>
                                        <div>
                                            <div class="font-extrabold text-slate-900 text-sm">Doxycycline 500mg Caps No. X</div>
                                            <div class="text-xs text-slate-500 font-medium italic">S 2 dd 1 caps (Habiskan)</div>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <span class="font-serif font-bold text-base text-slate-800 italic">R/</span>
                                        <div>
                                            <div class="font-extrabold text-slate-900 text-sm">Benzolac 5% Gel Tube No. I</div>
                                            <div class="text-xs text-slate-500 font-medium italic">S u.e (Oles tipis malam hari)</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row justify-between items-center bg-slate-50 p-4 rounded-xl ml-3">
                                    <div class="text-xs text-slate-500 font-medium mb-3 sm:mb-0">
                                        Pro: <span class="font-bold text-slate-700"><?= $user['name'] ?> (20th)</span>
                                    </div>
                                    <a href="<?= BASE_URL ?>/pages/prescription.php" class="bg-primary text-white text-sm font-bold px-6 py-2 rounded-lg shadow-sm hover:bg-blue-800 smooth-transition no-underline whitespace-nowrap w-full sm:w-auto text-center">
                                        Lihat & Tebus Obat
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="tab-pesanan" class="tab-content">
                    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-100 shadow-sm">
                        <div class="flex justify-between items-center mb-6 border-b border-slate-100 pb-4">
                            <h3 class="font-extrabold text-lg text-slate-900 m-0"><i class="fa-solid fa-box-open text-primary mr-2"></i> Riwayat Pemesanan Obat</h3>
                        </div>

                        <div class="flex flex-col gap-5">
                            <div class="border border-slate-200 rounded-2xl p-5 shadow-sm hover:border-blue-300 smooth-transition">
                                <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-3">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-bag-shopping text-primary"></i>
                                        <span class="text-xs font-extrabold text-slate-700">Belanja Apotek</span>
                                        <span class="text-[11px] text-slate-400">• 18 Apr 2026</span>
                                    </div>
                                    <span class="bg-blue-50 text-primary border border-blue-200 text-[10px] font-extrabold px-2 py-1 rounded uppercase tracking-wider">Sedang Dikirim</span>
                                </div>
                                
                                <div class="flex items-start gap-4 mb-4">
                                    <div class="w-16 h-16 bg-slate-50 rounded-xl flex items-center justify-center flex-shrink-0 border border-slate-100">
                                        <i class="fa-solid fa-bottle-droplet text-2xl text-slate-300"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-extrabold text-slate-900 text-sm m-0 mb-1">Blackmores Vitamin C 500mg...</h4>
                                        <div class="text-xs text-slate-500 mb-1">1 Barang x Rp 120.000</div>
                                        <div class="text-[10px] text-slate-400 font-bold">+1 Produk Lainnya</div>
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row justify-between items-end sm:items-center mt-4 pt-4 border-t border-slate-100 gap-4">
                                    <div>
                                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Total Tagihan</div>
                                        <div class="font-extrabold text-slate-900 text-base">Rp 207.000</div>
                                    </div>
                                    <a href="<?= BASE_URL ?>/pages/tracking.php" class="w-full sm:w-auto bg-primary text-white font-bold px-6 py-2.5 rounded-xl shadow-sm hover:bg-blue-800 active:scale-95 smooth-transition no-underline text-sm flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-location-crosshairs"></i> Lacak Pesanan
                                    </a>
                                </div>
                            </div>

                            <div class="border border-slate-200 rounded-2xl p-5 shadow-sm opacity-70 hover:opacity-100 smooth-transition">
                                <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-3">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-bag-shopping text-slate-500"></i>
                                        <span class="text-xs font-extrabold text-slate-700">Belanja Apotek</span>
                                        <span class="text-[11px] text-slate-400">• 05 Apr 2026</span>
                                    </div>
                                    <span class="bg-slate-100 text-slate-500 border border-slate-200 text-[10px] font-extrabold px-2 py-1 rounded uppercase tracking-wider">Selesai</span>
                                </div>
                                
                                <div class="flex items-start gap-4 mb-4">
                                    <div class="w-16 h-16 bg-slate-50 rounded-xl flex items-center justify-center flex-shrink-0 border border-slate-100">
                                        <i class="fa-solid fa-flask text-2xl text-slate-300"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-extrabold text-slate-900 text-sm m-0 mb-1">Tolak Angin Cair SidoMuncul</h4>
                                        <div class="text-xs text-slate-500">2 Box x Rp 18.000</div>
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row justify-between items-end sm:items-center mt-4 pt-4 border-t border-slate-100 gap-4">
                                    <div>
                                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Total Tagihan</div>
                                        <div class="font-extrabold text-slate-900 text-base">Rp 48.000</div>
                                    </div>
                                    <a href="<?= BASE_URL ?>/pages/marketplace.php" class="w-full sm:w-auto bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold px-6 py-2.5 rounded-xl shadow-sm active:scale-95 smooth-transition no-underline text-sm flex items-center justify-center gap-2">
                                        Beli Lagi
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    function switchTab(tabId, title, btnElement) {
        // Update Title Header
        document.getElementById('page-title').textContent = title;

        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.remove('active');
        });

        // Reset menu styles (kembalikan semua ke gaya transparan abu-abu)
        document.querySelectorAll('.menu-btn').forEach(btn => {
            btn.classList.remove('active', 'bg-primary', 'text-white', 'shadow-md', 'shadow-blue-200');
            btn.classList.add('bg-transparent', 'text-slate-600');
        });

        // Show selected tab
        document.getElementById('tab-' + tabId).classList.add('active');

        // Set active menu style (ubah yang diklik menjadi tombol biru)
        btnElement.classList.remove('bg-transparent', 'text-slate-600');
        btnElement.classList.add('active', 'bg-primary', 'text-white', 'shadow-md', 'shadow-blue-200');
    }
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>