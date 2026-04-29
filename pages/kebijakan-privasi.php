<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Kebijakan Privasi - CareSync';
$currentPage = 'privacy';

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
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-12">
            <h1 class="text-4xl font-extrabold text-dark mb-2">Kebijakan Privasi</h1>
            <p class="text-textSoft">Diperbarui: April 2026</p>
        </div>

        <div class="bg-white rounded-3xl p-8 md:p-12 shadow-soft border border-slate-100 space-y-8">
            
            <section>
                <h2 class="text-2xl font-extrabold text-dark mb-4">Pengantar</h2>
                <p class="text-textSoft leading-relaxed">
                    CareSync ("kami", "kita", atau "perusahaan kami") berkomitmen untuk melindungi privasi Anda. Kebijakan privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi pribadi Anda.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-extrabold text-dark mb-4">1. Informasi yang Kami Kumpulkan</h2>
                <p class="text-textSoft leading-relaxed mb-4">
                    Kami mengumpulkan informasi pribadi berikut:
                </p>
                <ul class="list-disc list-inside space-y-2 text-textSoft">
                    <li>Nama, alamat email, nomor telepon, dan alamat rumah</li>
                    <li>Tanggal lahir dan informasi demografis lainnya</li>
                    <li>Riwayat kesehatan dan informasi medis</li>
                    <li>Informasi pembayaran dan transaksi</li>
                    <li>Data penggunaan website dan aplikasi</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-extrabold text-dark mb-4">2. Bagaimana Kami Menggunakan Informasi Anda</h2>
                <p class="text-textSoft leading-relaxed mb-4">
                    Kami menggunakan informasi Anda untuk:
                </p>
                <ul class="list-disc list-inside space-y-2 text-textSoft">
                    <li>Menyediakan layanan konsultasi kesehatan</li>
                    <li>Mengelola akun dan profil Anda</li>
                    <li>Memproses transaksi pembayaran</li>
                    <li>Mengirimkan notifikasi dan update tentang layanan kami</li>
                    <li>Meningkatkan kualitas dan keamanan platform kami</li>
                    <li>Mematuhi kewajiban hukum dan peraturan</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-extrabold text-dark mb-4">3. Keamanan Data</h2>
                <p class="text-textSoft leading-relaxed">
                    Kami menggunakan enkripsi tingkat enterprise dan protokol keamanan berlapis untuk melindungi data Anda. Informasi sensitif seperti data medis disimpan dalam server yang aman dan terisolasi. Namun, kami tidak dapat menjamin keamanan 100% dari setiap ancaman cyber.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-extrabold text-dark mb-4">4. Berbagi Informasi</h2>
                <p class="text-textSoft leading-relaxed mb-4">
                    Kami tidak akan menjual atau membagikan informasi pribadi Anda kepada pihak ketiga tanpa persetujuan Anda, kecuali:
                </p>
                <ul class="list-disc list-inside space-y-2 text-textSoft">
                    <li>Kepada penyedia layanan yang membantu operasional kami</li>
                    <li>Jika diperlukan oleh hukum atau otoritas pemerintah</li>
                    <li>Untuk melindungi hak dan keamanan kami atau pengguna lain</li>
                    <li>Dalam hal merger, akuisisi, atau penjualan aset</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-extrabold text-dark mb-4">5. Cookie dan Teknologi Pelacakan</h2>
                <p class="text-textSoft leading-relaxed">
                    Kami menggunakan cookie dan teknologi pelacakan lainnya untuk meningkatkan pengalaman Anda. Anda dapat mengubah pengaturan browser untuk menolak cookie, tetapi ini mungkin mempengaruhi fungsionalitas platform kami.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-extrabold text-dark mb-4">6. Hak Akses dan Kontrol Data Anda</h2>
                <p class="text-textSoft leading-relaxed mb-4">
                    Anda memiliki hak untuk:
                </p>
                <ul class="list-disc list-inside space-y-2 text-textSoft">
                    <li>Mengakses data pribadi Anda</li>
                    <li>Memperbaiki atau memperbarui informasi Anda</li>
                    <li>Menghapus akun dan data Anda</li>
                    <li>Menolak pemrosesan data tertentu</li>
                    <li>Mengekspor data Anda dalam format yang dapat diakses</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-extrabold text-dark mb-4">7. Retensi Data</h2>
                <p class="text-textSoft leading-relaxed">
                    Kami menyimpan data pribadi Anda selama diperlukan untuk menyediakan layanan dan mematuhi kewajiban hukum. Setelah itu, data akan dihapus atau di-anonimkan kecuali diwajibkan untuk disimpan oleh hukum.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-extrabold text-dark mb-4">8. Perubahan Kebijakan Privasi</h2>
                <p class="text-textSoft leading-relaxed">
                    Kami mungkin memperbarui kebijakan privasi ini dari waktu ke waktu. Kami akan memberi tahu Anda tentang perubahan signifikan melalui email atau pemberitahuan di platform kami.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-extrabold text-dark mb-4">9. Hubungi Kami</h2>
                <p class="text-textSoft leading-relaxed">
                    Jika Anda memiliki pertanyaan tentang kebijakan privasi ini atau praktik privasi kami, silakan hubungi kami di:</p>
                <ul class="list-none space-y-2 text-textSoft mt-4">
                    <li><span class="font-semibold">Email:</span> privacy@caresync.com</li>
                    <li><span class="font-semibold">Telepon:</span> 0800-1-234-567</li>
                    <li><span class="font-semibold">Alamat:</span> Jl. Siliwangi No. 123, Tasikmalaya, Jawa Barat</li>
                </ul>
            </section>

        </div>

    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
