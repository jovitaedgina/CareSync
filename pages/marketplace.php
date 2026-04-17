<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle   = 'Apotek Digital — CareSync';
$currentPage = 'marketplace';

// Menyuntikkan Tailwind CSS khusus untuk halaman ini
$extraHead = '
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: [\'"Plus Jakarta Sans"\', \'sans-serif\'],
                },
                colors: {
                    primary: \'#1D4ED8\',
                    primaryLight: \'#EFF6FF\',
                    accent: \'#10B981\',
                    dark: \'#0F172A\',
                    textSoft: \'#64748B\'
                },
                boxShadow: {
                    \'floating\': \'0 20px 40px -15px rgba(29, 78, 216, 0.25)\',
                }
            }
        }
    }
</script>
<style>
    .smooth-transition { transition: all 0.3s ease-in-out; }
    body { background-color: #F8FAFC; margin: 0; }
</style>
';

include __DIR__ . '/../includes/header.php';

// Mock Data Kategori
$categories = [
    ['name' => 'Semua Produk', 'icon' => 'fa-layer-group', 'active' => true],
    ['name' => 'Obat Bebas', 'icon' => 'fa-pills', 'active' => false],
    ['name' => 'Obat Keras (Resep)', 'icon' => 'fa-file-signature', 'active' => false],
    ['name' => 'Vitamin & Suplemen', 'icon' => 'fa-apple-whole', 'active' => false],
    ['name' => 'Alat Kesehatan', 'icon' => 'fa-mask', 'active' => false],
    ['name' => 'P3K', 'icon' => 'fa-kit-medical', 'active' => false],
    ['name' => 'Ibu & Bayi', 'icon' => 'fa-baby', 'active' => false]
];

// Mock Data 15 Produk Lengkap
$products = [
    [
        'name' => 'Blackmores Vitamin C 500mg - 60 Tablet', 'category' => 'Vitamin & Suplemen', 
        'price' => 120000, 'sold' => '1.2k', 'rating' => '4.9', 'badge' => 'text-emerald-600 bg-emerald-50', 'icon' => 'fa-bottle-droplet', 
        'img' => 'https://blackmores-bucket.s3.ap-southeast-1.amazonaws.com/blackmores/product/images667bb9448c2ff.png'
    ],
    [
        'name' => 'Panadol Extra Paracetamol 10 Kaplet', 'category' => 'Obat Bebas', 
        'price' => 15000, 'sold' => '5.5k', 'rating' => '4.8', 'badge' => 'text-blue-600 bg-blue-50', 'icon' => 'fa-tablets', 
        'img' => 'https://d2qjkwm11akmwu.cloudfront.net/products/807265_19-11-2024_13-49-18.webp'
    ],
    [
        'name' => 'Sensi Masker Medis 3-Ply - Isi 50 Pcs', 'category' => 'Alat Kesehatan', 
        'price' => 35000, 'sold' => '10k+', 'rating' => '4.9', 'badge' => 'text-purple-600 bg-purple-50', 'icon' => 'fa-mask-face', 
        'img' => 'https://doktersehat.com/wp-content/uploads/2020/03/obat_dan_vitamin_Doktersehat_com_Masker_Sensi_Earloop_3_Ply_-_Hijau_50S.jpg'
    ],
    [
        'name' => 'Betadine Antiseptic Solution 60ml', 'category' => 'P3K', 
        'price' => 45000, 'sold' => '850', 'rating' => '4.7', 'badge' => 'text-orange-600 bg-orange-50', 'icon' => 'fa-prescription-bottle-medical', 
        'img' => 'https://guardianindonesia.co.id/media/catalog/product/0/0648432~1_20250723114509_4781.png?format=png&auto=webp&width=840&height=375&fit=cover'
    ],
    [
        'name' => 'Amoxicillin 500mg - 10 Kaplet', 'category' => 'Obat Keras (Resep)', 
        'price' => 12000, 'sold' => '3k+', 'rating' => '4.8', 'badge' => 'text-red-600 bg-red-50', 'icon' => 'fa-capsules', 
        'img' => 'https://images.alodokter.com/dk0z4ums3/image/upload/c_scale,h_500,w_500/v1/production/pharmacy/products/1687342946_amox_500_berno-seles'
    ],
    [
        'name' => 'Imboost Force - 10 Kaplet', 'category' => 'Vitamin & Suplemen', 
        'price' => 75000, 'sold' => '2.1k', 'rating' => '4.9', 'badge' => 'text-emerald-600 bg-emerald-50', 'icon' => 'fa-leaf', 
        'img' => 'https://images.alodokter.com/dk0z4ums3/image/upload/c_scale,h_500,w_500/v1/production/pharmacy/products/1659930367_629d9f10f15ee8089e029434'
    ],
    [
        'name' => 'Hansaplast Plester Kain - Isi 10', 'category' => 'P3K', 
        'price' => 8500, 'sold' => '4.2k', 'rating' => '4.8', 'badge' => 'text-orange-600 bg-orange-50', 'icon' => 'fa-bandage', 
        'img' => 'https://d2qjkwm11akmwu.cloudfront.net/products/178312_2-1-2025_13-8-47.webp'
    ],
    [
        'name' => 'Tolak Angin Cair SidoMuncul - 5 Sachet', 'category' => 'Obat Bebas', 
        'price' => 18000, 'sold' => '8k+', 'rating' => '4.9', 'badge' => 'text-blue-600 bg-blue-50', 'icon' => 'fa-flask', 
        'img' => 'https://cdn.ruparupa.io/fit-in/400x400/filters:format(webp)/filters:quality(90)/ruparupa-com/image/upload/Products/10443596_4.jpg'
    ],
    [
        'name' => 'Mylanta Sirup 50ml - Obat Maag', 'category' => 'Obat Bebas', 
        'price' => 17500, 'sold' => '12k+', 'rating' => '4.8', 'badge' => 'text-blue-600 bg-blue-50', 'icon' => 'fa-flask', 
        'img' => 'https://d2qjkwm11akmwu.cloudfront.net/products/854811_29-5-2022_21-2-6-1665780051.webp'
    ],
    [
        'name' => 'Omron Termometer Digital MC-246', 'category' => 'Alat Kesehatan', 
        'price' => 45000, 'sold' => '3.5k', 'rating' => '4.9', 'badge' => 'text-purple-600 bg-purple-50', 'icon' => 'fa-temperature-half', 
        'img' => 'https://d2qjkwm11akmwu.cloudfront.net/products/502601_26-7-2024_13-58-0.webp'
    ],
    [
        'name' => 'Zwitsal Baby Bath Hair & Body 200ml', 'category' => 'Ibu & Bayi', 
        'price' => 28000, 'sold' => '5k+', 'rating' => '4.9', 'badge' => 'text-pink-600 bg-pink-50', 'icon' => 'fa-baby', 
        'img' => 'https://down-id.img.susercontent.com/file/id-11134207-7r98q-lngv4psvmha9c1'
    ],
    [
        'name' => 'Sanmol Sirup Paracetamol 60ml', 'category' => 'Obat Bebas', 
        'price' => 16000, 'sold' => '15k+', 'rating' => '4.9', 'badge' => 'text-blue-600 bg-blue-50', 'icon' => 'fa-flask', 
        'img' => 'https://storage.googleapis.com/rxstorage/Product/Photos/farmaku_sanmol-syrup-60-ml-01.jpg'
    ],
    [
        'name' => 'Ventolin Inhaler 100mcg', 'category' => 'Obat Keras (Resep)', 
        'price' => 135000, 'sold' => '4k+', 'rating' => '4.8', 'badge' => 'text-red-600 bg-red-50', 'icon' => 'fa-spray-can', 
        'img' => 'https://d2qjkwm11akmwu.cloudfront.net/products/1896-1665761131.webp'
    ],
    [
        'name' => 'Counterpain Cream 30gr', 'category' => 'Obat Bebas', 
        'price' => 45000, 'sold' => '9k+', 'rating' => '4.8', 'badge' => 'text-blue-600 bg-blue-50', 'icon' => 'fa-tube', 
        'img' => 'https://d2qjkwm11akmwu.cloudfront.net/products/811461_25-8-2021_17-3-59-1665779688.webp'
    ],
    [
        'name' => 'Minyak Kayu Putih Cap Lang 60ml', 'category' => 'P3K', 
        'price' => 22000, 'sold' => '20k+', 'rating' => '4.9', 'badge' => 'text-orange-600 bg-orange-50', 'icon' => 'fa-bottle-droplet', 
        'img' => 'https://d2qjkwm11akmwu.cloudfront.net/products/432687_5-7-2021_10-21-56-1665780059.webp'
    ],
];
?>

<div class="bg-slate-50 min-h-screen py-10" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="bg-gradient-to-r from-primary to-blue-500 rounded-[2rem] p-8 md:p-10 mb-10 shadow-floating flex flex-col md:flex-row items-center justify-between relative overflow-hidden group">
            <i class="fa-solid fa-file-prescription text-white/10 text-9xl absolute -right-4 -bottom-8 group-hover:scale-110 group-hover:-rotate-12 smooth-transition duration-500"></i>
            
            <div class="relative z-10 text-white md:w-2/3">
                <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm border border-white/30 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-4">
                    <i class="fa-solid fa-camera"></i> Layanan Praktis
                </div>
                <h2 class="text-3xl md:text-4xl font-extrabold mb-3 leading-tight text-white m-0">Punya Resep dari Dokter?</h2>
                <p class="text-blue-100 text-lg mb-8 max-w-lg leading-relaxed mt-2">Unggah foto resepmu sekarang. Apoteker kami akan menyiapkan obatnya dan kurir siap mengantar langsung ke pintu rumahmu.</p>
                <button class="bg-white text-primary px-8 py-3.5 rounded-full font-bold shadow-lg hover:bg-slate-50 hover:-translate-y-1 active:scale-95 smooth-transition flex items-center gap-2 border-none cursor-pointer">
                    <i class="fa-solid fa-upload"></i> Unggah Resep Sekarang
                </button>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            
            <div class="lg:w-1/4">
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm sticky top-28">
                    <h3 class="font-extrabold text-lg text-slate-900 mb-4 m-0">Kategori Obat</h3>
                    <div class="flex flex-col gap-2 mt-4">
                        <?php foreach ($categories as $cat): ?>
                        <button onclick="filterCategory('<?= addslashes($cat['name']) ?>')" class="cat-btn flex items-center gap-3 px-4 py-3 rounded-2xl smooth-transition font-bold text-sm w-full text-left border-none cursor-pointer <?= $cat['active'] ? 'bg-primary text-white shadow-md shadow-blue-200' : 'bg-transparent text-slate-600 hover:bg-slate-50 hover:text-primary' ?>" data-cat="<?= htmlspecialchars($cat['name']) ?>">
                            <i class="fa-solid <?= $cat['icon'] ?> w-5 text-center"></i>
                            <?= $cat['name'] ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="lg:w-3/4">
                
                <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-sm flex flex-col sm:flex-row gap-4 justify-between items-center mb-8">
                    <div class="relative w-full sm:w-2/3">
                        <i class="fa-solid fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" placeholder="Cari nama obat, vitamin, atau suplemen..." class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primaryLight rounded-2xl py-3 pl-12 pr-4 text-sm font-semibold text-slate-700 outline-none smooth-transition">
                    </div>
                    <div class="w-full sm:w-auto flex gap-2">
                        <button class="flex-1 sm:flex-none bg-slate-50 hover:bg-slate-100 text-slate-600 px-5 py-3 rounded-2xl font-bold text-sm border border-slate-200 smooth-transition flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-arrow-down-a-z"></i> Urutkan
                        </button>
                        <button class="flex-1 sm:flex-none bg-slate-50 hover:bg-slate-100 text-slate-600 px-5 py-3 rounded-2xl font-bold text-sm border border-slate-200 smooth-transition flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-sliders"></i> Filter
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    <?php foreach ($products as $i => $p): ?>
                    <div class="product-card bg-white rounded-3xl border border-slate-100 shadow-sm hover:-translate-y-2 hover:shadow-floating group flex flex-col smooth-transition relative overflow-hidden" data-category="<?= htmlspecialchars($p['category']) ?>">
                        
                        <a href="<?= BASE_URL ?>/pages/product-detail.php?id=<?= $i + 1 ?>" class="block p-5 pb-0 cursor-pointer no-underline flex-1">
                            <div class="bg-white rounded-2xl h-48 flex items-center justify-center mb-4 group-hover:scale-105 smooth-transition overflow-hidden relative p-2">
                                <?php if (!empty($p['img'])): ?>
                                <img src="<?= $p['img'] ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="object-contain mix-blend-multiply h-full w-full group-hover:scale-110 smooth-transition">
                                <?php else: ?>
                                <div class="absolute inset-0 bg-gradient-to-tr from-slate-100 to-slate-50 opacity-50"></div>
                                <i class="fa-solid <?= $p['icon'] ?> text-7xl text-slate-300 group-hover:text-slate-400 smooth-transition relative z-10"></i>
                                <?php endif; ?>
                            </div>
                            
                            <span class="text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-1 rounded-md w-fit mb-3 block <?= $p['badge'] ?>">
                                <?= $p['category'] ?>
                            </span>
                            
                            <h4 class="font-extrabold text-slate-900 text-[15px] leading-snug mb-2 group-hover:text-primary smooth-transition line-clamp-2 m-0" title="<?= $p['name'] ?>">
                                <?= $p['name'] ?>
                            </h4>
                            
                            <div class="flex items-center gap-3 mb-2 text-xs font-bold text-slate-500 mt-2">
                                <span class="flex items-center gap-1 text-amber-500"><i class="fa-solid fa-star"></i> <?= $p['rating'] ?></span>
                                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                <span>Terjual <?= $p['sold'] ?></span>
                            </div>
                        </a>
                        
                        <div class="p-5 pt-3 mt-auto border-t border-slate-50 flex items-center justify-between">
                            <div class="font-extrabold text-primary text-lg text-slate-900 m-0">Rp <?= number_format($p['price'],0,',','.') ?></div>
                            <button onclick="addToCartDirect('<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>')" class="w-10 h-10 rounded-xl bg-primaryLight text-primary hover:bg-primary hover:text-white flex items-center justify-center active:scale-95 smooth-transition border-none cursor-pointer shadow-sm" title="Tambah ke Keranjang">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                        
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-12 text-center">
                    <button class="bg-white border-2 border-slate-200 text-slate-600 hover:text-primary hover:border-primary px-8 py-3 rounded-full font-bold shadow-sm hover:shadow-md smooth-transition active:scale-95 cursor-pointer">
                        Muat Lebih Banyak
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

<div id="toast-stack" style="position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:10px"></div>
<style>@keyframes slideIn{from{opacity:0;transform:translateX(20px)}to{opacity:1;transform:translateX(0)}}</style>
<script>
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
let cartCount=parseInt(localStorage.getItem("em_cart")||"0");
function syncCart(){const b=document.getElementById("cart-count");if(!b)return;if(cartCount>0){b.textContent=cartCount;b.classList.remove("hidden");}else b.classList.add("hidden");}
function addToCartDirect(name){
    cartCount++;localStorage.setItem("em_cart",cartCount);syncCart();
    showToast(`<b>${name}</b> ditambahkan ke keranjang!`,"success");
}
syncCart();
function filterCategory(cat){
    document.querySelectorAll(".cat-btn").forEach(b=>{
        b.className=b.className.replace("bg-primary text-white shadow-md shadow-blue-200","bg-transparent text-slate-600");
    });
    const active=document.querySelector(`.cat-btn[data-cat="${cat}"]`);
    if(active){active.className=active.className.replace("bg-transparent text-slate-600","bg-primary text-white shadow-md shadow-blue-200");}
    document.querySelectorAll(".product-card").forEach(c=>{
        c.style.display=(cat==="Semua Produk"||c.dataset.category===cat)?"":" none";
    });
    const v=[...document.querySelectorAll(".product-card")].filter(c=>c.style.display!="none"&&c.style.display!=" none").length;
    showToast(v+" produk ditemukan untuk kategori: "+cat,"info",2000);
}
document.addEventListener("DOMContentLoaded",()=>{
    const si=document.querySelector("input[placeholder*='obat'],input[placeholder*='Cari']");
    if(si)si.addEventListener("input",function(){
        const q=this.value.toLowerCase();
        document.querySelectorAll(".product-card").forEach(c=>{
            c.style.display=c.querySelector("h4")?.textContent.toLowerCase().includes(q)?"":" none";
        });
    });
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>