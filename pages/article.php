<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle   = 'Artikel Kesehatan — CareSync';
$currentPage = 'dashboard';

// Suntikkan Tailwind CSS
$extraHead = '
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: { sans: [\'"Plus Jakarta Sans"\', \'sans-serif\'] },
                colors: { primary: \'#1D4ED8\', primaryLight: \'#EFF6FF\', accent: \'#10B981\', dark: \'#0F172A\', textSoft: \'#64748B\' },
                boxShadow: { \'floating\': \'0 20px 40px -15px rgba(29, 78, 216, 0.15)\' }
            }
        }
    }
</script>
<style>
    body { background-color: #F8FAFC; margin: 0; }
    .smooth-transition { transition: all 0.3s ease-in-out; }
    /* Styling khusus untuk konten artikel */
    .article-content p { margin-bottom: 1.25rem; }
    .article-content ul, .article-content ol { margin-bottom: 1.25rem; padding-left: 1.5rem; }
    .article-content li { margin-bottom: 0.5rem; }
</style>
';

include __DIR__ . '/../includes/header.php';

/* * MOCK DATA ARTIKEL */
$articles = [
    1 => [
        'title' => 'Pentingnya Menjaga Pola Tidur untuk Kesehatan Mental', 'category' => 'Gaya Hidup', 'date' => '12 Apr 2026', 'read_time' => '4 Menit Baca',
        'img' => 'https://images.unsplash.com/photo-1505576399279-565b52d4ac71?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
        'content' => '
            <p>Tidur bukan sekadar waktu istirahat bagi tubuh, melainkan fase krusial di mana otak memproses informasi, memperbaiki sel-sel, dan menyeimbangkan zat kimia yang memengaruhi suasana hati.</p>
            <p>Kurang tidur kronis sering kali dikaitkan dengan peningkatan risiko gangguan kecemasan dan depresi. Saat kita kurang tidur, amigdala (pusat emosi di otak) menjadi lebih reaktif, membuat kita lebih mudah stres dan sensitif terhadap hal-hal kecil.</p>
            <h3 class="text-xl font-extrabold text-slate-800 mt-8 mb-4">Tips Memperbaiki Pola Tidur:</h3>
            <ul class="list-disc">
                <li><strong>Konsistensi:</strong> Tetapkan jadwal tidur dan bangun yang konsisten setiap hari, bahkan di akhir pekan.</li>
                <li><strong>Digital Detox:</strong> Hindari layar gawai (HP/Laptop) minimal 1 jam sebelum tidur karena blue light dapat menekan produksi melatonin.</li>
                <li><strong>Lingkungan:</strong> Ciptakan lingkungan kamar yang gelap, tenang, dan sejuk.</li>
                <li><strong>Kafein:</strong> Batasi konsumsi kopi atau teh di sore dan malam hari.</li>
            </ul>
            <p>Jika Anda terus mengalami kesulitan tidur atau merasa cemas berlebihan, sangat disarankan untuk berkonsultasi dengan ahlinya.</p>
        '
    ],
    2 => [
        'title' => '5 Makanan yang Wajib Dihindari Penderita Asam Lambung', 'category' => 'Nutrisi', 'date' => '10 Apr 2026', 'read_time' => '3 Menit Baca',
        'img' => 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
        'content' => '
            <p>Penyakit asam lambung (GERD) sering kali dipicu oleh pola makan yang tidak tepat. Bagi penderita, memilih makanan adalah kunci utama agar asam lambung tidak naik dan menyebabkan rasa terbakar di dada (heartburn).</p>
            <p>Berikut adalah 5 jenis makanan yang sebaiknya dihindari:</p>
            <ol class="list-decimal">
                <li><strong>Makanan Pedas:</strong> Cabai dan lada dapat mengiritasi lapisan esofagus dan memicu produksi asam berlebih.</li>
                <li><strong>Cokelat dan Kafein:</strong> Kandungan di dalamnya dapat merelaksasi katup esofagus bagian bawah, sehingga asam mudah naik ke kerongkongan.</li>
                <li><strong>Buah Sitrus:</strong> Jeruk, lemon, nanas, dan tomat memiliki tingkat keasaman tinggi yang bisa memperparah gejala.</li>
                <li><strong>Makanan Berlemak dan Digoreng:</strong> Makanan ini membutuhkan waktu lebih lama untuk dicerna, sehingga menunda pengosongan lambung.</li>
                <li><strong>Bawang-bawangan:</strong> Bawang putih dan bawang bombay mentah adalah pemicu umum GERD bagi sebagian besar orang.</li>
            </ol>
            <p>Selalu konsumsi makanan dalam porsi kecil namun sering untuk membantu meringankan beban kerja lambung Anda. Jika gejala GERD tidak membaik, segera lakukan konsultasi medis.</p>
        '
    ],
    3 => [
        'title' => 'Jadwal Imunisasi Dasar Lengkap Bayi Usia 0-12 Bulan', 'category' => 'Kesehatan Anak', 'date' => '08 Apr 2026', 'read_time' => '5 Menit Baca',
        'img' => 'https://images.unsplash.com/photo-1584036561566-baf8f5f1b144?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
        'content' => '
            <p>Imunisasi dasar adalah langkah preventif paling efektif untuk melindungi bayi dari penyakit menular yang berbahaya. Kementerian Kesehatan RI menetapkan jadwal wajib yang harus diikuti oleh setiap orang tua.</p>
            <h3 class="text-xl font-extrabold text-slate-800 mt-8 mb-4">Jadwal Usia 0-6 Bulan:</h3>
            <ul class="list-disc">
                <li><strong>Baru Lahir (0 hari):</strong> Hepatitis B (HB0) diberikan sebelum bayi berusia 24 jam.</li>
                <li><strong>Usia 1 Bulan:</strong> BCG (untuk mencegah Tuberkulosis) dan Polio 1 (tetes).</li>
                <li><strong>Usia 2 Bulan:</strong> DPT-HB-Hib 1 (mencegah Difteri, Pertusis, Tetanus, Hepatitis B, meningitis) dan Polio 2.</li>
                <li><strong>Usia 3 Bulan:</strong> DPT-HB-Hib 2 dan Polio 3.</li>
                <li><strong>Usia 4 Bulan:</strong> DPT-HB-Hib 3, Polio 4, dan IPV (Polio suntik).</li>
            </ul>
            <p>Pada usia 9 bulan, bayi disarankan untuk mendapatkan imunisasi Campak/MR. Pastikan Anda memiliki buku KIA (Kesehatan Ibu dan Anak) untuk mencatat setiap jadwal imunisasi agar tidak ada yang terlewat.</p>
        '
    ]
];

$id = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$article = $articles[$id] ?? $articles[1];
?>

<div class="bg-slate-50 min-h-screen py-10" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <nav class="flex text-sm font-medium text-slate-500 mb-8 gap-2 items-center">
            <a href="<?= BASE_URL ?>/pages/dashboard.php" class="hover:text-primary smooth-transition no-underline text-slate-500">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <a href="<?= BASE_URL ?>/pages/dashboard.php" class="hover:text-primary smooth-transition no-underline text-slate-500">Artikel</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-slate-800 font-bold truncate max-w-[150px] sm:max-w-none"><?= $article['title'] ?></span>
        </nav>

        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden mb-12">
            <div class="w-full h-[300px] sm:h-[450px] relative">
                <img src="<?= $article['img'] ?>" alt="Cover Artikel" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 to-transparent"></div>
            </div>

            <div class="p-6 sm:p-12 -mt-20 relative z-10">
                <div class="bg-white rounded-2xl p-6 sm:p-10 shadow-floating mb-8 border border-slate-100">
                    <div class="flex flex-wrap items-center gap-3 text-xs font-bold text-slate-500 mb-4">
                        <span class="text-primary bg-primaryLight px-3 py-1 rounded-md uppercase tracking-wider"><?= $article['category'] ?></span>
                        <span class="flex items-center gap-1"><i class="fa-regular fa-calendar"></i> <?= $article['date'] ?></span>
                        <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                        <span class="flex items-center gap-1"><i class="fa-regular fa-clock"></i> <?= $article['read_time'] ?></span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 leading-tight m-0 mb-6"><?= $article['title'] ?></h1>
                    
                    <div class="article-content text-slate-700 text-[15px] sm:text-base leading-loose m-0 text-justify">
                        <?= $article['content'] ?>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-primary to-blue-500 rounded-3xl p-6 sm:p-8 flex flex-col sm:flex-row items-center justify-between gap-6 shadow-floating relative overflow-hidden group">
                    <i class="fa-solid fa-user-doctor text-white/10 text-9xl absolute -right-6 -bottom-10 group-hover:scale-110 group-hover:-rotate-12 smooth-transition duration-500"></i>
                    <div class="relative z-10 text-white sm:w-2/3 text-center sm:text-left">
                        <h3 class="text-2xl font-extrabold mb-2 m-0 leading-snug">Punya keluhan serupa? Jangan dibiarkan!</h3>
                        <p class="text-blue-100 text-sm leading-relaxed m-0">Konsultasikan gejala yang Anda rasakan dengan dokter spesialis kami via chat atau video call sekarang juga.</p>
                    </div>
                    <a href="<?= BASE_URL ?>/pages/booking.php" class="relative z-10 bg-white text-primary px-6 py-3.5 rounded-xl font-bold shadow-md hover:bg-slate-50 active:scale-95 smooth-transition whitespace-nowrap no-underline cursor-pointer flex items-center gap-2">
                        <i class="fa-regular fa-comments"></i> Konsultasi Dokter
                    </a>
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-xl font-extrabold text-slate-900 mb-6">Artikel Terkait</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <?php 
                // Tampilkan 2 artikel selain artikel yang sedang dibaca
                foreach($articles as $key => $a): 
                    if($key === $id) continue;
                ?>
                <a href="<?= BASE_URL ?>/pages/article.php?id=<?= $key ?>" class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm hover:-translate-y-1 hover:shadow-md smooth-transition flex flex-col sm:flex-row items-center gap-4 no-underline group cursor-pointer">
                    <div class="w-full sm:w-32 h-32 flex-shrink-0 rounded-xl overflow-hidden">
                        <img src="<?= $a['img'] ?>" class="w-full h-full object-cover group-hover:scale-110 smooth-transition">
                    </div>
                    <div class="flex-1">
                        <span class="text-[10px] font-bold text-primary uppercase tracking-wider mb-1 block"><?= $a['category'] ?></span>
                        <h4 class="font-bold text-slate-900 text-sm m-0 mb-2 line-clamp-2 group-hover:text-primary smooth-transition"><?= $a['title'] ?></h4>
                        <span class="text-xs text-slate-400 font-medium"><?= $a['date'] ?></span>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>