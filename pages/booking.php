<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle   = 'Konsultasi Dokter — CareSync';
$currentPage = 'booking';

$extraHead = '
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: { sans: [\'"Plus Jakarta Sans"\', \'sans-serif\'] },
                colors: { primary:"#1D4ED8", primaryLight:"#EFF6FF", accent:"#10B981", dark:"#0F172A", textSoft:"#64748B" },
                boxShadow: { soft:"0 10px 40px -10px rgba(0,0,0,0.06)", floating:"0 20px 40px -15px rgba(29,78,216,0.2)" },
                keyframes: {
                    fadeInUp: {"from":{opacity:"0",transform:"translateY(20px)"},"to":{opacity:"1",transform:"translateY(0)"}}
                },
                animation: { "fade-in-up":"fadeInUp 0.4s ease-out forwards" }
            }
        }
    }
</script>
<style>
    body { background-color: #F8FAFC; margin: 0; }
    .smooth { transition: all 0.3s ease-in-out; }
    .modal-backdrop { position: fixed; inset: 0; background: rgba(15,23,42,0.55); backdrop-filter: blur(4px); z-index: 500; display: flex; align-items: center; justify-content: center; padding: 20px; }
    .modal-backdrop.hidden { display: none !important; }
    .modal-box { background: #fff; border-radius: 24px; padding: 32px; width: 100%; max-width: 460px; box-shadow: 0 25px 60px -10px rgba(0,0,0,0.2); animation: fadeInUp 0.3s ease; }
    #toast-stack{position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:10px}
</style>
';

include __DIR__ . '/../includes/header.php';

// 15 Data Dokter dengan Link Foto Pexels & Unsplash Aktif
$docs = [
    ['dr. Susanti Wulandari, Sp.KK', 'Spesialis Kulit & Kelamin', 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80', '4.9', '320+', 60000, true],
    ['dr. Budi Santoso, Sp.M', 'Spesialis Mata', 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80', '4.8', '280+', 75000, false],
    ['dr. Fenny Nurmahdi', 'Dokter Umum', 'https://images.unsplash.com/photo-1618498082410-b4aa22193b38?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80', '4.7', '195+', 35000, true],
    ['dr. Ika Syafitri, Sp.PD', 'Penyakit Dalam', 'https://images.pexels.com/photos/5215024/pexels-photo-5215024.jpeg?auto=compress&cs=tinysrgb&w=400', '4.9', '410+', 80000, true],
    ['dr. Ralph Edwards, Sp.D', 'Dermatologis', 'https://images.unsplash.com/photo-1537368910025-700350fe46c7?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80', '4.9', '150+', 55000, true],
    ['dr. Albert Boje, Sp.KG', 'Dokter Gigi', 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80', '4.8', '220+', 65000, true],
    ['dr. Hamida Jannat, Sp.JP', 'Kardiologis', 'https://images.unsplash.com/photo-1590086782957-93c06ef21604?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80', '5.0', '500+', 120000, false],
    ['dr. Leslie Alexander, Sp.KJ', 'Psikiater', 'https://images.unsplash.com/photo-1651008376811-b90baee60c1f?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80', '4.9', '340+', 90000, true],
    ['dr. Kevin Wijaya, Sp.A', 'Spesialis Anak', 'https://images.pexels.com/photos/5327585/pexels-photo-5327585.jpeg?auto=compress&cs=tinysrgb&w=400', '4.8', '400+', 70000, true],
    ['dr. Sarah Connor, Sp.OG', 'Kandungan', 'https://images.unsplash.com/photo-1532938911079-1b06ac7ceec7?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80', '4.9', '600+', 100000, false],
    ['dr. Ahmad Fauzi, Sp.THT', 'THT', 'https://images.unsplash.com/photo-1638202993928-7267aad84c31?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80', '4.7', '180+', 65000, true],
    ['dr. Diana Puspita', 'Dokter Umum', 'https://images.pexels.com/photos/5452293/pexels-photo-5452293.jpeg?auto=compress&cs=tinysrgb&w=400', '4.8', '850+', 30000, true],
    ['dr. Michael Chen, Sp.S', 'Saraf', 'https://images.unsplash.com/photo-1550831107-1553da8c8464?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80', '4.9', '210+', 110000, false],
    ['dr. Ratna Galih, Sp.Gk', 'Gizi Klinik', 'https://images.unsplash.com/photo-1622902046580-2b47f47f5471?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80', '4.8', '130+', 75000, true],
    ['dr. Yoseph Tarigan, Sp.OT', 'Ortopedi', 'https://images.unsplash.com/photo-1643297654416-05795d62e39c?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80', '4.9', '290+', 130000, true],
];
?>

<div class="bg-slate-50 min-h-screen py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="bg-gradient-to-r from-primary to-blue-500 rounded-[2rem] p-8 md:p-12 mb-10 shadow-floating flex flex-col md:flex-row items-center justify-between relative overflow-hidden group">
            <i class="fa-solid fa-user-doctor text-white/10 text-9xl absolute -right-6 -bottom-10 group-hover:scale-110 group-hover:-rotate-12 smooth duration-500"></i>
            <div class="relative z-10 text-white md:w-2/3">
                <span class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm border border-white/30 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-4">
                    <span class="w-2 h-2 bg-accent rounded-full animate-pulse"></span> Konsultasi Cepat
                </span>
                <h2 class="text-3xl md:text-4xl font-extrabold mb-3 leading-tight m-0">Pilih Dokter Spesialis Anda</h2>
                <p class="text-blue-100 text-lg mb-0 max-w-lg leading-relaxed mt-2">Ribuan pasien telah terbantu. Mulai konsultasi kesehatan Anda dengan dokter berlisensi resmi dari CareSync.</p>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8 items-start">
            
            <div class="w-full lg:w-1/4">
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm lg:sticky lg:top-28">
                    <h3 class="font-extrabold text-lg text-slate-900 mb-5 m-0">Kategori Dokter</h3>
                    
                    <div class="relative mb-6">
                        <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" id="search-doctor" placeholder="Cari dokter..." class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-primary rounded-xl py-2.5 pl-10 pr-4 text-sm font-semibold text-slate-700 outline-none smooth">
                    </div>

                    <div class="flex flex-col gap-2">
                        <?php 
                        $categories = [
                            ['name' => 'Semua Kategori', 'icon' => 'fa-user-doctor', 'active' => true],
                            ['name' => 'Dokter Umum', 'icon' => 'fa-stethoscope', 'active' => false],
                            ['name' => 'Spesialis Anak', 'icon' => 'fa-baby', 'active' => false],
                            ['name' => 'Kulit & Kelamin', 'icon' => 'fa-hand-dots', 'active' => false],
                            ['name' => 'Penyakit Dalam', 'icon' => 'fa-lungs', 'active' => false],
                            ['name' => 'Dokter Gigi', 'icon' => 'fa-tooth', 'active' => false],
                            ['name' => 'Psikiater', 'icon' => 'fa-brain', 'active' => false],
                        ];
                        foreach($categories as $cat): 
                        ?>
                        <button onclick="filterDoctorCategory(this, '<?= $cat['name'] ?>')" class="cat-btn flex items-center gap-3 px-4 py-3 rounded-2xl smooth font-bold text-sm w-full text-left border-none cursor-pointer <?= $cat['active'] ? 'bg-primary text-white shadow-md shadow-blue-200' : 'bg-transparent text-slate-600 hover:bg-slate-50 hover:text-primary' ?>" data-cat="<?= $cat['name'] ?>">
                            <i class="fa-solid <?= $cat['icon'] ?> w-5 text-center"></i>
                            <?= $cat['name'] ?>
                        </button>
                        <?php endforeach; ?>
                    </div>

                </div>
            </div>

            <div class="w-full lg:w-3/4">
                
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-extrabold text-xl text-slate-900 m-0">Menampilkan <span class="text-primary" id="doctor-count"><?= count($docs) ?> Dokter</span></h3>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-slate-500">Urutkan:</span>
                        <select class="bg-white border border-slate-200 text-sm font-bold text-slate-700 rounded-lg py-1.5 px-3 outline-none cursor-pointer hover:border-primary smooth">
                            <option>Rekomendasi</option>
                            <option>Harga Terendah</option>
                            <option>Harga Tertinggi</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" id="doctor-grid">
                    <?php foreach($docs as [$nm, $sp, $img, $rt, $pt, $pr, $av]): ?>
                    <div class="doctor-card bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-sm hover:-translate-y-2 hover:shadow-floating group smooth flex flex-col" 
                         data-category="<?= $sp ?>" data-name="<?= $nm ?>">
                        
                        <div class="overflow-hidden h-48 relative">
                            <img src="<?= $img ?>" alt="<?= $nm ?>" class="w-full h-full object-cover object-top group-hover:scale-110 smooth">
                            <div class="absolute top-3 right-3 <?= $av ? 'bg-accent' : 'bg-slate-400' ?> text-white text-[10px] font-bold px-2 py-1 rounded-full flex items-center gap-1 shadow-sm">
                                <span class="w-1.5 h-1.5 bg-white rounded-full <?= $av ? 'animate-pulse' : '' ?>"></span><?= $av ? 'Online' : 'Offline' ?>
                            </div>
                            <div class="absolute bottom-3 left-3 bg-white/90 px-2 py-1 rounded-full text-xs font-bold text-amber-500 flex items-center gap-1 shadow-sm">
                                <i class="fa-solid fa-star"></i> <?= $rt ?>
                            </div>
                        </div>
                        
                        <div class="p-5 flex flex-col flex-1">
                            <h3 class="font-extrabold text-dark text-[15px] group-hover:text-primary smooth mb-0.5 m-0 line-clamp-1" title="<?= $nm ?>"><?= $nm ?></h3>
                            <p class="text-primary text-xs font-bold mb-3 m-0"><?= $sp ?></p>
                            
                            <div class="flex items-center gap-2 text-xs text-textSoft mb-4 font-semibold">
                                <span class="flex items-center"><i class="fa-solid fa-users mr-1"></i><?= $pt ?></span>
                                <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                <span>Rp <?= number_format($pr, 0, ',', '.') ?></span>
                            </div>
                            
                            <div class="flex gap-2 mt-auto">
                                <button onclick="showDoctorModal('<?= addslashes($nm) ?>','<?= $sp ?>','<?= $img ?>','<?= $rt ?>','<?= $pt ?>',<?= $pr ?>)" class="flex-1 bg-primaryLight text-primary py-2.5 rounded-xl font-bold hover:bg-blue-100 active:scale-95 smooth text-sm border-none cursor-pointer">Detail</button>
                                <?php if($av): ?>
                                <button onclick="bookDoctor('<?= addslashes($nm) ?>')" class="flex-1 bg-primary text-white py-2.5 rounded-xl font-bold text-center hover:bg-blue-800 active:scale-95 smooth text-sm border-none cursor-pointer shadow-sm">Book Now</button>
                                <?php else: ?>
                                <button disabled class="flex-1 bg-slate-100 text-slate-400 py-2.5 rounded-xl font-bold text-sm cursor-not-allowed border-none">Offline</button>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                    <?php endforeach;?>
                </div>

                <div class="mt-12 text-center" id="load-more-container">
                    <button class="bg-white border-2 border-slate-200 text-slate-600 hover:text-primary hover:border-primary px-8 py-3 rounded-full font-bold shadow-sm hover:shadow-md smooth active:scale-95 cursor-pointer">
                        Muat Lebih Banyak
                    </button>
                </div>

            </div>
        </div>

    </div>
</div>

<div id="modal-doc" class="modal-backdrop hidden" onclick="if(event.target===this)closeModal('modal-doc')">
    <div class="modal-box">
        <div class="flex items-start gap-4 mb-5">
            <img id="md-img" src="" alt="" class="w-20 h-20 rounded-2xl object-cover object-top flex-shrink-0 border-2 border-slate-100">
            <div class="flex-1">
                <h3 id="md-name" class="font-extrabold text-xl text-dark mb-0.5 m-0"></h3>
                <p id="md-spec" class="text-primary text-sm font-bold mb-2 m-0"></p>
                <div class="flex gap-3 text-xs text-textSoft font-semibold">
                    <span id="md-rating" class="flex items-center gap-1"></span>
                    <span id="md-patients" class="flex items-center gap-1"></span>
                </div>
            </div>
            <button onclick="closeModal('modal-doc')" class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 smooth flex-shrink-0 border-none cursor-pointer"><i class="fa-solid fa-xmark text-sm"></i></button>
        </div>
        <div class="bg-blue-50 rounded-2xl p-4 mb-5">
            <div class="text-xs text-textSoft font-semibold mb-1">Biaya Konsultasi</div>
            <div id="md-price" class="text-2xl font-extrabold text-primary m-0"></div>
        </div>
        <div class="bg-slate-50 rounded-2xl p-4 mb-5 text-sm text-textSoft leading-relaxed">
            Dokter spesialis berpengalaman dengan track record pelayanan terbaik. Tersedia untuk konsultasi via chat maupun video call secara privat dan aman.
        </div>
        <div class="grid grid-cols-2 gap-3">
            <button onclick="closeModal('modal-doc')" class="py-3 rounded-2xl border-2 border-slate-200 font-bold text-slate-600 hover:border-slate-300 active:scale-95 smooth bg-transparent cursor-pointer">Tutup</button>
            <button onclick="bookDoctorFromModal()" class="py-3 rounded-2xl bg-primary text-white font-bold text-center hover:bg-blue-800 active:scale-95 smooth border-none cursor-pointer shadow-sm">Lanjut Booking</button>
        </div>
    </div>
</div>

<div id="toast-stack" style="position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:10px"></div>

<script>
function showToast(msg,type="info",dur=3500){
    const colors={info:"#1D4ED8",success:"#10B981",error:"#EF4444",warning:"#F59E0B"};
    const icons={info:"fa-circle-info",success:"fa-circle-check",error:"fa-circle-xmark",warning:"fa-triangle-exclamation"};
    const iconBgs={info:"#EFF6FF",success:"#ECFDF5",error:"#FEF2F2",warning:"#FFFBEB"};
    const s=document.getElementById("toast-stack");
    const t=document.createElement("div");
    t.style.cssText=`display:flex;align-items:center;gap:12px;padding:14px 18px;border-radius:16px;background:#fff;box-shadow:0 8px 30px -4px rgba(0,0,0,0.15);min-width:280px;font-size:14px;font-weight:600;color:#0F172A;border-left:4px solid ${colors[type]};animation:fadeInUp .3s ease`;
    t.innerHTML=`<div style="width:34px;height:34px;border-radius:10px;background:${iconBgs[type]};color:${colors[type]};display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fa-solid ${icons[type]}"></i></div><div style="flex:1">${msg}</div><button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:18px;padding:0;line-height:1">×</button>`;
    s.appendChild(t);
    setTimeout(()=>{t.style.opacity="0";t.style.transform="translateX(20px)";setTimeout(()=>t.remove(),300);},dur);
}

// Fitur Kategori Aktif
function filterDoctorCategory(btn, cat) {
    // Reset warna semua tombol
    document.querySelectorAll('.cat-btn').forEach(b => {
        b.className = b.className.replace("bg-primary text-white shadow-md shadow-blue-200", "bg-transparent text-slate-600 hover:bg-slate-50 hover:text-primary");
    });
    // Aktifkan tombol yang diklik
    btn.className = btn.className.replace("bg-transparent text-slate-600 hover:bg-slate-50 hover:text-primary", "bg-primary text-white shadow-md shadow-blue-200");

    // Lakukan filtering kartu
    document.querySelectorAll('.doctor-card').forEach(c => {
        const cardCat = c.getAttribute('data-category');
        let isMatch = false;
        
        if (cat === 'Semua Kategori') {
            isMatch = true;
        } else if (cat === 'Kulit & Kelamin' && (cardCat.includes('Kulit') || cardCat.includes('Dermatologis'))) {
            isMatch = true; // Menggabungkan Dermatologis & Kulit
        } else if (cardCat.includes(cat)) {
            isMatch = true;
        }

        c.style.display = isMatch ? 'flex' : 'none';
    });

    updateDoctorCount();
}

// Fitur Search Bar Aktif
document.getElementById('search-doctor').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    
    // Kembalikan filter ke "Semua Kategori" saat mulai mengetik
    const allBtn = document.querySelector('.cat-btn[data-cat="Semua Kategori"]');
    if(allBtn) {
        document.querySelectorAll('.cat-btn').forEach(b => {
            b.className = b.className.replace("bg-primary text-white shadow-md shadow-blue-200", "bg-transparent text-slate-600 hover:bg-slate-50 hover:text-primary");
        });
        allBtn.className = allBtn.className.replace("bg-transparent text-slate-600 hover:bg-slate-50 hover:text-primary", "bg-primary text-white shadow-md shadow-blue-200");
    }

    document.querySelectorAll('.doctor-card').forEach(c => {
        const name = c.getAttribute('data-name').toLowerCase();
        const spec = c.getAttribute('data-category').toLowerCase();
        
        if(name.includes(q) || spec.includes(q)) {
            c.style.display = 'flex';
        } else {
            c.style.display = 'none';
        }
    });
    
    updateDoctorCount();
});

// Update Teks Jumlah Dokter Dinamis
function updateDoctorCount() {
    const visibleCount = [...document.querySelectorAll('.doctor-card')].filter(c => c.style.display !== 'none').length;
    document.getElementById('doctor-count').textContent = visibleCount + ' Dokter';
    
    // Sembunyikan tombol "Muat Lebih Banyak" jika hasil sedikit
    const loadMoreBtn = document.getElementById('load-more-container');
    if (visibleCount < 6) {
        loadMoreBtn.style.display = 'none';
    } else {
        loadMoreBtn.style.display = 'block';
    }
}

// Modal Interaksi
function showDoctorModal(nm,sp,img,rt,pt,pr){
    document.getElementById('md-img').src=img;
    document.getElementById('md-name').textContent=nm;
    document.getElementById('md-spec').textContent=sp;
    document.getElementById('md-rating').innerHTML=`<i class="fa-solid fa-star text-amber-400"></i> ${rt} Rating`;
    document.getElementById('md-patients').innerHTML=`<i class="fa-solid fa-users"></i> ${pt} Pasien`;
    document.getElementById('md-price').textContent='Rp '+pr.toLocaleString('id-ID');
    document.getElementById('modal-doc').classList.remove('hidden');
    document.getElementById('modal-doc').setAttribute('data-current-doc', nm);
}

function closeModal(id){ document.getElementById(id).classList.add('hidden'); }
document.addEventListener('keydown',e=>{if(e.key==='Escape') closeModal('modal-doc'); });

function bookDoctor(doctorName) {
    showToast(`Memproses jadwal konsultasi dengan <b>${doctorName}</b>...`, "success");
    setTimeout(() => {
        window.location.href = '<?= BASE_URL ?>/pages/payment.php'; 
    }, 2000);
}

function bookDoctorFromModal() {
    const docName = document.getElementById('modal-doc').getAttribute('data-current-doc');
    closeModal('modal-doc');
    bookDoctor(docName);
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>