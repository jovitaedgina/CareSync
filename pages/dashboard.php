<?php
session_start();

require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/pages/login.php');
    exit;
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$pageTitle   = 'Beranda — CareSync';
$currentPage = 'dashboard';

$extraHead = '
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: { sans: ["Plus Jakarta Sans", "sans-serif"] },
                colors: { primary:"#1D4ED8", primaryLight:"#EFF6FF", accent:"#10B981", dark:"#0F172A", textSoft:"#64748B" },
                boxShadow: { soft:"0 10px 40px -10px rgba(0,0,0,0.06)", floating:"0 20px 40px -15px rgba(29,78,216,0.2)" },
                keyframes: {
                    fadeInUp:  {"0%":{opacity:"0",transform:"translateY(24px)"},"100%":{opacity:"1",transform:"translateY(0)"}},
                    float:     {"0%,100%":{transform:"translateY(0)"},"50%":{transform:"translateY(-10px)"}},
                    blob:      {"0%":{transform:"translate(0,0) scale(1)"},"33%":{transform:"translate(30px,-50px) scale(1.1)"},"66%":{transform:"translate(-20px,20px) scale(0.9)"},"100%":{transform:"translate(0,0) scale(1)"}},
                    slideDown: {"0%":{opacity:"0",transform:"translateY(-12px)"},"100%":{opacity:"1",transform:"translateY(0)"}},
                },
                animation: {
                    "fade-in-up":"fadeInUp 0.65s ease-out forwards",
                    "float":"float 3.5s ease-in-out infinite",
                    "float-delayed":"float 3.5s ease-in-out 1.5s infinite",
                    "blob":"blob 8s infinite",
                }
            }
        }
    }
