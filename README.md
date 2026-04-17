  <h1>🏥 CareSync</h1>
  <p><b>Telemedicine & E-Pharmacy Integrated Web Platform</b></p>

  <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP" />
  <img src="https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind" />
  <img src="https://img.shields.io/badge/Alpine.js-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white" alt="Alpine.js" />
  <img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL" />
  <br><br>
</div>

## 📖 Tentang Proyek

**CareSync** adalah aplikasi platform kesehatan terpadu berbasis web yang dirancang untuk menjembatani pasien dengan layanan medis secara efisien. Aplikasi ini memadukan layanan telemedisin (konsultasi dokter spesialis secara *online*) dengan apotek digital (E-Pharmacy) dalam satu ekosistem yang kohesif.

Proyek ini dikembangkan sebagai bagian dari pemenuhan tugas akademik jurusan Sistem Informasi.

---

## ✨ Fitur Utama (Front-End)

Aplikasi ini mengedepankan *User Experience* (UX) yang mulus dengan antarmuka yang modern, interaktif, dan responsif:

* **👨‍⚕️ Booking & Konsultasi:** Fitur direktori dokter dengan filter interaktif berbasis spesialisasi, pencarian *real-time*, dan *pop-up* detail dokter.
* **💊 E-Pharmacy (Marketplace):** Katalog alat kesehatan dan obat-obatan lengkap dengan fitur keranjang belanja (*Cart*) yang dinamis.
* **📂 Rekam Medis & Lab:** *Dashboard* riwayat kunjungan, akses ke resep digital digital (E-Rx), dan tab hasil pemeriksaan laboratorium.
* **🔔 Pusat Notifikasi:** Sistem *dropdown* notifikasi pintar berbasis Alpine.js untuk melacak pengingat jadwal dan status apotek.
* **🛒 Transaksi & Pelacakan:** UI lengkap untuk alur *checkout*, metode pembayaran, *shipping*, hingga resi pelacakan (*tracking*).
* **📱 100% Responsif:** Tata letak yang dioptimalkan secara presisi untuk Desktop, Tablet, dan Mobile.

---

## 📂 Struktur Direktori

```text
caresync/
├── includes/                      # Modul komponen (reusable)
│   ├── config.php                 # Konfigurasi sistem & Base URL
│   ├── header.php                 # Global Navbar & dependensi asset
│   └── footer.php                 # Global Footer
├── pages/                         # UI Views & Core Modules
│   ├── article.php                # Blog & Edukasi kesehatan
│   ├── booking.php                # Direktori filter dokter spesialis
│   ├── cart.php                   # Keranjang E-Pharmacy
│   ├── consultation.php           # Ruang interaksi medis
│   ├── dashboard.php              # Beranda (Dashboard Pasien)
│   ├── history.php                # Rekam Medis (E-MR)
│   ├── lab_result.php             # Detail hasil lab
│   ├── lab_results.php            # Daftar arsip laboratorium
│   ├── login.php                  # Autentikasi Masuk
│   ├── logout.php                 # Akhiri Sesi
│   ├── marketplace.php            # Apotek Digital
│   ├── notification.php           # Pusat notifikasi terpadu
│   ├── payment.php                # Mockup gerbang pembayaran
│   ├── pharmacist_consultation.php# Ruang chat Apoteker
│   ├── prescription.php           # Modul tebus resep
│   ├── product-detail.php         # Halaman SKU produk
│   ├── profile.php                # Pengaturan akun pengguna
│   ├── register.php               # Pendaftaran akun baru
│   ├── shipping.php               # Modul kurir & alamat
│   └── tracking.php               # Pelacakan pesanan real-time
└── index.php                      # Entry point (Main Router)

🚀 Cara Menjalankan Secara Lokal
1. Nyalakan modul Apache di aplikasi XAMPP atau Laragon.
2. Clone repository ini ke dalam folder htdocs (XAMPP) atau www (Laragon):
3. Buka browser dan akses URL:
