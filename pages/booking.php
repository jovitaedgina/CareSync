<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/booking_helpers.php';

requireLogin();

$pageTitle = 'Konsultasi Dokter CareSync';
$currentPage = 'booking';

$doctors = getBookingDoctors($pdo);
$specializations = getBookingSpecializations($doctors);
$dateOptions = getBookingDateOptions();

$extraHead = '
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: { sans: [\'"Plus Jakarta Sans"\', \'sans-serif\'] },
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
                },
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
    .modal-box { background: #fff; border-radius: 24px; padding: 32px; width: 100%; max-width: 560px; box-shadow: 0 25px 60px -10px rgba(0,0,0,0.2); animation: fadeInUp 0.3s ease; }
    .slot-btn.active { background: #1D4ED8; color: #fff; border-color: #1D4ED8; box-shadow: 0 10px 30px -15px rgba(29,78,216,0.45); }
</style>
';

include __DIR__ . '/../includes/header.php';
?>

<div class="bg-slate-50 min-h-screen py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-r from-primary to-blue-500 rounded-[2rem] p-8 md:p-12 mb-10 shadow-floating flex flex-col md:flex-row items-center justify-between relative overflow-hidden group">
            <i class="fa-solid fa-user-doctor text-white/10 text-9xl absolute -right-6 -bottom-10 group-hover:scale-110 group-hover:-rotate-12 smooth duration-500"></i>
            <div class="relative z-10 text-white md:w-2/3">
                <span class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm border border-white/30 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-4">
                    <span class="w-2 h-2 bg-accent rounded-full animate-pulse"></span> Booking Konsultasi
                </span>
                <h2 class="text-3xl md:text-4xl font-extrabold mb-3 leading-tight m-0">Pilih Dokter, Spesialisasi, dan Slot Konsultasi</h2>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] gap-8 items-start">
            <aside class="w-full">
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm lg:sticky lg:top-28">
                    <h3 class="font-extrabold text-lg text-slate-900 mb-5 m-0">Filter Booking</h3>

                    <div class="relative mb-5">
                        <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" id="search-doctor" placeholder="Cari dokter atau spesialisasi..." class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-primary rounded-xl py-3 pl-10 pr-4 text-sm font-semibold text-slate-700 outline-none smooth">
                    </div>

                    <div class="space-y-2" id="category-list">
                        <button type="button" data-category="Semua Kategori" class="cat-btn flex items-center gap-3 px-4 py-3 rounded-2xl smooth font-bold text-sm w-full text-left border-none cursor-pointer bg-primary text-white shadow-md shadow-blue-200">
                            <i class="fa-solid fa-user-doctor w-5 text-center"></i>
                            Semua Kategori
                        </button>
                        <?php foreach ($specializations as $specialization): ?>
                        <button type="button" data-category="<?= htmlspecialchars($specialization['name']) ?>" class="cat-btn flex items-center gap-3 px-4 py-3 rounded-2xl smooth font-bold text-sm w-full text-left border-none cursor-pointer bg-transparent text-slate-600 hover:bg-slate-50 hover:text-primary">
                            <i class="fa-solid <?= htmlspecialchars($specialization['icon']) ?> w-5 text-center"></i>
                            <?= htmlspecialchars($specialization['name']) ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </aside>

            <section class="w-full">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <div>
                        <h3 class="font-extrabold text-xl text-slate-900 m-0">Dokter Tersedia</h3>
                        <p class="text-slate-500 font-medium mt-1 m-0">Menampilkan <span class="text-primary font-extrabold" id="doctor-count"><?= count($doctors) ?> dokter</span> untuk booking konsultasi.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-slate-500">Urutkan:</span>
                        <select id="sort-doctor" class="bg-white border border-slate-200 text-sm font-bold text-slate-700 rounded-xl py-2 px-3 outline-none cursor-pointer hover:border-primary smooth">
                            <option value="recommendation">Rekomendasi</option>
                            <option value="fee_low">Harga Terendah</option>
                            <option value="fee_high">Harga Tertinggi</option>
                            <option value="name">Nama A-Z</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6" id="doctor-grid">
                    <?php foreach ($doctors as $doctor): ?>
                    <article class="doctor-card bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-sm hover:-translate-y-2 hover:shadow-floating group smooth flex flex-col"
                        data-id="<?= $doctor['id'] ?>"
                        data-name="<?= htmlspecialchars($doctor['name']) ?>"
                        data-specialization="<?= htmlspecialchars($doctor['specialization']) ?>"
                        data-fee="<?= $doctor['fee'] ?>"
                        data-rating="<?= htmlspecialchars($doctor['rating']) ?>"
                        data-patients="<?= htmlspecialchars($doctor['patients']) ?>">

                        <div class="overflow-hidden h-48 relative">
                            <img src="<?= htmlspecialchars($doctor['image']) ?>" alt="<?= htmlspecialchars($doctor['name']) ?>" class="w-full h-full object-cover object-top group-hover:scale-110 smooth">
                            <div class="absolute top-3 right-3 <?= $doctor['online'] ? 'bg-accent' : 'bg-slate-400' ?> text-white text-[10px] font-bold px-2 py-1 rounded-full flex items-center gap-1 shadow-sm">
                                <span class="w-1.5 h-1.5 bg-white rounded-full <?= $doctor['online'] ? 'animate-pulse' : '' ?>"></span><?= $doctor['online'] ? 'Online' : 'Offline' ?>
                            </div>
                            <div class="absolute bottom-3 left-3 bg-white/90 px-2 py-1 rounded-full text-xs font-bold text-amber-500 flex items-center gap-1 shadow-sm">
                                <i class="fa-solid fa-star"></i> <?= htmlspecialchars($doctor['rating']) ?>
                            </div>
                        </div>

                        <div class="p-5 flex flex-col flex-1">
                            <h3 class="font-extrabold text-dark text-[15px] group-hover:text-primary smooth mb-0.5 m-0 line-clamp-1" title="<?= htmlspecialchars($doctor['name']) ?>"><?= htmlspecialchars($doctor['name']) ?></h3>
                            <p class="text-primary text-xs font-bold mb-3 m-0"><?= htmlspecialchars($doctor['specialization']) ?></p>

                            <div class="flex items-center gap-2 text-xs text-textSoft mb-4 font-semibold flex-wrap">
                                <span class="flex items-center"><i class="fa-solid fa-users mr-1"></i><?= htmlspecialchars($doctor['patients']) ?></span>
                                <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                <span><?= 'Rp ' . number_format($doctor['fee'], 0, ',', '.') ?></span>
                                <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                <span class="truncate max-w-[120px]" title="<?= htmlspecialchars($doctor['license']) ?>"><?= htmlspecialchars($doctor['license']) ?></span>
                            </div>

                            <div class="mt-auto grid grid-cols-2 gap-2">
                                <button type="button" onclick="openDoctorDetail(<?= $doctor['id'] ?>)" class="bg-primaryLight text-primary py-2.5 rounded-xl font-bold hover:bg-blue-100 active:scale-95 smooth text-sm border-none cursor-pointer">Detail</button>
                                <button type="button" onclick="openBookingModal(<?= $doctor['id'] ?>)" class="bg-primary text-white py-2.5 rounded-xl font-bold text-center hover:bg-blue-800 active:scale-95 smooth text-sm border-none cursor-pointer shadow-sm">Booking</button>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>

                <div id="empty-state" class="hidden bg-white border border-dashed border-slate-200 rounded-3xl p-12 text-center shadow-sm">
                    <div class="w-20 h-20 rounded-full bg-primaryLight text-primary flex items-center justify-center mx-auto mb-4 text-3xl">
                        <i class="fa-solid fa-user-doctor"></i>
                    </div>
                    <h4 class="text-xl font-extrabold text-slate-900 m-0 mb-2">Dokter tidak ditemukan</h4>
                    <p class="text-slate-500 font-medium max-w-md mx-auto m-0">Coba ganti kata kunci pencarian atau pilih spesialisasi lain agar hasil booking lebih luas.</p>
                </div>
            </section>
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
                <div class="flex flex-wrap gap-3 text-xs text-textSoft font-semibold">
                    <span id="md-rating" class="flex items-center gap-1"></span>
                    <span id="md-patients" class="flex items-center gap-1"></span>
                    <span id="md-license" class="flex items-center gap-1"></span>
                </div>
            </div>
            <button type="button" onclick="closeModal('modal-doc')" class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 smooth flex-shrink-0 border-none cursor-pointer"><i class="fa-solid fa-xmark text-sm"></i></button>
        </div>
        <div class="bg-blue-50 rounded-2xl p-4 mb-5">
            <div class="text-xs text-textSoft font-semibold mb-1">Biaya Konsultasi</div>
            <div id="md-price" class="text-2xl font-extrabold text-primary m-0"></div>
        </div>
        <div class="bg-slate-50 rounded-2xl p-4 mb-5 text-sm text-textSoft leading-relaxed">
            Dokter ini bisa langsung dibooking berdasarkan spesialisasinya. Sistem akan menampilkan slot yang masih kosong agar tidak bentrok dengan jadwal konsultasi lain.
        </div>
        <div class="grid grid-cols-2 gap-3">
            <button type="button" onclick="closeModal('modal-doc')" class="py-3 rounded-2xl border-2 border-slate-200 font-bold text-slate-600 hover:border-slate-300 active:scale-95 smooth bg-transparent cursor-pointer">Tutup</button>
            <button type="button" id="md-book-btn" class="py-3 rounded-2xl bg-primary text-white font-bold text-center hover:bg-blue-800 active:scale-95 smooth border-none cursor-pointer shadow-sm">Lanjut Booking</button>
        </div>
    </div>
</div>

<div id="modal-book" class="modal-backdrop hidden" onclick="if(event.target===this)closeModal('modal-book')">
    <div class="modal-box max-w-2xl">
        <div class="flex items-start gap-4 mb-6">
            <img id="bk-img" src="" alt="" class="w-20 h-20 rounded-2xl object-cover object-top flex-shrink-0 border-2 border-slate-100">
            <div class="flex-1">
                <h3 id="bk-name" class="font-extrabold text-xl text-dark mb-0.5 m-0"></h3>
                <p id="bk-spec" class="text-primary text-sm font-bold mb-2 m-0"></p>
                <div class="flex flex-wrap gap-3 text-xs text-textSoft font-semibold">
                    <span id="bk-rating" class="flex items-center gap-1"></span>
                    <span id="bk-fee" class="flex items-center gap-1"></span>
                </div>
            </div>
            <button type="button" onclick="closeModal('modal-book')" class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 smooth flex-shrink-0 border-none cursor-pointer"><i class="fa-solid fa-xmark text-sm"></i></button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[220px_minmax(0,1fr)] gap-6">
            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tanggal Konsultasi</label>
                <select id="booking-date" class="w-full bg-white border border-slate-200 text-sm font-bold text-slate-700 rounded-xl py-3 px-3 outline-none cursor-pointer hover:border-primary smooth mb-4">
                    <?php foreach ($dateOptions as $option): ?>
                    <option value="<?= htmlspecialchars($option['value']) ?>"><?= htmlspecialchars($option['label']) ?></option>
                    <?php endforeach; ?>
                </select>

                <div class="bg-white rounded-2xl border border-slate-200 p-4">
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Ringkasan</div>
                    <p class="text-sm text-slate-600 m-0 leading-relaxed">Booking akan disimpan ke jadwal konsultasi Anda dengan status awal <span class="font-extrabold text-amber-500">Menunggu</span>.</p>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h4 class="font-extrabold text-slate-900 text-lg m-0">Pilih Slot Tersedia</h4>
                        <p class="text-slate-500 text-sm font-medium m-0 mt-1">Slot otomatis difilter dari jadwal yang sudah terbooking.</p>
                    </div>
                </div>

                <div id="slot-state" class="text-sm text-slate-500 font-semibold bg-slate-50 rounded-2xl px-4 py-3 border border-slate-100 mb-4">
                    Pilih tanggal untuk melihat slot yang tersedia.
                </div>

                <div id="slot-list" class="grid grid-cols-2 sm:grid-cols-3 gap-3"></div>

                <div class="mt-6 flex flex-col sm:flex-row gap-3">
                    <button type="button" onclick="closeModal('modal-book')" class="flex-1 py-3 rounded-2xl border-2 border-slate-200 font-bold text-slate-600 hover:border-slate-300 active:scale-95 smooth bg-transparent cursor-pointer">Batal</button>
                    <button type="button" id="confirm-booking" class="flex-1 py-3 rounded-2xl bg-primary text-white font-bold text-center hover:bg-blue-800 active:scale-95 smooth border-none cursor-pointer shadow-sm disabled:bg-slate-300 disabled:cursor-not-allowed" disabled>
                        Konfirmasi Booking
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="toast-stack" style="position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:10px"></div>

<script>
const BASE_URL = '<?= BASE_URL ?>';
const doctors = <?= json_encode($doctors, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
const doctorMap = Object.fromEntries(doctors.map((doctor) => [doctor.id, doctor]));

let activeCategory = 'Semua Kategori';
let searchQuery = '';
let sortMode = 'recommendation';
let currentBookingDoctorId = null;
let selectedSlot = null;

function showToast(msg, type = 'info', dur = 3500) {
    const colors = { info: '#1D4ED8', success: '#10B981', error: '#EF4444', warning: '#F59E0B' };
    const icons = { info: 'fa-circle-info', success: 'fa-circle-check', error: 'fa-circle-xmark', warning: 'fa-triangle-exclamation' };
    const iconBgs = { info: '#EFF6FF', success: '#ECFDF5', error: '#FEF2F2', warning: '#FFFBEB' };
    const stack = document.getElementById('toast-stack');
    const toast = document.createElement('div');
    toast.style.cssText = `display:flex;align-items:center;gap:12px;padding:14px 18px;border-radius:16px;background:#fff;box-shadow:0 8px 30px -4px rgba(0,0,0,0.15);min-width:280px;font-size:14px;font-weight:600;color:#0F172A;border-left:4px solid ${colors[type]};animation:fadeInUp .3s ease`;
    toast.innerHTML = `<div style="width:34px;height:34px;border-radius:10px;background:${iconBgs[type]};color:${colors[type]};display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fa-solid ${icons[type]}"></i></div><div style="flex:1">${msg}</div><button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:18px;padding:0;line-height:1">×</button>`;
    stack.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(20px)';
        setTimeout(() => toast.remove(), 300);
    }, dur);
}

function formatRupiah(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
}

function extractPatients(patientLabel) {
    const normalized = String(patientLabel || '').replace(/\D/g, '');
    return normalized ? parseInt(normalized, 10) : 0;
}

function getSortedCards() {
    const cards = Array.from(document.querySelectorAll('.doctor-card'));

    cards.sort((a, b) => {
        const doctorA = doctorMap[parseInt(a.dataset.id, 10)];
        const doctorB = doctorMap[parseInt(b.dataset.id, 10)];

        if (sortMode === 'fee_low') {
            return doctorA.fee - doctorB.fee;
        }

        if (sortMode === 'fee_high') {
            return doctorB.fee - doctorA.fee;
        }

        if (sortMode === 'name') {
            return doctorA.name.localeCompare(doctorB.name, 'id');
        }

        const ratingDiff = parseFloat(doctorB.rating) - parseFloat(doctorA.rating);
        if (ratingDiff !== 0) {
            return ratingDiff;
        }

        return extractPatients(doctorB.patients) - extractPatients(doctorA.patients);
    });

    return cards;
}

function applyDoctorFilters() {
    const grid = document.getElementById('doctor-grid');
    const cards = getSortedCards();

    cards.forEach((card) => {
        const doctor = doctorMap[parseInt(card.dataset.id, 10)];
        const matchesCategory = activeCategory === 'Semua Kategori' || doctor.specialization === activeCategory;
        const haystack = `${doctor.name} ${doctor.specialization}`.toLowerCase();
        const matchesSearch = haystack.includes(searchQuery);

        card.style.display = matchesCategory && matchesSearch ? 'flex' : 'none';
        grid.appendChild(card);
    });

    const visibleCount = cards.filter((card) => card.style.display !== 'none').length;
    document.getElementById('doctor-count').textContent = `${visibleCount} dokter`;
    document.getElementById('empty-state').classList.toggle('hidden', visibleCount > 0);
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        closeModal('modal-doc');
        closeModal('modal-book');
    }
});

