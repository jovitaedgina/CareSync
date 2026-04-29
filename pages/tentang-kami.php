<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Tentang Kami - CareSync';
$currentPage = 'about';

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
            <h1 class="text-4xl md:text-5xl font-extrabold text-dark mb-4">Tentang CareSync</h1>
            <p class="text-lg text-textSoft max-w-2xl mx-auto">Platform kesehatan terpadu yang mengubah cara Anda mengakses layanan kesehatan berkualitas</p>
        </div>

        <div class="bg-white rounded-3xl p-8 md:p-12 shadow-soft border border-slate-100 mb-12">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-extrabold text-dark mb-6">Misi Kami</h2>
                    <p class="text-textSoft leading-relaxed mb-4">
                        CareSync didirikan dengan visi untuk membuat layanan kesehatan berkualitas tinggi menjadi mudah diakses oleh semua orang. Kami percaya bahwa teknologi dapat menghubungkan pasien dengan dokter spesialis terbaik dan layanan farmasi yang terpercaya.
                    </p>
                    <p class="text-textSoft leading-relaxed">
                        Dengan platform kami, Anda dapat berkonsultasi dengan dokter berpengalaman, mengakses rekam medis digital, dan membeli obat dengan mudah - semua dalam satu aplikasi yang aman dan terpercaya.
                    </p>
                </div>
                <div class="bg-primaryLight rounded-2xl p-8 flex items-center justify-center">
                    <div class="text-center">
                        <div class="text-6xl text-primary mb-4">
                            <i class="fa-solid fa-heart"></i>
                        </div>
                        <p class="text-dark font-bold text-lg">Kesehatan Adalah Prioritas</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <div class="bg-white rounded-3xl p-8 shadow-soft border border-slate-100">
                <div class="text-4xl text-primary mb-4">
                    <i class="fa-solid fa-users"></i>
                </div>
                <h3 class="text-xl font-extrabold text-dark mb-3">Komunitas Besar</h3>
                <p class="text-textSoft text-sm leading-relaxed">
                    Bergabung dengan ribuan pengguna yang telah mempercayai CareSync untuk kebutuhan kesehatan mereka
                </p>
            </div>
            <div class="bg-white rounded-3xl p-8 shadow-soft border border-slate-100">
                <div class="text-4xl text-accent mb-4">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h3 class="text-xl font-extrabold text-dark mb-3">Aman & Terpercaya</h3>
                <p class="text-textSoft text-sm leading-relaxed">
                    Data Anda dilindungi dengan enkripsi tingkat enterprise dan mematuhi standar keamanan internasional
                </p>
            </div>
            <div class="bg-white rounded-3xl p-8 shadow-soft border border-slate-100">
                <div class="text-4xl text-orange-500 mb-4">
                    <i class="fa-solid fa-bolt"></i>
                </div>
                <h3 class="text-xl font-extrabold text-dark mb-3">Cepat & Mudah</h3>
                <p class="text-textSoft text-sm leading-relaxed">
                    Dapatkan respon dari dokter dalam hitungan menit, bukan jam atau hari
                </p>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-8 md:p-12 shadow-soft border border-slate-100 mb-12">
            <h2 class="text-3xl font-extrabold text-dark mb-8">Nilai-Nilai Kami</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h4 class="font-bold text-dark text-lg mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-check text-accent"></i> Keunggulan
                    </h4>
                    <p class="text-textSoft text-sm leading-relaxed">
                        Kami berkomitmen untuk memberikan layanan terbaik dengan inovasi berkelanjutan
                    </p>
                </div>
                <div>
                    <h4 class="font-bold text-dark text-lg mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-check text-accent"></i> Integritas
                    </h4>
                    <p class="text-textSoft text-sm leading-relaxed">
                        Transparansi dan kejujuran adalah fondasi dalam setiap interaksi kami
                    </p>
                </div>
                <div>
                    <h4 class="font-bold text-dark text-lg mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-check text-accent"></i> Kepedulian
                    </h4>
                    <p class="text-textSoft text-sm leading-relaxed">
                        Kami peduli dengan kesehatan dan kesejahteraan setiap pengguna platform
                    </p>
                </div>
                <div>
                    <h4 class="font-bold text-dark text-lg mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-check text-accent"></i> Aksesibilitas
                    </h4>
                    <p class="text-textSoft text-sm leading-relaxed">
                        Layanan kesehatan berkualitas harus dapat diakses oleh semua orang
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-primary to-blue-500 rounded-3xl p-12 text-white text-center">
            <h2 class="text-3xl font-extrabold mb-4">Bergabunglah dengan CareSync</h2>
            <p class="text-blue-100 text-lg mb-8">Mulai perjalanan kesehatan yang lebih baik hari ini</p>
            <a href="<?= BASE_URL ?>/pages/dashboard.php" class="inline-block bg-white text-primary px-8 py-3 rounded-2xl font-bold hover:bg-slate-100 smooth-transition">
                Mulai Sekarang
            </a>
        </div>

    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
