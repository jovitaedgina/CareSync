<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/booking_helpers.php';

requireLogin();

$pageTitle = 'Riwayat Konsultasi';
$currentPage = 'history';

$user = currentUser();
$userId = (int) ($user['id'] ?? 0);
$patientId = ensurePatientProfile($pdo, $userId);
syncConsultationStatuses($pdo);
$doctorMap = [];

foreach (getBookingDoctors($pdo) as $doctor) {
    $doctorMap[$doctor['id']] = $doctor;
}

$stmt = $pdo->prepare(
    'SELECT sk.idKonsultasi, sk.tanggal, sk.status, d.idDokter, d.spesialisasi, d.nomorSTR, u.nama AS doctor_name
     FROM SesiKonsultasi sk
     INNER JOIN Dokter d ON d.idDokter = sk.idDokter
     INNER JOIN users u ON u.id = d.id_user
     WHERE sk.idPasien = :idPasien
     ORDER BY sk.tanggal DESC'
);
$stmt->execute([':idPasien' => $patientId]);

$colorClasses = ['bg-blue-500', 'bg-emerald-500', 'bg-amber-500', 'bg-violet-500', 'bg-rose-500'];
$histories = [];

foreach ($stmt->fetchAll() as $index => $row) {
    $doctorId = (int) $row['iddokter'];
    $doctor = $doctorMap[$doctorId] ?? null;
    $status = $row['status'];
    $statusKey = $status === 'Selesai' ? 'selesai' : 'aktif';
    $initials = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $row['doctor_name']), 0, 2) ?: 'DR');

    $histories[] = [
        'id' => (int) $row['idkonsultasi'],
        'doctor_name' => $row['doctor_name'],
        'specialization' => $row['spesialisasi'],
        'date_iso' => $row['tanggal'],
        'date_label' => date('d M Y H:i', strtotime($row['tanggal'])),
        'status' => $status,
        'status_key' => $statusKey,
        'license' => $row['nomorstr'],
        'fee' => $doctor['fee'] ?? 0,
        'image' => $doctor['image'] ?? '',
        'rating' => $doctor['rating'] ?? '4.8',
        'patients' => $doctor['patients'] ?? '100+',
        'initials' => $initials,
        'color' => $colorClasses[$index % count($colorClasses)],
    ];
}

$firstHistory = $histories[0] ?? null;

$extraHead = '
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: { sans: [\'"Plus Jakarta Sans"\', \'sans-serif\'] },
                colors: {
                    primary: \'#1D4ED8\',
                    primaryLight: \'#EFF6FF\',
                    accent: \'#10B981\',
                    dark: \'#0F172A\',
                    textSoft: \'#64748B\'
                },
                boxShadow: {
                    floating: \'0 20px 40px -15px rgba(29, 78, 216, 0.15)\'
                }
            }
        }
    }