function openDoctorDetail(doctorId) {
    const doctor = doctorMap[doctorId];
    if (!doctor) {
        return;
    }

    document.getElementById('md-img').src = doctor.image;
    document.getElementById('md-name').textContent = doctor.name;
    document.getElementById('md-spec').textContent = doctor.specialization;
    document.getElementById('md-rating').innerHTML = `<i class="fa-solid fa-star text-amber-400"></i> ${doctor.rating} Rating`;
    document.getElementById('md-patients').innerHTML = `<i class="fa-solid fa-users"></i> ${doctor.patients} Pasien`;
    document.getElementById('md-license').innerHTML = `<i class="fa-solid fa-id-card"></i> ${doctor.license}`;
    document.getElementById('md-price').textContent = formatRupiah(doctor.fee);
    document.getElementById('md-book-btn').onclick = () => {
        closeModal('modal-doc');
        openBookingModal(doctorId);
    };
    document.getElementById('modal-doc').classList.remove('hidden');
}

function resetBookingState() {
    selectedSlot = null;
    document.getElementById('confirm-booking').disabled = true;
    document.getElementById('slot-list').innerHTML = '';
}

async function loadSlots() {
    if (!currentBookingDoctorId) {
        return;
    }

    const date = document.getElementById('booking-date').value;
    const slotState = document.getElementById('slot-state');
    const slotList = document.getElementById('slot-list');

    resetBookingState();
    slotState.textContent = 'Memuat slot konsultasi...';

    try {
        const response = await fetch(`${BASE_URL}/api/booking/slots.php?doctor_id=${currentBookingDoctorId}&date=${encodeURIComponent(date)}`, {
            credentials: 'same-origin'
        });
        const result = await response.json();

        if (!response.ok || result.status !== 'success') {
            throw new Error(result.message || 'Gagal mengambil slot booking.');
        }

        if (!result.data.slots.length) {
            slotState.textContent = 'Semua slot pada tanggal ini sudah penuh. Pilih tanggal lain.';
            return;
        }

        slotState.textContent = `Pilih salah satu dari ${result.data.slots.length} slot yang tersedia.`;

        result.data.slots.forEach((slot) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'slot-btn border border-slate-200 rounded-2xl py-3 px-4 font-bold text-slate-700 hover:border-primary hover:text-primary smooth bg-white cursor-pointer';
            button.textContent = slot.label;
            button.onclick = () => {
                selectedSlot = slot.value;
                document.querySelectorAll('.slot-btn').forEach((slotBtn) => slotBtn.classList.remove('active'));
                button.classList.add('active');
                document.getElementById('confirm-booking').disabled = false;
            };
            slotList.appendChild(button);
        });
    } catch (error) {
        slotState.textContent = error.message;
        showToast(error.message, 'error');
    }
}