</script>
<style>
.smooth{transition:all 0.3s ease-in-out}
.delay-100{animation-delay:100ms}.delay-200{animation-delay:200ms}
.delay-300{animation-delay:300ms}.delay-400{animation-delay:400ms}
#toast-stack{position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:10px}
.toast-item{display:flex;align-items:center;gap:12px;padding:14px 18px;border-radius:16px;background:#fff;box-shadow:0 8px 30px -4px rgba(0,0,0,0.15);min-width:280px;max-width:360px;font-size:14px;font-weight:600;color:#0F172A;border-left:4px solid #1D4ED8;animation:slideDown 0.3s ease}
.toast-item.success{border-left-color:#10B981}.toast-item.error{border-left-color:#EF4444}.toast-item.warning{border-left-color:#F59E0B}
.toast-icon{width:34px;height:34px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:15px;background:#EFF6FF;color:#1D4ED8}
.toast-item.success .toast-icon{background:#ECFDF5;color:#10B981}
.toast-item.error   .toast-icon{background:#FEF2F2;color:#EF4444}
.toast-item.warning .toast-icon{background:#FFFBEB;color:#F59E0B}
.modal-backdrop{position:fixed;inset:0;background:rgba(15,23,42,0.55);backdrop-filter:blur(4px);z-index:500;display:flex;align-items:center;justify-content:center;padding:20px}
.modal-box{background:#fff;border-radius:24px;padding:32px;width:100%;max-width:460px;box-shadow:0 25px 60px -10px rgba(0,0,0,0.2);animation:fadeInUp 0.3s ease}
@keyframes fadeInUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
@keyframes slideDown{from{opacity:0;transform:translateY(-10px)}to{opacity:1;transform:translateY(0)}}
</style>
';

include __DIR__ . '/../includes/header.php';
?>

<section class="bg-primaryLight relative overflow-hidden pt-12 pb-32 border-b border-slate-100">
    <div class="absolute top-0 right-0 -mt-20 -mr-20 w-[600px] h-[600px] bg-blue-100/60 rounded-full filter blur-3xl opacity-60 animate-blob pointer-events-none"></div>
    <div class="absolute inset-0 flex items-center justify-center opacity-[0.06] pointer-events-none overflow-hidden">
        <i class="fa-solid fa-dna text-[500px] text-blue-400"></i>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col lg:flex-row items-center relative z-10 gap-12 mt-4 lg:mt-0">
        <div class="lg:w-1/2 text-center lg:text-left">
            <div class="inline-flex items-center gap-2 bg-white border border-blue-100 text-primary text-xs font-bold px-3 py-1.5 rounded-full mb-6 shadow-sm opacity-0 animate-fade-in-up">
                <span class="w-2 h-2 bg-accent rounded-full animate-pulse"></span> 200+ Dokter Online Sekarang
            </div>
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold leading-[1.1] text-dark opacity-0 animate-fade-in-up delay-100">
                Book Your Doctor Appointment <span class="text-primary">Mudah & Cepat!</span>
            </h1>
            <p class="mt-6 text-base md:text-xl text-textSoft leading-relaxed max-w-lg mx-auto lg:mx-0 opacity-0 animate-fade-in-up delay-200">
                Temukan dokter terbaik dan buat janji temu hanya dengan beberapa klik. Keamanan dan kesehatan Anda adalah prioritas kami.
            </p>
            <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center lg:justify-start opacity-0 animate-fade-in-up delay-300">
                <a href="<?= BASE_URL ?>/pages/booking.php" class="bg-primary text-white text-center px-8 py-4 rounded-full font-bold hover:bg-blue-800 hover:-translate-y-1 active:scale-95 smooth shadow-floating text-base flex items-center justify-center gap-2">
                    <i class="fa-solid fa-calendar-check"></i> Buat Janji Temu
                </a>
                <a href="<?= BASE_URL ?>/pages/booking.php" class="bg-white text-dark text-center px-8 py-4 rounded-full font-bold hover:bg-slate-50 border border-slate-200 hover:-translate-y-1 active:scale-95 smooth shadow-sm text-base flex items-center justify-center gap-2">
                    <i class="fa-solid fa-search text-primary"></i> Cari Dokter
                </a>
            </div>
            <div class="mt-14 grid grid-cols-2 gap-4 opacity-0 animate-fade-in-up delay-400">
                <div onclick="showToast('40+ dokter spesialis siap melayani!','info')" class="flex items-center gap-3 bg-white p-4 rounded-2xl shadow-soft hover:shadow-floating hover:-translate-y-1 smooth cursor-pointer">
                    <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-primary text-xl flex-shrink-0"><i class="fa-solid fa-stethoscope"></i></div>
                    <div class="text-left"><div class="text-2xl font-extrabold text-dark" id="stat-dokter">0+</div><div class="text-[11px] text-textSoft font-semibold">Dokter Spesialis</div></div>
                </div>
                <div onclick="showToast('Layanan darurat 24/7 tersedia!','info')" class="flex items-center gap-3 bg-white p-4 rounded-2xl shadow-soft hover:shadow-floating hover:-translate-y-1 smooth cursor-pointer">
                    <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-accent text-xl flex-shrink-0"><i class="fa-solid fa-clock"></i></div>
                    <div class="text-left"><div class="text-2xl font-extrabold text-dark">24/7</div><div class="text-[11px] text-textSoft font-semibold">Emergency Services</div></div>
                </div>
            </div>
        </div>
        <div class="lg:w-1/2 flex justify-center mt-8 lg:mt-0 opacity-0 animate-fade-in-up delay-400">
            <div class="relative w-full max-w-sm md:max-w-xl animate-float">
                <img src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Dokter CareSync" class="rounded-[2rem] shadow-floating object-cover h-[350px] md:h-[480px] w-full border-4 border-white relative z-10">
                <img src="https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Tim Dokter" class="absolute hidden md:block -right-10 top-8 rounded-[1.5rem] shadow-soft object-cover h-[300px] w-52 border-4 border-white z-0 opacity-70 blur-[1px] animate-float-delayed">
                <div onclick="showToast('Semua dokter telah tersertifikasi resmi','success')" class="absolute -bottom-5 left-4 md:-left-8 bg-white p-4 rounded-2xl shadow-floating flex items-center gap-3 z-20 hover:scale-105 smooth cursor-pointer">
                    <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-accent text-xl flex-shrink-0"><i class="fa-solid fa-shield-halved"></i></div>
                    <div><p class="text-[10px] text-textSoft font-bold uppercase tracking-wider">Tersertifikasi</p><p class="font-bold text-dark text-sm leading-tight">100% Profesional Berlisensi</p></div>
                </div>
                <div class="absolute -top-4 right-0 bg-white px-4 py-2 rounded-full shadow-floating flex items-center gap-2 z-20 hover:scale-105 smooth cursor-pointer">
                    <i class="fa-solid fa-star text-amber-400"></i><span class="font-extrabold text-dark text-sm">4.9</span><span class="text-textSoft text-xs">50k+ Pasien</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-white relative z-20 -mt-14 py-0">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="<?= BASE_URL ?>/pages/booking.php" class="bg-white p-6 rounded-3xl shadow-floating border border-slate-100 hover:-translate-y-2 hover:shadow-xl smooth group no-underline">
                <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-primary text-2xl mb-5 group-hover:bg-primary group-hover:text-white group-hover:rotate-6 smooth"><i class="fa-solid fa-stethoscope"></i></div>
                <h3 class="font-extrabold text-xl text-dark mb-2 group-hover:text-primary smooth">Konsultasi Dokter</h3>
                <p class="text-textSoft text-sm leading-relaxed">Chat atau Video Call dengan dokter spesialis kapan saja dan di mana saja.</p>
                <div class="mt-4 text-primary font-bold text-sm flex items-center gap-1 group-hover:gap-2 smooth">Mulai <i class="fa-solid fa-arrow-right text-xs"></i></div>
            </a>
            <a href="<?= BASE_URL ?>/pages/marketplace.php" class="bg-white p-6 rounded-3xl shadow-floating border border-slate-100 hover:-translate-y-2 hover:shadow-xl smooth group no-underline">
                <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-accent text-2xl mb-5 group-hover:bg-accent group-hover:text-white group-hover:-rotate-6 smooth"><i class="fa-solid fa-pills"></i></div>
                <h3 class="font-extrabold text-xl text-dark mb-2 group-hover:text-accent smooth">Apotek & Tebus Resep</h3>
                <p class="text-textSoft text-sm leading-relaxed">Beli produk kesehatan atau unggah resep dokter, kami antar ke rumah.</p>
                <div class="mt-4 text-accent font-bold text-sm flex items-center gap-1 group-hover:gap-2 smooth">Ke Apotek <i class="fa-solid fa-arrow-right text-xs"></i></div>
            </a>
            <a href="<?= BASE_URL ?>/pages/profile.php" class="bg-white p-6 rounded-3xl shadow-floating border border-slate-100 hover:-translate-y-2 hover:shadow-xl smooth group no-underline">
                <div class="w-14 h-14 bg-purple-50 rounded-2xl flex items-center justify-center text-purple-600 text-2xl mb-5 group-hover:bg-purple-600 group-hover:text-white group-hover:rotate-6 smooth"><i class="fa-solid fa-laptop-medical"></i></div>
                <h3 class="font-extrabold text-xl text-dark mb-2 group-hover:text-purple-600 smooth">Rekam Medis Digital</h3>
                <p class="text-textSoft text-sm leading-relaxed">Akses riwayat kesehatan, hasil lab, dan catatan konsultasi dengan aman.</p>
                <div class="mt-4 text-purple-600 font-bold text-sm flex items-center gap-1 group-hover:gap-2 smooth">Lihat Riwayat <i class="fa-solid fa-arrow-right text-xs"></i></div>
            </a>
        </div>
    </div>
</section>

<section class="py-12 bg-white mt-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-r from-primary to-blue-500 rounded-3xl p-8 md:p-12 shadow-floating flex flex-col md:flex-row items-center justify-between overflow-hidden relative group">
            <i class="fa-solid fa-comment-medical text-white/10 text-9xl absolute -right-10 -bottom-10 group-hover:scale-125 group-hover:rotate-12 smooth duration-700"></i>
            <div class="relative z-10 md:w-2/3 text-white">
                <span class="bg-white/20 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Telemedicine Promo</span>
                <h2 class="text-3xl md:text-4xl font-extrabold mt-4 mb-3">Konsultasi dari Rumah Lebih Hemat!</h2>
                <p class="text-blue-100 text-base md:text-lg mb-6 max-w-lg leading-relaxed">Dapatkan diskon hingga 30% untuk konsultasi Video Call pertama Anda.</p>
                <a href="<?= BASE_URL ?>/pages/booking.php" class="inline-flex items-center gap-2 bg-white text-primary px-6 py-3 rounded-full font-bold shadow-md hover:bg-slate-50 hover:scale-105 active:scale-95 smooth">
                    <i class="fa-solid fa-video"></i> Klaim Promo Sekarang
                </a>
            </div>
            <div class="hidden md:block relative z-10 w-1/3 text-right animate-float">
                <img src="https://images.unsplash.com/photo-1605684954998-685c79d6a018?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Doctor App" class="w-48 h-48 object-cover rounded-full border-4 border-white shadow-lg inline-block">
            </div>
        </div>
    </div>
</section>

<section class="py-20 bg-slate-50 border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-end mb-8 gap-6">
            <div><h2 class="text-3xl font-extrabold text-dark">Rekomendasi Dokter Spesialis</h2><p class="mt-2 text-textSoft">Pilih dokter yang tepat untuk keluhan Anda.</p></div>
            <a href="<?= BASE_URL ?>/pages/booking.php" class="hidden sm:flex text-primary font-bold hover:text-blue-800 items-center gap-1 group smooth whitespace-nowrap">Lihat Semua <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 smooth"></i></a>
        </div>

        <div class="flex gap-2 flex-wrap mb-8">
            <?php foreach(['Semua','Dermatologis','Kardiologis','Dokter Gigi','Psikiater'] as $i=>$s): ?>
            <button onclick="filterDokter(this,'<?=$s?>')" class="spec-btn px-4 py-2 rounded-full text-sm font-bold border smooth <?=$i===0?'bg-primary text-white border-primary shadow-md':'bg-white text-slate-600 border-slate-200 hover:border-primary hover:text-primary'?>">
                <?=$s?>
            </button>
            <?php endforeach; ?>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6" id="doctor-grid">
            <?php
            $docs = [
                ['Dr. Ralph Edwards','Dermatologis','https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80','4.9','320+',45000,true],
                ['Dr. Hamida Jannat','Kardiologis','https://images.unsplash.com/photo-1590086782957-93c06ef21604?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80','4.8','280+',60000,true],
                ['Dr. Albert Boje','Dokter Gigi','https://images.unsplash.com/photo-1622253692010-333f2da6031d?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80','4.7','195+',35000,false],
                ['Dr. Leslie Alexander','Psikiater','https://images.unsplash.com/photo-1559839734-2b71ea197ec2?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80','4.9','410+',55000,true],
            ];
            foreach($docs as [$nm,$sp,$img,$rt,$pt,$pr,$av]):
            ?>
            <div class="doctor-card bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-sm hover:-translate-y-2 hover:shadow-floating group smooth" data-spec="<?=$sp?>">
                <div class="overflow-hidden h-48 relative">
                    <img src="<?=$img?>" alt="<?=$nm?>" class="w-full h-full object-cover object-top group-hover:scale-110 smooth">
                    <div class="absolute top-3 right-3 <?=$av?'bg-accent':'bg-slate-400'?> text-white text-[10px] font-bold px-2 py-1 rounded-full flex items-center gap-1">
                        <span class="w-1.5 h-1.5 bg-white rounded-full <?=$av?'animate-pulse':''?>"></span><?=$av?'Online':'Offline'?>
                    </div>
                    <div class="absolute bottom-3 left-3 bg-white/90 px-2 py-1 rounded-full text-xs font-bold text-amber-500 flex items-center gap-1">
                        <i class="fa-solid fa-star"></i> <?=$rt?>
                    </div>
                </div>
                <div class="p-5">
                    <h3 class="font-extrabold text-dark text-[15px] group-hover:text-primary smooth mb-0.5"><?=$nm?></h3>
                    <p class="text-primary text-xs font-bold mb-3"><?=$sp?></p>
                    <div class="flex items-center gap-2 text-xs text-textSoft mb-4 font-semibold">
                        <span><i class="fa-solid fa-users mr-1"></i><?=$pt?></span>
                        <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                        <span>Rp <?=number_format($pr,0,',','.')?></span>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="showDoctorModal('<?=addslashes($nm)?>','<?=$sp?>','<?=$img?>','<?=$rt?>','<?=$pt?>',<?=$pr?>)" class="flex-1 bg-primaryLight text-primary py-2 rounded-xl font-bold hover:bg-blue-100 active:scale-95 smooth text-sm">Detail</button>
                        <?php if($av):?>
                        <a href="<?=BASE_URL?>/pages/booking.php" class="flex-1 bg-primary text-white py-2 rounded-xl font-bold text-center hover:bg-blue-800 active:scale-95 smooth text-sm no-underline">Book Now</a>
                        <?php else:?>
                        <button disabled class="flex-1 bg-slate-100 text-slate-400 py-2 rounded-xl font-bold text-sm cursor-not-allowed">Offline</button>
                        <?php endif;?>
                    </div>
                </div>
            </div>
            <?php endforeach;?>
        </div>
    </div>
</section>

<section class="py-10 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-emerald-50 border border-emerald-100 rounded-3xl p-6 md:p-8 flex flex-col lg:flex-row items-center justify-between gap-6 hover:shadow-md smooth">
            <div class="flex items-center gap-5 group">
                <div class="w-16 h-16 bg-accent rounded-2xl flex items-center justify-center text-white text-3xl shadow-md group-hover:scale-110 group-hover:rotate-6 smooth flex-shrink-0"><i class="fa-solid fa-truck-fast"></i></div>
                <div><h3 class="text-xl md:text-2xl font-extrabold text-dark mb-1">Gratis Ongkir Ke Seluruh Kota!</h3><p class="text-textSoft text-sm">Belanja min. Rp 50.000, pengiriman instan gratis ke rumah Anda.</p></div>
            </div>
            <a href="<?=BASE_URL?>/pages/marketplace.php" class="bg-accent text-white px-7 py-3 rounded-full font-bold shadow-md hover:bg-emerald-600 hover:scale-105 active:scale-95 smooth whitespace-nowrap no-underline">Belanja Sekarang</a>
        </div>
    </div>
</section>

<section class="py-20 bg-slate-50 border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-end mb-10 gap-6">
            <div><h2 class="text-3xl font-extrabold text-dark">Katalog Produk Kesehatan</h2><p class="mt-2 text-textSoft">Vitamin, suplemen, dan alat kesehatan terlaris.</p></div>
            <a href="<?=BASE_URL?>/pages/marketplace.php" class="hidden sm:flex text-primary font-bold hover:text-blue-800 items-center gap-1 group smooth whitespace-nowrap">Ke Apotek <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 smooth"></i></a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
            <?php
            $prods=[
                ['Blackmores Vitamin C 500mg - 60 Tablet','Vitamin','text-emerald-600 bg-emerald-50','fa-bottle-droplet',120000, 'https://blackmores-bucket.s3.ap-southeast-1.amazonaws.com/blackmores/product/images667bb9448c2ff.png'],
                ['Sensi Masker Medis 3-Ply - Isi 50 Pcs','Alat Kesehatan','text-blue-600 bg-blue-50','fa-mask-face',35000, 'https://doktersehat.com/wp-content/uploads/2020/03/obat_dan_vitamin_Doktersehat_com_Masker_Sensi_Earloop_3_Ply_-_Hijau_50S.jpg'],
                ['Betadine Antiseptic Solution 60ml','P3K','text-orange-600 bg-orange-50','fa-prescription-bottle-medical',45000, 'https://guardianindonesia.co.id/media/catalog/product/0/0648432~1_20250723114509_4781.png?format=png&auto=webp&width=840&height=375&fit=cover'],
                ['Panadol Extra Paracetamol 10 Kaplet','Obat Bebas','text-red-600 bg-red-50','fa-tablets',15000, 'https://d2qjkwm11akmwu.cloudfront.net/products/807265_19-11-2024_13-49-18.webp'],
            ];
            foreach($prods as $i => [$nm,$cat,$badge,$icon,$price,$img]):
            ?>
            <a href="<?=BASE_URL?>/pages/product-detail.php?id=<?= $i + 1 ?>" class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm hover:-translate-y-2 hover:shadow-soft group flex flex-col smooth cursor-pointer no-underline">
                <div class="bg-white rounded-xl flex justify-center mb-4 h-32 md:h-40 overflow-hidden items-center p-2">
                    <?php if($img):?>
                        <img src="<?=$img?>" alt="<?=$nm?>" class="object-contain mix-blend-multiply group-hover:scale-110 smooth h-full w-full">
                    <?php else:?>
                        <i class="fa-solid <?=$icon?> text-5xl md:text-6xl text-slate-300 group-hover:scale-110 group-hover:text-slate-400 smooth"></i>
                    <?php endif;?>
                </div>
                <span class="text-[10px] md:text-xs font-bold px-2 py-1 rounded w-fit <?=$badge?>"><?=$cat?></span>
                <h4 class="font-bold text-sm md:text-base text-dark mt-2 line-clamp-2 leading-snug group-hover:text-primary smooth"><?=$nm?></h4>
                <p class="text-primary font-extrabold text-sm md:text-base mt-2 mb-3">Rp <?=number_format($price,0,',','.')?></p>
                <button onclick="event.preventDefault();addToCart('<?=addslashes($nm)?>')" class="w-full mt-auto border border-primary text-primary hover:bg-primary hover:text-white py-2 rounded-lg font-bold text-xs md:text-sm active:scale-95 smooth">
                    <i class="fa-solid fa-cart-plus mr-1"></i> + Keranjang
                </button>
            </a>
            <?php endforeach;?>
        </div>
    </div>
</section>

<section class="py-20 bg-white border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <h2 class="text-3xl font-extrabold text-dark">Informasi & Artikel Kesehatan Terbaru</h2>
            <p class="mt-2 text-textSoft text-lg">Baca tips kesehatan terpercaya dari para ahli medis kami.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php 
            $articles=[
                [
                    'id' => 1,
                    'title' => 'Pentingnya Menjaga Pola Tidur untuk Kesehatan Mental', 'category' => 'Gaya Hidup', 'date' => '12 Apr 2026', 
                    'desc' => 'Kurang tidur dapat berdampak signifikan pada suasana hati dan produktivitas harian Anda...', 
                    'img' => 'https://images.unsplash.com/photo-1505576399279-565b52d4ac71?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'
                ],
                [
                    'id' => 2,
                    'title' => '5 Makanan yang Wajib Dihindari Penderita Asam Lambung', 'category' => 'Nutrisi', 'date' => '10 Apr 2026', 
                    'desc' => 'Kenali jenis makanan pemicu naiknya asam lambung agar aktivitas tidak terganggu...', 
                    'img' => 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'
                ],
                [
                    'id' => 3,
                    'title' => 'Jadwal Imunisasi Dasar Lengkap Bayi Usia 0-12 Bulan', 'category' => 'Kesehatan Anak', 'date' => '08 Apr 2026', 
                    'desc' => 'Panduan lengkap bagi orang tua untuk memastikan buah hati mendapat perlindungan maksimal...', 
                    'img' => 'https://images.unsplash.com/photo-1584036561566-baf8f5f1b144?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'
                ],
            ];
            foreach($articles as $a):?>
            
            <a href="<?= BASE_URL ?>/pages/article.php?id=<?= $a['id'] ?>" class="group cursor-pointer block no-underline">
                <div class="overflow-hidden rounded-2xl mb-4 shadow-sm border border-slate-100 h-56">
                    <img src="<?= $a['img'] ?>" alt="<?= htmlspecialchars($a['title']) ?>" class="w-full h-full object-cover group-hover:scale-110 smooth">
                </div>
                <div class="flex gap-3 text-xs font-bold text-textSoft mb-2">
                    <span class="text-primary"><?= $a['category'] ?></span>
                    <span>• <?= $a['date'] ?></span>
                </div>
                <h3 class="text-lg font-bold text-dark mb-2 group-hover:text-primary smooth leading-snug"><?= $a['title'] ?></h3>
                <p class="text-textSoft text-sm line-clamp-2 leading-relaxed m-0"><?= $a['desc'] ?></p>
            </a>
            <?php endforeach;?>
        </div>
    </div>
</section>

<section class="py-16 bg-slate-50 border-t border-slate-100">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <div class="bg-dark rounded-[2.5rem] p-10 md:p-16 shadow-2xl relative overflow-hidden">
            <div class="absolute -top-20 -left-20 w-48 h-48 bg-primary rounded-full filter blur-3xl opacity-40"></div>
            <div class="absolute -bottom-20 -right-20 w-48 h-48 bg-accent rounded-full filter blur-3xl opacity-40"></div>
            <div class="relative z-10">
                <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">Punya Pertanyaan atau Butuh Bantuan?</h2>
                <p class="text-slate-400 mb-8 text-lg leading-relaxed max-w-lg mx-auto">Tim Support CareSync siap membantu Anda 24 jam setiap harinya.</p>
                <button onclick="showToast('Tim CS kami akan segera menghubungi Anda!','success')" class="inline-flex items-center gap-3 bg-primary hover:bg-blue-600 text-white px-8 py-4 rounded-full font-bold text-lg hover:scale-105 active:scale-95 smooth shadow-floating">
                    <i class="fa-solid fa-headset text-xl"></i> Hubungi CS Kami
                </button>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<div id="modal-doc" class="modal-backdrop hidden" onclick="if(event.target===this)closeModal('modal-doc')">
    <div class="modal-box">
        <div class="flex items-start gap-4 mb-5">
            <img id="md-img" src="" alt="" class="w-20 h-20 rounded-2xl object-cover object-top flex-shrink-0 border-2 border-slate-100">
            <div class="flex-1">
                <h3 id="md-name" class="font-extrabold text-xl text-dark mb-0.5"></h3>
                <p id="md-spec" class="text-primary text-sm font-bold mb-2"></p>
                <div class="flex gap-3 text-xs text-textSoft font-semibold">
                    <span id="md-rating" class="flex items-center gap-1"></span>
                    <span id="md-patients" class="flex items-center gap-1"></span>
                </div>
            </div>
            <button onclick="closeModal('modal-doc')" class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 smooth flex-shrink-0 border-none cursor-pointer"><i class="fa-solid fa-xmark text-sm"></i></button>
        </div>
        <div class="bg-blue-50 rounded-2xl p-4 mb-5">
            <div class="text-xs text-textSoft font-semibold mb-1">Biaya Konsultasi</div>
            <div id="md-price" class="text-2xl font-extrabold text-primary"></div>
        </div>
        <div class="bg-slate-50 rounded-2xl p-4 mb-5 text-sm text-textSoft leading-relaxed">
            Dokter spesialis berpengalaman dengan track record pelayanan terbaik. Tersedia untuk konsultasi via chat maupun video call.
        </div>
        <div class="grid grid-cols-2 gap-3">
            <button onclick="closeModal('modal-doc')" class="py-3 rounded-2xl border-2 border-slate-200 font-bold text-slate-600 hover:border-slate-300 active:scale-95 smooth bg-transparent cursor-pointer">Tutup</button>
            <a id="md-link" href="<?=BASE_URL?>/pages/booking.php" class="py-3 rounded-2xl bg-primary text-white font-bold text-center hover:bg-blue-800 active:scale-95 smooth no-underline">Book Now</a>
        </div>
    </div>
</div>

<div id="toast-stack"></div>

<div id="mob-overlay" class="hidden fixed inset-0 bg-black/40 z-40" onclick="closeMob()"></div>
<div id="mob-drawer" class="hidden fixed bottom-0 left-0 right-0 bg-white z-50 rounded-t-3xl shadow-2xl p-6 pb-10" style="animation:slideDown .3s ease">
    <div class="w-12 h-1 bg-slate-200 rounded-full mx-auto mb-6"></div>
    <nav class="flex flex-col gap-1">
        <a href="<?=BASE_URL?>/pages/dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-bold text-sm text-primary bg-primaryLight no-underline"><i class="fa-solid fa-house w-5 text-center"></i> Beranda</a>
        <a href="<?=BASE_URL?>/pages/booking.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-bold text-sm text-slate-600 hover:bg-slate-50 smooth no-underline"><i class="fa-solid fa-stethoscope w-5 text-center"></i> Konsultasi</a>
        <a href="<?=BASE_URL?>/pages/marketplace.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-bold text-sm text-slate-600 hover:bg-slate-50 smooth no-underline"><i class="fa-solid fa-pills w-5 text-center"></i> Apotek</a>
        <a href="<?=BASE_URL?>/pages/profile.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-bold text-sm text-slate-600 hover:bg-slate-50 smooth no-underline"><i class="fa-solid fa-clock-rotate-left w-5 text-center"></i> Riwayat</a>
        <a href="<?=BASE_URL?>/pages/cart.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-bold text-sm text-slate-600 hover:bg-slate-50 smooth no-underline"><i class="fa-solid fa-cart-shopping w-5 text-center"></i> Keranjang</a>
        <div class="border-t border-slate-100 pt-3 mt-2">
            <a href="<?=BASE_URL?>/pages/login.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-bold text-sm text-slate-600 hover:bg-slate-50 smooth no-underline"><i class="fa-solid fa-right-to-bracket w-5 text-center"></i> Masuk</a>
        </div>
    </nav>
</div>

<script>

const token = localStorage.getItem('em_token');
    
    if (!token) {
        // Jika em_token tidak ada, langsung tendang ke login
        window.location.replace('<?= BASE_URL ?>/pages/login.php');
    }

/* Toast */
function showToast(msg,type='info',dur=3500){
    const icons={info:'fa-circle-info',success:'fa-circle-check',error:'fa-circle-xmark',warning:'fa-triangle-exclamation'};
    const s=document.getElementById('toast-stack');
    const t=document.createElement('div');
    t.className=`toast-item ${type}`;
    t.innerHTML=`<div class="toast-icon"><i class="fa-solid ${icons[type]}"></i></div><div style="flex:1">${msg}</div><button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:18px;line-height:1;padding:0;margin-left:8px">×</button>`;
    s.appendChild(t);
    setTimeout(()=>{t.style.cssText='opacity:0;transform:translateX(20px);transition:all .3s';setTimeout(()=>t.remove(),300);},dur);
}

/* Cart */
let cc=parseInt(localStorage.getItem('em_cart')||'0');
function syncCart(){const b=document.getElementById('cart-count');if(!b)return;if(cc>0){b.textContent=cc;b.classList.remove('hidden');}else b.classList.add('hidden');}
function addToCart(n){cc++;localStorage.setItem('em_cart',cc);syncCart();showToast(`<b>${n}</b> ditambahkan ke keranjang!`,'success');}
syncCart();

/* Filter dokter */
function filterDokter(btn,spec){
    document.querySelectorAll('.spec-btn').forEach(b=>{b.className=b.className.replace(/bg-primary text-white border-primary shadow-md/g,'bg-white text-slate-600 border-slate-200');});
    btn.className=btn.className.replace('bg-white text-slate-600 border-slate-200','bg-primary text-white border-primary shadow-md');
    document.querySelectorAll('.doctor-card').forEach(c=>{c.style.display=(spec==='Semua'||c.dataset.spec===spec)?'':'none';});
    const v=[...document.querySelectorAll('.doctor-card')].filter(c=>c.style.display!=='none').length;
    showToast(`Menampilkan ${v} dokter${spec!=='Semua'?' '+spec:''}`, 'info', 2000);
}

/* Modal Dokter */
function showDoctorModal(nm,sp,img,rt,pt,pr){
    document.getElementById('md-img').src=img;
    document.getElementById('md-name').textContent=nm;
    document.getElementById('md-spec').textContent=sp;
    document.getElementById('md-rating').innerHTML=`<i class="fa-solid fa-star text-amber-400"></i> ${rt} Rating`;
    document.getElementById('md-patients').innerHTML=`<i class="fa-solid fa-users"></i> ${pt} Pasien`;
    document.getElementById('md-price').textContent='Rp '+pr.toLocaleString('id-ID');
    document.getElementById('modal-doc').classList.remove('hidden');
}

function closeModal(id){document.getElementById(id).classList.add('hidden');}
document.addEventListener('keydown',e=>{if(e.key==='Escape')document.querySelectorAll('.modal-backdrop').forEach(m=>m.classList.add('hidden'));});

/* Mobile menu */
function openMob(){document.getElementById('mob-overlay').classList.remove('hidden');document.getElementById('mob-drawer').classList.remove('hidden');}
function closeMob(){document.getElementById('mob-overlay').classList.add('hidden');document.getElementById('mob-drawer').classList.add('hidden');}

/* Inject hamburger ke navbar */
document.addEventListener('DOMContentLoaded',()=>{
    const actions=document.querySelector('.navbar-actions,.navbar [class*="actions"]')||document.querySelector('nav>div:last-child');
    if(actions){
        const hb=document.createElement('button');
        hb.className='flex md:hidden items-center justify-center w-10 h-10 rounded-xl bg-slate-50 text-slate-700 border border-slate-200 smooth cursor-pointer';
        hb.style='font-size:16px';
        hb.innerHTML='<i class="fa-solid fa-bars"></i>';
        hb.onclick=openMob;
        actions.appendChild(hb);
    }
    /* Counter animate */
    let n=0;const el=document.getElementById('stat-dokter');
    const iv=setInterval(()=>{n=Math.min(n+2,40);el.textContent=n+'+';if(n>=40)clearInterval(iv);},40);
    /* Welcome toast */
    setTimeout(()=>showToast('Selamat datang di CareSync! 👋','success',4000),1200);
});

/* Navbar scroll effect */
window.addEventListener('scroll',()=>{
    const nav=document.querySelector('nav');
    if(!nav)return;
    if(window.scrollY>20){nav.style.boxShadow='0 4px 20px rgba(0,0,0,0.08)';}
    else{nav.style.boxShadow='0 2px 10px rgba(0,0,0,0.02)';}
});
</script>