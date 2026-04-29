<?php
require_once __DIR__ . '/booking_helpers.php';

function getMarketplaceProductCatalog(): array
{
    return [
        [
            'id' => 1,
            'name' => 'Blackmores Vitamin C 500mg - 60 Tablet',
            'category' => 'Vitamin & Suplemen',
            'price' => 120000,
            'sold' => '1.2k',
            'rating' => '4.9',
            'reviews' => 342,
            'stock' => 45,
            'badge' => 'text-emerald-600 bg-emerald-50',
            'icon' => 'fa-bottle-droplet',
            'weight' => 200,
            'requires_prescription' => false,
            'img' => 'https://blackmores-bucket.s3.ap-southeast-1.amazonaws.com/blackmores/product/images667bb9448c2ff.png',
            'description' => 'Blackmores Vitamin C 500mg adalah suplemen kesehatan yang mengandung Vitamin C untuk membantu memelihara daya tahan tubuh. Suplemen ini sangat baik dikonsumsi untuk mencegah flu, mempercepat penyembuhan luka, serta bertindak sebagai antioksidan.',
            'indikasi' => 'Membantu memelihara daya tahan tubuh, mencegah defisiensi vitamin C.',
            'komposisi' => 'Asam Askorbat (Vitamin C) 500 mg.',
            'dosis' => 'Dewasa: 1 tablet sehari.',
            'aturan_pakai' => 'Dikonsumsi sesudah makan.',
            'golongan' => 'Suplemen Kesehatan',
            'kemasan' => 'Botol Kaca @ 60 tablet',
            'manufaktur' => 'Kalbe Blackmores Nutrition',
            'no_registrasi' => 'BPOM: SI164507251',
            'kategori_kehamilan' => 'Kategori A: Aman untuk ibu hamil dan janin.',
            'perhatian' => 'Hati-hati pada penderita gangguan ginjal.',
            'kontraindikasi' => 'Hipersensitivitas terhadap Vitamin C.',
            'efek_samping' => 'Dosis berlebih dapat menyebabkan gangguan pencernaan.',
        ],
        [
            'id' => 2,
            'name' => 'Panadol Extra Paracetamol 10 Kaplet',
            'category' => 'Obat Bebas',
            'price' => 15000,
            'sold' => '5.5k',
            'rating' => '4.8',
            'reviews' => 1250,
            'stock' => 120,
            'badge' => 'text-blue-600 bg-blue-50',
            'icon' => 'fa-tablets',
            'weight' => 50,
            'requires_prescription' => false,
            'img' => 'https://d2qjkwm11akmwu.cloudfront.net/products/807265_19-11-2024_13-49-18.webp',
            'description' => 'Panadol Extra adalah obat dengan kandungan Paracetamol dan Caffeine. Obat ini digunakan untuk meredakan sakit kepala, sakit gigi, sakit pada otot, serta menurunkan demam.',
            'indikasi' => 'Meredakan sakit kepala, sakit gigi, nyeri otot, dan menurunkan demam.',
            'komposisi' => 'Paracetamol 500 mg, Caffeine 65 mg.',
            'dosis' => 'Dewasa & Anak > 12 th: 1 kaplet, 3-4 kali sehari. Maksimal 8 kaplet/hari.',
            'aturan_pakai' => 'Dapat dikonsumsi sebelum atau sesudah makan.',
            'golongan' => 'Obat Bebas',
            'kemasan' => 'Blister @ 10 Kaplet',
            'manufaktur' => 'GlaxoSmithKline',
            'no_registrasi' => 'BPOM: DBL0403810104A1',
            'kategori_kehamilan' => 'Kategori B: Studi pada reproduksi hewan tidak menunjukkan risiko janin.',
            'perhatian' => 'Penggunaan jangka panjang dapat menyebabkan kerusakan hati.',
            'kontraindikasi' => 'Penderita dengan gangguan fungsi hati berat.',
            'efek_samping' => 'Jarang terjadi: ruam kulit, reaksi alergi.',
        ],
        [
            'id' => 3,
            'name' => 'Sensi Masker Medis 3-Ply - Isi 50 Pcs',
            'category' => 'Alat Kesehatan',
            'price' => 35000,
            'sold' => '10k+',
            'rating' => '4.9',
            'reviews' => 4500,
            'stock' => 300,
            'badge' => 'text-purple-600 bg-purple-50',
            'icon' => 'fa-mask-face',
            'weight' => 300,
            'requires_prescription' => false,
            'img' => 'https://doktersehat.com/wp-content/uploads/2020/03/obat_dan_vitamin_Doktersehat_com_Masker_Sensi_Earloop_3_Ply_-_Hijau_50S.jpg',
            'description' => 'Sensi Masker Medis 3-Ply adalah masker pelindung wajah dengan 3 lapisan penyaring untuk melindungi saluran pernapasan dari debu, asap, kuman, cairan, dan partikel mikro lainnya.',
            'indikasi' => 'Melindungi saluran pernapasan dari partikel udara, kuman, dan cairan.',
            'komposisi' => 'Non-woven fabric, Meltblown filter, Earloop elastis.',
            'dosis' => 'Digunakan sesuai kebutuhan. Segera ganti jika kotor atau basah.',
            'aturan_pakai' => 'Kaitkan tali pada telinga, tekan kawat bagian hidung, dan tarik bagian bawah hingga menutupi dagu.',
            'golongan' => 'Alat Kesehatan',
            'kemasan' => 'Box @ 50 Pcs',
            'manufaktur' => 'Arista Latindo',
            'no_registrasi' => 'KEMENKES RI AKD 11603010092',
            'kategori_kehamilan' => 'Aman untuk semua kalangan.',
            'perhatian' => 'Masker sekali pakai, tidak untuk dicuci ulang.',
            'kontraindikasi' => 'Tidak ada.',
            'efek_samping' => 'Penggunaan terlalu ketat mungkin menyebabkan iritasi ringan.',
        ],
        [
            'id' => 4,
            'name' => 'Betadine Antiseptic Solution 60ml',
            'category' => 'P3K',
            'price' => 45000,
            'sold' => '850',
            'rating' => '4.7',
            'reviews' => 210,
            'stock' => 55,
            'badge' => 'text-orange-600 bg-orange-50',
            'icon' => 'fa-prescription-bottle-medical',
            'weight' => 120,
            'requires_prescription' => false,
            'img' => 'https://guardianindonesia.co.id/media/catalog/product/0/0648432~1_20250723114509_4781.png?format=png&auto=webp&width=840&height=375&fit=cover',
            'description' => 'Betadine Antiseptic Solution merupakan antiseptik pada luka untuk membunuh kuman penyebab infeksi. Mengandung Povidone-Iodine 10%.',
            'indikasi' => 'Antiseptik untuk mencegah infeksi pada luka lecet, luka sayat, luka bakar ringan.',
            'komposisi' => 'Povidone-Iodine 10%',
            'dosis' => 'Dioleskan sesuai kebutuhan pada bagian yang terluka.',
            'aturan_pakai' => 'Bersihkan luka terlebih dahulu, lalu oleskan.',
            'golongan' => 'Obat Bebas Terbatas',
            'kemasan' => 'Botol Plastik @ 60 ml',
            'manufaktur' => 'Mundipharma Healthcare',
            'no_registrasi' => 'BPOM: DTL1613711941B1',
            'kategori_kehamilan' => 'Kategori D: Ada bukti risiko pada janin.',
            'perhatian' => 'Hanya untuk pemakaian luar.',
            'kontraindikasi' => 'Hipersensitivitas terhadap iodium.',
            'efek_samping' => 'Iritasi lokal pada area yang diolesi.',
        ],
        [
            'id' => 5,
            'name' => 'Amoxicillin 500mg - 10 Kaplet',
            'category' => 'Obat Keras (Resep)',
            'price' => 12000,
            'sold' => '3k+',
            'rating' => '4.8',
            'reviews' => 890,
            'stock' => 200,
            'badge' => 'text-red-600 bg-red-50',
            'icon' => 'fa-capsules',
            'weight' => 60,
            'requires_prescription' => true,
            'img' => 'https://images.alodokter.com/dk0z4ums3/image/upload/c_scale,h_500,w_500/v1/production/pharmacy/products/1687342946_amox_500_berno-seles',
            'description' => 'Amoxicillin adalah antibiotik penisilin spektrum luas untuk mengobati infeksi bakteri. HARUS DENGAN RESEP DOKTER.',
            'indikasi' => 'Mengobati infeksi bakteri pada saluran pernapasan, kulit, dan saluran kemih.',
            'komposisi' => 'Amoxicillin Trihydrate 500 mg',
            'dosis' => 'Sesuai resep dokter. Biasanya: Dewasa 250-500 mg tiap 8 jam.',
            'aturan_pakai' => 'Dikonsumsi sesudah makan. Obat harus dihabiskan.',
            'golongan' => 'Obat Keras',
            'kemasan' => 'Strip @ 10 Kaplet',
            'manufaktur' => 'Generik',
            'no_registrasi' => 'BPOM: GKL9810023410A1',
            'kategori_kehamilan' => 'Kategori B: Aman.',
            'perhatian' => 'Harus dihabiskan untuk mencegah resistensi antibiotik.',
            'kontraindikasi' => 'Hipersensitif terhadap Penisilin.',
            'efek_samping' => 'Mual, muntah, diare, ruam kulit.',
        ],
        [
            'id' => 6,
            'name' => 'Imboost Force - 10 Kaplet',
            'category' => 'Vitamin & Suplemen',
            'price' => 75000,
            'sold' => '2.1k',
            'rating' => '4.9',
            'reviews' => 600,
            'stock' => 85,
            'badge' => 'text-emerald-600 bg-emerald-50',
            'icon' => 'fa-leaf',
            'weight' => 80,
            'requires_prescription' => false,
            'img' => 'https://images.alodokter.com/dk0z4ums3/image/upload/c_scale,h_500,w_500/v1/production/pharmacy/products/1659930367_629d9f10f15ee8089e029434',
            'description' => 'Imboost Force adalah suplemen kesehatan untuk meningkatkan sistem kekebalan tubuh.',
            'indikasi' => 'Membantu memelihara dan meningkatkan daya tahan tubuh.',
            'komposisi' => 'Echinacea purpurea 250 mg, Black elderberry 400 mg, Zn picolinate 10 mg.',
            'dosis' => 'Dewasa: 1 kaplet, 1-3 kali sehari.',
            'aturan_pakai' => 'Dikonsumsi sesudah makan.',
            'golongan' => 'Suplemen Kesehatan',
            'kemasan' => 'Strip @ 10 Kaplet',
            'manufaktur' => 'Soho Industri Pharmasi',
            'no_registrasi' => 'BPOM: SD021503871',
            'kategori_kehamilan' => 'Tidak direkomendasikan lebih dari 8 minggu.',
            'perhatian' => 'Hentikan pemakaian jika terjadi alergi.',
            'kontraindikasi' => 'Penyakit autoimun.',
            'efek_samping' => 'Gangguan perut ringan.',
        ],
        [
            'id' => 7,
            'name' => 'Hansaplast Plester Kain - Isi 10',
            'category' => 'P3K',
            'price' => 8500,
            'sold' => '4.2k',
            'rating' => '4.8',
            'reviews' => 120,
            'stock' => 400,
            'badge' => 'text-orange-600 bg-orange-50',
            'icon' => 'fa-bandage',
            'weight' => 30,
            'requires_prescription' => false,
            'img' => 'https://d2qjkwm11akmwu.cloudfront.net/products/178312_2-1-2025_13-8-47.webp',
            'description' => 'Hansaplast Plester Kain merupakan plester luka dengan bantalan yang tidak lengket, membantu mencegah infeksi.',
            'indikasi' => 'Melindungi luka lecet, luka sayat dari kuman dan kotoran.',
            'komposisi' => 'Kain elastis berpori dengan bantalan penyerap.',
            'dosis' => 'Gunakan sesuai kebutuhan. Ganti plester setiap hari.',
            'aturan_pakai' => 'Bersihkan dan keringkan luka, lalu tempelkan plester.',
            'golongan' => 'Alat Kesehatan',
            'kemasan' => 'Amplop @ 10 Lembar',
            'manufaktur' => 'Beiersdorf Indonesia',
            'no_registrasi' => 'KEMENKES RI AKD 10902810051',
            'kategori_kehamilan' => 'Aman.',
            'perhatian' => 'Pastikan kulit kering sebelum ditempel.',
            'kontraindikasi' => 'Hipersensitivitas bahan perekat.',
            'efek_samping' => 'Kemerahan pada kulit sensitif.',
        ],
        [
            'id' => 8,
            'name' => 'Tolak Angin Cair SidoMuncul - 5 Sachet',
            'category' => 'Obat Bebas',
            'price' => 18000,
            'sold' => '8k+',
            'rating' => '4.9',
            'reviews' => 3100,
            'stock' => 150,
            'badge' => 'text-blue-600 bg-blue-50',
            'icon' => 'fa-flask',
            'weight' => 90,
            'requires_prescription' => false,
            'img' => 'https://cdn.ruparupa.io/fit-in/400x400/filters:format(webp)/filters:quality(90)/ruparupa-com/image/upload/Products/10443596_4.jpg',
            'description' => 'Tolak Angin diformulasikan untuk mengatasi masuk angin dengan gejala mual, perut kembung, dan pusing.',
            'indikasi' => 'Meredakan masuk angin, mual, kembung, dan pusing.',
            'komposisi' => 'Madu, Ekstrak Jahe, Daun Mint, Cengkeh, Kayu Ules.',
            'dosis' => '1 sachet, 3-4 kali sehari saat sakit.',
            'aturan_pakai' => 'Dapat diminum langsung atau dicampur teh hangat.',
            'golongan' => 'Obat Herbal',
            'kemasan' => 'Dus, 5 Sachet @ 15 ml',
            'manufaktur' => 'Sido Muncul',
            'no_registrasi' => 'BPOM: HT122600301',
            'kategori_kehamilan' => 'Tidak dianjurkan.',
            'perhatian' => 'Tidak disarankan bagi yang alergi madu.',
            'kontraindikasi' => 'Tidak ada.',
            'efek_samping' => 'Jarang terjadi.',
        ],
        [
            'id' => 9,
            'name' => 'Mylanta Sirup 50ml - Obat Maag',
            'category' => 'Obat Bebas',
            'price' => 17500,
            'sold' => '12k+',
            'rating' => '4.8',
            'reviews' => 5420,
            'stock' => 100,
            'badge' => 'text-blue-600 bg-blue-50',
            'icon' => 'fa-flask',
            'weight' => 110,
            'requires_prescription' => false,
            'img' => 'https://d2qjkwm11akmwu.cloudfront.net/products/854811_29-5-2022_21-2-6-1665780051.webp',
            'description' => 'Mylanta Sirup digunakan untuk meredakan gejala asam lambung berlebih, gastritis, tukak lambung, dengan gejala seperti mual, nyeri lambung, nyeri ulu hati.',
            'indikasi' => 'Meredakan gejala hiperasiditas lambung.',
            'komposisi' => 'Aluminium Hydroxide, Magnesium Hydroxide, Simethicone.',
            'dosis' => 'Dewasa: 1-2 sendok takar (5-10 ml) 3-4 kali sehari.',
            'aturan_pakai' => 'Diminum 1 jam sebelum makan atau 2 jam setelah makan.',
            'golongan' => 'Obat Bebas',
            'kemasan' => 'Botol @ 50 ml',
            'manufaktur' => 'Johnson & Johnson',
            'no_registrasi' => 'BPOM: DBL1441200233A1',
            'kategori_kehamilan' => 'Kategori C.',
            'perhatian' => 'Tidak dianjurkan digunakan terus menerus lebih dari 2 minggu.',
            'kontraindikasi' => 'Penderita gangguan fungsi ginjal berat.',
            'efek_samping' => 'Sembelit atau diare.',
        ],
        [
            'id' => 10,
            'name' => 'Omron Termometer Digital MC-246',
            'category' => 'Alat Kesehatan',
            'price' => 45000,
            'sold' => '3.5k',
            'rating' => '4.9',
            'reviews' => 1100,
            'stock' => 30,
            'badge' => 'text-purple-600 bg-purple-50',
            'icon' => 'fa-temperature-half',
            'weight' => 150,
            'requires_prescription' => false,
            'img' => 'https://d2qjkwm11akmwu.cloudfront.net/products/502601_26-7-2024_13-58-0.webp',
            'description' => 'Termometer digital Omron MC-246 memberikan pembacaan suhu yang cepat, aman dan akurat.',
            'indikasi' => 'Mengukur suhu tubuh melalui mulut, rektal, atau ketiak.',
            'komposisi' => 'Alat ukur digital anti air.',
            'dosis' => '-',
            'aturan_pakai' => 'Nyalakan alat, letakkan pada area pengukuran hingga terdengar bunyi bip.',
            'golongan' => 'Alat Kesehatan',
            'kemasan' => 'Box, 1 Unit Termometer',
            'manufaktur' => 'Omron Healthcare',
            'no_registrasi' => 'KEMENKES RI AKL 20901815124',
            'kategori_kehamilan' => 'Aman.',
            'perhatian' => 'Bersihkan ujung sensor sebelum dan sesudah digunakan.',
            'kontraindikasi' => 'Tidak ada.',
            'efek_samping' => '-',
        ],
        [
            'id' => 11,
            'name' => 'Zwitsal Baby Bath Hair & Body 200ml',
            'category' => 'Ibu & Bayi',
            'price' => 28000,
            'sold' => '5k+',
            'rating' => '4.9',
            'reviews' => 2300,
            'stock' => 80,
            'badge' => 'text-pink-600 bg-pink-50',
            'icon' => 'fa-baby',
            'weight' => 230,
            'requires_prescription' => false,
            'img' => 'https://down-id.img.susercontent.com/file/id-11134207-7r98q-lngv4psvmha9c1',
            'description' => 'Sabun cair dan shampoo yang dikemas menjadi satu produk yang praktis. Mengandung Aloe Vera dan Chamomile yang baik untuk kulit bayi.',
            'indikasi' => 'Membersihkan rambut dan tubuh bayi dengan lembut.',
            'komposisi' => 'Aloe Vera, Pro-Vitamin B5, Chamomile.',
            'dosis' => 'Sesuai kebutuhan saat mandi.',
            'aturan_pakai' => 'Tuangkan pada tangan atau spons, usapkan ke seluruh badan dan rambut bayi, lalu bilas.',
            'golongan' => 'Perawatan Bayi',
            'kemasan' => 'Botol @ 200 ml',
            'manufaktur' => 'Unilever Indonesia',
            'no_registrasi' => 'BPOM: NA18181002598',
            'kategori_kehamilan' => 'Aman.',
            'perhatian' => 'Hanya untuk pemakaian luar.',
            'kontraindikasi' => 'Tidak ada.',
            'efek_samping' => 'Tidak perih di mata.',
        ],
        [
            'id' => 12,
            'name' => 'Sanmol Sirup Paracetamol 60ml',
            'category' => 'Obat Bebas',
            'price' => 16000,
            'sold' => '15k+',
            'rating' => '4.9',
            'reviews' => 8700,
            'stock' => 140,
            'badge' => 'text-blue-600 bg-blue-50',
            'icon' => 'fa-flask',
            'weight' => 120,
            'requires_prescription' => false,
            'img' => 'https://storage.googleapis.com/rxstorage/Product/Photos/farmaku_sanmol-syrup-60-ml-01.jpg',
            'description' => 'Sanmol Sirup mengandung Paracetamol yang digunakan untuk meringankan rasa sakit dan menurunkan demam pada anak.',
            'indikasi' => 'Meredakan nyeri seperti sakit kepala, sakit gigi, dan menurunkan demam.',
            'komposisi' => 'Tiap 5 ml mengandung Paracetamol 120 mg.',
            'dosis' => 'Anak 1-2 th: 1 sendok takar (5 ml) 3-4x sehari.',
            'aturan_pakai' => 'Diminum sebelum atau sesudah makan.',
            'golongan' => 'Obat Bebas',
            'kemasan' => 'Botol Kaca @ 60 ml',
            'manufaktur' => 'Sanbe Farma',
            'no_registrasi' => 'BPOM: DBL7622203537A1',
            'kategori_kehamilan' => 'Aman.',
            'perhatian' => 'Hati-hati penggunaan pada penderita gangguan ginjal/hati.',
            'kontraindikasi' => 'Hipersensitivitas Paracetamol.',
            'efek_samping' => 'Penggunaan jangka panjang bisa merusak hati.',
        ],
        [
            'id' => 13,
            'name' => 'Ventolin Inhaler 100mcg',
            'category' => 'Obat Keras (Resep)',
            'price' => 135000,
            'sold' => '4k+',
            'rating' => '4.8',
            'reviews' => 1500,
            'stock' => 20,
            'badge' => 'text-red-600 bg-red-50',
            'icon' => 'fa-spray-can',
            'weight' => 90,
            'requires_prescription' => true,
            'img' => 'https://d2qjkwm11akmwu.cloudfront.net/products/1896-1665761131.webp',
            'description' => 'Ventolin Inhaler mengandung Salbutamol yang bekerja untuk meredakan bronkospasme pada asma. HARUS DENGAN RESEP DOKTER.',
            'indikasi' => 'Pereda cepat asma dan kondisi bronkospasme.',
            'komposisi' => 'Salbutamol sulfate 100 mcg per hisapan.',
            'dosis' => 'Dewasa: 1-2 hisapan sebagai dosis tunggal saat serangan.',
            'aturan_pakai' => 'Dihisap melalui mulut.',
            'golongan' => 'Obat Keras',
            'kemasan' => 'Canister @ 200 Dosis',
            'manufaktur' => 'GlaxoSmithKline',
            'no_registrasi' => 'BPOM: DKI1132000239A1',
            'kategori_kehamilan' => 'Kategori C.',
            'perhatian' => 'Hati-hati pada pasien gangguan jantung dan hipertensi.',
            'kontraindikasi' => 'Hipersensitif Salbutamol.',
            'efek_samping' => 'Tremor halus, jantung berdebar, sakit kepala.',
        ],
        [
            'id' => 14,
            'name' => 'Counterpain Cream 30gr',
            'category' => 'Obat Bebas',
            'price' => 45000,
            'sold' => '9k+',
            'rating' => '4.8',
            'reviews' => 4100,
            'stock' => 90,
            'badge' => 'text-blue-600 bg-blue-50',
            'icon' => 'fa-tube',
            'weight' => 70,
            'requires_prescription' => false,
            'img' => 'https://d2qjkwm11akmwu.cloudfront.net/products/811461_25-8-2021_17-3-59-1665779688.webp',
            'description' => 'Counterpain adalah krim pereda nyeri untuk meringankan rasa sakit pada otot, sendi, keseleo, dan encok.',
            'indikasi' => 'Nyeri otot, nyeri sendi yang berhubungan dengan tertarik atau robeknya ligamen otot, memar.',
            'komposisi' => 'Methyl Salicylate, Eugenol, Menthol.',
            'dosis' => 'Dioleskan 1-3 kali sehari.',
            'aturan_pakai' => 'Oleskan pada bagian yang sakit lalu gosok secara merata.',
            'golongan' => 'Obat Bebas',
            'kemasan' => 'Tube @ 30 gr',
            'manufaktur' => 'Taisho Pharmaceutical',
            'no_registrasi' => 'BPOM: QD111709511',
            'kategori_kehamilan' => 'Aman untuk luar.',
            'perhatian' => 'Hanya untuk pemakaian luar, hindari area mata dan luka terbuka.',
            'kontraindikasi' => 'Anak di bawah 2 tahun.',
            'efek_samping' => 'Iritasi kulit lokal.',
        ],
        [
            'id' => 15,
            'name' => 'Minyak Kayu Putih Cap Lang 60ml',
            'category' => 'P3K',
            'price' => 22000,
            'sold' => '20k+',
            'rating' => '4.9',
            'reviews' => 12500,
            'stock' => 500,
            'badge' => 'text-orange-600 bg-orange-50',
            'icon' => 'fa-bottle-droplet',
            'weight' => 110,
            'requires_prescription' => false,
            'img' => 'https://d2qjkwm11akmwu.cloudfront.net/products/432687_5-7-2021_10-21-56-1665780059.webp',
            'description' => 'Minyak Kayu Putih Cap Lang membantu meredakan perut kembung, mual, masuk angin, sakit perut, dan gatal akibat gigitan serangga.',
            'indikasi' => 'Meredakan masuk angin, perut kembung, gatal digigit serangga.',
            'komposisi' => 'Minyak Kayu Putih 100%.',
            'dosis' => 'Sesuai kebutuhan.',
            'aturan_pakai' => 'Oleskan secukupnya pada bagian yang membutuhkan.',
            'golongan' => 'Obat Tradisional',
            'kemasan' => 'Botol @ 60 ml',
            'manufaktur' => 'Eagle Indo Pharma',
            'no_registrasi' => 'BPOM: TR142679841',
            'kategori_kehamilan' => 'Aman.',
            'perhatian' => 'Jangan diminum. Hindari area sensitif.',
            'kontraindikasi' => 'Hipersensitivitas terhadap cajuput oil.',
            'efek_samping' => 'Ruam kulit pada kulit yang teramat sensitif.',
        ],
    ];
}

