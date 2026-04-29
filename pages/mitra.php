<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Mitra Dokter & Apotek - CareSync';
$currentPage = 'partners';

$extraHead = '
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: { sans: [\'\"Plus Jakarta Sans\"\', \'sans-serif\'] },
                colors: {
                    primary:"#1D4ED8",
                    primaryLight:"#EFF6FF",
                    accent:"#10B981",
                    dark:"#0F172A",
                    textSoft:"#64748B"
                },
                boxShadow: {
                    soft:"0 10px 40px -10px rgba(0,0,0,0.06)",
                    floating:"0 20px 40px -15px rgba(29,78,216,0.2)"
                }
            }
        }
    }
</script>
';

include __DIR__ . '/../includes/header.php';
?>

<div class="bg-slate-50 min-h-screen py-16">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-extrabold text-dark mb-4">Mitra Dokter & Apotek</h1>
            <p class="text-lg text-textSoft max-w-2xl mx-auto">Tingkatkan jangkauan praktik Anda dan kelola pasien lebih efisien dengan platform CareSync</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
            <div class="bg-white rounded-3xl p-8 shadow-soft border border-slate-100">
                <div class="text-5xl text-primary mb-4">
                    <i class="fa-solid fa-user-doctor"></i>
                </div>
                <h2 class="text-2xl font-extrabold text-dark mb-6">Untuk Dokter</h2>
                <ul class="space-y-4 mb-8">
                    <li class="flex items-start gap-3">
                        <i class="fa-solid fa-check text-accent mt-1 flex-shrink-0"></i>
                        <span class="text-textSoft">Perluas basis pasien Anda secara online</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fa-solid fa-check text-accent mt-1 flex-shrink-0"></i>
                        <span class="text-textSoft">Kelola jadwal dan resep dengan mudah</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fa-solid fa-check text-accent mt-1 flex-shrink-0"></i>
                        <span class="text-textSoft">Terima pembayaran langsung dan aman</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fa-solid fa-check text-accent mt-1 flex-shrink-0"></i>
                        <span class="text-textSoft">Akses rekam medis digital pasien</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fa-solid fa-check text-accent mt-1 flex-shrink-0"></i>
                        <span class="text-textSoft">Dukungan teknis dan pelatihan 24/7</span>
                    </li>
                </ul>
                <a href="mailto:partners@caresync.com?subject=Bergabung%20sebagai%20Dokter%20Mitra" class="block w-full bg-primary text-white text-center py-3 rounded-2xl font-bold hover:bg-blue-800 smooth-transition">
                    Daftar sebagai Dokter Mitra
                </a>
            </div>

            <div class="bg-white rounded-3xl p-8 shadow-soft border border-slate-100">
                <div class="text-5xl text-accent mb-4">
                    <i class="fa-solid fa-prescription-bottle"></i>
                </div>
                <h2 class="text-2xl font-extrabold text-dark mb-6">Untuk Apotek</h2>
                <ul class="space-y-4 mb-8">
                    <li class="flex items-start gap-3">
                        <i class="fa-solid fa-check text-primary mt-1 flex-shrink-0"></i>
                        <span class="text-textSoft">Jangkau lebih banyak pelanggan online</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fa-solid fa-check text-primary mt-1 flex-shrink-0"></i>
                        <span class="text-textSoft">Kelola inventory produk secara terpusat</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fa-solid fa-check text-primary mt-1 flex-shrink-0"></i>
                        <span class="text-textSoft">Terima pesanan dan kelola pengiriman</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fa-solid fa-check text-primary mt-1 flex-shrink-0"></i>
                        <span class="text-textSoft">Integrasi dengan resep digital dokter</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fa-solid fa-check text-primary mt-1 flex-shrink-0"></i>
                        <span class="text-textSoft">Dashboard analitik dan laporan penjualan</span>
                    </li>
                </ul>
                <a href="mailto:partners@caresync.com?subject=Bergabung%20sebagai%20Apotek%20Mitra" class="block w-full bg-accent text-white text-center py-3 rounded-2xl font-bold hover:bg-green-700 smooth-transition">
                    Daftar sebagai Apotek Mitra
                </a>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-8 md:p-12 shadow-soft border border-slate-100 mb-12">
            <h2 class="text-3xl font-extrabold text-dark mb-8">Keuntungan Menjadi Mitra CareSync</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="border-l-4 border-primary pl-6">
                    <h3 class="font-bold text-dark text-lg mb-2">Komisi Kompetitif</h3>
                    <p class="text-textSoft text-sm">
                        Dapatkan komisi yang adil dan transparan untuk setiap transaksi yang berhasil
                    </p>
                </div>
                <div class="border-l-4 border-accent pl-6">
                    <h3 class="font-bold text-dark text-lg mb-2">Pelatihan & Support</h3>
                    <p class="text-textSoft text-sm">
                        Tim support kami siap membantu Anda mengoptimalkan platform setiap hari
                    </p>
                </div>
                <div class="border-l-4 border-orange-500 pl-6">
                    <h3 class="font-bold text-dark text-lg mb-2">Teknologi Terdepan</h3>
                    <p class="text-textSoft text-sm">
                        Gunakan teknologi terbaru untuk meningkatkan efisiensi operasional Anda
                    </p>
                </div>
                <div class="border-l-4 border-blue-500 pl-6">
                    <h3 class="font-bold text-dark text-lg mb-2">Skalabilitas</h3>
                    <p class="text-textSoft text-sm">
                        Infrastruktur kami dapat mendukung pertumbuhan bisnis Anda tanpa batas
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-primary to-blue-500 rounded-3xl p-12 text-white text-center">
            <h2 class="text-3xl font-extrabold mb-4">Siap Menjadi Mitra?</h2>
            <p class="text-blue-100 text-lg mb-8">Hubungi tim partnership kami untuk mendiskusikan peluang kolaborasi</p>
            <a href="mailto:partners@caresync.com" class="inline-block bg-white text-primary px-8 py-3 rounded-2xl font-bold hover:bg-slate-100 smooth-transition">
                Hubungi Partnership Team
            </a>
        </div>

    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