</script>
<style>
    body { background-color: #F8FAFC; margin: 0; }
    .smooth-transition { transition: all 0.3s ease-in-out; }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
';

include __DIR__ . '/../includes/header.php';
?>

<div class="bg-slate-50 min-h-screen py-10" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 m-0">Riwayat Booking Konsultasi</h1>
                <p class="text-slate-500 font-medium mt-2 m-0"><?= htmlspecialchars($user['name'] ?? 'Pasien CareSync') ?> | Total booking <?= count($histories) ?></p>
            </div>
            <a href="<?= BASE_URL ?>/pages/booking.php" class="bg-primary text-white hover:bg-blue-800 px-5 py-3 rounded-xl font-bold text-sm shadow-md flex items-center gap-2 smooth-transition no-underline">
                <i class="fa-solid fa-calendar-plus"></i> Booking Baru
            </a>
        </div>

        <?php if (!$histories): ?>
        <div class="bg-white border border-dashed border-slate-200 rounded-[2rem] p-12 text-center shadow-sm">
            <div class="w-20 h-20 rounded-full bg-primaryLight text-primary flex items-center justify-center mx-auto mb-5 text-3xl">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900 m-0 mb-2">Belum ada booking konsultasi</h2>
            <p class="text-slate-500 font-medium max-w-lg mx-auto m-0 mb-6">Saat user memilih dokter, spesialisasi, tanggal, dan slot di modul booking, data konsultasi akan otomatis muncul di halaman ini.</p>
            <a href="<?= BASE_URL ?>/pages/booking.php" class="inline-flex items-center gap-2 bg-primary text-white px-6 py-3 rounded-xl font-bold shadow-md hover:bg-blue-800 smooth-transition no-underline">
                <i class="fa-solid fa-stethoscope"></i> Mulai Booking
            </a>
        </div>
        <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_360px] gap-8 items-start">
            <div>
                <div class="flex overflow-x-auto no-scrollbar gap-2 mb-6 border-b border-slate-200 pb-2">
                    <button class="filter-btn active whitespace-nowrap px-4 py-2 rounded-full text-sm font-bold bg-dark text-white smooth-transition cursor-pointer border-none" data-filter="all">Semua</button>
                    <button class="filter-btn whitespace-nowrap px-4 py-2 rounded-full text-sm font-bold bg-transparent text-slate-500 hover:text-dark smooth-transition cursor-pointer border-none" data-filter="aktif">Aktif</button>
                    <button class="filter-btn whitespace-nowrap px-4 py-2 rounded-full text-sm font-bold bg-transparent text-slate-500 hover:text-dark smooth-transition cursor-pointer border-none" data-filter="selesai">Selesai</button>
                </div>

                <div id="history-list" class="flex flex-col gap-4">
                    <?php foreach ($histories as $index => $history): ?>
                    <?php $isActive = $index === 0; ?>
                    <article
                        class="history-card bg-white p-5 rounded-2xl border <?= $isActive ? 'border-primary bg-primaryLight' : 'border-slate-200' ?> hover:border-primary cursor-pointer smooth-transition flex gap-4 items-start group"
                        data-status="<?= htmlspecialchars($history['status_key']) ?>"
                        data-id="<?= $history['id'] ?>"
                        data-doctor="<?= htmlspecialchars($history['doctor_name']) ?>"
                        data-specialization="<?= htmlspecialchars($history['specialization']) ?>"
                        data-date="<?= htmlspecialchars($history['date_label']) ?>"
                        data-status-label="<?= htmlspecialchars($history['status']) ?>"
                        data-license="<?= htmlspecialchars($history['license']) ?>"
                        data-fee="<?= $history['fee'] ?>"
                        data-rating="<?= htmlspecialchars($history['rating']) ?>"
                        data-patients="<?= htmlspecialchars($history['patients']) ?>"
                        data-image="<?= htmlspecialchars($history['image']) ?>">

                        <div class="w-12 h-12 rounded-xl <?= $history['color'] ?> flex items-center justify-center text-sm font-extrabold text-white flex-shrink-0 shadow-sm group-hover:scale-105 smooth-transition">
                            <?= htmlspecialchars($history['initials']) ?>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start gap-2 mb-1">
                                <h4 class="font-extrabold text-slate-900 text-[15px] m-0 truncate"><?= htmlspecialchars($history['doctor_name']) ?></h4>
                                <span class="text-[11px] font-semibold text-slate-400 whitespace-nowrap"><?= htmlspecialchars($history['date_label']) ?></span>
                            </div>
                            <div class="text-xs font-semibold text-primary mb-3"><?= htmlspecialchars($history['specialization']) ?></div>

                            <div class="flex flex-wrap gap-2 items-center">
                                <span class="text-[10px] font-bold px-2.5 py-1 rounded-md border <?= $history['status'] === 'Selesai' ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : 'bg-amber-50 text-amber-600 border-amber-200' ?>">
                                    <i class="fa-solid fa-calendar-check mr-1"></i> <?= htmlspecialchars($history['status']) ?>
                                </span>
                                <span class="bg-slate-100 text-slate-600 text-[10px] font-bold px-2.5 py-1 rounded-md border border-slate-200">
                                    <i class="fa-solid fa-wallet mr-1"></i> <?= 'Rp ' . number_format($history['fee'], 0, ',', '.') ?>
                                </span>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>

            <aside class="lg:sticky lg:top-28">
                <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden">
                    <div class="bg-gradient-to-br from-primary to-blue-400 p-6 relative overflow-hidden">
                        <i class="fa-solid fa-file-waveform text-white/10 text-6xl absolute -right-2 -bottom-2"></i>
                        <div class="relative z-10">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-blue-100 m-0 mb-2">Detail Booking</p>
                            <h3 id="detail-doctor" class="font-extrabold text-xl text-white m-0"><?= htmlspecialchars($firstHistory['doctor_name']) ?></h3>
                            <p id="detail-specialization" class="text-sm text-blue-100 font-semibold m-0 mt-1"><?= htmlspecialchars($firstHistory['specialization']) ?></p>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Tanggal</div>
                                <div id="detail-date" class="text-sm font-extrabold text-slate-900"><?= htmlspecialchars($firstHistory['date_label']) ?></div>
                            </div>
                            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Status</div>
                                <div id="detail-status" class="text-sm font-extrabold text-primary"><?= htmlspecialchars($firstHistory['status']) ?></div>
                            </div>
                            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Biaya</div>
                                <div id="detail-fee" class="text-sm font-extrabold text-slate-900"><?= 'Rp ' . number_format($firstHistory['fee'], 0, ',', '.') ?></div>
                            </div>
                            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Konsultasi ID</div>
                                <div id="detail-id" class="text-sm font-extrabold text-slate-900">#CS-<?= $firstHistory['id'] ?></div>
                            </div>
                        </div>

                        <div class="bg-primaryLight rounded-2xl p-5 border border-blue-100">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-11 h-11 rounded-2xl bg-white text-primary flex items-center justify-center shadow-sm">
                                    <i class="fa-solid fa-id-card"></i>
                                </div>
                                <div>
                                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Nomor STR</div>
                                    <div id="detail-license" class="text-sm font-extrabold text-slate-900"><?= htmlspecialchars($firstHistory['license']) ?></div>
                                </div>
                            </div>
                            <div class="text-sm text-slate-600 leading-relaxed">
                                Status booking akan berubah otomatis saat sesi konsultasi dimulai atau selesai. Slot yang sudah dipesan tidak akan ditawarkan lagi ke user lain.
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Rating</div>
                                <div id="detail-rating" class="text-sm font-extrabold text-slate-900"><?= htmlspecialchars($firstHistory['rating']) ?></div>
                            </div>
                            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Pasien</div>
                                <div id="detail-patients" class="text-sm font-extrabold text-slate-900"><?= htmlspecialchars($firstHistory['patients']) ?></div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3">
                            <a id="detail-link" href="<?= BASE_URL ?>/pages/consultation.php?consultation_id=<?= $firstHistory['id'] ?>" class="w-full bg-primary text-white hover:bg-blue-800 py-3.5 rounded-xl font-bold shadow-md text-center no-underline smooth-transition">
                                Buka Ruang Konsultasi
                            </a>
                            <a href="<?= BASE_URL ?>/pages/booking.php" class="w-full bg-white border border-slate-200 text-slate-700 hover:border-primary hover:text-primary py-3.5 rounded-xl font-bold text-center no-underline smooth-transition">
                                Booking Slot Lain
                            </a>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<?php if ($histories): ?>