function getMarketplaceCategories(): array
{
    $categories = [
        ['name' => 'Semua Produk', 'icon' => 'fa-layer-group'],
    ];

    foreach (getMarketplaceProductCatalog() as $product) {
        $exists = false;
        foreach ($categories as $category) {
            if ($category['name'] === $product['category']) {
                $exists = true;
                break;
            }
        }

        if ($exists) {
            continue;
        }

        $icon = 'fa-pills';
        if ($product['category'] === 'Obat Keras (Resep)') {
            $icon = 'fa-file-signature';
        } elseif ($product['category'] === 'Vitamin & Suplemen') {
            $icon = 'fa-apple-whole';
        } elseif ($product['category'] === 'Alat Kesehatan') {
            $icon = 'fa-mask';
        } elseif ($product['category'] === 'P3K') {
            $icon = 'fa-kit-medical';
        } elseif ($product['category'] === 'Ibu & Bayi') {
            $icon = 'fa-baby';
        }

        $categories[] = [
            'name' => $product['category'],
            'icon' => $icon,
        ];
    }

    return $categories;
}

function ensureMarketplaceSchema(PDO $pdo): void
{
    static $checked = false;

    if ($checked) {
        return;
    }

    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    if ($driver === 'pgsql') {
        $stmt = $pdo->prepare(
            "SELECT data_type, character_maximum_length
             FROM information_schema.columns
             WHERE table_schema = 'public'
               AND table_name = 'obat'
               AND column_name = 'dosis'
             LIMIT 1"
        );
    } else {
        $stmt = $pdo->prepare(
            "SELECT DATA_TYPE AS data_type, CHARACTER_MAXIMUM_LENGTH AS character_maximum_length
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_NAME = 'Obat'
               AND COLUMN_NAME = 'dosis'
             LIMIT 1"
        );
    }

    $stmt->execute();
    $column = $stmt->fetch();

    if ($column) {
        $dataType = strtolower((string) ($column['data_type'] ?? ''));
        $maxLength = isset($column['character_maximum_length']) ? (int) $column['character_maximum_length'] : null;

        if ($dataType !== 'text' && $maxLength !== null && $maxLength < 150) {
            if ($driver === 'pgsql') {
                $pdo->exec('ALTER TABLE Obat ALTER COLUMN dosis TYPE TEXT');
            } else {
                $pdo->exec('ALTER TABLE Obat MODIFY dosis TEXT');
            }
        }
    }

    $checked = true;
}

