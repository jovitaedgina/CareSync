<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Karir - CareSync';
$currentPage = 'careers';

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
            <h1 class="text-4xl md:text-5xl font-extrabold text-dark mb-4">Karir di CareSync</h1>
            <p class="text-lg text-textSoft max-w-2xl mx-auto">Bergabunglah dengan tim kami dan bantu mengubah industri kesehatan digital</p>
        </div>

        <div class="bg-white rounded-3xl p-8 md:p-12 shadow-soft border border-slate-100 mb-12">
            <h2 class="text-3xl font-extrabold text-dark mb-6">Mengapa Bekerja di CareSync?</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="flex gap-4">
                    <div class="text-2xl text-primary flex-shrink-0">
                        <i class="fa-solid fa-star"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-dark text-lg mb-2">Misi Bermakna</h3>
                        <p class="text-textSoft text-sm leading-relaxed">
                            Bekerja pada platform yang mengubah kehidupan jutaan orang
                        </p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="text-2xl text-accent flex-shrink-0">
                        <i class="fa-solid fa-people-group"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-dark text-lg mb-2">Tim Hebat</h3>
                        <p class="text-textSoft text-sm leading-relaxed">
                            Kolaborasi dengan talenta terbaik di industri teknologi dan kesehatan
                        </p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="text-2xl text-orange-500 flex-shrink-0">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-dark text-lg mb-2">Pengembangan Karir</h3>
                        <p class="text-textSoft text-sm leading-relaxed">
                            Program pelatihan dan mentoring untuk pertumbuhan profesional Anda
                        </p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="text-2xl text-blue-500 flex-shrink-0">
                        <i class="fa-solid fa-heart"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-dark text-lg mb-2">Benefit Kompetitif</h3>
                        <p class="text-textSoft text-sm leading-relaxed">
                            Paket kompensasi, asuransi kesehatan, dan work-life balance yang baik
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-8 md:p-12 shadow-soft border border-slate-100 mb-12">
            <h2 class="text-3xl font-extrabold text-dark mb-8">Posisi Terbuka</h2>
            <div class="space-y-4">
                <div class="border border-slate-200 rounded-2xl p-6 hover:shadow-soft smooth-transition">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="text-xl font-bold text-dark mb-1">Senior Backend Developer</h3>
                            <p class="text-sm text-textSoft">Jakarta, Indonesia • Full-time</p>
                        </div>
                        <span class="bg-primaryLight text-primary text-xs font-bold px-3 py-1 rounded-full">Terbuka</span>
                    </div>
                    <p class="text-textSoft text-sm mb-4">
                        Kami mencari developer PHP berpengalaman untuk mengembangkan backend platform CareSync
                    </p>
                    <a href="mailto:careers@caresync.com" class="text-primary font-bold text-sm hover:underline">
                        Lihat Detail & Lamar →
                    </a>
                </div>

                <div class="border border-slate-200 rounded-2xl p-6 hover:shadow-soft smooth-transition">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="text-xl font-bold text-dark mb-1">UI/UX Designer</h3>
                            <p class="text-sm text-textSoft">Jakarta, Indonesia • Full-time</p>
                        </div>
                        <span class="bg-primaryLight text-primary text-xs font-bold px-3 py-1 rounded-full">Terbuka</span>
                    </div>
                    <p class="text-textSoft text-sm mb-4">
                        Desainer berbakat untuk menciptakan pengalaman pengguna yang intuitif dan menarik
                    </p>
                    <a href="mailto:careers@caresync.com" class="text-primary font-bold text-sm hover:underline">
                        Lihat Detail & Lamar →
                    </a>
                </div>

                <div class="border border-slate-200 rounded-2xl p-6 hover:shadow-soft smooth-transition">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="text-xl font-bold text-dark mb-1">Customer Success Manager</h3>
                            <p class="text-sm text-textSoft">Jakarta, Indonesia • Full-time</p>
                        </div>
                        <span class="bg-primaryLight text-primary text-xs font-bold px-3 py-1 rounded-full">Terbuka</span>
                    </div>
                    <p class="text-textSoft text-sm mb-4">
                        Kelola hubungan dengan dokter dan apotek mitra untuk memastikan kesuksesan mereka
                    </p>
                    <a href="mailto:careers@caresync.com" class="text-primary font-bold text-sm hover:underline">
                        Lihat Detail & Lamar →
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-primary to-blue-500 rounded-3xl p-12 text-white text-center">
            <h2 class="text-3xl font-extrabold mb-4">Tertarik Bergabung?</h2>
            <p class="text-blue-100 text-lg mb-8">Hubungi tim rekrutmen kami untuk informasi lebih lanjut</p>
            <a href="mailto:careers@caresync.com" class="inline-block bg-white text-primary px-8 py-3 rounded-2xl font-bold hover:bg-slate-100 smooth-transition">
                Hubungi Kami
            </a>
        </div>

    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
