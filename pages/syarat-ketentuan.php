<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Syarat & Ketentuan - CareSync';
$currentPage = 'terms';

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
            <h1 class="text-4xl font-extrabold text-dark mb-2">Syarat & Ketentuan</h1>
            <p class="text-textSoft">Diperbarui: April 2026</p>
        </div>

        <div class="bg-white rounded-3xl p-8 md:p-12 shadow-soft border border-slate-100 space-y-8">
            
            <section>
                <h2 class="text-2xl font-extrabold text-dark mb-4">1. Penerimaan Syarat & Ketentuan</h2>
                <p class="text-textSoft leading-relaxed">
                    Dengan mengakses dan menggunakan platform CareSync, Anda menyetujui untuk terikat oleh syarat dan ketentuan ini. Jika Anda tidak setuju dengan bagian manapun dari syarat ini, mohon jangan menggunakan layanan kami.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-extrabold text-dark mb-4">2. Penggunaan Layanan</h2>
                <p class="text-textSoft leading-relaxed mb-4">
                    Anda setuju untuk menggunakan platform CareSync hanya untuk tujuan yang sah dan sesuai dengan hukum yang berlaku. Anda berjanji untuk tidak:
                </p>
                <ul class="list-disc list-inside space-y-2 text-textSoft">
                    <li>Menggunakan layanan untuk tujuan yang ilegal atau merugikan</li>
                    <li>Mengirimkan konten yang bersifat mencemarkan, mengancam, atau mengganggu</li>
                    <li>Mengganggu atau merusak infrastruktur atau keamanan platform</li>
                    <li>Mengakses akun pengguna lain tanpa izin</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-extrabold text-dark mb-4">3. Akun Pengguna</h2>
                <p class="text-textSoft leading-relaxed">
                    Anda bertanggung jawab atas kerahasiaan password akun Anda dan semua aktivitas yang terjadi di akun Anda. Anda setuju untuk segera memberi tahu kami jika terdapat penggunaan akun yang tidak sah.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-extrabold text-dark mb-4">4. Layanan Konsultasi Medis</h2>
                <p class="text-textSoft leading-relaxed mb-4">
                    Layanan konsultasi medis di CareSync:
                </p>
                <ul class="list-disc list-inside space-y-2 text-textSoft">
                    <li>Tidak menggantikan konsultasi medis langsung dengan dokter</li>
                    <li>Hanya untuk tujuan informatif dan konsultasi awal</li>
                    <li>Dokter mitra memiliki lisensi resmi dari badan kesehatan yang berwenang</li>
                    <li>Tidak untuk keadaan darurat medis - hubungi 112 jika diperlukan</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-extrabold text-dark mb-4">5. Batasan Tanggung Jawab</h2>
                <p class="text-textSoft leading-relaxed">
                    CareSync tidak bertanggung jawab atas kerugian yang timbul dari penggunaan atau ketidakinginan untuk menggunakan layanan kami, termasuk kerugian data, pendapatan, atau reputasi, kecuali ditetapkan sebaliknya oleh hukum yang berlaku.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-extrabold text-dark mb-4">6. Perubahan Syarat</h2>
                <p class="text-textSoft leading-relaxed">
                    CareSync berhak untuk mengubah syarat dan ketentuan ini kapan saja. Perubahan akan berlaku segera setelah diposting di platform. Penggunaan berkelanjutan berarti Anda menerima perubahan tersebut.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-extrabold text-dark mb-4">7. Hukum yang Berlaku</h2>
                <p class="text-textSoft leading-relaxed">
                    Syarat dan ketentuan ini diatur oleh hukum Negara Kesatuan Republik Indonesia. Setiap perselisihan akan diselesaikan melalui pengadilan yang kompeten di Indonesia.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-extrabold text-dark mb-4">8. Hubungi Kami</h2>
                <p class="text-textSoft leading-relaxed">
                    Jika Anda memiliki pertanyaan tentang syarat dan ketentuan ini, silakan hubungi kami di:</p>
                <ul class="list-none space-y-2 text-textSoft mt-4">
                    <li><span class="font-semibold">Email:</span> legal@caresync.com</li>
                    <li><span class="font-semibold">Telepon:</span> 0800-1-234-567</li>
                    <li><span class="font-semibold">Alamat:</span> Jl. Siliwangi No. 123, Tasikmalaya, Jawa Barat</li>
                </ul>
            </section>

        </div>

    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