function ensureMarketplacePatientSchema(PDO $pdo): void
{
    static $checked = false;

    if ($checked) {
        return;
    }

    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    if ($driver === 'pgsql') {
        $ddl = [
            "ALTER TABLE Pasien ADD COLUMN IF NOT EXISTS blood_type VARCHAR(5)",
            "ALTER TABLE Pasien ADD COLUMN IF NOT EXISTS weight VARCHAR(20)",
            "ALTER TABLE Pasien ADD COLUMN IF NOT EXISTS height VARCHAR(20)",
            "ALTER TABLE Pasien ADD COLUMN IF NOT EXISTS allergy_notes TEXT",
            "ALTER TABLE Pasien ADD COLUMN IF NOT EXISTS address_label VARCHAR(50)",
            "ALTER TABLE Pasien ADD COLUMN IF NOT EXISTS recipient_name VARCHAR(100)",
            "ALTER TABLE Pasien ADD COLUMN IF NOT EXISTS village VARCHAR(100)",
            "ALTER TABLE Pasien ADD COLUMN IF NOT EXISTS district VARCHAR(100)",
            "ALTER TABLE Pasien ADD COLUMN IF NOT EXISTS city VARCHAR(100)",
            "ALTER TABLE Pasien ADD COLUMN IF NOT EXISTS province VARCHAR(100)",
            "ALTER TABLE Pasien ADD COLUMN IF NOT EXISTS postal_code VARCHAR(10)",
            "ALTER TABLE Pasien ADD COLUMN IF NOT EXISTS address_notes TEXT",
        ];
    } else {
        $ddl = [
            "ALTER TABLE Pasien ADD COLUMN blood_type VARCHAR(5) NULL",
            "ALTER TABLE Pasien ADD COLUMN weight VARCHAR(20) NULL",
            "ALTER TABLE Pasien ADD COLUMN height VARCHAR(20) NULL",
            "ALTER TABLE Pasien ADD COLUMN allergy_notes TEXT NULL",
            "ALTER TABLE Pasien ADD COLUMN address_label VARCHAR(50) NULL",
            "ALTER TABLE Pasien ADD COLUMN recipient_name VARCHAR(100) NULL",
            "ALTER TABLE Pasien ADD COLUMN village VARCHAR(100) NULL",
            "ALTER TABLE Pasien ADD COLUMN district VARCHAR(100) NULL",
            "ALTER TABLE Pasien ADD COLUMN city VARCHAR(100) NULL",
            "ALTER TABLE Pasien ADD COLUMN province VARCHAR(100) NULL",
            "ALTER TABLE Pasien ADD COLUMN postal_code VARCHAR(10) NULL",
            "ALTER TABLE Pasien ADD COLUMN address_notes TEXT NULL",
        ];
    }

    foreach ($ddl as $sql) {
        try {
            $pdo->exec($sql);
        } catch (Throwable $e) {
            // Abaikan jika kolom sudah ada atau engine tidak mendukung sintaks IF NOT EXISTS.
        }
    }

    $checked = true;
}

function ensureUserProfilePhotoSchema(PDO $pdo): void
{
    static $checked = false;

    if ($checked) {
        return;
    }

    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    $ddl = $driver === 'pgsql'
        ? ["ALTER TABLE users ADD COLUMN IF NOT EXISTS profile_photo VARCHAR(255)"]
        : ["ALTER TABLE users ADD COLUMN profile_photo VARCHAR(255) NULL"];

    foreach ($ddl as $sql) {
        try {
            $pdo->exec($sql);
        } catch (Throwable $e) {
            // Abaikan jika kolom sudah ada atau engine tidak mendukung sintaks IF NOT EXISTS.
        }
    }

    $checked = true;
}

function getMarketplaceProfileInitials(string $name): string
{
    $name = trim($name);
    if ($name === '') {
        return 'CS';
    }

    $parts = preg_split('/\s+/', $name) ?: [];
    $initials = '';

    foreach ($parts as $part) {
        if ($part === '') {
            continue;
        }

        $initials .= strtoupper(substr($part, 0, 1));
        if (strlen($initials) >= 2) {
            break;
        }
    }

    return $initials !== '' ? $initials : strtoupper(substr($name, 0, 2));
}

function getUserProfilePhotoUrl(?string $path): string
{
    $path = trim((string) $path);
    if ($path === '') {
        return '';
    }

    if (preg_match('#^https?://#i', $path) === 1) {
        return $path;
    }

    return rtrim(BASE_URL, '/') . '/' . ltrim(str_replace('\\', '/', $path), '/');
}

function ensureMarketplacePaymentSchema(PDO $pdo): void
{
    static $checked = false;

    if ($checked) {
        return;
    }

    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    if ($driver === 'pgsql') {
        $ddl = [
            "ALTER TABLE Pembayaran ADD COLUMN IF NOT EXISTS payment_code VARCHAR(120)",
            "ALTER TABLE Pembayaran ADD COLUMN IF NOT EXISTS payment_channel VARCHAR(50)",
            "ALTER TABLE Pembayaran ADD COLUMN IF NOT EXISTS payment_type VARCHAR(30)",
            "ALTER TABLE Pembayaran ADD COLUMN IF NOT EXISTS payment_reference VARCHAR(120)",
            "ALTER TABLE Pembayaran ADD COLUMN IF NOT EXISTS payment_account_name VARCHAR(120)",
            "ALTER TABLE Pembayaran ADD COLUMN IF NOT EXISTS payment_account_number VARCHAR(120)",
            "ALTER TABLE Pembayaran ADD COLUMN IF NOT EXISTS payment_instructions TEXT",
            "ALTER TABLE Pembayaran ADD COLUMN IF NOT EXISTS payment_payload JSONB",
        ];
    } else {
        $ddl = [
            "ALTER TABLE Pembayaran ADD COLUMN payment_code VARCHAR(120) NULL",
            "ALTER TABLE Pembayaran ADD COLUMN payment_channel VARCHAR(50) NULL",
            "ALTER TABLE Pembayaran ADD COLUMN payment_type VARCHAR(30) NULL",
            "ALTER TABLE Pembayaran ADD COLUMN payment_reference VARCHAR(120) NULL",
            "ALTER TABLE Pembayaran ADD COLUMN payment_account_name VARCHAR(120) NULL",
            "ALTER TABLE Pembayaran ADD COLUMN payment_account_number VARCHAR(120) NULL",
            "ALTER TABLE Pembayaran ADD COLUMN payment_instructions TEXT NULL",
            "ALTER TABLE Pembayaran ADD COLUMN payment_payload TEXT NULL",
        ];
    }

    foreach ($ddl as $sql) {
        try {
            $pdo->exec($sql);
        } catch (Throwable $e) {
            // Abaikan jika kolom sudah ada atau engine tidak mendukung sintaks IF NOT EXISTS.
        }
    }

    $checked = true;
}

function syncMarketplaceProducts(PDO $pdo): void
{
    ensureMarketplaceSchema($pdo);

    $find = $pdo->prepare('SELECT idObat FROM Obat WHERE nama = :nama');
    $insert = $pdo->prepare(
        'INSERT INTO Obat (nama, dosis, harga, stok, deskripsi)
         VALUES (:nama, :dosis, :harga, :stok, :deskripsi)'
    );
    $update = $pdo->prepare(
        'UPDATE Obat SET dosis = :dosis, harga = :harga, stok = :stok, deskripsi = :deskripsi WHERE nama = :nama'
    );

    foreach (getMarketplaceProductCatalog() as $product) {
        $params = [
            ':nama' => $product['name'],
            ':dosis' => $product['dosis'],
            ':harga' => $product['price'],
            ':stok' => $product['stock'],
            ':deskripsi' => $product['description'],
        ];

        $find->execute([':nama' => $product['name']]);
        if ($find->fetchColumn()) {
            $update->execute($params);
            continue;
        }

        $insert->execute($params);
    }
}

function getMarketplaceProducts(PDO $pdo): array
{
    syncMarketplaceProducts($pdo);

    $stmt = $pdo->query('SELECT idObat, nama, harga, stok, dosis, deskripsi FROM Obat');
    $dbMap = [];

    foreach ($stmt->fetchAll() as $row) {
        $dbMap[$row['nama']] = [
            'db_id' => (int) $row['idobat'],
            'price' => (int) $row['harga'],
            'stock' => (int) $row['stok'],
            'dosis' => $row['dosis'],
            'description' => $row['deskripsi'],
        ];
    }

    $products = [];
    foreach (getMarketplaceProductCatalog() as $product) {
        $db = $dbMap[$product['name']] ?? null;
        if ($db) {
            $product['db_id'] = $db['db_id'];
            $product['price'] = $db['price'];
            $product['stock'] = $db['stock'];
            $product['dosis'] = $db['dosis'] ?: $product['dosis'];
            $product['description'] = $db['description'] ?: $product['description'];
        } else {
            $product['db_id'] = 0;
        }

        $products[] = $product;
    }

    return $products;
}

function findMarketplaceProduct(PDO $pdo, int $productId): ?array
{
    foreach (getMarketplaceProducts($pdo) as $product) {
        if ((int) $product['id'] === $productId) {
            return $product;
        }
    }

    return null;
}

function getMarketplacePaymentMethods(): array
{
    return [
        'QRIS' => ['name' => 'QRIS', 'type' => 'qris'],
        'GoPay' => ['name' => 'GoPay', 'type' => 'ewallet'],
        'VA_BCA' => ['name' => 'BCA Virtual Account', 'type' => 'va'],
        'VA_MANDIRI' => ['name' => 'Mandiri Virtual Account', 'type' => 'va'],
        'VA_BNI' => ['name' => 'BNI Virtual Account', 'type' => 'va'],
        'VA_BRI' => ['name' => 'BRIVA (BRI Virtual Account)', 'type' => 'va'],
    ];
}

function getMarketplacePaymentMethodLabel(string $method): string
{
    $methods = getMarketplacePaymentMethods();
    return $methods[$method]['name'] ?? $method;
}

function normalizeMarketplaceAddressText(string $value): string
{
    $value = strtolower(trim(preg_replace('/\s+/', ' ', $value)));
    $value = str_replace(
        ['kab.', 'kabupaten', 'kota administrasi', 'kota adm.', 'kota', 'provinsi', 'dki ', 'daerah khusus ibukota '],
        ['', '', '', '', '', '', '', ''],
        $value
    );
    $value = trim(preg_replace('/\s+/', ' ', $value));

    $aliases = [
        'jakarta selatan' => 'jakarta selatan',
        'jaksel' => 'jakarta selatan',
        'jakarta pusat' => 'jakarta pusat',
        'jakpus' => 'jakarta pusat',
        'jakarta barat' => 'jakarta barat',
        'jakbar' => 'jakarta barat',
        'jakarta timur' => 'jakarta timur',
        'jaktim' => 'jakarta timur',
        'jakarta utara' => 'jakarta utara',
        'jakut' => 'jakarta utara',
        'bogor' => 'bogor',
        'depok' => 'depok',
        'tangerang selatan' => 'tangerang selatan',
        'tangsel' => 'tangerang selatan',
        'tangerang' => 'tangerang',
        'bekasi' => 'bekasi',
        'bandung' => 'bandung',
        'tasik' => 'tasikmalaya',
        'tasikmalaya' => 'tasikmalaya',
        'jogja' => 'yogyakarta',
        'jogjakarta' => 'yogyakarta',
        'yogyakarta' => 'yogyakarta',
        'semarang' => 'semarang',
        'surabaya' => 'surabaya',
        'jawa barat' => 'jawa barat',
        'jabar' => 'jawa barat',
        'jawa tengah' => 'jawa tengah',
        'jateng' => 'jawa tengah',
        'jawa timur' => 'jawa timur',
        'jatim' => 'jawa timur',
        'banten' => 'banten',
        'dki jakarta' => 'jakarta',
        'jakarta' => 'jakarta',
    ];

    return $aliases[$value] ?? $value;
}

function sanitizeMarketplaceLocationPart(string $value): string
{
    $value = strtolower(trim($value));
    if ($value === '') {
        return '';
    }

    $value = preg_replace('/\b(kec\.?|kecamatan|kel\.?|kelurahan|desa|ds\.?|kab\.?|kabupaten|kota)\b/ui', ' ', $value);
    $value = preg_replace('/[^a-z0-9\s-]/ui', ' ', $value);
    $value = trim(preg_replace('/\s+/', ' ', (string) $value));

    return normalizeMarketplaceAddressText($value);
}

function sanitizeMarketplaceLocationLabel(string $value): string
{
    $value = strtolower(trim($value));
    if ($value === '') {
        return '';
    }

    $value = preg_replace('/[^a-z0-9\s.-]/ui', ' ', $value);
    $value = str_replace(['kab.', 'kab ', 'kota adm.', 'kota administrasi'], ['kabupaten ', 'kabupaten ', 'kota ', 'kota '], $value);
    $value = trim(preg_replace('/\s+/', ' ', (string) $value));

    return $value;
}

function sanitizeMarketplacePrefixCandidate(string $value): string
{
    $value = trim($value);
    if ($value === '') {
        return '';
    }

    $segments = preg_split('/\s*,\s*/', $value) ?: [];
    $segments = array_values(array_filter(array_map('sanitizeMarketplaceLocationPart', $segments), static function (string $segment): bool {
        return $segment !== '';
    }));

    if (!$segments) {
        return '';
    }

    return implode(',', $segments);
}

function getMarketplaceLocationVariants(string $value, bool $includeAdministrativeCityVariants = false): array
{
    $variants = [];

    $raw = sanitizeMarketplaceLocationLabel($value);
    if ($raw !== '') {
        $variants[] = $raw;
    }

    $normalized = sanitizeMarketplaceLocationPart($value);
    if ($normalized !== '') {
        $variants[] = $normalized;

        if ($includeAdministrativeCityVariants) {
            $variants[] = 'kota ' . $normalized;
            $variants[] = 'kabupaten ' . $normalized;
        }
    }

    $unique = [];
    foreach ($variants as $variant) {
        $variant = trim(preg_replace('/\s+/', ' ', (string) $variant));
        if ($variant === '' || in_array($variant, $unique, true)) {
            continue;
        }
        $unique[] = $variant;
    }

    return $unique;
}

function extractMarketplaceDistrictCandidate(string $addressLine): string
{
    $addressLine = trim($addressLine);
    if ($addressLine === '') {
        return '';
    }

    if (preg_match('/\bkec(?:amatan)?\.?\s+([a-z0-9\s-]+)/iu', $addressLine, $matches)) {
        return sanitizeMarketplaceLocationPart($matches[1]);
    }

    $segments = preg_split('/[,;\/|]/', $addressLine) ?: [];
    $segments = array_reverse($segments);

    foreach ($segments as $segment) {
        $candidate = sanitizeMarketplaceLocationPart((string) $segment);
        if ($candidate === '' || preg_match('/\d/', $candidate)) {
            continue;
        }

        if (strlen($candidate) < 3 || strlen($candidate) > 40) {
            continue;
        }

        return $candidate;
    }

    return '';
}

function buildMarketplaceBinderbyteLocationCandidates(string $city, string $addressLine = '', string $district = '', string $prefix = '', string $village = ''): array
{
    $candidates = [];

    $prefix = sanitizeMarketplacePrefixCandidate($prefix);
    if ($prefix !== '') {
        $candidates[] = $prefix;
    }

    $villageVariants = getMarketplaceLocationVariants($village);
    $districtVariants = getMarketplaceLocationVariants($district);
    $addressDistrict = extractMarketplaceDistrictCandidate($addressLine);
    if ($addressDistrict !== '') {
        $districtVariants = array_values(array_unique(array_merge(
            $districtVariants,
            getMarketplaceLocationVariants($addressDistrict)
        )));
    }
    $cityVariants = getMarketplaceLocationVariants($city, true);

    foreach ($cityVariants as $cityVariant) {
        $candidates[] = $cityVariant;

        foreach ($districtVariants as $districtVariant) {
            $candidates[] = $districtVariant . ',' . $cityVariant;

            foreach ($villageVariants as $villageVariant) {
                $candidates[] = $villageVariant . ',' . $districtVariant . ',' . $cityVariant;
            }
        }
    }

    $unique = [];
    foreach ($candidates as $candidate) {
        $candidate = trim($candidate, " ,");
        if ($candidate === '' || in_array($candidate, $unique, true)) {
            continue;
        }
        $unique[] = $candidate;
    }

    return $unique;
}

