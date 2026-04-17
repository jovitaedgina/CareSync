<?php
// includes/footer.php
?>
<footer class="bg-white border-t border-slate-100 pt-16 pb-8 mt-auto" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
            
            <div class="col-span-1 md:col-span-2 lg:col-span-1">
                <a href="<?= BASE_URL ?>/pages/dashboard.php" class="flex items-center gap-2.5 no-underline mb-5 group">
                    <div class="flex items-center justify-center w-10 h-10 bg-primary rounded-xl shadow-soft group-hover:scale-105 group-hover:rotate-3 smooth-transition">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.0002 21.35L10.5502 20.03C5.4002 15.36 2.0002 12.28 2.0002 8.5C2.0002 5.42 4.4202 3 7.5002 3C9.2402 3 10.9102 3.81 12.0002 5.09C13.0902 3.81 14.7602 3 16.5002 3C19.5802 3 22.0002 5.42 22.0002 8.5C22.0002 12.28 18.6002 15.36 13.4502 20.04L12.0002 21.35Z" fill="white"/>
                            <path d="M12 17L14 15M12 17L10 15M12 17V11M8 11V13M16 11V13" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="12" cy="17" r="6" stroke="#10B981" stroke-width="2"/>
                        </svg>
                    </div>
                    <span class="text-2xl font-extrabold text-dark tracking-tighter m-0">
                        Care<span class="text-primary">Sync</span>
                    </span>
                </a>
                <p class="text-textSoft text-sm leading-relaxed mb-6 font-medium">
                    Platform kesehatan terpadu yang menghubungkan Anda dengan dokter spesialis dan layanan apotek digital secara cepat, aman, dan tepercaya.
                </p>
                <div class="flex items-center gap-3">
                    <a href="#" class="w-9 h-9 rounded-full bg-slate-50 flex items-center justify-center text-slate-500 hover:bg-primary hover:text-white smooth-transition no-underline"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="w-9 h-9 rounded-full bg-slate-50 flex items-center justify-center text-slate-500 hover:bg-primary hover:text-white smooth-transition no-underline"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#" class="w-9 h-9 rounded-full bg-slate-50 flex items-center justify-center text-slate-500 hover:bg-primary hover:text-white smooth-transition no-underline"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
            </div>

            <div>
                <h4 class="font-extrabold text-dark mb-5">Layanan Kami</h4>
                <ul class="flex flex-col gap-3 p-0 m-0 list-none text-sm font-bold">
                    <li><a href="<?= BASE_URL ?>/pages/booking.php" class="text-textSoft hover:text-primary smooth-transition no-underline">Konsultasi Dokter</a></li>
                    <li><a href="<?= BASE_URL ?>/pages/marketplace.php" class="text-textSoft hover:text-primary smooth-transition no-underline">Apotek Digital</a></li>
                    <li><a href="<?= BASE_URL ?>/pages/profile.php" class="text-textSoft hover:text-primary smooth-transition no-underline">Rekam Medis</a></li>
                    <li><a href="<?= BASE_URL ?>/pages/dashboard.php" class="text-textSoft hover:text-primary smooth-transition no-underline">Artikel Kesehatan</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-extrabold text-dark mb-5">Perusahaan</h4>
                <ul class="flex flex-col gap-3 p-0 m-0 list-none text-sm font-bold">
                    <li><a href="#" class="text-textSoft hover:text-primary smooth-transition no-underline">Tentang Kami</a></li>
                    <li><a href="#" class="text-textSoft hover:text-primary smooth-transition no-underline">Karir</a></li>
                    <li><a href="#" class="text-textSoft hover:text-primary smooth-transition no-underline">Mitra Dokter & Apotek</a></li>
                    <li><a href="#" class="text-textSoft hover:text-primary smooth-transition no-underline">Syarat & Ketentuan</a></li>
                    <li><a href="#" class="text-textSoft hover:text-primary smooth-transition no-underline">Kebijakan Privasi</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-extrabold text-dark mb-5">Hubungi Kami</h4>
                <ul class="flex flex-col gap-4 p-0 m-0 list-none text-sm font-semibold">
                    <li class="flex items-start gap-3 text-textSoft leading-relaxed">
                        <i class="fa-solid fa-location-dot mt-1 text-primary"></i>
                        <span>Jl. Siliwangi No. 123, Kahuripan, Tawang, Kota Tasikmalaya, Jawa Barat 46115</span>
                    </li>
                    <li class="flex items-center gap-3 text-textSoft">
                        <i class="fa-solid fa-envelope text-primary"></i>
                        <span>support@caresync.com</span>
                    </li>
                    <li class="flex items-center gap-3 text-textSoft">
                        <i class="fa-solid fa-phone text-primary"></i>
                        <span>0800-1-234-567 (Bebas Pulsa)</span>
                    </li>
                </ul>
            </div>

        </div>

        <div class="border-t border-slate-200 pt-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-xs font-bold text-slate-400 m-0 text-center md:text-left">
                &copy; <?= date('Y') ?> CareSync. All rights reserved.
            </p>
            <div class="flex items-center gap-3">
                <i class="fa-brands fa-cc-visa text-3xl text-slate-300 hover:text-slate-500 smooth-transition"></i>
                <i class="fa-brands fa-cc-mastercard text-3xl text-slate-300 hover:text-slate-500 smooth-transition"></i>
                <i class="fa-solid fa-money-bill-transfer text-2xl text-slate-300 hover:text-slate-500 smooth-transition"></i>
            </div>
        </div>
    </div>
</footer>

</body>
</html>