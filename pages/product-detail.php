<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle   = 'Detail Produk — CareSync';
$currentPage = 'marketplace'; // Tetap aktifkan menu Apotek di Header

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
                boxShadow: { \'floating\': \'0 20px 40px -15px rgba(29, 78, 216, 0.15)\' }
            }
        }
    }
</script>
<style>
    body { background-color: #F8FAFC; margin: 0; }
    .smooth-transition { transition: all 0.3s ease-in-out; }
    /* Menghilangkan panah spinner pada input number */
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
    @keyframes slideIn{from{opacity:0;transform:translateX(20px)}to{opacity:1;transform:translateX(0)}}
</style>
';

include __DIR__ . '/../includes/header.php';

/* * MOCK DATA DETAIL PRODUK DINAMIS (15 PRODUK) */
$allProducts = [
    [
        'id' => 1, 'name' => 'Blackmores Vitamin C 500mg - 60 Tablet', 'category' => 'Vitamin & Suplemen', 'price' => 120000, 'sold' => '1.2k', 'rating' => '4.9', 'reviews' => 342, 'stock' => 45, 'badge' => 'text-emerald-600 bg-emerald-50', 'icon' => 'fa-bottle-droplet', 
        'img' => 'https://blackmores-bucket.s3.ap-southeast-1.amazonaws.com/blackmores/product/images667bb9448c2ff.png',
        'description' => 'Blackmores Vitamin C 500mg adalah suplemen kesehatan yang mengandung Vitamin C untuk membantu memelihara daya tahan tubuh. Suplemen ini sangat baik dikonsumsi untuk mencegah flu, mempercepat penyembuhan luka, serta bertindak sebagai antioksidan.',
        'indikasi' => 'Membantu memelihara daya tahan tubuh, mencegah defisiensi vitamin C.',
        'komposisi' => 'Asam Askorbat (Vitamin C) 500 mg.', 'dosis' => 'Dewasa: 1 tablet sehari.', 'aturan_pakai' => 'Dikonsumsi sesudah makan.', 'golongan' => 'Suplemen Kesehatan', 'kemasan' => 'Botol Kaca @ 60 tablet', 'manufaktur' => 'Kalbe Blackmores Nutrition', 'no_registrasi' => 'BPOM: SI164507251', 'kategori_kehamilan' => 'Kategori A: Aman untuk ibu hamil dan janin.', 'perhatian' => 'Hati-hati pada penderita gangguan ginjal.', 'kontraindikasi' => 'Hipersensitivitas terhadap Vitamin C.', 'efek_samping' => 'Dosis berlebih dapat menyebabkan gangguan pencernaan.'
    ],
    [
        'id' => 2, 'name' => 'Panadol Extra Paracetamol 10 Kaplet', 'category' => 'Obat Bebas', 'price' => 15000, 'sold' => '5.5k', 'rating' => '4.8', 'reviews' => 1250, 'stock' => 120, 'badge' => 'text-blue-600 bg-blue-50', 'icon' => 'fa-tablets', 
        'img' => 'https://d2qjkwm11akmwu.cloudfront.net/products/807265_19-11-2024_13-49-18.webp',
        'description' => 'Panadol Extra adalah obat dengan kandungan Paracetamol dan Caffeine. Obat ini digunakan untuk meredakan sakit kepala, sakit gigi, sakit pada otot, serta menurunkan demam.',
        'indikasi' => 'Meredakan sakit kepala, sakit gigi, nyeri otot, dan menurunkan demam.',
        'komposisi' => 'Paracetamol 500 mg, Caffeine 65 mg.', 'dosis' => 'Dewasa & Anak > 12 th: 1 kaplet, 3-4 kali sehari. Maksimal 8 kaplet/hari.', 'aturan_pakai' => 'Dapat dikonsumsi sebelum atau sesudah makan.', 'golongan' => 'Obat Bebas', 'kemasan' => 'Blister @ 10 Kaplet', 'manufaktur' => 'GlaxoSmithKline', 'no_registrasi' => 'BPOM: DBL0403810104A1', 'kategori_kehamilan' => 'Kategori B: Studi pada reproduksi hewan tidak menunjukkan risiko janin.', 'perhatian' => 'Penggunaan jangka panjang dapat menyebabkan kerusakan hati.', 'kontraindikasi' => 'Penderita dengan gangguan fungsi hati berat.', 'efek_samping' => 'Jarang terjadi: ruam kulit, reaksi alergi.'
    ],
    [
        'id' => 3, 'name' => 'Sensi Masker Medis 3-Ply - Isi 50 Pcs', 'category' => 'Alat Kesehatan', 'price' => 35000, 'sold' => '10k+', 'rating' => '4.9', 'reviews' => 4500, 'stock' => 300, 'badge' => 'text-purple-600 bg-purple-50', 'icon' => 'fa-mask-face', 
        'img' => 'https://doktersehat.com/wp-content/uploads/2020/03/obat_dan_vitamin_Doktersehat_com_Masker_Sensi_Earloop_3_Ply_-_Hijau_50S.jpg',
        'description' => 'Sensi Masker Medis 3-Ply adalah masker pelindung wajah dengan 3 lapisan penyaring untuk melindungi saluran pernapasan dari debu, asap, kuman, cairan, dan partikel mikro lainnya.',
        'indikasi' => 'Melindungi saluran pernapasan dari partikel udara, kuman, dan cairan.',
        'komposisi' => 'Non-woven fabric, Meltblown filter, Earloop elastis.', 'dosis' => 'Digunakan sesuai kebutuhan. Segera ganti jika kotor atau basah.', 'aturan_pakai' => 'Kaitkan tali pada telinga, tekan kawat bagian hidung, dan tarik bagian bawah hingga menutupi dagu.', 'golongan' => 'Alat Kesehatan', 'kemasan' => 'Box @ 50 Pcs', 'manufaktur' => 'Arista Latindo', 'no_registrasi' => 'KEMENKES RI AKD 11603010092', 'kategori_kehamilan' => 'Aman untuk semua kalangan.', 'perhatian' => 'Masker sekali pakai, tidak untuk dicuci ulang.', 'kontraindikasi' => 'Tidak ada.', 'efek_samping' => 'Penggunaan terlalu ketat mungkin menyebabkan iritasi ringan.'
    ],
    [
        'id' => 4, 'name' => 'Betadine Antiseptic Solution 60ml', 'category' => 'P3K', 'price' => 45000, 'sold' => '850', 'rating' => '4.7', 'reviews' => 210, 'stock' => 55, 'badge' => 'text-orange-600 bg-orange-50', 'icon' => 'fa-prescription-bottle-medical', 
        'img' => 'https://guardianindonesia.co.id/media/catalog/product/0/0648432~1_20250723114509_4781.png?format=png&auto=webp&width=840&height=375&fit=cover',
        'description' => 'Betadine Antiseptic Solution merupakan antiseptik pada luka untuk membunuh kuman penyebab infeksi. Mengandung Povidone-Iodine 10%.',
        'indikasi' => 'Antiseptik untuk mencegah infeksi pada luka lecet, luka sayat, luka bakar ringan.',
        'komposisi' => 'Povidone-Iodine 10%', 'dosis' => 'Dioleskan sesuai kebutuhan pada bagian yang terluka.', 'aturan_pakai' => 'Bersihkan luka terlebih dahulu, lalu oleskan.', 'golongan' => 'Obat Bebas Terbatas', 'kemasan' => 'Botol Plastik @ 60 ml', 'manufaktur' => 'Mundipharma Healthcare', 'no_registrasi' => 'BPOM: DTL1613711941B1', 'kategori_kehamilan' => 'Kategori D: Ada bukti risiko pada janin.', 'perhatian' => 'Hanya untuk pemakaian luar.', 'kontraindikasi' => 'Hipersensitivitas terhadap iodium.', 'efek_samping' => 'Iritasi lokal pada area yang diolesi.'
    ],
    [
        'id' => 5, 'name' => 'Amoxicillin 500mg - 10 Kaplet', 'category' => 'Obat Keras (Resep)', 'price' => 12000, 'sold' => '3k+', 'rating' => '4.8', 'reviews' => 890, 'stock' => 200, 'badge' => 'text-red-600 bg-red-50', 'icon' => 'fa-capsules', 
        'img' => 'https://images.alodokter.com/dk0z4ums3/image/upload/c_scale,h_500,w_500/v1/production/pharmacy/products/1687342946_amox_500_berno-seles',
        'description' => 'Amoxicillin adalah antibiotik penisilin spektrum luas untuk mengobati infeksi bakteri. HARUS DENGAN RESEP DOKTER.',
        'indikasi' => 'Mengobati infeksi bakteri pada saluran pernapasan, kulit, dan saluran kemih.',
        'komposisi' => 'Amoxicillin Trihydrate 500 mg', 'dosis' => 'Sesuai resep dokter. Biasanya: Dewasa 250-500 mg tiap 8 jam.', 'aturan_pakai' => 'Dikonsumsi sesudah makan. Obat harus dihabiskan.', 'golongan' => 'Obat Keras', 'kemasan' => 'Strip @ 10 Kaplet', 'manufaktur' => 'Generik', 'no_registrasi' => 'BPOM: GKL9810023410A1', 'kategori_kehamilan' => 'Kategori B: Aman.', 'perhatian' => 'Harus dihabiskan untuk mencegah resistensi antibiotik.', 'kontraindikasi' => 'Hipersensitif terhadap Penisilin.', 'efek_samping' => 'Mual, muntah, diare, ruam kulit.'
    ],
    [
        'id' => 6, 'name' => 'Imboost Force - 10 Kaplet', 'category' => 'Vitamin & Suplemen', 'price' => 75000, 'sold' => '2.1k', 'rating' => '4.9', 'reviews' => 600, 'stock' => 85, 'badge' => 'text-emerald-600 bg-emerald-50', 'icon' => 'fa-leaf', 
        'img' => 'https://images.alodokter.com/dk0z4ums3/image/upload/c_scale,h_500,w_500/v1/production/pharmacy/products/1659930367_629d9f10f15ee8089e029434',
        'description' => 'Imboost Force adalah suplemen kesehatan untuk meningkatkan sistem kekebalan tubuh.',
        'indikasi' => 'Membantu memelihara dan meningkatkan daya tahan tubuh.',
        'komposisi' => 'Echinacea purpurea 250 mg, Black elderberry 400 mg, Zn picolinate 10 mg.', 'dosis' => 'Dewasa: 1 kaplet, 1-3 kali sehari.', 'aturan_pakai' => 'Dikonsumsi sesudah makan.', 'golongan' => 'Suplemen Kesehatan', 'kemasan' => 'Strip @ 10 Kaplet', 'manufaktur' => 'Soho Industri Pharmasi', 'no_registrasi' => 'BPOM: SD021503871', 'kategori_kehamilan' => 'Tidak direkomendasikan lebih dari 8 minggu.', 'perhatian' => 'Hentikan pemakaian jika terjadi alergi.', 'kontraindikasi' => 'Penyakit autoimun.', 'efek_samping' => 'Gangguan perut ringan.'
    ],
    [
        'id' => 7, 'name' => 'Hansaplast Plester Kain - Isi 10', 'category' => 'P3K', 'price' => 8500, 'sold' => '4.2k', 'rating' => '4.8', 'reviews' => 120, 'stock' => 400, 'badge' => 'text-orange-600 bg-orange-50', 'icon' => 'fa-bandage', 
        'img' => 'https://d2qjkwm11akmwu.cloudfront.net/products/178312_2-1-2025_13-8-47.webp',
        'description' => 'Hansaplast Plester Kain merupakan plester luka dengan bantalan yang tidak lengket, membantu mencegah infeksi.',
        'indikasi' => 'Melindungi luka lecet, luka sayat dari kuman dan kotoran.',
        'komposisi' => 'Kain elastis berpori dengan bantalan penyerap.', 'dosis' => 'Gunakan sesuai kebutuhan. Ganti plester setiap hari.', 'aturan_pakai' => 'Bersihkan dan keringkan luka, lalu tempelkan plester.', 'golongan' => 'Alat Kesehatan', 'kemasan' => 'Amplop @ 10 Lembar', 'manufaktur' => 'Beiersdorf Indonesia', 'no_registrasi' => 'KEMENKES RI AKD 10902810051', 'kategori_kehamilan' => 'Aman.', 'perhatian' => 'Pastikan kulit kering sebelum ditempel.', 'kontraindikasi' => 'Hipersensitivitas bahan perekat.', 'efek_samping' => 'Kemerahan pada kulit sensitif.'
    ],
    [
        'id' => 8, 'name' => 'Tolak Angin Cair SidoMuncul - 5 Sachet', 'category' => 'Obat Bebas', 'price' => 18000, 'sold' => '8k+', 'rating' => '4.9', 'reviews' => 3100, 'stock' => 150, 'badge' => 'text-blue-600 bg-blue-50', 'icon' => 'fa-flask', 
        'img' => 'https://cdn.ruparupa.io/fit-in/400x400/filters:format(webp)/filters:quality(90)/ruparupa-com/image/upload/Products/10443596_4.jpg',
        'description' => 'Tolak Angin diformulasikan untuk mengatasi masuk angin dengan gejala mual, perut kembung, dan pusing.',
        'indikasi' => 'Meredakan masuk angin, mual, kembung, dan pusing.',
        'komposisi' => 'Madu, Ekstrak Jahe, Daun Mint, Cengkeh, Kayu Ules.', 'dosis' => '1 sachet, 3-4 kali sehari saat sakit.', 'aturan_pakai' => 'Dapat diminum langsung atau dicampur teh hangat.', 'golongan' => 'Obat Herbal', 'kemasan' => 'Dus, 5 Sachet @ 15 ml', 'manufaktur' => 'Sido Muncul', 'no_registrasi' => 'BPOM: HT122600301', 'kategori_kehamilan' => 'Tidak dianjurkan.', 'perhatian' => 'Tidak disarankan bagi yang alergi madu.', 'kontraindikasi' => 'Tidak ada.', 'efek_samping' => 'Jarang terjadi.'
    ],
    [
        'id' => 9, 'name' => 'Mylanta Sirup 50ml - Obat Maag', 'category' => 'Obat Bebas', 'price' => 17500, 'sold' => '12k+', 'rating' => '4.8', 'reviews' => 5420, 'stock' => 100, 'badge' => 'text-blue-600 bg-blue-50', 'icon' => 'fa-flask', 
        'img' => 'https://d2qjkwm11akmwu.cloudfront.net/products/854811_29-5-2022_21-2-6-1665780051.webp',
        'description' => 'Mylanta Sirup digunakan untuk meredakan gejala asam lambung berlebih, gastritis, tukak lambung, dengan gejala seperti mual, nyeri lambung, nyeri ulu hati.',
        'indikasi' => 'Meredakan gejala hiperasiditas lambung.',
        'komposisi' => 'Aluminium Hydroxide, Magnesium Hydroxide, Simethicone.', 'dosis' => 'Dewasa: 1-2 sendok takar (5-10 ml) 3-4 kali sehari.', 'aturan_pakai' => 'Diminum 1 jam sebelum makan atau 2 jam setelah makan.', 'golongan' => 'Obat Bebas', 'kemasan' => 'Botol @ 50 ml', 'manufaktur' => 'Johnson & Johnson', 'no_registrasi' => 'BPOM: DBL1441200233A1', 'kategori_kehamilan' => 'Kategori C.', 'perhatian' => 'Tidak dianjurkan digunakan terus menerus lebih dari 2 minggu.', 'kontraindikasi' => 'Penderita gangguan fungsi ginjal berat.', 'efek_samping' => 'Sembelit atau diare.'
    ],
    [
        'id' => 10, 'name' => 'Omron Termometer Digital MC-246', 'category' => 'Alat Kesehatan', 'price' => 45000, 'sold' => '3.5k', 'rating' => '4.9', 'reviews' => 1100, 'stock' => 30, 'badge' => 'text-purple-600 bg-purple-50', 'icon' => 'fa-temperature-half', 
        'img' => 'https://d2qjkwm11akmwu.cloudfront.net/products/502601_26-7-2024_13-58-0.webp',
        'description' => 'Termometer digital Omron MC-246 memberikan pembacaan suhu yang cepat, aman dan akurat.',
        'indikasi' => 'Mengukur suhu tubuh melalui mulut, rektal, atau ketiak.',
        'komposisi' => 'Alat ukur digital anti air.', 'dosis' => '-', 'aturan_pakai' => 'Nyalakan alat, letakkan pada area pengukuran hingga terdengar bunyi bip.', 'golongan' => 'Alat Kesehatan', 'kemasan' => 'Box, 1 Unit Termometer', 'manufaktur' => 'Omron Healthcare', 'no_registrasi' => 'KEMENKES RI AKL 20901815124', 'kategori_kehamilan' => 'Aman.', 'perhatian' => 'Bersihkan ujung sensor sebelum dan sesudah digunakan.', 'kontraindikasi' => 'Tidak ada.', 'efek_samping' => '-'
    ],
    [
        'id' => 11, 'name' => 'Zwitsal Baby Bath Hair & Body 200ml', 'category' => 'Ibu & Bayi', 'price' => 28000, 'sold' => '5k+', 'rating' => '4.9', 'reviews' => 2300, 'stock' => 80, 'badge' => 'text-pink-600 bg-pink-50', 'icon' => 'fa-baby', 
        'img' => 'https://down-id.img.susercontent.com/file/id-11134207-7r98q-lngv4psvmha9c1',
        'description' => 'Sabun cair dan shampoo yang dikemas menjadi satu produk yang praktis. Mengandung Aloe Vera dan Chamomile yang baik untuk kulit bayi.',
        'indikasi' => 'Membersihkan rambut dan tubuh bayi dengan lembut.',
        'komposisi' => 'Aloe Vera, Pro-Vitamin B5, Chamomile.', 'dosis' => 'Sesuai kebutuhan saat mandi.', 'aturan_pakai' => 'Tuangkan pada tangan atau spons, usapkan ke seluruh badan dan rambut bayi, lalu bilas.', 'golongan' => 'Perawatan Bayi', 'kemasan' => 'Botol @ 200 ml', 'manufaktur' => 'Unilever Indonesia', 'no_registrasi' => 'BPOM: NA18181002598', 'kategori_kehamilan' => 'Aman.', 'perhatian' => 'Hanya untuk pemakaian luar.', 'kontraindikasi' => 'Tidak ada.', 'efek_samping' => 'Tidak perih di mata.'
    ],
    [
        'id' => 12, 'name' => 'Sanmol Sirup Paracetamol 60ml', 'category' => 'Obat Bebas', 'price' => 16000, 'sold' => '15k+', 'rating' => '4.9', 'reviews' => 8700, 'stock' => 140, 'badge' => 'text-blue-600 bg-blue-50', 'icon' => 'fa-flask', 
        'img' => 'https://storage.googleapis.com/rxstorage/Product/Photos/farmaku_sanmol-syrup-60-ml-01.jpg',
        'description' => 'Sanmol Sirup mengandung Paracetamol yang digunakan untuk meringankan rasa sakit dan menurunkan demam pada anak.',
        'indikasi' => 'Meredakan nyeri seperti sakit kepala, sakit gigi, dan menurunkan demam.',
        'komposisi' => 'Tiap 5 ml mengandung Paracetamol 120 mg.', 'dosis' => 'Anak 1-2 th: 1 sendok takar (5 ml) 3-4x sehari.', 'aturan_pakai' => 'Diminum sebelum atau sesudah makan.', 'golongan' => 'Obat Bebas', 'kemasan' => 'Botol Kaca @ 60 ml', 'manufaktur' => 'Sanbe Farma', 'no_registrasi' => 'BPOM: DBL7622203537A1', 'kategori_kehamilan' => 'Aman.', 'perhatian' => 'Hati-hati penggunaan pada penderita gangguan ginjal/hati.', 'kontraindikasi' => 'Hipersensitivitas Paracetamol.', 'efek_samping' => 'Penggunaan jangka panjang bisa merusak hati.'
    ],
    [
        'id' => 13, 'name' => 'Ventolin Inhaler 100mcg', 'category' => 'Obat Keras (Resep)', 'price' => 135000, 'sold' => '4k+', 'rating' => '4.8', 'reviews' => 1500, 'stock' => 20, 'badge' => 'text-red-600 bg-red-50', 'icon' => 'fa-spray-can', 
        'img' => 'https://d2qjkwm11akmwu.cloudfront.net/products/1896-1665761131.webp',
        'description' => 'Ventolin Inhaler mengandung Salbutamol yang bekerja untuk meredakan bronkospasme pada asma. HARUS DENGAN RESEP DOKTER.',
        'indikasi' => 'Pereda cepat asma dan kondisi bronkospasme.',
        'komposisi' => 'Salbutamol sulfate 100 mcg per hisapan.', 'dosis' => 'Dewasa: 1-2 hisapan sebagai dosis tunggal saat serangan.', 'aturan_pakai' => 'Dihisap melalui mulut.', 'golongan' => 'Obat Keras', 'kemasan' => 'Canister @ 200 Dosis', 'manufaktur' => 'GlaxoSmithKline', 'no_registrasi' => 'BPOM: DKI1132000239A1', 'kategori_kehamilan' => 'Kategori C.', 'perhatian' => 'Hati-hati pada pasien gangguan jantung dan hipertensi.', 'kontraindikasi' => 'Hipersensitif Salbutamol.', 'efek_samping' => 'Tremor halus, jantung berdebar, sakit kepala.'
    ],
    [
        'id' => 14, 'name' => 'Counterpain Cream 30gr', 'category' => 'Obat Bebas', 'price' => 45000, 'sold' => '9k+', 'rating' => '4.8', 'reviews' => 4100, 'stock' => 90, 'badge' => 'text-blue-600 bg-blue-50', 'icon' => 'fa-tube', 
        'img' => 'https://d2qjkwm11akmwu.cloudfront.net/products/811461_25-8-2021_17-3-59-1665779688.webp',
        'description' => 'Counterpain adalah krim pereda nyeri untuk meringankan rasa sakit pada otot, sendi, keseleo, dan encok.',
        'indikasi' => 'Nyeri otot, nyeri sendi yang berhubungan dengan tertarik atau robeknya ligamen otot, memar.',
        'komposisi' => 'Methyl Salicylate, Eugenol, Menthol.', 'dosis' => 'Dioleskan 1-3 kali sehari.', 'aturan_pakai' => 'Oleskan pada bagian yang sakit lalu gosok secara merata.', 'golongan' => 'Obat Bebas', 'kemasan' => 'Tube @ 30 gr', 'manufaktur' => 'Taisho Pharmaceutical', 'no_registrasi' => 'BPOM: QD111709511', 'kategori_kehamilan' => 'Aman untuk luar.', 'perhatian' => 'Hanya untuk pemakaian luar, hindari area mata dan luka terbuka.', 'kontraindikasi' => 'Anak di bawah 2 tahun.', 'efek_samping' => 'Iritasi kulit lokal.'
    ],
    [
        'id' => 15, 'name' => 'Minyak Kayu Putih Cap Lang 60ml', 'category' => 'P3K', 'price' => 22000, 'sold' => '20k+', 'rating' => '4.9', 'reviews' => 12500, 'stock' => 500, 'badge' => 'text-orange-600 bg-orange-50', 'icon' => 'fa-bottle-droplet', 
        'img' => 'https://d2qjkwm11akmwu.cloudfront.net/products/432687_5-7-2021_10-21-56-1665780059.webp',
        'description' => 'Minyak Kayu Putih Cap Lang membantu meredakan perut kembung, mual, masuk angin, sakit perut, dan gatal akibat gigitan serangga.',
        'indikasi' => 'Meredakan masuk angin, perut kembung, gatal digigit serangga.',
        'komposisi' => 'Minyak Kayu Putih 100%.', 'dosis' => 'Sesuai kebutuhan.', 'aturan_pakai' => 'Oleskan secukupnya pada bagian yang membutuhkan.', 'golongan' => 'Obat Tradisional', 'kemasan' => 'Botol @ 60 ml', 'manufaktur' => 'Eagle Indo Pharma', 'no_registrasi' => 'BPOM: TR142679841', 'kategori_kehamilan' => 'Aman.', 'perhatian' => 'Jangan diminum. Hindari area sensitif.', 'kontraindikasi' => 'Hipersensitivitas terhadap cajuput oil.', 'efek_samping' => 'Ruam kulit pada kulit yang teramat sensitif.'
    ]
];

// Ambil ID dari URL, default ke 1
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 1;

$product = null;
foreach ($allProducts as $item) {
    if ($item['id'] === $productId) {
        $product = $item;
        break;
    }
}
if (!$product) $product = $allProducts[0];
?>

<div class="bg-slate-50 min-h-screen py-8" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <nav class="flex text-sm font-medium text-slate-500 mb-8 gap-2 items-center">
            <a href="<?= BASE_URL ?>/pages/dashboard.php" class="hover:text-primary smooth-transition no-underline text-slate-500">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <a href="<?= BASE_URL ?>/pages/marketplace.php" class="hover:text-primary smooth-transition no-underline text-slate-500">Obat & Vitamin</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-slate-800 font-bold truncate max-w-[200px] sm:max-w-none"><?= $product['name'] ?></span>
        </nav>

        <div class="flex flex-col lg:flex-row gap-10 items-start">
            
            <div class="w-full lg:w-1/3 flex flex-col gap-6 lg:sticky lg:top-28">
                
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 flex items-center justify-center relative aspect-square overflow-hidden group">
                    <div class="absolute inset-0 bg-slate-50/50 rounded-3xl m-2"></div>
                    <?php if (!empty($product['img'])): ?>
                        <img src="<?= $product['img'] ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="object-contain mix-blend-multiply h-full w-full relative z-10 group-hover:scale-110 smooth-transition cursor-pointer" title="Zoom Gambar">
                    <?php else: ?>
                        <i class="fa-solid <?= $product['icon'] ?> text-[140px] text-slate-300 relative z-10 hover:scale-110 smooth-transition cursor-pointer" title="Zoom Gambar"></i>
                    <?php endif; ?>
                    
                    <span class="absolute top-6 left-6 z-20 text-[10px] font-extrabold uppercase tracking-wider px-3 py-1.5 rounded-full bg-white text-slate-700 shadow-sm border border-slate-200">
                        <i class="fa-solid fa-shield-halved text-primary mr-1"></i> Asli
                    </span>
                </div>

                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 leading-snug mb-3">
                        <?= $product['name'] ?>
                    </h1>
                    
                    <div class="text-sm font-semibold text-slate-500 mb-2 border-b border-slate-200 pb-4">
                        Harga berbeda di tiap apotek
                    </div>
                    
                    <div class="text-3xl font-extrabold text-slate-900 tracking-tight">
                        Rp <span id="display-price"><?= number_format($product['price'], 0, ',', '.') ?></span>
                    </div>
                    <div class="text-xs font-semibold text-slate-400 mt-1">Per <?= explode('@', $product['kemasan'])[0] ?? 'Kemasan' ?></div>
                </div>

                <div class="flex flex-col gap-4 mt-2">
                    <div class="flex items-center gap-4">
                        <span class="text-sm font-bold text-slate-700">Jumlah:</span>
                        <div class="flex items-center bg-white border border-slate-200 rounded-xl p-1 shadow-sm w-fit">
                            <button onclick="updateQty(-1)" class="w-8 h-8 flex items-center justify-center text-slate-500 hover:text-primary hover:bg-slate-50 rounded-lg transition-colors border-none cursor-pointer bg-transparent">
                                <i class="fa-solid fa-minus"></i>
                            </button>
                            <input type="number" id="product-qty" value="1" min="1" max="<?= $product['stock'] ?>" class="w-12 text-center bg-transparent font-bold text-slate-800 outline-none border-none text-sm" onchange="manualQtyChange(this)">
                            <button onclick="updateQty(1)" class="w-8 h-8 flex items-center justify-center text-slate-500 hover:text-primary hover:bg-slate-50 rounded-lg transition-colors border-none cursor-pointer bg-transparent">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <button onclick="addToCart()" class="w-full bg-primary text-white border-none hover:bg-blue-800 py-3.5 rounded-xl font-bold shadow-md shadow-blue-200 active:scale-95 smooth-transition flex items-center justify-center gap-2 cursor-pointer text-[15px]">
                        Tambah ke Keranjang
                    </button>
                </div>
            </div>

            <div class="w-full lg:w-2/3 flex flex-col gap-6">
                
                <div class="bg-[#F0FAFA] border border-[#A6E1D8] rounded-2xl p-6">
                    <h4 class="font-extrabold text-slate-800 text-[15px] mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-notes-medical text-primary"></i> Deskripsi & Manfaat
                    </h4>
                    <p class="text-slate-700 text-[13px] font-medium leading-relaxed m-0 text-justify">
                        <?= $product['description'] ?>
                    </p>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <h4 class="font-extrabold text-slate-800 text-[15px] mb-5 flex items-center gap-2">
                        <i class="fa-solid fa-prescription-bottle text-primary"></i> Dosis & Aturan Pakai
                    </h4>
                    <div class="flex flex-col gap-5">
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Dosis</span>
                            <p class="text-slate-700 text-[13px] font-semibold m-0 leading-relaxed"><?= $product['dosis'] ?></p>
                        </div>
                        <hr class="border-slate-100 border-dashed">
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Aturan Pakai</span>
                            <p class="text-slate-700 text-[13px] font-semibold m-0 leading-relaxed"><?= $product['aturan_pakai'] ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <h4 class="font-extrabold text-slate-800 text-[15px] mb-5 flex items-center gap-2">
                        <i class="fa-solid fa-shield-virus text-primary"></i> Informasi Keamanan
                    </h4>
                    <div class="flex flex-col gap-5">
                        <div class="flex gap-4 items-start">
                            <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center flex-shrink-0 border border-slate-100">
                                <i class="fa-solid fa-person-pregnant text-slate-400 text-sm"></i>
                            </div>
                            <div>
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Kategori Kehamilan</span>
                                <p class="text-slate-700 text-[13px] font-semibold m-0 leading-relaxed text-justify">
                                    <?= $product['kategori_kehamilan'] ?>
                                </p>
                            </div>
                        </div>
                        <hr class="border-slate-100 border-dashed">
                        <div class="flex gap-4 items-start">
                            <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center flex-shrink-0 border border-slate-100">
                                <i class="fa-solid fa-triangle-exclamation text-slate-400 text-sm"></i>
                            </div>
                            <div>
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Perhatian Khusus</span>
                                <p class="text-slate-700 text-[13px] font-semibold m-0 leading-relaxed text-justify"><?= $product['perhatian'] ?></p>
                            </div>
                        </div>
                        <hr class="border-slate-100 border-dashed">
                        <div class="flex gap-4 items-start">
                            <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center flex-shrink-0 border border-slate-100">
                                <i class="fa-solid fa-ban text-slate-400 text-sm"></i>
                            </div>
                            <div>
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Kontraindikasi</span>
                                <p class="text-slate-700 text-[13px] font-semibold m-0 leading-relaxed text-justify"><?= $product['kontraindikasi'] ?></p>
                            </div>
                        </div>
                        <hr class="border-slate-100 border-dashed">
                        <div class="flex gap-4 items-start">
                            <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center flex-shrink-0 border border-slate-100">
                                <i class="fa-solid fa-head-side-cough text-slate-400 text-sm"></i>
                            </div>
                            <div>
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Efek Samping</span>
                                <p class="text-slate-700 text-[13px] font-semibold m-0 leading-relaxed text-justify"><?= $product['efek_samping'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <h4 class="font-extrabold text-slate-800 text-[15px] mb-5 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-primary"></i> Detail Produk
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Indikasi Umum</span>
                            <p class="text-slate-700 text-[13px] font-semibold m-0"><?= $product['indikasi'] ?></p>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Komposisi</span>
                            <p class="text-slate-700 text-[13px] font-semibold m-0"><?= $product['komposisi'] ?></p>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Golongan Produk</span>
                            <p class="text-slate-700 text-[13px] font-semibold m-0"><?= $product['golongan'] ?></p>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Kemasan</span>
                            <p class="text-slate-700 text-[13px] font-semibold m-0"><?= $product['kemasan'] ?></p>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Manufaktur</span>
                            <p class="text-slate-700 text-[13px] font-semibold m-0"><?= $product['manufaktur'] ?></p>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">No. Registrasi BPOM</span>
                            <p class="text-slate-700 text-[13px] font-bold m-0"><?= $product['no_registrasi'] ?></p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<div id="toast-stack" style="position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:10px"></div>

<script>
    const basePrice = <?= $product['price'] ?>;
    const maxStock = <?= $product['stock'] ?>;
    const qtyInput = document.getElementById('product-qty');
    const priceDisplay = document.getElementById('display-price');

    // Komponen Toast Notification
    function showToast(msg,type="info",dur=3200){
        const colors={info:"#1D4ED8",success:"#10B981",error:"#EF4444",warning:"#F59E0B"};
        const icons={info:"fa-circle-info",success:"fa-circle-check",error:"fa-circle-xmark",warning:"fa-triangle-exclamation"};
        const iconBgs={info:"#EFF6FF",success:"#ECFDF5",error:"#FEF2F2",warning:"#FFFBEB"};
        const s=document.getElementById("toast-stack");
        const t=document.createElement("div");
        t.style.cssText=`display:flex;align-items:center;gap:12px;padding:14px 18px;border-radius:16px;background:#fff;box-shadow:0 8px 30px -4px rgba(0,0,0,0.15);min-width:280px;font-size:14px;font-weight:600;color:#0F172A;border-left:4px solid ${colors[type]};animation:slideIn .3s ease`;
        t.innerHTML=`<div style="width:34px;height:34px;border-radius:10px;background:${iconBgs[type]};color:${colors[type]};display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fa-solid ${icons[type]}"></i></div><div style="flex:1">${msg}</div><button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:18px;padding:0;line-height:1">×</button>`;
        s.appendChild(t);
        setTimeout(()=>{t.style.transition="all .3s";t.style.opacity="0";t.style.transform="translateX(20px)";setTimeout(()=>t.remove(),300);},dur);
    }

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka);
    }

    function updatePrice() {
        const qty = parseInt(qtyInput.value) || 1;
        priceDisplay.textContent = formatRupiah(basePrice * qty);
    }

    function updateQty(change) {
        let currentQty = parseInt(qtyInput.value) || 1;
        let newQty = currentQty + change;
        
        if (newQty >= 1 && newQty <= maxStock) {
            qtyInput.value = newQty;
            updatePrice();
        } else if (newQty > maxStock) {
            showToast('Maksimal pembelian ' + maxStock + ' item.', 'warning');
        }
    }

    function manualQtyChange(input) {
        let val = parseInt(input.value);
        if (isNaN(val) || val < 1) input.value = 1;
        if (val > maxStock) {
            input.value = maxStock;
            showToast('Maksimal pembelian ' + maxStock + ' item.', 'warning');
        }
        updatePrice();
    }

    // Fungsi Add to Cart terintegrasi dengan Navbar Header
    function addToCart() {
        const qty = qtyInput.value;
        const productName = "<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>";
        
        // Simpan ke LocalStorage agar badge navbar bertambah
        let cartCount = parseInt(localStorage.getItem("em_cart") || "0");
        cartCount += parseInt(qty);
        localStorage.setItem("em_cart", cartCount);
        
        // Update Badge di Header secara realtime
        const cartBadge = document.getElementById('cart-count');
        if(cartBadge) {
            cartBadge.classList.remove('hidden');
            cartBadge.textContent = cartCount;
            cartBadge.classList.add('scale-150');
            setTimeout(() => cartBadge.classList.remove('scale-150'), 200);
        }
        
        showToast(`<b>${qty}x ${productName}</b> berhasil ditambahkan ke keranjang!`, "success");
    }
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>