function limitMarketplaceLocationCandidates(array $candidates, int $max = 6): array
{
    if ($max <= 0 || count($candidates) <= $max) {
        return $candidates;
    }

    return array_slice($candidates, 0, $max);
}

function getMarketplaceDestinationZone(string $address): string
{
    $address = normalizeMarketplaceAddressText($address);
    if ($address === '' || $address === 'alamat belum diatur. silakan lengkapi alamat pada profil pasien anda.') {
        return 'national';
    }

    foreach (['jakarta', 'bogor', 'depok', 'tangerang', 'bekasi'] as $keyword) {
        if (str_contains($address, $keyword)) {
            return 'metro';
        }
    }

    foreach (['bandung', 'tasikmalaya', 'cirebon', 'semarang', 'yogyakarta', 'surabaya'] as $keyword) {
        if (str_contains($address, $keyword)) {
            return 'regional';
        }
    }

    return 'national';
}

function getMarketplaceZoneLabel(string $zone): string
{
    return [
        'metro' => 'Jabodetabek',
        'regional' => 'Jawa-Bali & Kota Besar',
        'national' => 'Nasional',
    ][$zone] ?? 'Nasional';
}

function calculateMarketplaceCourierPrice(array $courier, int $weightGrams, string $address): int
{
    $zone = getMarketplaceDestinationZone($address);
    $chargeableKg = max(1, (int) ceil(max(1, $weightGrams) / 1000));
    $zoneMultiplier = [
        'metro' => 1.0,
        'regional' => 1.2,
        'national' => 1.45,
    ][$zone] ?? 1.0;

    $base = (int) ($courier['base_price'] ?? 0);
    $perKg = (int) ($courier['price_per_kg'] ?? 0);

    return (int) ceil(($base + ($perKg * $chargeableKg)) * $zoneMultiplier / 500) * 500;
}

function calculateMarketplaceCourierEtd(array $courier, string $address): string
{
    $zone = getMarketplaceDestinationZone($address);
    $etdMap = $courier['etd_map'] ?? [];

    return $etdMap[$zone] ?? ($courier['etd'] ?? '2 - 4 Hari');
}

function isMarketplaceShippingLiveConfigured(): bool
{
    $provider = MARKETPLACE_SHIPPING_PROVIDER;

    if ($provider === 'rajaongkir') {
        return RAJAONGKIR_API_KEY !== '';
    }

    return BINDERBYTE_API_KEY !== '';
}

function getMarketplaceShippingProviderLabel(): string
{
    return match (MARKETPLACE_SHIPPING_PROVIDER) {
        'rajaongkir' => 'RajaOngkir',
        default => 'Binderbyte',
    };
}

function marketplaceHttpGetJson(string $url, array $headers = []): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);

    $response = curl_exec($ch);
    $statusCode = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($response === false || $error !== '') {
        throw new RuntimeException('Gagal menghubungi provider ongkir live: ' . $error);
    }

    $decoded = json_decode($response, true);
    if (!is_array($decoded)) {
        throw new RuntimeException('Provider ongkir live mengembalikan respons yang tidak valid.');
    }

    return [
        'status_code' => $statusCode,
        'data' => $decoded,
    ];
}

function marketplaceHttpPostFormJson(string $url, array $payload, array $headers = []): array
{
    $ch = curl_init($url);
    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 8,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($payload),
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);

    $response = curl_exec($ch);
    $statusCode = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($response === false || $error !== '') {
        throw new RuntimeException('Gagal menghubungi provider ongkir live: ' . $error);
    }

    $decoded = json_decode($response, true);
    if (!is_array($decoded)) {
        throw new RuntimeException('Provider ongkir live mengembalikan respons yang tidak valid.');
    }

    return [
        'status_code' => $statusCode,
        'data' => $decoded,
    ];
}