<script>
document.querySelectorAll('.filter-btn').forEach((button) => {
    button.addEventListener('click', function () {
        const filter = this.dataset.filter;

        document.querySelectorAll('.filter-btn').forEach((btn) => {
            btn.className = 'filter-btn whitespace-nowrap px-4 py-2 rounded-full text-sm font-bold bg-transparent text-slate-500 hover:text-dark smooth-transition cursor-pointer border-none';
        });
        this.className = 'filter-btn active whitespace-nowrap px-4 py-2 rounded-full text-sm font-bold bg-dark text-white smooth-transition cursor-pointer border-none';

        document.querySelectorAll('.history-card').forEach((card) => {
            card.style.display = filter === 'all' || card.dataset.status === filter ? 'flex' : 'none';
        });
    });
});

function selectHistoryCard(card) {
    document.querySelectorAll('.history-card').forEach((item) => {
        item.classList.remove('border-primary', 'bg-primaryLight');
        item.classList.add('border-slate-200');
    });
    card.classList.remove('border-slate-200');
    card.classList.add('border-primary', 'bg-primaryLight');

    document.getElementById('detail-doctor').textContent = card.dataset.doctor;
    document.getElementById('detail-specialization').textContent = card.dataset.specialization;
    document.getElementById('detail-date').textContent = card.dataset.date;
    document.getElementById('detail-status').textContent = card.dataset.statusLabel;
    document.getElementById('detail-fee').textContent = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(parseInt(card.dataset.fee, 10) || 0);
    document.getElementById('detail-id').textContent = `#CS-${card.dataset.id}`;
    document.getElementById('detail-license').textContent = card.dataset.license;
    document.getElementById('detail-rating').textContent = card.dataset.rating;
    document.getElementById('detail-patients').textContent = card.dataset.patients;
    document.getElementById('detail-link').href = `<?= BASE_URL ?>/pages/consultation.php?consultation_id=${card.dataset.id}`;
}

document.querySelectorAll('.history-card').forEach((card) => {
    card.addEventListener('click', () => selectHistoryCard(card));
});
</script>
<?php endif; ?>