function openBookingModal(doctorId) {
    const doctor = doctorMap[doctorId];
    if (!doctor) {
        return;
    }

    currentBookingDoctorId = doctorId;
    selectedSlot = null;

    document.getElementById('bk-img').src = doctor.image;
    document.getElementById('bk-name').textContent = doctor.name;
    document.getElementById('bk-spec').textContent = doctor.specialization;
    document.getElementById('bk-rating').innerHTML = `<i class="fa-solid fa-star text-amber-400"></i> ${doctor.rating} Rating`;
    document.getElementById('bk-fee').innerHTML = `<i class="fa-solid fa-wallet text-primary"></i> ${formatRupiah(doctor.fee)}`;
    document.getElementById('modal-book').classList.remove('hidden');
    document.getElementById('confirm-booking').disabled = true;
    loadSlots();
}

async function confirmBooking() {
    if (!currentBookingDoctorId || !selectedSlot) {
        showToast('Pilih slot booking terlebih dahulu.', 'warning');
        return;
    }

    const button = document.getElementById('confirm-booking');
    const date = document.getElementById('booking-date').value;
    const originalText = button.textContent;

    button.disabled = true;
    button.textContent = 'Memproses...';

    try {
        const response = await fetch(`${BASE_URL}/api/booking/create.php`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                doctor_id: currentBookingDoctorId,
                date,
                time: selectedSlot
            })
        });
        const result = await response.json();

        if (!response.ok || result.status !== 'success') {
            throw new Error(result.message || 'Booking konsultasi gagal diproses.');
        }

        const doctorName = result.data.doctor.name;
        showToast(`Booking dengan <b>${doctorName}</b> berhasil untuk ${result.data.date} pukul ${result.data.time}.`, 'success', 4500);
        closeModal('modal-book');
        setTimeout(() => {
            window.location.href = `${BASE_URL}/pages/consultation.php?consultation_id=${result.data.consultation_id}`;
        }, 1200);
    } catch (error) {
        showToast(error.message, 'error');
        button.disabled = false;
        button.textContent = originalText;
        loadSlots();
        return;
    }

    button.textContent = originalText;
}

document.getElementById('search-doctor').addEventListener('input', function () {
    searchQuery = this.value.trim().toLowerCase();
    applyDoctorFilters();
});

document.getElementById('sort-doctor').addEventListener('change', function () {
    sortMode = this.value;
    applyDoctorFilters();
});

document.querySelectorAll('.cat-btn').forEach((button) => {
    button.addEventListener('click', function () {
        activeCategory = this.dataset.category;
        document.querySelectorAll('.cat-btn').forEach((btn) => {
            btn.className = 'cat-btn flex items-center gap-3 px-4 py-3 rounded-2xl smooth font-bold text-sm w-full text-left border-none cursor-pointer bg-transparent text-slate-600 hover:bg-slate-50 hover:text-primary';
        });
        this.className = 'cat-btn flex items-center gap-3 px-4 py-3 rounded-2xl smooth font-bold text-sm w-full text-left border-none cursor-pointer bg-primary text-white shadow-md shadow-blue-200';
        applyDoctorFilters();
    });
});

document.getElementById('booking-date').addEventListener('change', loadSlots);
document.getElementById('confirm-booking').addEventListener('click', confirmBooking);

applyDoctorFilters();
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