function logMarketplaceShippingDebug(string $event, array $payload): void
{
    $logDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0777, true);
    }

    $line = json_encode([
        'time' => date('c'),
        'event' => $event,
        'payload' => $payload,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    if ($line !== false) {
        @file_put_contents($logDir . DIRECTORY_SEPARATOR . 'shipping-debug.log', $line . PHP_EOL, FILE_APPEND);
    }
}

function getMarketplaceCourierDefinitions(): array
{
    return [
        'instant_gosend' => ['id' => 'instant_gosend', 'code' => 'gosend', 'name' => 'GoSend Instant', 'provider' => 'GoSend', 'service' => 'Instant', 'icon' => 'fa-motorcycle', 'color' => 'text-emerald-500 bg-emerald-50'],
        'same_day_gosend' => ['id' => 'same_day_gosend', 'code' => 'gosend', 'name' => 'GoSend Same Day', 'provider' => 'GoSend', 'service' => 'Same Day', 'icon' => 'fa-motorcycle', 'color' => 'text-emerald-500 bg-emerald-50'],
        'jne_yes' => ['id' => 'jne_yes', 'code' => 'jne', 'name' => 'JNE YES', 'provider' => 'JNE', 'service' => 'YES', 'icon' => 'fa-bolt', 'color' => 'text-indigo-500 bg-indigo-50'],
        'jne_reg' => ['id' => 'jne_reg', 'code' => 'jne', 'name' => 'JNE Reguler', 'provider' => 'JNE', 'service' => 'REG', 'icon' => 'fa-truck-fast', 'color' => 'text-blue-500 bg-blue-50'],
        'jnt_reg' => ['id' => 'jnt_reg', 'code' => 'jnt', 'name' => 'J&T Express', 'provider' => 'J&T', 'service' => 'EZ', 'icon' => 'fa-truck-fast', 'color' => 'text-red-500 bg-red-50'],
        'pos_reguler' => ['id' => 'pos_reguler', 'code' => 'pos', 'name' => 'POS Reguler', 'provider' => 'POS Indonesia', 'service' => 'POS REGULER', 'icon' => 'fa-box', 'color' => 'text-amber-500 bg-amber-50'],
        'pos_nextday' => ['id' => 'pos_nextday', 'code' => 'pos', 'name' => 'POS Nextday', 'provider' => 'POS Indonesia', 'service' => 'POS NEXTDAY', 'icon' => 'fa-bolt', 'color' => 'text-yellow-500 bg-yellow-50'],
        'sicepat_best' => ['id' => 'sicepat_best', 'code' => 'sicepat', 'name' => 'SiCepat BEST', 'provider' => 'SiCepat', 'service' => 'BEST', 'icon' => 'fa-box', 'color' => 'text-orange-500 bg-orange-50'],
        'sicepat_reg' => ['id' => 'sicepat_reg', 'code' => 'sicepat', 'name' => 'SiCepat REG', 'provider' => 'SiCepat', 'service' => 'REG', 'icon' => 'fa-boxes-packing', 'color' => 'text-sky-500 bg-sky-50'],
        'tiki_dat' => ['id' => 'tiki_dat', 'code' => 'tiki', 'name' => 'TIKI DAT', 'provider' => 'TIKI', 'service' => 'DAT', 'icon' => 'fa-bolt', 'color' => 'text-fuchsia-500 bg-fuchsia-50'],
        'tiki_reg' => ['id' => 'tiki_reg', 'code' => 'tiki', 'name' => 'TIKI REG', 'provider' => 'TIKI', 'service' => 'REG', 'icon' => 'fa-truck-fast', 'color' => 'text-violet-500 bg-violet-50'],
    ];
}

function getMarketplaceSupportedLiveCourierCodes(?string $provider = null): array
{
    $provider = strtolower(trim((string) ($provider ?? MARKETPLACE_SHIPPING_PROVIDER)));
    if ($provider === 'rajaongkir') {
        return ['jne', 'jnt', 'sicepat', 'pos', 'tiki'];
    }

    $codes = [];
    foreach (getMarketplaceCourierDefinitions() as $definition) {
        $codes[$definition['code']] = true;
    }

    return array_keys($codes);
}

function findMarketplaceCourierDefinition(string $providerCode, string $serviceName): ?array
{
    $providerCode = strtolower(trim($providerCode));
    $serviceName = strtoupper(trim($serviceName));

    foreach (getMarketplaceCourierDefinitions() as $definition) {
        if ($definition['code'] !== $providerCode) {
            continue;
        }

        if ($serviceName === strtoupper($definition['service'])) {
            return $definition;
        }

        if ($providerCode === 'jnt' && in_array($serviceName, ['EZ', 'REG', 'REGULER', 'REGULAR'], true)) {
            return $definition;
        }
    }

    return null;
}

function getMarketplaceShippingCacheKey(array $context): string
{
    return sha1(json_encode([
        'provider' => MARKETPLACE_SHIPPING_PROVIDER,
        'origin_district' => MARKETPLACE_ORIGIN_DISTRICT,
        'origin_prefix' => MARKETPLACE_ORIGIN_BINDERBYTE_PREFIX,
        'origin_city' => MARKETPLACE_ORIGIN_CITY,
        'origin_province' => MARKETPLACE_ORIGIN_PROVINCE,
        'destination_village' => $context['address']['village'] ?? '',
        'destination_address_line' => $context['address']['address_line'] ?? '',
        'destination_district' => $context['address']['district'] ?? '',
        'destination_city' => $context['address']['city'] ?? '',
        'destination_province' => $context['address']['province'] ?? '',
        'postal_code' => $context['address']['postal_code'] ?? '',
        'weight' => (int) ($context['total_weight'] ?? 0),
    ]));
}

function getMarketplaceCachedLiveCouriers(array $context): ?array
{
    $cacheKey = getMarketplaceShippingCacheKey($context);
    $cache = $_SESSION['marketplace_live_shipping'][$cacheKey] ?? null;
    if (!is_array($cache)) {
        return null;
    }

    $createdAt = strtotime((string) ($cache['created_at'] ?? ''));
    if (!$createdAt || (time() - $createdAt) > 900) {
        unset($_SESSION['marketplace_live_shipping'][$cacheKey]);
        return null;
    }

    return is_array($cache['data'] ?? null) ? $cache['data'] : null;
}

function setMarketplaceCachedLiveCouriers(array $context, array $couriers): void
{
    $cacheKey = getMarketplaceShippingCacheKey($context);
    $_SESSION['marketplace_live_shipping'][$cacheKey] = [
        'created_at' => date('c'),
        'data' => $couriers,
    ];
}

function searchRajaOngkirProvince(string $province): ?array
{
    return null;
}

function searchRajaOngkirCity(
    string $city,
    string $province = '',
    string $district = '',
    string $village = '',
    string $postalCode = '',
    string $addressLine = ''
): ?array
{
    $candidates = array_values(array_unique(array_filter([
        trim(implode(' ', array_filter([$village, $district, $city, $province, $postalCode]))),
        trim(implode(' ', array_filter([$district, $city, $province, $postalCode]))),
        trim(implode(' ', array_filter([$city, $province, $postalCode]))),
        trim(implode(' ', array_filter([$district, $city, $province]))),
        trim(implode(' ', array_filter([$city, $province]))),
        trim($postalCode),
        trim($city),
        trim($addressLine),
    ])));

    $cityNeedle = normalizeMarketplaceAddressText($city);
    $provinceNeedle = normalizeMarketplaceAddressText($province);
    $districtNeedle = normalizeMarketplaceAddressText($district);
    $villageNeedle = normalizeMarketplaceAddressText($village);
    $postalNeedle = trim($postalCode);

    $bestMatch = null;
    $bestScore = -1;

    foreach ($candidates as $candidate) {
        $response = marketplaceHttpGetJson(
            'https://rajaongkir.komerce.id/api/v1/destination/domestic-destination?search=' . rawurlencode($candidate) . '&limit=10&offset=0',
            ['key: ' . RAJAONGKIR_API_KEY]
        );
        $results = $response['data']['data'] ?? [];
        if (!is_array($results)) {
            continue;
        }

        foreach ($results as $result) {
            $score = 0;
            $resultCity = normalizeMarketplaceAddressText((string) ($result['city_name'] ?? ''));
            $resultProvince = normalizeMarketplaceAddressText((string) ($result['province_name'] ?? ''));
            $resultDistrict = normalizeMarketplaceAddressText((string) ($result['district_name'] ?? ''));
            $resultVillage = normalizeMarketplaceAddressText((string) ($result['subdistrict_name'] ?? ''));
            $resultPostal = trim((string) ($result['zip_code'] ?? ''));

            if ($cityNeedle !== '' && $resultCity === $cityNeedle) {
                $score += 5;
            }
            if ($provinceNeedle !== '' && $resultProvince === $provinceNeedle) {
                $score += 3;
            }
            if ($districtNeedle !== '' && $resultDistrict === $districtNeedle) {
                $score += 4;
            }
            if ($villageNeedle !== '' && $resultVillage === $villageNeedle) {
                $score += 2;
            }
            if ($postalNeedle !== '' && $resultPostal === $postalNeedle) {
                $score += 4;
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $result;
            }

            if ($score >= 8) {
                return $result;
            }
        }
    }

    return $bestMatch;
}

function getMarketplaceProviderAttemptOrder(): array
{
    $provider = strtolower(trim((string) MARKETPLACE_SHIPPING_PROVIDER));

    return match ($provider) {
        'rajaongkir' => ['rajaongkir', 'binderbyte'],
        'binderbyte' => ['binderbyte', 'rajaongkir'],
        default => [$provider, 'rajaongkir', 'binderbyte'],
    };
}

function fetchRajaOngkirLiveCouriers(array $context): array
{
    $origin = searchRajaOngkirCity(
        MARKETPLACE_ORIGIN_CITY,
        MARKETPLACE_ORIGIN_PROVINCE,
        MARKETPLACE_ORIGIN_DISTRICT
    );
    $destination = searchRajaOngkirCity(
        (string) ($context['address']['city'] ?? ''),
        (string) ($context['address']['province'] ?? ''),
        (string) ($context['address']['district'] ?? ''),
        (string) ($context['address']['village'] ?? ''),
        (string) ($context['address']['postal_code'] ?? ''),
        (string) ($context['address']['address_line'] ?? '')
    );

    if (!$origin || !$destination) {
        throw new RuntimeException('Lokasi asal atau tujuan belum dapat dipetakan ke RajaOngkir. Lengkapi kota, kecamatan, dan kode pos pada alamat pasien.');
    }

    $weight = max(100, (int) ($context['total_weight'] ?? 0));
    $couriers = [];

    foreach (getMarketplaceSupportedLiveCourierCodes('rajaongkir') as $courierCode) {
        try {
            $response = marketplaceHttpPostFormJson(
                'https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost',
                [
                    'origin' => (string) ($origin['id'] ?? ''),
                    'destination' => (string) ($destination['id'] ?? ''),
                    'weight' => $weight,
                    'courier' => $courierCode,
                ],
                ['key: ' . RAJAONGKIR_API_KEY]
            );
            $results = $response['data']['data'] ?? [];
            if (!is_array($results)) {
                continue;
            }

            foreach ($results as $service) {
                $definition = findMarketplaceCourierDefinition(
                    (string) ($service['code'] ?? $courierCode),
                    (string) ($service['service'] ?? '')
                );
                if (!$definition && count(array_filter(getMarketplaceCourierDefinitions(), static fn(array $item): bool => $item['code'] === $courierCode)) === 1) {
                    foreach (getMarketplaceCourierDefinitions() as $item) {
                        if ($item['code'] === $courierCode) {
                            $definition = $item;
                            break;
                        }
                    }
                }

                if (!$definition) {
                    continue;
                }

                $couriers[] = [
                    'id' => $definition['id'],
                    'name' => $definition['name'],
                    'provider' => $definition['provider'],
                    'service' => $definition['service'],
                    'icon' => $definition['icon'],
                    'color' => $definition['color'],
                    'price' => (int) ($service['cost'] ?? 0),
                    'etd' => trim((string) ($service['etd'] ?? '')),
                    'weight_kg' => max(1, (int) ceil($weight / 1000)),
                    'zone' => (string) ($context['address']['zone'] ?? 'national'),
                    'reference' => 'Live via RajaOngkir',
                    'is_live' => true,
                ];
            }
        } catch (Throwable $e) {
            logMarketplaceShippingDebug('rajaongkir_cost_response', [
                'request' => [
                    'origin' => (string) ($origin['id'] ?? ''),
                    'destination' => (string) ($destination['id'] ?? ''),
                    'weight' => $weight,
                    'courier' => $courierCode,
                ],
                'error' => $e->getMessage(),
            ]);
        }
    }

    return $couriers;
}

function fetchBinderbyteLiveCouriers(array $context): array
{
    $destinationCity = trim((string) ($context['address']['city'] ?? ''));
    if ($destinationCity === '') {
        throw new RuntimeException('Kota tujuan belum diisi. Lengkapi alamat pasien untuk mengambil ongkir live.');
    }

    $originCandidates = buildMarketplaceBinderbyteLocationCandidates(
        MARKETPLACE_ORIGIN_CITY,
        '',
        MARKETPLACE_ORIGIN_DISTRICT,
        MARKETPLACE_ORIGIN_BINDERBYTE_PREFIX
    );
    $destinationCandidates = buildMarketplaceBinderbyteLocationCandidates(
        $destinationCity,
        (string) ($context['address']['address_line'] ?? ''),
        (string) ($context['address']['district'] ?? ''),
        '',
        (string) ($context['address']['village'] ?? '')
    );
    $originCandidates = limitMarketplaceLocationCandidates($originCandidates, 4);
    $destinationCandidates = limitMarketplaceLocationCandidates($destinationCandidates, 5);

    if (!$originCandidates) {
        throw new RuntimeException('Origin Binderbyte belum valid. Periksa MARKETPLACE_ORIGIN_CITY atau tambahkan MARKETPLACE_ORIGIN_DISTRICT.');
    }

    if (!$destinationCandidates) {
        throw new RuntimeException('Lokasi tujuan belum cukup spesifik untuk Binderbyte. Lengkapi alamat pasien.');
    }

    $weight = max(100, (int) ($context['total_weight'] ?? 0));
    $couriers = [];
    $attemptedLocations = [];
    $supportedCouriers = getMarketplaceSupportedLiveCourierCodes();
    $probeCourier = in_array('jne', $supportedCouriers, true) ? 'jne' : ($supportedCouriers[0] ?? 'jne');
    $maxPairAttempts = 12;
    $pairAttempts = 0;
    $winningPair = null;

    $extractCouriers = static function (array $result, string $courierCode, int $weight, array $context): array {
        $items = [];

        foreach (($result['data']['results'] ?? []) as $serviceGroup) {
            $providerCode = strtolower((string) ($serviceGroup['code'] ?? $courierCode));
            foreach (($serviceGroup['costs'] ?? []) as $service) {
                $definition = findMarketplaceCourierDefinition($providerCode, (string) ($service['service'] ?? ''));
                if (!$definition && count(array_filter(getMarketplaceCourierDefinitions(), static fn(array $item): bool => $item['code'] === $providerCode)) === 1) {
                    foreach (getMarketplaceCourierDefinitions() as $item) {
                        if ($item['code'] === $providerCode) {
                            $definition = $item;
                            break;
                        }
                    }
                }

                if (!$definition) {
                    continue;
                }

                $costValue = $service['cost'] ?? null;
                $etdValue = $service['etd'] ?? '';
                if (!is_numeric($costValue)) {
                    continue;
                }

                $items[] = [
                    'id' => $definition['id'],
                    'name' => $definition['name'],
                    'provider' => $definition['provider'],
                    'service' => $definition['service'],
                    'icon' => $definition['icon'],
                    'color' => $definition['color'],
                    'price' => (int) $costValue,
                    'etd' => trim((string) $etdValue),
                    'weight_kg' => max(1, (int) ceil($weight / 1000)),
                    'zone' => (string) ($context['address']['zone'] ?? 'national'),
                    'reference' => 'Live via Binderbyte',
                    'is_live' => true,
                ];
            }
        }

        return $items;
    };

    foreach ($originCandidates as $origin) {
        foreach ($destinationCandidates as $destination) {
            if ($pairAttempts >= $maxPairAttempts) {
                break 2;
            }

            $pairAttempts++;
            $requestPayload = [
                'api_key' => BINDERBYTE_API_KEY,
                'courier' => $probeCourier,
                'origin' => $origin,
                'destination' => $destination,
                'weight' => max(1, (int) ceil($weight / 1000)),
            ];
            $response = marketplaceHttpPostFormJson('https://api.binderbyte.com/v1/cost', $requestPayload);
            logMarketplaceShippingDebug('binderbyte_cost_response', [
                'request' => $requestPayload,
                'response' => $response['data'],
            ]);
            $result = $response['data'] ?? [];
            if (!is_array($result) || (string) ($result['code'] ?? '') !== '200') {
                $attemptedLocations[] = $origin . ' -> ' . $destination . ' [' . $probeCourier . ']';
                continue;
            }

            $couriers = $extractCouriers($result, $probeCourier, $weight, $context);
            $winningPair = [
                'origin' => $origin,
                'destination' => $destination,
            ];
            break 2;
        }
    }

    if ($winningPair) {
        foreach ($supportedCouriers as $courierCode) {
            if ($courierCode === $probeCourier) {
                continue;
            }

            $requestPayload = [
                'api_key' => BINDERBYTE_API_KEY,
                'courier' => $courierCode,
                'origin' => $winningPair['origin'],
                'destination' => $winningPair['destination'],
                'weight' => max(1, (int) ceil($weight / 1000)),
            ];
            $response = marketplaceHttpPostFormJson('https://api.binderbyte.com/v1/cost', $requestPayload);
            logMarketplaceShippingDebug('binderbyte_cost_response', [
                'request' => $requestPayload,
                'response' => $response['data'],
            ]);
            $result = $response['data'] ?? [];
            if (!is_array($result) || (string) ($result['code'] ?? '') !== '200') {
                $attemptedLocations[] = $winningPair['origin'] . ' -> ' . $winningPair['destination'] . ' [' . $courierCode . ']';
                continue;
            }

            $couriers = array_merge($couriers, $extractCouriers($result, $courierCode, $weight, $context));
        }
    }

    if ($couriers) {
        return $couriers;
    }

    if ($attemptedLocations) {
        throw new RuntimeException(
            'Binderbyte belum menemukan prefix lokasi yang cocok. Kandidat yang dicoba: ' . implode(' | ', array_slice($attemptedLocations, 0, 8))
        );
    }

    return $couriers;
}

function getMarketplaceSimulatedCouriers(?array $context = null): array
{
    $weight = (int) ($context['total_weight'] ?? 0);
    $address = (string) ($context['address']['address'] ?? '');

    $couriers = [
        [
            'id' => 'instant_gosend',
            'name' => 'GoSend Instant',
            'provider' => 'GoSend',
            'service' => 'Instant',
            'icon' => 'fa-motorcycle',
            'color' => 'text-emerald-500 bg-emerald-50',
            'base_price' => 18000,
            'price_per_kg' => 3500,
            'supports' => ['metro'],
            'etd_map' => ['metro' => '1 - 2 Jam'],
            'reference' => 'Simulasi berbasis layanan resmi',
        ],
        [
            'id' => 'same_day_gosend',
            'name' => 'GoSend Same Day',
            'provider' => 'GoSend',
            'service' => 'Same Day',
            'icon' => 'fa-motorcycle',
            'color' => 'text-emerald-500 bg-emerald-50',
            'base_price' => 15000,
            'price_per_kg' => 3000,
            'supports' => ['metro', 'regional'],
            'etd_map' => ['metro' => '6 - 8 Jam', 'regional' => 'Hari yang sama'],
            'reference' => 'Simulasi berbasis layanan resmi',
        ],
        [
            'id' => 'jne_yes',
            'name' => 'JNE YES',
            'provider' => 'JNE',
            'service' => 'YES',
            'icon' => 'fa-bolt',
            'color' => 'text-indigo-500 bg-indigo-50',
            'base_price' => 18000,
            'price_per_kg' => 5000,
            'supports' => ['metro', 'regional', 'national'],
            'etd_map' => ['metro' => '1 Hari', 'regional' => '1 Hari', 'national' => '1 - 2 Hari'],
            'reference' => 'Simulasi berbasis layanan resmi',
        ],
        [
            'id' => 'jne_reg',
            'name' => 'JNE Reguler',
            'provider' => 'JNE',
            'service' => 'REG',
            'icon' => 'fa-truck-fast',
            'color' => 'text-blue-500 bg-blue-50',
            'base_price' => 9000,
            'price_per_kg' => 3000,
            'supports' => ['metro', 'regional', 'national'],
            'etd_map' => ['metro' => '1 - 2 Hari', 'regional' => '2 - 3 Hari', 'national' => '3 - 5 Hari'],
            'reference' => 'Simulasi berbasis layanan resmi',
        ],
        [
            'id' => 'jnt_reg',
            'name' => 'J&T Express',
            'provider' => 'J&T',
            'service' => 'Regular / EZ',
            'icon' => 'fa-truck-fast',
            'color' => 'text-red-500 bg-red-50',
            'base_price' => 8500,
            'price_per_kg' => 3200,
            'supports' => ['metro', 'regional', 'national'],
            'etd_map' => ['metro' => '1 - 2 Hari', 'regional' => '2 - 3 Hari', 'national' => '2 - 4 Hari'],
            'reference' => 'Simulasi berbasis layanan resmi',
        ],
        [
            'id' => 'sicepat_best',
            'name' => 'SiCepat BEST',
            'provider' => 'SiCepat',
            'service' => 'BEST',
            'icon' => 'fa-box',
            'color' => 'text-orange-500 bg-orange-50',
            'base_price' => 12000,
            'price_per_kg' => 3500,
            'supports' => ['metro', 'regional', 'national'],
            'etd_map' => ['metro' => '1 Hari', 'regional' => '1 - 2 Hari', 'national' => '2 - 3 Hari'],
            'reference' => 'Simulasi berbasis layanan resmi',
        ],
        [
            'id' => 'sicepat_reg',
            'name' => 'SiCepat REG',
            'provider' => 'SiCepat',
            'service' => 'Regular',
            'icon' => 'fa-boxes-packing',
            'color' => 'text-sky-500 bg-sky-50',
            'base_price' => 9000,
            'price_per_kg' => 2800,
            'supports' => ['metro', 'regional', 'national'],
            'etd_map' => ['metro' => '1 Hari', 'regional' => '1 - 3 Hari', 'national' => '2 - 4 Hari'],
            'reference' => 'Simulasi berbasis layanan resmi',
        ],
    ];

    $zone = getMarketplaceDestinationZone($address);
    $available = [];

    foreach ($couriers as $courier) {
        if (!in_array($zone, $courier['supports'], true)) {
            continue;
        }

        $courier['etd'] = calculateMarketplaceCourierEtd($courier, $address);
        $courier['price'] = calculateMarketplaceCourierPrice($courier, $weight, $address);
        $courier['zone'] = $zone;
        $courier['weight_kg'] = max(1, (int) ceil(max(1, $weight) / 1000));
        $courier['is_live'] = false;
        $available[] = $courier;
    }

    usort($available, static function (array $left, array $right): int {
        return ($left['price'] <=> $right['price']) ?: strcmp($left['name'], $right['name']);
    });

    return $available;
}

function fetchMarketplaceLiveCouriers(array $context): array
{
    $cached = getMarketplaceCachedLiveCouriers($context);
    if ($cached !== null) {
        return $cached;
    }

    $errors = [];
    $couriers = [];

    foreach (getMarketplaceProviderAttemptOrder() as $provider) {
        try {
            if ($provider === 'rajaongkir') {
                if (RAJAONGKIR_API_KEY === '') {
                    throw new RuntimeException('API key RajaOngkir belum diisi.');
                }
                $couriers = fetchRajaOngkirLiveCouriers($context);
            }

            if ($provider === 'binderbyte') {
                if (BINDERBYTE_API_KEY === '') {
                    throw new RuntimeException('API key Binderbyte belum diisi.');
                }
                $couriers = fetchBinderbyteLiveCouriers($context);
            }

            if ($couriers) {
                break;
            }

            $errors[] = strtoupper($provider) . ': tidak ada layanan yang tersedia.';
        } catch (Throwable $e) {
            $errors[] = strtoupper($provider) . ': ' . $e->getMessage();
        }
    }

    if (!$couriers) {
        throw new RuntimeException('Ongkir live gagal diambil. ' . implode(' | ', $errors));
    }

    usort($couriers, static function (array $left, array $right): int {
        return ($left['price'] <=> $right['price']) ?: strcmp($left['name'], $right['name']);
    });

    setMarketplaceCachedLiveCouriers($context, $couriers);
    return $couriers;
}

function getMarketplaceShippingOptions(?array $context = null): array
{
    if (!$context) {
        return [
            'couriers' => getMarketplaceSimulatedCouriers($context),
            'is_live' => false,
            'warning' => '',
            'status' => 'no_checkout',
            'status_label' => 'Checkout Belum Aktif',
        ];
    }

    if (!isMarketplaceShippingLiveConfigured()) {
        return [
            'couriers' => getMarketplaceSimulatedCouriers($context),
            'is_live' => false,
            'warning' => 'API key ' . getMarketplaceShippingProviderLabel() . ' belum diatur, ongkir memakai simulasi lokal.',
            'status' => 'missing_api_key',
            'status_label' => 'Mode Simulasi',
        ];
    }

    try {
        return [
            'couriers' => fetchMarketplaceLiveCouriers($context),
            'is_live' => true,
            'warning' => '',
            'status' => 'live',
            'status_label' => 'Live ' . getMarketplaceShippingProviderLabel(),
        ];
    } catch (Throwable $e) {
        $message = $e->getMessage();
        $status = 'live_failed';
        $statusLabel = 'Simulasi Aktif';

        if (stripos($message, 'prefix lokasi') !== false) {
            $status = 'invalid_prefix';
            $statusLabel = 'Prefix Lokasi Belum Cocok';
        } elseif (stripos($message, 'lokasi tujuan belum cukup spesifik') !== false) {
            $status = 'address_not_specific';
            $statusLabel = 'Alamat Belum Spesifik';
        } elseif (stripos($message, 'api key') !== false) {
            $status = 'missing_api_key';
            $statusLabel = 'API Key Belum Aktif';
        }

        logMarketplaceShippingDebug('shipping_fallback', [
            'message' => $message,
            'provider' => getMarketplaceShippingProviderLabel(),
            'status' => $status,
            'context' => [
                'district' => $context['address']['district'] ?? '',
                'village' => $context['address']['village'] ?? '',
                'city' => $context['address']['city'] ?? '',
                'province' => $context['address']['province'] ?? '',
                'postal_code' => $context['address']['postal_code'] ?? '',
                'weight' => (int) ($context['total_weight'] ?? 0),
            ],
        ]);

        return [
            'couriers' => getMarketplaceSimulatedCouriers($context),
            'is_live' => false,
            'warning' => getMarketplaceShippingProviderLabel() . ' live gagal, ongkir sementara memakai simulasi. Detail error sudah dicatat di logs/shipping-debug.log.',
            'status' => $status,
            'status_label' => $statusLabel,
            'debug_message' => $message,
        ];
    }
}

function getMarketplaceCouriers(?array $context = null): array
{
    return getMarketplaceShippingOptions($context)['couriers'];
}

function findMarketplaceCourier(string $courierId, ?array $context = null): ?array
{
    foreach (getMarketplaceCouriers($context) as $courier) {
        if ($courier['id'] === $courierId) {
            return $courier;
        }
    }

    return null;
}

function getMarketplaceCartState(): array
{
    if (!isset($_SESSION['marketplace_cart']) || !is_array($_SESSION['marketplace_cart'])) {
        $_SESSION['marketplace_cart'] = [];
    }

    return $_SESSION['marketplace_cart'];
}

function saveMarketplaceCartState(array $cart): void
{
    $_SESSION['marketplace_cart'] = $cart;
}

function getMarketplaceCartCount(): int
{
    $count = 0;
    foreach (getMarketplaceCartState() as $item) {
        $count += (int) ($item['qty'] ?? 0);
    }

    return $count;
}

function userHasPrescriptionForProduct(PDO $pdo, int $userId, array $product): bool
{
    if (empty($product['requires_prescription'])) {
        return true;
    }

    $productDbId = (int) ($product['db_id'] ?? 0);
    if ($productDbId <= 0) {
        return false;
    }

    $stmt = $pdo->prepare(
        "SELECT ir.idItemResep
         FROM ItemResep ir
         INNER JOIN Resep r ON r.idResep = ir.idResep
         INNER JOIN SesiKonsultasi sk ON sk.idKonsultasi = r.idKonsultasi
         INNER JOIN Pasien p ON p.idPasien = sk.idPasien
         WHERE p.id_user = :user_id
           AND ir.idObat = :id_obat
         LIMIT 1"
    );
    $stmt->execute([
        ':user_id' => $userId,
        ':id_obat' => $productDbId,
    ]);

    return (bool) $stmt->fetchColumn();
}

function assertMarketplaceProductPurchasable(PDO $pdo, int $userId, array $product, int $qty): void
{
    if ($qty <= 0) {
        throw new RuntimeException('Jumlah pembelian harus lebih dari 0.');
    }

    if ((int) ($product['stock'] ?? 0) <= 0) {
        throw new RuntimeException('Produk sedang habis dan belum bisa dibeli.');
    }

    if ($qty > (int) $product['stock']) {
        throw new RuntimeException('Jumlah pembelian melebihi stok yang tersedia.');
    }

    if (!userHasPrescriptionForProduct($pdo, $userId, $product)) {
        throw new RuntimeException('Obat ini memerlukan resep dokter dan tidak bisa dibeli tanpa resep yang valid.');
    }
}

function addMarketplaceCartItem(PDO $pdo, int $userId, int $productId, int $qty): array
{
    $product = findMarketplaceProduct($pdo, $productId);
    if (!$product) {
        throw new RuntimeException('Produk tidak ditemukan.');
    }

    $cart = getMarketplaceCartState();
    $currentQty = (int) ($cart[$productId]['qty'] ?? 0);
    $newQty = $currentQty + $qty;

    assertMarketplaceProductPurchasable($pdo, $userId, $product, $newQty);

    $cart[$productId] = [
        'qty' => $newQty,
        'updated_at' => date('c'),
    ];
    saveMarketplaceCartState($cart);

    return [
        'product' => $product,
        'qty' => $newQty,
        'cart_count' => getMarketplaceCartCount(),
    ];
}

function updateMarketplaceCartItem(PDO $pdo, int $userId, int $productId, int $qty): array
{
    $product = findMarketplaceProduct($pdo, $productId);
    if (!$product) {
        throw new RuntimeException('Produk tidak ditemukan.');
    }

    $cart = getMarketplaceCartState();

    if ($qty <= 0) {
        unset($cart[$productId]);
        saveMarketplaceCartState($cart);

        return [
            'removed' => true,
            'cart_count' => getMarketplaceCartCount(),
        ];
    }

    assertMarketplaceProductPurchasable($pdo, $userId, $product, $qty);

    $cart[$productId] = [
        'qty' => $qty,
        'updated_at' => date('c'),
    ];
    saveMarketplaceCartState($cart);

    return [
        'removed' => false,
        'product' => $product,
        'qty' => $qty,
        'cart_count' => getMarketplaceCartCount(),
    ];
}

function removeMarketplaceCartItem(int $productId): array
{
    $cart = getMarketplaceCartState();
    unset($cart[$productId]);
    saveMarketplaceCartState($cart);

    return [
        'cart_count' => getMarketplaceCartCount(),
    ];
}

function getMarketplaceCartItems(PDO $pdo, int $userId): array
{
    $products = [];
    foreach (getMarketplaceProducts($pdo) as $product) {
        $products[(int) $product['id']] = $product;
    }

    $items = [];
    foreach (getMarketplaceCartState() as $productId => $cartItem) {
        $productId = (int) $productId;
        if (!isset($products[$productId])) {
            continue;
        }

        $product = $products[$productId];
        $qty = (int) ($cartItem['qty'] ?? 0);
        if ($qty <= 0) {
            continue;
        }

        $items[] = [
            'id' => $product['id'],
            'db_id' => $product['db_id'],
            'name' => $product['name'],
            'category' => $product['category'],
            'price' => $product['price'],
            'qty' => min($qty, (int) $product['stock']),
            'stock' => (int) $product['stock'],
            'icon' => $product['icon'],
            'img' => $product['img'],
            'badge' => $product['badge'],
            'weight' => (int) ($product['weight'] ?? 0),
            'requires_prescription' => (bool) $product['requires_prescription'],
            'has_valid_prescription' => userHasPrescriptionForProduct($pdo, $userId, $product),
            'subtotal' => $product['price'] * min($qty, (int) $product['stock']),
        ];
    }

    return $items;
}

function getMarketplaceCartSummary(PDO $pdo, int $userId): array
{
    $items = getMarketplaceCartItems($pdo, $userId);
    $totalItems = 0;
    $subtotal = 0;

    foreach ($items as $item) {
        $totalItems += (int) $item['qty'];
        $subtotal += (int) $item['subtotal'];
    }

    return [
        'items' => $items,
        'total_items' => $totalItems,
        'subtotal' => $subtotal,
    ];
}

function getMarketplaceUserAddress(PDO $pdo, int $userId): array
{
    ensureMarketplacePatientSchema($pdo);

    $stmt = $pdo->prepare(
        "SELECT
            u.nama,
            u.phone,
            p.alamat,
            p.address_label,
            p.recipient_name,
            p.village,
            p.district,
            p.city,
            p.province,
            p.postal_code,
            p.address_notes
         FROM users u
         LEFT JOIN Pasien p ON p.id_user = u.id
         WHERE u.id = :id
         LIMIT 1"
    );
    $stmt->execute([':id' => $userId]);
    $row = $stmt->fetch() ?: [];

    $recipient = trim((string) ($row['recipient_name'] ?? '')) ?: ($row['nama'] ?? 'Pasien CareSync');
    $phone = trim((string) ($row['phone'] ?? '')) ?: '-';
    $addressLine = trim((string) ($row['alamat'] ?? ''));
    $village = trim((string) ($row['village'] ?? ''));
    $district = trim((string) ($row['district'] ?? ''));
    $city = trim((string) ($row['city'] ?? ''));
    $province = trim((string) ($row['province'] ?? ''));
    $postalCode = trim((string) ($row['postal_code'] ?? ''));
    $note = trim((string) ($row['address_notes'] ?? ''));

    $addressParts = array_values(array_filter([$addressLine, $village, $district, $city, $province, $postalCode]));
    $fullAddress = $addressParts ? implode(', ', $addressParts) : 'Alamat belum diatur. Silakan lengkapi alamat pada profil pasien Anda.';
    $zone = getMarketplaceDestinationZone($fullAddress);

    return [
        'label' => trim((string) ($row['address_label'] ?? '')) ?: 'Rumah',
        'name' => $recipient,
        'phone' => $phone,
        'address' => $fullAddress,
        'address_line' => $addressLine,
        'village' => $village,
        'district' => $district,
        'city' => $city,
        'province' => $province,
        'postal_code' => $postalCode,
        'zone' => $zone,
        'zone_label' => getMarketplaceZoneLabel($zone),
        'note' => $note !== '' ? $note : 'Pastikan nama penerima, nomor telepon, dan kode pos sudah benar agar kurir mudah menemukan lokasi.',
    ];
}

function getMarketplaceMissingAddressFields(array $address): array
{
    $required = [
        'address_line' => 'alamat lengkap',
        'province' => 'provinsi',
        'city' => 'kota/kabupaten',
        'district' => 'kecamatan',
        'postal_code' => 'kode pos',
    ];

    $missing = [];
    foreach ($required as $key => $label) {
        if (trim((string) ($address[$key] ?? '')) === '') {
            $missing[] = $label;
        }
    }

    return $missing;
}

function assertMarketplaceAddressReady(array $address): void
{
    $missing = getMarketplaceMissingAddressFields($address);
    if (!$missing) {
        return;
    }

    throw new RuntimeException(
        'Alamat pengiriman belum lengkap. Lengkapi ' . implode(', ', $missing) . ' di profil pasien terlebih dahulu.'
    );
}

function prepareMarketplaceCheckout(PDO $pdo, int $userId, array $productIds): array
{
    $cartItems = getMarketplaceCartItems($pdo, $userId);
    $cartMap = [];
    foreach ($cartItems as $item) {
        $cartMap[(int) $item['id']] = $item;
    }

    $selected = [];
    foreach ($productIds as $productId) {
        $productId = (int) $productId;
        if ($productId <= 0 || !isset($cartMap[$productId])) {
            continue;
        }

        $selected[$productId] = $cartMap[$productId];
    }

    if (!$selected) {
        throw new RuntimeException('Pilih minimal satu produk untuk checkout.');
    }

    foreach ($selected as $item) {
        if ($item['requires_prescription'] && !$item['has_valid_prescription']) {
            throw new RuntimeException('Masih ada obat resep tanpa resep dokter yang valid di pilihan checkout Anda.');
        }
    }

    $_SESSION['marketplace_checkout'] = [
        'user_id' => $userId,
        'product_ids' => array_keys($selected),
        'prepared_at' => date('c'),
        'shipping' => null,
    ];
    unset($_SESSION['marketplace_payment']);

    return getMarketplaceCheckoutContext($pdo, $userId) ?? [];
}

function getMarketplaceCheckoutContext(PDO $pdo, int $userId): ?array
{
    $checkout = $_SESSION['marketplace_checkout'] ?? null;
    if (!is_array($checkout) || (int) ($checkout['user_id'] ?? 0) !== $userId) {
        return null;
    }

    $cartItems = getMarketplaceCartItems($pdo, $userId);
    $cartMap = [];
    foreach ($cartItems as $item) {
        $cartMap[(int) $item['id']] = $item;
    }

    $items = [];
    foreach (($checkout['product_ids'] ?? []) as $productId) {
        $productId = (int) $productId;
        if (isset($cartMap[$productId])) {
            $items[] = $cartMap[$productId];
        }
    }

    if (!$items) {
        return null;
    }

    $subtotal = 0;
    $totalWeight = 0;
    foreach ($items as $item) {
        $subtotal += (int) $item['subtotal'];
        $totalWeight += ((int) $item['weight'] * (int) $item['qty']);
    }

    $shipping = $checkout['shipping'] ?? null;
    $serviceFee = 2000;
    $shippingPrice = (int) ($shipping['price'] ?? 0);
    $address = getMarketplaceUserAddress($pdo, $userId);
    $missingAddressFields = getMarketplaceMissingAddressFields($address);

    return [
        'items' => $items,
        'subtotal' => $subtotal,
        'total_weight' => $totalWeight,
        'service_fee' => $serviceFee,
        'shipping' => $shipping,
        'shipping_price' => $shippingPrice,
        'grand_total' => $subtotal + $serviceFee + $shippingPrice,
        'address' => $address,
        'missing_address_fields' => $missingAddressFields,
        'prepared_at' => $checkout['prepared_at'] ?? null,
    ];
}

function setMarketplaceCheckoutShipping(PDO $pdo, int $userId, string $courierId): array
{
    $context = getMarketplaceCheckoutContext($pdo, $userId);
    if (!$context) {
        throw new RuntimeException('Tidak ada data checkout aktif. Silakan pilih ulang produk dari keranjang.');
    }

    assertMarketplaceAddressReady((array) ($context['address'] ?? []));

    $courier = findMarketplaceCourier($courierId, $context);
    if (!$courier) {
        throw new RuntimeException('Metode pengiriman tidak ditemukan.');
    }

    $_SESSION['marketplace_checkout']['shipping'] = $courier;
    unset($_SESSION['marketplace_payment']);

    return getMarketplaceCheckoutContext($pdo, $userId) ?? [];
}

function getMarketplaceOrderCode(int $orderId, ?string $createdAt = null): string
{
    $date = $createdAt ? date('Ymd', strtotime($createdAt)) : date('Ymd');
    return 'APT-' . $date . '-' . str_pad((string) $orderId, 5, '0', STR_PAD_LEFT);
}

function getMarketplaceTrackingStatusMeta(): array
{
    return [
        'Menunggu Pembayaran' => ['label' => 'Menunggu Pembayaran', 'location' => 'Payment Gateway', 'detail' => 'Pesanan menunggu penyelesaian pembayaran digital.'],
        'Dikemas' => ['label' => 'Dikemas', 'location' => 'CareSync Pharmacy', 'detail' => 'Pesanan sedang dikemas oleh apoteker.'],
        'Menunggu Kurir' => ['label' => 'Menunggu Kurir', 'location' => 'CareSync Pharmacy', 'detail' => 'Pesanan selesai dikemas dan menunggu pickup kurir.'],
        'Dikirim' => ['label' => 'Dikirim', 'location' => 'Hub Ekspedisi', 'detail' => 'Pesanan sudah diserahkan ke kurir dan sedang dalam perjalanan.'],
        'Terkirim' => ['label' => 'Terkirim', 'location' => 'Alamat Tujuan', 'detail' => 'Pesanan telah diterima di alamat tujuan.'],
        'Selesai' => ['label' => 'Selesai', 'location' => 'CareSync', 'detail' => 'Pesanan selesai dan transaksi ditutup.'],
        'Dibatalkan' => ['label' => 'Dibatalkan', 'location' => 'CareSync', 'detail' => 'Pesanan dibatalkan.'],
    ];
}

function appendMarketplaceTrackingHistory(PDO $pdo, int $shippingId, string $status): void
{
    if ($shippingId <= 0) {
        return;
    }

    $meta = getMarketplaceTrackingStatusMeta()[$status] ?? [
        'location' => 'CareSync',
        'detail' => $status,
    ];

    $stmt = $pdo->prepare(
        'INSERT INTO RiwayatPelacakan (idPengiriman, waktu, lokasi, detailStatus)
         VALUES (:idPengiriman, CURRENT_TIMESTAMP, :lokasi, :detailStatus)'
    );
    $stmt->execute([
        ':idPengiriman' => $shippingId,
        ':lokasi' => $meta['location'],
        ':detailStatus' => $meta['detail'],
    ]);
}

function buildMarketplacePaymentSignature(array $context, string $method): string
{
    $lineItems = [];
    foreach ($context['items'] as $item) {
        $lineItems[] = [
            'id' => (int) $item['id'],
            'qty' => (int) $item['qty'],
            'subtotal' => (int) $item['subtotal'],
        ];
    }

    return sha1(json_encode([
        'items' => $lineItems,
        'shipping' => $context['shipping']['id'] ?? '',
        'shipping_price' => (int) ($context['shipping_price'] ?? 0),
        'grand_total' => (int) ($context['grand_total'] ?? 0),
        'method' => $method,
    ]));
}

function buildMarketplaceGatewayPayload(string $method, int $grandTotal): array
{
    $methodMeta = getMarketplacePaymentMethods()[$method] ?? ['name' => $method, 'type' => 'manual'];
    $expiresAt = (new DateTimeImmutable('+15 minutes'))->format('Y-m-d H:i:s');

    $payload = [
        'method' => $method,
        'method_name' => $methodMeta['name'],
        'type' => $methodMeta['type'],
        'expires_at' => $expiresAt,
        'amount' => $grandTotal,
    ];

    if ($methodMeta['type'] === 'qris') {
        $payload['reference'] = 'QRIS-' . strtoupper(substr(sha1((string) microtime(true)), 0, 10));
        $payload['qr_url'] = 'https://upload.wikimedia.org/wikipedia/commons/d/d0/QR_code_for_mobile_English_Wikipedia.svg';
        $payload['account_name'] = 'CareSync QRIS';
        $payload['account_number'] = $payload['reference'];
        $payload['instructions'] = 'Scan QRIS menggunakan aplikasi bank atau e-wallet Anda.';
        return $payload;
    }

    if ($methodMeta['type'] === 'va') {
        $payload['reference'] = 'VA-' . strtoupper(substr(sha1((string) microtime(true)), 0, 8));
        $payload['va_number'] = '8800' . random_int(100000000, 999999999);
        $payload['account_name'] = $methodMeta['name'];
        $payload['account_number'] = $payload['va_number'];
        $payload['instructions'] = 'Transfer ke nomor virtual account sesuai bank yang dipilih sebelum batas waktu pembayaran.';
        return $payload;
    }

    $payload['reference'] = 'EWALLET-' . strtoupper(substr(sha1((string) microtime(true)), 0, 8));
    $payload['deeplink_label'] = 'Konfirmasi pembayaran di aplikasi ' . $methodMeta['name'];
    $payload['account_name'] = $methodMeta['name'];
    $payload['account_number'] = $payload['reference'];
    $payload['instructions'] = 'Buka aplikasi ' . $methodMeta['name'] . ' lalu selesaikan otorisasi pembayaran digital.';

    return $payload;
}

function getMarketplacePendingOrderForSession(PDO $pdo, int $userId, string $signature, string $method): ?array
{
    $state = $_SESSION['marketplace_payment'] ?? null;
    if (!is_array($state)) {
        return null;
    }

    $orderId = (int) ($state['order_id'] ?? 0);
    if ($orderId <= 0 || ($state['signature'] ?? '') !== $signature || ($state['method'] ?? '') !== $method) {
        return null;
    }

    $order = getMarketplaceOrderDetail($pdo, $userId, $orderId);
    if (!$order || ($order['payment_status'] ?? '') !== 'Pending') {
        return null;
    }

    return [
        'order' => $order,
        'gateway' => $state['gateway'] ?? null,
    ];
}

function createMarketplacePendingPayment(PDO $pdo, int $userId, string $method): array
{
    ensureMarketplacePaymentSchema($pdo);
    $context = getMarketplaceCheckoutContext($pdo, $userId);
    if (!$context) {
        throw new RuntimeException('Checkout tidak ditemukan. Silakan pilih produk terlebih dahulu.');
    }

    if (empty($context['shipping'])) {
        throw new RuntimeException('Metode pengiriman belum dipilih.');
    }

    $methods = getMarketplacePaymentMethods();
    if (!isset($methods[$method])) {
        throw new RuntimeException('Metode pembayaran tidak valid.');
    }

    foreach ($context['items'] as $item) {
        $product = findMarketplaceProduct($pdo, (int) $item['id']);
        if (!$product) {
            throw new RuntimeException('Ada produk checkout yang tidak lagi tersedia.');
        }
        assertMarketplaceProductPurchasable($pdo, $userId, $product, (int) $item['qty']);
    }

    $signature = buildMarketplacePaymentSignature($context, $method);
    $existing = getMarketplacePendingOrderForSession($pdo, $userId, $signature, $method);
    if ($existing) {
        return [
            'order' => $existing['order'],
            'gateway' => $existing['gateway'],
        ];
    }

    $patientId = ensurePatientProfile($pdo, $userId);

    $gateway = buildMarketplaceGatewayPayload($method, (int) $context['grand_total']);

    try {
        $pdo->beginTransaction();

        $insertOrder = $pdo->prepare(
            "INSERT INTO Pemesanan (idPasien, totalHarga, statusPesanan, tanggal)
             VALUES (:idPasien, :totalHarga, 'Menunggu Pembayaran', CURRENT_TIMESTAMP)
             RETURNING idPemesanan, tanggal"
        );
        $insertOrder->execute([
            ':idPasien' => $patientId,
            ':totalHarga' => $context['grand_total'],
        ]);
        $orderRow = $insertOrder->fetch();
        $orderId = (int) ($orderRow['idpemesanan'] ?? 0);
        $createdAt = $orderRow['tanggal'] ?? date('Y-m-d H:i:s');

        $insertItem = $pdo->prepare(
            'INSERT INTO ItemPemesanan (idPemesanan, idObat, jumlah, subtotal)
             VALUES (:idPemesanan, :idObat, :jumlah, :subtotal)'
        );
        foreach ($context['items'] as $item) {
            $insertItem->execute([
                ':idPemesanan' => $orderId,
                ':idObat' => $item['db_id'],
                ':jumlah' => $item['qty'],
                ':subtotal' => $item['subtotal'],
            ]);
        }

        $resi = null;
        $insertShipping = $pdo->prepare(
            "INSERT INTO Pengiriman (idPemesanan, kurir, nomorResi, statusPengiriman)
             VALUES (:idPemesanan, :kurir, :nomorResi, 'Menunggu Pembayaran')
             RETURNING idPengiriman"
        );
        $insertShipping->execute([
            ':idPemesanan' => $orderId,
            ':kurir' => $context['shipping']['name'],
            ':nomorResi' => $resi,
        ]);
        $shippingId = (int) $insertShipping->fetchColumn();

        $insertTracking = $pdo->prepare(
            'INSERT INTO RiwayatPelacakan (idPengiriman, waktu, lokasi, detailStatus)
             VALUES (:idPengiriman, CURRENT_TIMESTAMP, :lokasi, :detailStatus)'
        );
        $insertTracking->execute([
            ':idPengiriman' => $shippingId,
            ':lokasi' => 'CareSync Pharmacy',
            ':detailStatus' => 'Pesanan dibuat.',
        ]);

        $insertPayment = $pdo->prepare(
            "INSERT INTO Pembayaran (
                idPemesanan, metode, status, waktuBayar, payment_code, payment_channel, payment_type,
                payment_reference, payment_account_name, payment_account_number, payment_instructions, payment_payload
             )
             VALUES (
                :idPemesanan, :metode, 'Pending', NULL, :payment_code, :payment_channel, :payment_type,
                :payment_reference, :payment_account_name, :payment_account_number, :payment_instructions, CAST(:payment_payload AS JSONB)
             )"
        );
        $insertPayment->execute([
            ':idPemesanan' => $orderId,
            ':metode' => $methods[$method]['name'],
            ':payment_code' => $method,
            ':payment_channel' => $methods[$method]['name'],
            ':payment_type' => $methods[$method]['type'],
            ':payment_reference' => $gateway['reference'] ?? null,
            ':payment_account_name' => $gateway['account_name'] ?? $methods[$method]['name'],
            ':payment_account_number' => $gateway['account_number'] ?? ($gateway['va_number'] ?? null),
            ':payment_instructions' => $gateway['instructions'] ?? null,
            ':payment_payload' => json_encode($gateway, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);

        $insertNotification = $pdo->prepare(
            'INSERT INTO Notifikasi (id_user, pesan) VALUES (:id_user, :pesan)'
        );
        $insertNotification->execute([
            ':id_user' => $userId,
            ':pesan' => sprintf(
                'Transaksi apotek %s berhasil dibuat dan menunggu pembayaran digital.',
                getMarketplaceOrderCode($orderId, $createdAt)
            ),
        ]);

        $pdo->commit();

        $_SESSION['marketplace_payment'] = [
            'order_id' => $orderId,
            'signature' => $signature,
            'method' => $method,
            'gateway' => $gateway,
        ];

        return [
            'order' => getMarketplaceOrderDetail($pdo, $userId, $orderId),
            'gateway' => $gateway,
        ];
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $e;
    }
}

function getMarketplacePendingPaymentByOrder(PDO $pdo, int $userId, int $orderId): ?array
{
    ensureMarketplacePaymentSchema($pdo);

    $order = getMarketplaceOrderDetail($pdo, $userId, $orderId);
    if (!$order || ($order['payment_status'] ?? '') !== 'Pending') {
        return null;
    }

    $gateway = is_array($order['payment_payload'] ?? null) ? $order['payment_payload'] : [];
    if (!$gateway) {
        $gateway = buildMarketplaceGatewayPayload((string) ($order['payment_code'] ?? 'QRIS'), (int) ($order['total'] ?? 0));
    }

    $_SESSION['marketplace_payment'] = [
        'order_id' => $order['id'],
        'signature' => $_SESSION['marketplace_payment']['signature'] ?? '',
        'method' => $order['payment_code'] ?: 'QRIS',
        'gateway' => $gateway,
    ];

    return [
        'order' => $order,
        'gateway' => $gateway,
    ];
}

function clearMarketplaceCheckoutSessions(): void
{
    unset($_SESSION['marketplace_checkout'], $_SESSION['marketplace_payment']);
}

function removePurchasedItemsFromMarketplaceCart(array $orderItems): void
{
    $cart = getMarketplaceCartState();
    foreach ($orderItems as $item) {
        unset($cart[(int) $item['product_id']]);
    }
    saveMarketplaceCartState($cart);
}

function confirmMarketplacePayment(PDO $pdo, int $userId, int $orderId): array
{
    $order = getMarketplaceOrderDetail($pdo, $userId, $orderId);
    if (!$order) {
        throw new RuntimeException('Order apotek tidak ditemukan.');
    }

    if ($order['payment_status'] === 'Berhasil') {
        return $order;
    }

    if ($order['payment_status'] !== 'Pending') {
        throw new RuntimeException('Pembayaran tidak dapat dikonfirmasi karena statusnya tidak lagi pending.');
    }

    try {
        $pdo->beginTransaction();

        $stockStmt = $pdo->prepare('SELECT stok FROM Obat WHERE idObat = :idObat FOR UPDATE');
        $updateStockStmt = $pdo->prepare('UPDATE Obat SET stok = stok - :jumlah WHERE idObat = :idObat');

        foreach ($order['items'] as $item) {
            $stockStmt->execute([':idObat' => $item['db_id']]);
            $stock = (int) $stockStmt->fetchColumn();

            if ($stock < (int) $item['qty']) {
                throw new RuntimeException('Stok untuk ' . $item['name'] . ' sudah tidak mencukupi.');
            }

            $updateStockStmt->execute([
                ':idObat' => $item['db_id'],
                ':jumlah' => $item['qty'],
            ]);
        }

        $paymentMethod = $_SESSION['marketplace_payment']['method'] ?? ($order['payment_code'] ?? 'QRIS');
        $updatePayment = $pdo->prepare(
            "UPDATE Pembayaran
             SET status = 'Berhasil', metode = :metode, waktuBayar = CURRENT_TIMESTAMP
             WHERE idPemesanan = :idPemesanan"
        );
        $updatePayment->execute([
            ':idPemesanan' => $orderId,
            ':metode' => getMarketplacePaymentMethodLabel($paymentMethod),
        ]);

        $updateOrder = $pdo->prepare(
            "UPDATE Pemesanan
             SET statusPesanan = 'Dikemas'
             WHERE idPemesanan = :idPemesanan"
        );
        $updateOrder->execute([':idPemesanan' => $orderId]);

        $resi = $order['awb'] ?: ('CSX-' . random_int(100000000, 999999999));
        $updateShipping = $pdo->prepare(
            "UPDATE Pengiriman
             SET nomorResi = :nomorResi, statusPengiriman = 'Dikemas'
             WHERE idPemesanan = :idPemesanan"
        );
        $updateShipping->execute([
            ':nomorResi' => $resi,
            ':idPemesanan' => $orderId,
        ]);

        appendMarketplaceTrackingHistory($pdo, (int) $order['shipping_id'], 'Menunggu Pembayaran');
        appendMarketplaceTrackingHistory($pdo, (int) $order['shipping_id'], 'Dikemas');

        $insertNotification = $pdo->prepare(
            'INSERT INTO Notifikasi (id_user, pesan) VALUES (:id_user, :pesan)'
        );
        $insertNotification->execute([
            ':id_user' => $userId,
            ':pesan' => sprintf(
                'Pembayaran untuk order apotek %s berhasil. Pesanan sedang diproses apotek.',
                getMarketplaceOrderCode($orderId, $order['created_at'])
            ),
        ]);

        $pdo->commit();

        removePurchasedItemsFromMarketplaceCart($order['items']);
        clearMarketplaceCheckoutSessions();

        return getMarketplaceOrderDetail($pdo, $userId, $orderId) ?? $order;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $e;
    }
}

function getMarketplaceOrderDetail(PDO $pdo, int $userId, int $orderId): ?array
{
    ensureMarketplacePatientSchema($pdo);
    ensureMarketplacePaymentSchema($pdo);

    $stmt = $pdo->prepare(
        "SELECT
            pm.idPemesanan,
            pm.totalHarga,
            pm.statusPesanan,
            pm.tanggal,
            py.status AS payment_status,
            py.metode AS payment_method,
            py.waktuBayar,
            py.payment_code,
            py.payment_channel,
            py.payment_type,
            py.payment_reference,
            py.payment_account_name,
            py.payment_account_number,
            py.payment_instructions,
            py.payment_payload,
            pg.idPengiriman,
            pg.kurir,
            pg.nomorResi,
            pg.statusPengiriman,
            u.nama AS patient_name,
            u.phone AS patient_phone,
            p.alamat AS patient_address,
            p.city AS patient_city,
            p.province AS patient_province,
            p.postal_code AS patient_postal_code,
            p.address_label AS patient_address_label
         FROM Pemesanan pm
         INNER JOIN Pasien p ON p.idPasien = pm.idPasien
         INNER JOIN users u ON u.id = p.id_user
         LEFT JOIN Pembayaran py ON py.idPemesanan = pm.idPemesanan
         LEFT JOIN Pengiriman pg ON pg.idPemesanan = pm.idPemesanan
         WHERE pm.idPemesanan = :idPemesanan
           AND u.id = :userId
         LIMIT 1"
    );
    $stmt->execute([
        ':idPemesanan' => $orderId,
        ':userId' => $userId,
    ]);
    $order = $stmt->fetch();

    if (!$order) {
        return null;
    }

    $itemStmt = $pdo->prepare(
        "SELECT
            ip.idObat,
            ip.jumlah,
            ip.subtotal,
            o.nama,
            o.harga
         FROM ItemPemesanan ip
         INNER JOIN Obat o ON o.idObat = ip.idObat
         WHERE ip.idPemesanan = :idPemesanan
         ORDER BY ip.idItemPemesanan ASC"
    );
    $itemStmt->execute([':idPemesanan' => $orderId]);

    $productMap = [];
    foreach (getMarketplaceProducts($pdo) as $product) {
        $productMap[(int) $product['db_id']] = $product;
    }

    $items = [];
    foreach ($itemStmt->fetchAll() as $row) {
        $dbId = (int) $row['idobat'];
        $product = $productMap[$dbId] ?? null;
        $items[] = [
            'db_id' => $dbId,
            'product_id' => $product['id'] ?? 0,
            'name' => $row['nama'],
            'price' => (int) $row['harga'],
            'qty' => (int) $row['jumlah'],
            'subtotal' => (int) $row['subtotal'],
            'icon' => $product['icon'] ?? 'fa-pills',
            'img' => $product['img'] ?? '',
            'category' => $product['category'] ?? 'Obat',
        ];
    }

    $trackingStmt = $pdo->prepare(
        "SELECT waktu, lokasi, detailStatus
         FROM RiwayatPelacakan
         WHERE idPengiriman = :idPengiriman
         ORDER BY waktu DESC, idPelacakan DESC"
    );
    $trackingHistory = [];
    if (!empty($order['idpengiriman'])) {
        $trackingStmt->execute([':idPengiriman' => $order['idpengiriman']]);
        foreach ($trackingStmt->fetchAll() as $row) {
            $trackingHistory[] = [
                'status' => $row['detailstatus'],
                'date' => date('d M Y', strtotime($row['waktu'])),
                'time' => date('H:i', strtotime($row['waktu'])),
                'location' => $row['lokasi'],
            ];
        }
    }

    $addressParts = array_values(array_filter([
        $order['patient_address'] ?? '',
        $order['patient_city'] ?? '',
        $order['patient_province'] ?? '',
        $order['patient_postal_code'] ?? '',
    ]));
    $fullAddress = $addressParts ? implode(', ', $addressParts) : 'Alamat belum diatur.';
    $paymentPayload = [];
    if (!empty($order['payment_payload'])) {
        $decoded = json_decode((string) $order['payment_payload'], true);
        if (is_array($decoded)) {
            $paymentPayload = $decoded;
        }
    }

    return [
        'id' => (int) $order['idpemesanan'],
        'order_code' => getMarketplaceOrderCode((int) $order['idpemesanan'], $order['tanggal']),
        'created_at' => $order['tanggal'],
        'date_label' => date('d F Y, H:i', strtotime($order['tanggal'])) . ' WIB',
        'status' => $order['statuspesanan'],
        'payment_status' => $order['payment_status'] ?? 'Pending',
        'payment_method' => $order['payment_method'] ?? '',
        'payment_code' => $order['payment_code'] ?? '',
        'payment_channel' => $order['payment_channel'] ?? ($order['payment_method'] ?? ''),
        'payment_type' => $order['payment_type'] ?? '',
        'payment_reference' => $order['payment_reference'] ?? '',
        'payment_account_name' => $order['payment_account_name'] ?? '',
        'payment_account_number' => $order['payment_account_number'] ?? '',
        'payment_instructions' => $order['payment_instructions'] ?? '',
        'payment_payload' => $paymentPayload,
        'paid_at' => $order['waktubayar'] ?? null,
        'courier' => $order['kurir'] ?? '-',
        'awb' => $order['nomorresi'] ?? '-',
        'shipping_status' => $order['statuspengiriman'] ?? 'Menunggu Pembayaran',
        'shipping_id' => (int) ($order['idpengiriman'] ?? 0),
        'receiver' => $order['patient_name'] ?? 'Pasien CareSync',
        'phone' => $order['patient_phone'] ?: '-',
        'address' => $fullAddress,
        'address_label' => $order['patient_address_label'] ?? 'Rumah',
        'total' => (int) $order['totalharga'],
        'items' => $items,
        'tracking_history' => $trackingHistory,
    ];
}

function userCanManageMarketplace(array $user): bool
{
    $role = strtolower((string) ($user['role'] ?? ''));
    return $role !== '' && $role !== 'user' && $role !== 'pasien';
}

function updateMarketplaceProductStock(PDO $pdo, int $productId, int $stock, ?int $price = null): array
{
    $product = findMarketplaceProduct($pdo, $productId);
    if (!$product || (int) ($product['db_id'] ?? 0) <= 0) {
        throw new RuntimeException('Produk tidak ditemukan.');
    }

    if ($stock < 0) {
        throw new RuntimeException('Stok tidak boleh minus.');
    }

    $params = [
        ':idObat' => (int) $product['db_id'],
        ':stok' => $stock,
    ];

    if ($price !== null) {
        if ($price < 0) {
            throw new RuntimeException('Harga tidak valid.');
        }

        $stmt = $pdo->prepare('UPDATE Obat SET stok = :stok, harga = :harga WHERE idObat = :idObat');
        $params[':harga'] = $price;
    } else {
        $stmt = $pdo->prepare('UPDATE Obat SET stok = :stok WHERE idObat = :idObat');
    }

    $stmt->execute($params);

    $updated = findMarketplaceProduct($pdo, $productId);
    if (!$updated) {
        throw new RuntimeException('Gagal memuat ulang produk.');
    }

    return $updated;
}

function getMarketplaceOrdersForManagement(PDO $pdo): array
{
    $stmt = $pdo->query(
        "SELECT
            pm.idPemesanan,
            pm.tanggal,
            pm.statusPesanan,
            pm.totalHarga,
            py.status AS payment_status,
            py.metode AS payment_method,
            pg.statusPengiriman,
            pg.kurir,
            pg.nomorResi,
            u.nama AS patient_name
         FROM Pemesanan pm
         INNER JOIN Pasien p ON p.idPasien = pm.idPasien
         INNER JOIN users u ON u.id = p.id_user
         LEFT JOIN Pembayaran py ON py.idPemesanan = pm.idPemesanan
         LEFT JOIN Pengiriman pg ON pg.idPemesanan = pm.idPemesanan
         ORDER BY pm.tanggal DESC, pm.idPemesanan DESC"
    );

    $orders = [];
    foreach ($stmt->fetchAll() as $row) {
        $orders[] = [
            'id' => (int) $row['idpemesanan'],
            'order_code' => getMarketplaceOrderCode((int) $row['idpemesanan'], $row['tanggal']),
            'created_at' => $row['tanggal'],
            'date_label' => date('d M Y H:i', strtotime($row['tanggal'])) . ' WIB',
            'status' => $row['statuspesanan'] ?? 'Menunggu Pembayaran',
            'shipping_status' => $row['statuspengiriman'] ?? 'Menunggu Pembayaran',
            'payment_status' => $row['payment_status'] ?? 'Pending',
            'payment_method' => $row['payment_method'] ?? '-',
            'courier' => $row['kurir'] ?? '-',
            'awb' => $row['nomorresi'] ?? '-',
            'patient_name' => $row['patient_name'] ?? 'Pasien CareSync',
            'total' => (int) ($row['totalharga'] ?? 0),
        ];
    }

    return $orders;
}

function getMarketplaceAllowedNextStatuses(array $order): array
{
    $current = (string) ($order['shipping_status'] ?? '');

    $map = [
        'Dikemas' => ['Menunggu Kurir', 'Dikirim'],
        'Menunggu Kurir' => ['Dikirim'],
        'Dikirim' => ['Terkirim'],
        'Terkirim' => ['Selesai'],
        'Selesai' => [],
        'Dibatalkan' => [],
        'Menunggu Pembayaran' => [],
        '' => [],
    ];

    return $map[$current] ?? [];
}

function updateMarketplaceOrderShippingStatus(PDO $pdo, int $orderId, string $newStatus): array
{
    $metaMap = getMarketplaceTrackingStatusMeta();
    if (!isset($metaMap[$newStatus])) {
        throw new RuntimeException('Status pengiriman tidak valid.');
    }

    $stmt = $pdo->prepare(
        "SELECT pm.idPemesanan, pm.tanggal, pm.statusPesanan, pg.idPengiriman, pg.statusPengiriman, pg.nomorResi, p.id_user
         FROM Pemesanan pm
         INNER JOIN Pasien p ON p.idPasien = pm.idPasien
         LEFT JOIN Pengiriman pg ON pg.idPemesanan = pm.idPemesanan
         WHERE pm.idPemesanan = :idPemesanan
         LIMIT 1"
    );
    $stmt->execute([':idPemesanan' => $orderId]);
    $row = $stmt->fetch();

    if (!$row) {
        throw new RuntimeException('Pesanan tidak ditemukan.');
    }

    $currentOrder = [
        'shipping_status' => $row['statuspengiriman'] ?? '',
    ];
    $allowed = getMarketplaceAllowedNextStatuses($currentOrder);
    if (!in_array($newStatus, $allowed, true)) {
        throw new RuntimeException('Perubahan status tidak sesuai alur pengiriman.');
    }

    try {
        $pdo->beginTransaction();

        $orderStatus = $newStatus === 'Selesai' ? 'Selesai' : 'Diproses Apotek';
        $updateOrder = $pdo->prepare(
            'UPDATE Pemesanan SET statusPesanan = :statusPesanan WHERE idPemesanan = :idPemesanan'
        );
        $updateOrder->execute([
            ':statusPesanan' => $orderStatus,
            ':idPemesanan' => $orderId,
        ]);

        $awb = $row['nomorresi'] ?: null;
        if ($newStatus === 'Dikirim' && !$awb) {
            $awb = 'CSX-' . random_int(100000000, 999999999);
        }

        $updateShipping = $pdo->prepare(
            'UPDATE Pengiriman SET statusPengiriman = :statusPengiriman, nomorResi = COALESCE(:nomorResi, nomorResi) WHERE idPemesanan = :idPemesanan'
        );
        $updateShipping->execute([
            ':statusPengiriman' => $newStatus,
            ':nomorResi' => $awb,
            ':idPemesanan' => $orderId,
        ]);

        appendMarketplaceTrackingHistory($pdo, (int) ($row['idpengiriman'] ?? 0), $newStatus);

        $notification = $pdo->prepare('INSERT INTO Notifikasi (id_user, pesan) VALUES (:id_user, :pesan)');
        $notification->execute([
            ':id_user' => (int) $row['id_user'],
            ':pesan' => sprintf(
                'Status pesanan apotek %s diperbarui menjadi %s.',
                getMarketplaceOrderCode($orderId, $row['tanggal']),
                $newStatus
            ),
        ]);

        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $e;
    }

    $detail = getMarketplaceOrderDetail($pdo, (int) $row['id_user'], $orderId);
    if (!$detail) {
        throw new RuntimeException('Gagal memuat detail pesanan setelah update status.');
    }

    return $detail;
}
