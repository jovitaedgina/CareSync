<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/marketplace_helpers.php';
require_once __DIR__ . '/../includes/booking_helpers.php';

requireLogin();

$loggedInUser = currentUser();
$userId = (int) ($loggedInUser['id'] ?? 0);

if ($userId <= 0) {
    header('Location: ' . BASE_URL . '/pages/logout.php');
    exit;
}

$patientId = ensurePatientProfile($pdo, $userId);
ensureMarketplacePatientSchema($pdo);
ensureUserProfilePhotoSchema($pdo);
syncConsultationStatuses($pdo);

$stmt = $pdo->prepare(
    "SELECT
        u.*,
        p.alamat,
        p.tanggalLahir,
        p.blood_type,
        p.weight,
        p.height,
        p.allergy_notes,
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
$userData = $stmt->fetch() ?: [];

$doctorMap = [];
foreach (getBookingDoctors($pdo) as $doctor) {
    $doctorMap[$doctor['id']] = $doctor;
}

$consultationStmt = $pdo->prepare(
    "SELECT sk.idKonsultasi, sk.tanggal, sk.status, d.idDokter, d.spesialisasi, d.nomorSTR, u.nama AS doctor_name
     FROM SesiKonsultasi sk
     INNER JOIN Dokter d ON d.idDokter = sk.idDokter
     INNER JOIN users u ON u.id = d.id_user
     WHERE sk.idPasien = :idPasien
     ORDER BY sk.tanggal DESC"
);
$consultationStmt->execute([':idPasien' => $patientId]);
$consultations = [];
$consultationColors = ['bg-blue-500', 'bg-emerald-500', 'bg-amber-500', 'bg-violet-500'];

foreach ($consultationStmt->fetchAll() as $index => $row) {
    $doctorId = (int) $row['iddokter'];
    $doctor = $doctorMap[$doctorId] ?? null;
    $initials = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', (string) $row['doctor_name']), 0, 2) ?: 'DR');

    $consultations[] = [
        'id' => (int) $row['idkonsultasi'],
        'doctor_name' => $row['doctor_name'] ?: 'Dokter CareSync',
        'specialization' => $row['spesialisasi'] ?: 'Dokter Umum',
        'date_label' => date('d M Y, H:i', strtotime($row['tanggal'])) . ' WIB',
        'status' => $row['status'] ?: 'Menunggu',
        'license' => $row['nomorstr'] ?: '-',
        'initials' => $initials,
        'color' => $consultationColors[$index % count($consultationColors)],
        'doctor_id' => $doctorId,
        'fee' => (int) ($doctor['fee'] ?? 0),
    ];
}

$ordersStmt = $pdo->prepare(
    "SELECT pm.idPemesanan
     FROM Pemesanan pm
     INNER JOIN Pasien p ON p.idPasien = pm.idPasien
     WHERE p.id_user = :userId
     ORDER BY pm.tanggal DESC, pm.idPemesanan DESC"
);
$ordersStmt->execute([':userId' => $userId]);
$marketplaceOrders = [];

foreach ($ordersStmt->fetchAll() as $row) {
    $detail = getMarketplaceOrderDetail($pdo, $userId, (int) $row['idpemesanan']);
    if ($detail) {
        $marketplaceOrders[] = $detail;
    }
}

$activePaymentOrder = null;
foreach ($marketplaceOrders as $order) {
    if (($order['payment_status'] ?? '') === 'Pending') {
        $activePaymentOrder = $order;
        break;
    }
}
if (!$activePaymentOrder && $marketplaceOrders) {
    $activePaymentOrder = $marketplaceOrders[0];
}

$hasPrescription = false;
$prescriptionCountStmt = $pdo->prepare(
    "SELECT COUNT(*) 
     FROM Resep r
     INNER JOIN SesiKonsultasi sk ON sk.idKonsultasi = r.idKonsultasi
     INNER JOIN Pasien p ON p.idPasien = sk.idPasien
     WHERE p.id_user = :userId"
);
$prescriptionCountStmt->execute([':userId' => $userId]);
$hasPrescription = ((int) $prescriptionCountStmt->fetchColumn()) > 0;

$pageTitle = 'Dashboard Akun - CareSync';
$currentPage = 'profile';

$displayName = $userData['nama'] ?? ($loggedInUser['name'] ?? 'Pasien CareSync');
$displayEmail = $userData['email'] ?? ($loggedInUser['email'] ?? '');
$displayGender = $userData['gender'] ?? '';
$displayDob = $userData['dob'] ?? $userData['tanggallahir'] ?? null;
$displayPhone = $userData['phone'] ?? '';
$displayAddress = $userData['alamat'] ?? '';
$addressLabel = $userData['address_label'] ?? 'Rumah';
$recipientName = $userData['recipient_name'] ?? $displayName;
$village = $userData['village'] ?? '';
$district = $userData['district'] ?? '';
$city = $userData['city'] ?? '';
$province = $userData['province'] ?? '';
$postalCode = $userData['postal_code'] ?? '';
$addressNotes = $userData['address_notes'] ?? '';
$bloodType = $userData['blood_type'] ?? '-';
$weight = $userData['weight'] ?? '-';
$height = $userData['height'] ?? '-';
$allergy = $userData['allergy_notes'] ?? 'Tidak ada data';
$profilePhotoUrl = getUserProfilePhotoUrl((string) ($userData['profile_photo'] ?? ''));
$profileInitials = getMarketplaceProfileInitials((string) $displayName);
$regionDefaults = [
    'province' => (string) $province,
    'city' => (string) $city,
    'district' => (string) $district,
    'village' => (string) $village,
];

$extraHead = '
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: { sans: [\'"Plus Jakarta Sans"\', \'sans-serif\'] },
                colors: {
                    primary: \'#1D4ED8\', primaryLight: \'#EFF6FF\',
                    accent: \'#10B981\', dark: \'#0F172A\', textSoft: \'#64748B\'
                },
                boxShadow: {
                    sm: \'0 2px 8px rgba(0,0,0,0.04)\',
                    floating: \'0 20px 40px -15px rgba(29, 78, 216, 0.15)\'
                }
            }
        }
    }
</script>
<style>
    body { background-color: #F8FAFC; margin: 0; }
    .smooth-transition { transition: all .2s ease-in-out; }
    .tab-content { display: none; animation: fadeIn .25s ease; }
    .tab-content.active { display: block; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .profile-header-pattern {
        background-color: #0F172A;
        background-image: url("data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'#ffffff\' fill-opacity=\'0.05\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .cropper-view-box,
    .cropper-face {
        border-radius: 9999px;
    }
</style>
';

include __DIR__ . '/../includes/header.php';
?>

<div class="bg-slate-50 min-h-screen py-8" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row gap-8 items-start">
            <div class="w-full md:w-[280px] lg:w-[320px] bg-white rounded-2xl shadow-sm border border-slate-100 flex-shrink-0 sticky top-28 overflow-hidden">
                <div class="profile-header-pattern h-28 w-full relative"></div>
                <div class="px-6 pb-4 text-center relative -mt-12 border-b border-slate-100">
                    <div class="w-24 h-24 bg-slate-200 border-4 border-white rounded-full mx-auto mb-3 flex items-center justify-center text-slate-400 text-3xl font-extrabold shadow-sm relative overflow-hidden">
                        <?php if ($profilePhotoUrl !== ''): ?>
                        <img id="profile-sidebar-photo" src="<?= htmlspecialchars($profilePhotoUrl) ?>" alt="Foto Profil" class="w-full h-full object-cover">
                        <?php else: ?>
                        <span id="profile-sidebar-initials"><?= htmlspecialchars($profileInitials) ?></span>
                        <?php endif; ?>
                        <div class="absolute bottom-1 right-1 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full" title="Online"></div>
                    </div>
                    <h3 class="font-extrabold text-slate-900 text-base m-0 leading-tight"><?= htmlspecialchars($displayName) ?></h3>
                    <p class="text-xs text-slate-500 mb-2 truncate"><?= htmlspecialchars($displayEmail) ?></p>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                        <?= $displayGender === 'L' ? 'Laki-laki' : ($displayGender === 'P' ? 'Perempuan' : 'Belum diset') ?>
                        • TL : <?= $displayDob ? htmlspecialchars(date('d M Y', strtotime($displayDob))) : '-' ?>
                    </p>
                </div>

                <div class="p-4 flex flex-col gap-1 pb-6">
                    <div class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mt-2 mb-1 px-4">Menu Utama</div>
                    <button id="menu-konsultasi" onclick="switchTab('konsultasi', 'Konsultasi Medis', this)" class="menu-btn w-full text-left px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 flex items-center gap-3 smooth-transition border-none cursor-pointer bg-transparent">
                        <i class="fa-solid fa-stethoscope w-4 text-center"></i> Konsultasi
                    </button>
                    <button id="menu-resep" onclick="switchTab('resep', 'Tebus Resep', this)" class="menu-btn w-full text-left px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 flex items-center gap-3 smooth-transition border-none cursor-pointer bg-transparent">
                        <i class="fa-solid fa-file-prescription w-4 text-center"></i> Tebus Resep
                    </button>

                    <div class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mt-4 mb-1 px-4">Transaksi</div>
                    <button id="menu-pembayaran" onclick="switchTab('pembayaran', 'Pembayaran', this)" class="menu-btn active w-full text-left px-4 py-2.5 rounded-xl text-sm font-bold text-primary bg-primaryLight flex items-center gap-3 smooth-transition border-none cursor-pointer border border-blue-200">
                        <i class="fa-solid fa-wallet w-4 text-center"></i> Pembayaran
                    </button>
                    <button id="menu-pesanan" onclick="switchTab('pesanan', 'Riwayat Pemesanan', this)" class="menu-btn w-full text-left px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 flex items-center gap-3 smooth-transition border-none cursor-pointer bg-transparent">
                        <i class="fa-solid fa-box-open w-4 text-center"></i> Pemesanan
                    </button>
                    <button id="menu-alamat" onclick="switchTab('alamat', 'Alamat Pengiriman', this)" class="menu-btn w-full text-left px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 flex items-center gap-3 smooth-transition border-none cursor-pointer bg-transparent">
                        <i class="fa-solid fa-location-dot w-4 text-center"></i> Alamat Pengiriman
                    </button>

                    <div class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mt-4 mb-1 px-4">Profil</div>
                    <button id="menu-akun" onclick="switchTab('akun', 'Profil Saya', this)" class="menu-btn w-full text-left px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 flex items-center gap-3 smooth-transition border-none cursor-pointer bg-transparent">
                        <i class="fa-regular fa-user w-4 text-center"></i> Profil Saya
                    </button>
                    <button id="menu-medis" onclick="switchTab('medis', 'Rekam Medis', this)" class="menu-btn w-full text-left px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 flex items-center gap-3 smooth-transition border-none cursor-pointer bg-transparent">
                        <i class="fa-solid fa-notes-medical w-4 text-center"></i> Rekam Medis
                    </button>

                    <div class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mt-4 mb-1 px-4">Akses</div>
                    <a href="<?= BASE_URL ?>/pages/logout.php" class="w-full text-left px-4 py-2.5 rounded-xl text-sm font-bold text-slate-500 hover:text-red-500 hover:bg-red-50 flex items-center gap-3 smooth-transition no-underline">
                        <i class="fa-solid fa-arrow-right-from-bracket w-4 text-center"></i> Keluar
                    </a>
                </div>
            </div>

            <div class="flex-1 min-w-0">
                <h2 id="page-title" class="text-2xl font-extrabold text-slate-900 mb-6 m-0 border-b border-slate-200 pb-4">Pembayaran</h2>

                <div id="tab-pembayaran" class="tab-content active">
                    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-100 shadow-sm">
                        <div class="flex justify-between items-center mb-6 border-b border-slate-100 pb-4">
                            <h3 class="font-extrabold text-lg text-slate-900 m-0"><i class="fa-solid fa-wallet text-primary mr-2"></i>Status Pembayaran</h3>
                            <span class="text-xs font-bold text-slate-500"><?= count($marketplaceOrders) ?> transaksi apotek</span>
                        </div>

                        <?php if (!$activePaymentOrder): ?>
                        <div class="bg-slate-50 border border-dashed border-slate-200 rounded-2xl p-8 text-center">
                            <i class="fa-solid fa-wallet text-4xl text-slate-300 mb-4"></i>
                            <h4 class="font-extrabold text-slate-900 text-lg m-0 mb-2">Belum ada transaksi pembayaran</h4>
                            <p class="text-sm text-slate-500 m-0 mb-5">Saat Anda checkout obat di marketplace, status pembayaran akan muncul di sini.</p>
                            <a href="<?= BASE_URL ?>/pages/marketplace.php" class="inline-flex items-center gap-2 bg-primary text-white px-5 py-3 rounded-xl font-bold no-underline">Belanja di Apotek</a>
                        </div>
                        <?php else: ?>
                        <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-[2rem] p-6 text-white mb-6">
                            <div class="flex flex-col sm:flex-row justify-between gap-4">
                                <div>
                                    <div class="text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Order Aktif</div>
                                    <div class="text-2xl font-extrabold"><?= htmlspecialchars($activePaymentOrder['order_code']) ?></div>
                                    <div class="text-sm text-slate-300 mt-2"><?= htmlspecialchars($activePaymentOrder['date_label']) ?></div>
                                </div>
                                <div class="text-left sm:text-right">
                                    <div class="text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Total Tagihan</div>
                                    <div class="text-3xl font-extrabold">Rp <?= number_format($activePaymentOrder['total'], 0, ',', '.') ?></div>
                                    <div class="text-sm text-slate-300 mt-2"><?= htmlspecialchars($activePaymentOrder['payment_method'] ?: 'Menunggu metode') ?> · <?= htmlspecialchars($activePaymentOrder['payment_status']) ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6">
                            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="font-extrabold text-slate-900 text-base m-0">Ringkasan Transaksi</h4>
                                    <span class="text-[10px] font-extrabold px-2 py-1 rounded-full <?= $activePaymentOrder['payment_status'] === 'Pending' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' ?>">
                                        <?= htmlspecialchars(strtoupper($activePaymentOrder['payment_status'])) ?>
                                    </span>
                                </div>
                                <div class="space-y-3">
                                    <?php foreach ($activePaymentOrder['items'] as $item): ?>
                                    <div class="flex items-center gap-3 bg-white rounded-xl border border-slate-100 p-3">
                                        <img src="<?= htmlspecialchars($item['img']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-100 p-2 object-contain">
                                        <div class="flex-1 min-w-0">
                                            <div class="font-bold text-slate-900 text-sm truncate"><?= htmlspecialchars($item['name']) ?></div>
                                            <div class="text-xs text-slate-500"><?= (int) $item['qty'] ?> x Rp <?= number_format($item['price'], 0, ',', '.') ?></div>
                                        </div>
                                        <div class="font-extrabold text-slate-900 text-sm">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                    <div class="bg-white rounded-xl border border-slate-100 p-4">
                                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Metode Pembayaran</div>
                                        <div class="font-extrabold text-slate-900 text-sm"><?= htmlspecialchars($activePaymentOrder['payment_channel'] ?: $activePaymentOrder['payment_method'] ?: 'Belum dipilih') ?></div>
                                        <div class="text-xs text-slate-500 mt-1">
                                            <?= htmlspecialchars(
                                                $activePaymentOrder['payment_type'] === 'va'
                                                    ? 'Virtual Account'
                                                    : ($activePaymentOrder['payment_type'] === 'qris'
                                                        ? 'QRIS'
                                                        : ($activePaymentOrder['payment_type'] === 'ewallet' ? 'E-Wallet' : 'Pembayaran Digital'))
                                            ) ?>
                                        </div>
                                    </div>
                                    <div class="bg-white rounded-xl border border-slate-100 p-4">
                                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">
                                            <?= $activePaymentOrder['payment_type'] === 'va' ? 'Nomor Virtual Account' : 'Kode / Referensi Pembayaran' ?>
                                        </div>
                                        <div class="font-extrabold text-slate-900 text-sm break-all">
                                            <?= htmlspecialchars(
                                                $activePaymentOrder['payment_account_number']
                                                ?: $activePaymentOrder['payment_reference']
                                                ?: '-'
                                            ) ?>
                                        </div>
                                        <?php if (!empty($activePaymentOrder['payment_account_name'])): ?>
                                        <div class="text-xs text-slate-500 mt-1"><?= htmlspecialchars($activePaymentOrder['payment_account_name']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if (!empty($activePaymentOrder['payment_instructions'])): ?>
                                <div class="mt-4 bg-blue-50 border border-blue-100 rounded-xl p-4">
                                    <div class="text-[10px] text-primary font-extrabold uppercase tracking-wider mb-1">Instruksi Pembayaran</div>
                                    <div class="text-sm text-slate-700 font-semibold leading-relaxed"><?= htmlspecialchars($activePaymentOrder['payment_instructions']) ?></div>
                                </div>
                                <?php endif; ?>
                                <div class="flex flex-col sm:flex-row justify-between items-end sm:items-center mt-4 pt-4 border-t border-slate-200 gap-4">
                                    <div>
                                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Total Tagihan</div>
                                        <div class="font-extrabold text-slate-900 text-lg">Rp <?= number_format($activePaymentOrder['total'], 0, ',', '.') ?></div>
                                    </div>
                                    <div class="flex gap-3 w-full sm:w-auto">
                                        <a href="<?= BASE_URL ?>/pages/marketplace.php" class="flex-1 sm:flex-none bg-white border border-slate-200 text-slate-700 font-bold px-6 py-3 rounded-xl no-underline flex items-center justify-center gap-2">
                                            <i class="fa-solid fa-pills"></i> Belanja Lagi
                                        </a>
                                        <?php if ($activePaymentOrder['payment_status'] !== 'Pending'): ?>
                                        <button type="button" onclick="goToProfileTab('pesanan', 'Riwayat Pemesanan')" class="flex-1 sm:flex-none bg-primaryLight text-primary border border-blue-200 font-bold px-6 py-3 rounded-xl flex items-center justify-center gap-2 border-none cursor-pointer">
                                            <i class="fa-solid fa-box-open"></i> Lihat di Pemesanan
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div id="tab-akun" class="tab-content">
                    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-100 shadow-sm">
                        <form id="profile-form" onsubmit="updateProfile(event)">
                            <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-100">
                                <h3 class="font-extrabold text-lg text-slate-900 m-0"><i class="fa-regular fa-id-badge text-primary mr-2"></i>Data Diri Pasien</h3>
                                <div class="flex gap-2">
                                    <button type="button" id="edit-btn" onclick="enableEdit()" class="text-sm font-bold text-primary hover:text-blue-800 bg-primaryLight px-4 py-2 rounded-lg border-none cursor-pointer smooth-transition">Edit Data</button>
                                    <button type="button" id="discard-btn" onclick="discardProfileChanges()" class="hidden text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-lg border-none cursor-pointer smooth-transition">Discard</button>
                                    <button type="submit" id="save-btn" class="hidden text-sm font-bold text-white bg-primary hover:bg-blue-800 px-4 py-2 rounded-lg border-none cursor-pointer smooth-transition">Simpan Perubahan</button>
                                </div>
                            </div>

                            <div class="mb-6 bg-slate-50 border border-slate-200 rounded-2xl p-5 flex flex-col sm:flex-row items-center gap-5">
                                <div class="w-24 h-24 rounded-full border-4 border-white shadow-sm bg-slate-200 overflow-hidden flex items-center justify-center text-slate-500 text-2xl font-extrabold">
                                    <?php if ($profilePhotoUrl !== ''): ?>
                                    <img id="profile-photo-preview" src="<?= htmlspecialchars($profilePhotoUrl) ?>" alt="Foto Profil" class="w-full h-full object-cover">
                                    <?php else: ?>
                                    <span id="profile-photo-fallback"><?= htmlspecialchars($profileInitials) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1 text-center sm:text-left">
                                    <div class="text-sm font-extrabold text-slate-900">Foto Profil</div>
                                    <div class="text-xs text-slate-500 mt-1">Format: JPG, PNG, atau WEBP. Maksimal 2 MB.</div>
                                    <div class="mt-3">
                                        <input type="file" id="profile-photo-input" accept="image/jpeg,image/png,image/webp" class="hidden">
                                        <button type="button" id="change-photo-btn" onclick="triggerProfilePhotoPicker()" disabled class="inline-flex items-center gap-2 bg-slate-100 border border-slate-200 text-slate-400 font-bold px-4 py-2 rounded-xl text-sm cursor-not-allowed smooth-transition disabled:opacity-100">
                                            <i class="fa-solid fa-camera"></i> Ganti Foto Profil
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div id="photo-crop-modal" class="hidden fixed inset-0 z-[70] bg-slate-950/70 px-4 py-6">
                                <div class="max-w-4xl mx-auto bg-white rounded-[2rem] shadow-2xl overflow-hidden">
                                    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
                                        <div>
                                            <div class="text-lg font-extrabold text-slate-900">Preview Foto Profil</div>
                                            <div class="text-xs text-slate-500 mt-1">Atur posisi foto dulu, lalu simpan kalau sudah pas.</div>
                                        </div>
                                        <button type="button" onclick="closeProfilePhotoModal()" class="w-10 h-10 rounded-full border border-slate-200 text-slate-500 hover:bg-slate-50 cursor-pointer">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_240px] gap-0">
                                        <div class="bg-slate-100 min-h-[420px] flex items-center justify-center p-4">
                                            <img id="cropper-image" alt="Preview Crop" class="max-w-full">
                                        </div>
                                        <div class="p-6 border-t lg:border-t-0 lg:border-l border-slate-100">
                                            <div class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-3">Preview Avatar</div>
                                            <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-slate-100 shadow-sm mx-auto mb-5">
                                                <div id="cropper-preview" class="w-full h-full overflow-hidden"></div>
                                            </div>
                                            <div class="space-y-3">
                                                <button type="button" id="save-cropped-photo-btn" onclick="saveCroppedProfilePhoto()" class="w-full inline-flex items-center justify-center gap-2 bg-primary text-white hover:bg-blue-800 font-bold px-4 py-3 rounded-xl text-sm cursor-pointer smooth-transition border-none">
                                                    <i class="fa-solid fa-floppy-disk"></i> Simpan Foto
                                                </button>
                                                <button type="button" onclick="resetProfilePhotoCrop()" class="w-full inline-flex items-center justify-center gap-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold px-4 py-3 rounded-xl text-sm cursor-pointer smooth-transition">
                                                    <i class="fa-solid fa-rotate-left"></i> Reset Crop
                                                </button>
                                                <button type="button" onclick="closeProfilePhotoModal()" class="w-full inline-flex items-center justify-center gap-2 bg-slate-100 text-slate-600 hover:bg-slate-200 font-bold px-4 py-3 rounded-xl text-sm cursor-pointer smooth-transition border-none">
                                                    Batal
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div><label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Nama Lengkap</label><input type="text" name="nama" value="<?= htmlspecialchars($displayName) ?>" readonly class="profile-input w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 font-semibold text-slate-700 outline-none text-sm"></div>
                                <div><label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Email</label><input type="text" value="<?= htmlspecialchars($displayEmail) ?>" disabled class="w-full bg-slate-100 border border-slate-200 rounded-xl px-4 py-2.5 font-semibold text-slate-400 outline-none text-sm"></div>
                                <div><label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Nomor Telepon</label><input type="text" name="phone" value="<?= htmlspecialchars($displayPhone) ?>" readonly class="profile-input w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 font-semibold text-slate-700 outline-none text-sm"></div>
                                <div><label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Tanggal Lahir</label><input type="date" name="dob" value="<?= htmlspecialchars((string) $displayDob) ?>" readonly class="profile-input w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 font-semibold text-slate-700 outline-none text-sm"></div>
                                <div><label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Jenis Kelamin</label><select name="gender" disabled class="profile-input w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 font-semibold text-slate-700 outline-none text-sm"><option value="L" <?= $displayGender === 'L' ? 'selected' : '' ?>>Laki-laki</option><option value="P" <?= $displayGender === 'P' ? 'selected' : '' ?>>Perempuan</option></select></div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 pt-6 border-t border-slate-100">
                                <div><label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Golongan Darah</label><input type="text" name="blood_type" value="<?= htmlspecialchars((string) ($bloodType === '-' ? '' : $bloodType)) ?>" readonly class="profile-input w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 font-semibold text-slate-700 outline-none text-sm"></div>
                                <div><label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Alergi</label><input type="text" name="allergy_notes" value="<?= htmlspecialchars((string) ($allergy === 'Tidak ada data' ? '' : $allergy)) ?>" readonly class="profile-input w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 font-semibold text-slate-700 outline-none text-sm"></div>
                                <div><label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Berat Badan</label><input type="text" name="weight" value="<?= htmlspecialchars((string) ($weight === '-' ? '' : $weight)) ?>" readonly class="profile-input w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 font-semibold text-slate-700 outline-none text-sm"></div>
                                <div><label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Tinggi Badan</label><input type="text" name="height" value="<?= htmlspecialchars((string) ($height === '-' ? '' : $height)) ?>" readonly class="profile-input w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 font-semibold text-slate-700 outline-none text-sm"></div>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="tab-alamat" class="tab-content">
                    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-100 shadow-sm">
                        <form id="address-form" onsubmit="updateAddress(event)">
                            <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-100">
                                <h3 class="font-extrabold text-lg text-slate-900 m-0"><i class="fa-solid fa-location-dot text-primary mr-2"></i>Alamat Pengiriman</h3>
                                <div class="flex gap-2">
                                    <button type="button" id="address-edit-btn" onclick="enableAddressEdit()" class="text-sm font-bold text-primary hover:text-blue-800 bg-primaryLight px-4 py-2 rounded-lg border-none cursor-pointer smooth-transition">Edit Alamat</button>
                                    <button type="button" id="address-discard-btn" onclick="discardAddressChanges()" class="hidden text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-lg border-none cursor-pointer smooth-transition">Discard</button>
                                    <button type="submit" id="address-save-btn" class="hidden text-sm font-bold text-white bg-primary hover:bg-blue-800 px-4 py-2 rounded-lg border-none cursor-pointer smooth-transition">Simpan Alamat</button>
                                </div>
                            </div>

                            <input type="hidden" name="alamat" value="<?= htmlspecialchars($displayAddress) ?>">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-2">
                                <div><label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Label Alamat</label><input type="text" name="address_label" value="<?= htmlspecialchars($addressLabel) ?>" readonly class="address-input w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 font-semibold text-slate-700 outline-none text-sm"></div>
                                <div><label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Nama Penerima</label><input type="text" name="recipient_name" value="<?= htmlspecialchars($recipientName) ?>" readonly class="address-input w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 font-semibold text-slate-700 outline-none text-sm"></div>
                            </div>
                            <div class="mt-6 bg-blue-50 border border-blue-100 rounded-2xl p-4">
                                <div class="text-[11px] font-extrabold text-primary uppercase tracking-wider mb-1">Panduan Alamat Pengiriman</div>
                                <div class="text-sm font-semibold text-slate-700 leading-relaxed">Pilih wilayah secara berurutan: <span class="text-primary">Provinsi -> Kota/Kabupaten -> Kecamatan -> Kelurahan/Desa</span>. Detail alamat jalan, nomor rumah, atau patokan mohon diisi pada form catatan kurir.</div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-6 mt-6">
                                <div>
                                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">1. Provinsi</label>
                                    <select name="province" data-region="province" data-placeholder="Pilih provinsi" disabled class="address-input w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 font-semibold text-slate-700 outline-none text-sm">
                                        <option value="<?= htmlspecialchars($province) ?>"><?= htmlspecialchars($province !== '' ? $province : 'Pilih provinsi') ?></option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">2. Kota / Kabupaten</label>
                                    <select name="city" data-region="city" data-placeholder="Pilih kota / kabupaten" disabled class="address-input w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 font-semibold text-slate-700 outline-none text-sm">
                                        <option value="<?= htmlspecialchars($city) ?>"><?= htmlspecialchars($city !== '' ? $city : 'Pilih kota / kabupaten') ?></option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">3. Kecamatan</label>
                                    <select name="district" data-region="district" data-placeholder="Pilih kecamatan" disabled class="address-input w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 font-semibold text-slate-700 outline-none text-sm">
                                        <option value="<?= htmlspecialchars($district) ?>"><?= htmlspecialchars($district !== '' ? $district : 'Pilih kecamatan') ?></option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">4. Kelurahan / Desa</label>
                                    <select name="village" data-region="village" data-placeholder="Pilih kelurahan / desa" disabled class="address-input w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 font-semibold text-slate-700 outline-none text-sm">
                                        <option value="<?= htmlspecialchars($village) ?>"><?= htmlspecialchars($village !== '' ? $village : 'Pilih kelurahan / desa') ?></option>
                                    </select>
                                </div>
                                <div><label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">5. Kode Pos</label><input type="text" name="postal_code" value="<?= htmlspecialchars($postalCode) ?>" readonly data-lock-readonly="true" class="address-input w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 font-semibold text-slate-700 outline-none text-sm"></div>
                            </div>
                            <div class="mt-6"><label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Catatan Kurir</label><textarea name="address_notes" readonly class="address-input w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-semibold text-slate-700 outline-none resize-none h-20 text-sm"><?= htmlspecialchars($addressNotes) ?></textarea></div>
                        </form>
                    </div>
                </div>

                <div id="tab-medis" class="tab-content">
                    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-100 shadow-sm">
                        <h3 class="font-extrabold text-lg text-slate-900 mb-6 m-0 border-b border-slate-100 pb-4">Ringkasan Medis</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
                            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 text-center"><div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Gol. Darah</div><div class="font-extrabold text-red-500 text-xl"><?= htmlspecialchars((string) $bloodType) ?></div></div>
                            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 text-center"><div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Berat Badan</div><div class="font-extrabold text-slate-800 text-xl"><?= htmlspecialchars((string) $weight) ?></div></div>
                            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 text-center"><div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tinggi Badan</div><div class="font-extrabold text-slate-800 text-xl"><?= htmlspecialchars((string) $height) ?></div></div>
                            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 text-center"><div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Alergi</div><div class="font-extrabold text-slate-800 text-base mt-1"><?= htmlspecialchars((string) $allergy) ?></div></div>
                        </div>
                        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white text-primary rounded-full flex items-center justify-center text-xl shadow-sm"><i class="fa-solid fa-flask"></i></div>
                                <div>
                                    <div class="font-bold text-slate-900 text-sm">Pemeriksaan Laboratorium</div>
                                    <div class="text-xs text-slate-500">Akses hasil pemeriksaan dari modul laboratorium CareSync.</div>
                                </div>
                            </div>
                            <a href="<?= BASE_URL ?>/pages/lab_results.php" class="bg-primary text-white font-bold px-4 py-2 rounded-lg shadow-sm hover:bg-blue-800 no-underline text-xs smooth-transition">Lihat Hasil</a>
                        </div>
                    </div>
                </div>

                <div id="tab-konsultasi" class="tab-content">
                    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-100 shadow-sm">
                        <div class="flex justify-between items-center mb-6 border-b border-slate-100 pb-4">
                            <h3 class="font-extrabold text-lg text-slate-900 m-0"><i class="fa-solid fa-user-doctor text-primary mr-2"></i>Riwayat Konsultasi</h3>
                            <a href="<?= BASE_URL ?>/pages/booking.php" class="text-sm font-bold text-primary no-underline">Booking Baru</a>
                        </div>

                        <?php if (!$consultations): ?>
                        <div class="bg-slate-50 border border-dashed border-slate-200 rounded-2xl p-8 text-center">
                            <i class="fa-solid fa-stethoscope text-4xl text-slate-300 mb-4"></i>
                            <h4 class="font-extrabold text-slate-900 text-lg m-0 mb-2">Belum ada konsultasi</h4>
                            <p class="text-sm text-slate-500 m-0 mb-5">Riwayat konsultasi dokter akan muncul di sini setelah Anda melakukan booking.</p>
                            <a href="<?= BASE_URL ?>/pages/booking.php" class="inline-flex items-center gap-2 bg-primary text-white px-5 py-3 rounded-xl font-bold no-underline">Mulai Booking</a>
                        </div>
                        <?php else: ?>
                        <div class="flex flex-col gap-5">
                            <?php foreach ($consultations as $consultation): ?>
                            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:border-blue-300 smooth-transition">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 <?= htmlspecialchars($consultation['color']) ?> text-white rounded-full flex items-center justify-center font-extrabold text-lg"><?= htmlspecialchars($consultation['initials']) ?></div>
                                        <div>
                                            <h4 class="font-extrabold text-slate-900 text-sm m-0"><?= htmlspecialchars($consultation['doctor_name']) ?></h4>
                                            <div class="text-[11px] text-slate-500 font-medium"><?= htmlspecialchars($consultation['specialization']) ?></div>
                                        </div>
                                    </div>
                                    <span class="bg-slate-100 text-slate-600 text-[10px] font-extrabold px-2 py-1 rounded uppercase tracking-wider border border-slate-200"><?= htmlspecialchars($consultation['status']) ?></span>
                                </div>
                                <div class="pl-4 mb-4 border-l-2 border-slate-100">
                                    <div class="text-xs text-slate-600 font-medium mb-1"><i class="fa-regular fa-calendar text-slate-400 mr-1"></i><?= htmlspecialchars($consultation['date_label']) ?></div>
                                    <div class="text-xs text-slate-500">STR/SIP: <?= htmlspecialchars($consultation['license']) ?></div>
                                </div>
                                <div class="flex gap-3 mt-4 border-t border-slate-100 pt-4">
                                    <a href="<?= BASE_URL ?>/pages/prescription.php" class="flex-1 bg-primaryLight text-primary border border-blue-200 hover:bg-blue-100 font-bold px-4 py-2.5 rounded-xl text-center text-xs sm:text-sm no-underline smooth-transition"><i class="fa-solid fa-file-prescription mr-1"></i>Lihat E-Resep</a>
                                    <a href="<?= BASE_URL ?>/pages/consultation.php?doctor=<?= (int) $consultation['doctor_id'] ?>" class="flex-1 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-primary font-bold px-4 py-2.5 rounded-xl text-center text-xs sm:text-sm no-underline smooth-transition">Chat Ulang</a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div id="tab-resep" class="tab-content">
                    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-100 shadow-sm">
                        <div class="flex justify-between items-center mb-6 border-b border-slate-100 pb-4">
                            <h3 class="font-extrabold text-lg text-slate-900 m-0"><i class="fa-solid fa-file-prescription text-primary mr-2"></i>Resep Digital</h3>
                        </div>

                        <div class="border border-slate-200 rounded-2xl p-6 shadow-sm relative overflow-hidden">
                            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-primary"></div>
                            <div class="ml-4">
                                <div class="flex flex-col sm:flex-row justify-between gap-4 mb-5 border-b border-slate-100 pb-4">
                                    <div>
                                        <h2 class="font-extrabold text-lg text-primary m-0">CareSync E-Prescription</h2>
                                        <div class="text-[10px] text-slate-400 font-bold tracking-wider">Terintegrasi dengan modul konsultasi dan marketplace apotek</div>
                                    </div>
                                    <div class="sm:text-right">
                                        <div class="font-extrabold text-slate-900 text-sm"><?= htmlspecialchars($displayName) ?></div>
                                        <div class="text-[10px] text-slate-500 font-medium"><?= count($consultations) ?> riwayat konsultasi</div>
                                    </div>
                                </div>

                                <div class="space-y-3 mb-5">
                                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-4">
                                        <div class="font-bold text-slate-900 text-sm mb-1">Status resep</div>
                                        <div class="text-sm text-slate-600"><?= $hasPrescription ? 'Anda sudah memiliki data resep digital dari konsultasi sebelumnya.' : 'Belum ada resep yang tercatat untuk akun ini.' ?></div>
                                    </div>
                                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-4">
                                        <div class="font-bold text-slate-900 text-sm mb-1">Aturan tebus obat</div>
                                        <div class="text-sm text-slate-600">Obat resep tetap tampil di marketplace, tetapi hanya bisa dibeli jika resep dokter valid sudah tercatat pada akun pasien.</div>
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row justify-between items-center bg-slate-50 p-4 rounded-xl">
                                    <div class="text-xs text-slate-500 font-medium mb-3 sm:mb-0">Pasien: <span class="font-bold text-slate-700"><?= htmlspecialchars($displayName) ?></span></div>
                                    <a href="<?= BASE_URL ?>/pages/prescription.php" class="bg-primary text-white text-sm font-bold px-6 py-2 rounded-lg shadow-sm hover:bg-blue-800 smooth-transition no-underline whitespace-nowrap w-full sm:w-auto text-center">Lihat & Tebus Obat</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="tab-pesanan" class="tab-content">
                    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-100 shadow-sm">
                        <div class="flex justify-between items-center mb-6 border-b border-slate-100 pb-4">
                            <h3 class="font-extrabold text-lg text-slate-900 m-0"><i class="fa-solid fa-box-open text-primary mr-2"></i>Riwayat Pemesanan Obat</h3>
                            <a href="<?= BASE_URL ?>/pages/marketplace.php" class="text-sm font-bold text-primary no-underline">Ke Marketplace</a>
                        </div>

                        <?php if (!$marketplaceOrders): ?>
                        <div class="bg-slate-50 border border-dashed border-slate-200 rounded-2xl p-8 text-center">
                            <i class="fa-solid fa-box-open text-4xl text-slate-300 mb-4"></i>
                            <h4 class="font-extrabold text-slate-900 text-lg m-0 mb-2">Belum ada pesanan obat</h4>
                            <p class="text-sm text-slate-500 m-0 mb-5">Setelah checkout marketplace, riwayat pemesanan obat akan tampil di sini lengkap dengan tracking.</p>
                            <a href="<?= BASE_URL ?>/pages/marketplace.php" class="inline-flex items-center gap-2 bg-primary text-white px-5 py-3 rounded-xl font-bold no-underline">Belanja di Apotek</a>
                        </div>
                        <?php else: ?>
                        <div class="flex flex-col gap-5">
                            <?php foreach ($marketplaceOrders as $order): ?>
                            <div class="border border-slate-200 rounded-2xl p-5 shadow-sm hover:border-blue-300 smooth-transition">
                                <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-3">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-bag-shopping text-primary"></i>
                                        <span class="text-xs font-extrabold text-slate-700"><?= htmlspecialchars($order['order_code']) ?></span>
                                        <span class="text-[11px] text-slate-400">• <?= htmlspecialchars($order['date_label']) ?></span>
                                    </div>
                                    <span class="bg-blue-50 text-primary border border-blue-200 text-[10px] font-extrabold px-2 py-1 rounded uppercase tracking-wider"><?= htmlspecialchars($order['shipping_status']) ?></span>
                                </div>

                                <?php $firstItem = $order['items'][0] ?? null; ?>
                                <?php if ($firstItem): ?>
                                <div class="flex items-start gap-4 mb-4">
                                    <img src="<?= htmlspecialchars($firstItem['img']) ?>" alt="<?= htmlspecialchars($firstItem['name']) ?>" class="w-16 h-16 bg-slate-50 rounded-xl flex-shrink-0 border border-slate-100 p-2 object-contain">
                                    <div>
                                        <h4 class="font-extrabold text-slate-900 text-sm m-0 mb-1"><?= htmlspecialchars($firstItem['name']) ?></h4>
                                        <div class="text-xs text-slate-500 mb-1"><?= (int) $firstItem['qty'] ?> Barang x Rp <?= number_format($firstItem['price'], 0, ',', '.') ?></div>
                                        <?php if (count($order['items']) > 1): ?>
                                        <div class="text-[10px] text-slate-400 font-bold">+<?= count($order['items']) - 1 ?> produk lainnya</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm mb-4">
                                    <div class="bg-slate-50 rounded-xl p-3 border border-slate-100"><div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Pembayaran</div><div class="font-extrabold text-slate-900"><?= htmlspecialchars($order['payment_method']) ?> · <?= htmlspecialchars($order['payment_status']) ?></div></div>
                                    <div class="bg-slate-50 rounded-xl p-3 border border-slate-100"><div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Kurir</div><div class="font-extrabold text-slate-900"><?= htmlspecialchars($order['courier']) ?></div></div>
                                    <div class="bg-slate-50 rounded-xl p-3 border border-slate-100"><div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">No. Resi</div><div class="font-extrabold text-slate-900"><?= htmlspecialchars($order['awb']) ?></div></div>
                                </div>

                                <div class="flex flex-col sm:flex-row justify-between items-end sm:items-center mt-4 pt-4 border-t border-slate-100 gap-4">
                                    <div>
                                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Total Tagihan</div>
                                        <div class="font-extrabold text-slate-900 text-base">Rp <?= number_format($order['total'], 0, ',', '.') ?></div>
                                    </div>
                                    <div class="flex gap-3 w-full sm:w-auto">
                                        <a href="<?= BASE_URL ?>/pages/tracking.php?order_id=<?= (int) $order['id'] ?>" class="flex-1 sm:flex-none bg-primary text-white font-bold px-6 py-2.5 rounded-xl shadow-sm hover:bg-blue-800 smooth-transition no-underline text-sm flex items-center justify-center gap-2"><i class="fa-solid fa-location-crosshairs"></i>Lacak Pesanan</a>
                                        <a href="<?= BASE_URL ?>/pages/marketplace.php" class="flex-1 sm:flex-none bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold px-6 py-2.5 rounded-xl shadow-sm smooth-transition no-underline text-sm flex items-center justify-center gap-2">Beli Lagi</a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const regionDefaults = <?= json_encode($regionDefaults, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
const regionState = {
    provinceId: '',
    cityId: '',
    districtId: ''
};
let isProfileEditMode = false;
let profilePhotoCropper = null;
let selectedProfilePhotoObjectUrl = '';

function findRegionSelect(name) {
    return document.querySelector(`[data-region="${name}"]`);
}

function findPostalCodeInput() {
    return document.querySelector('input[name="postal_code"]');
}

function triggerProfilePhotoPicker() {
    if (!isProfileEditMode) {
        return;
    }

    document.getElementById('profile-photo-input')?.click();
}

function setProfileEditingState(isEditing) {
    isProfileEditMode = isEditing;

    document.querySelectorAll('.profile-input').forEach((input) => {
        const isLockedReadonly = input.dataset.lockReadonly === 'true';
        const isSelect = input.tagName === 'SELECT';

        if (isEditing) {
            input.readOnly = isLockedReadonly ? true : false;
            input.disabled = false;
            input.classList.remove('bg-slate-100', 'text-slate-400', 'cursor-not-allowed');
            input.classList.add('bg-white', 'text-slate-700', 'border-primary');
            input.classList.remove('bg-slate-50');

            if (isSelect && isLockedReadonly) {
                input.disabled = true;
            }
        } else {
            input.readOnly = true;
            input.disabled = true;
            input.classList.remove('bg-white', 'text-slate-700', 'border-primary');
            input.classList.add('bg-slate-100', 'text-slate-400', 'cursor-not-allowed');
        }
    });

    const changePhotoBtn = document.getElementById('change-photo-btn');
    if (changePhotoBtn) {
        changePhotoBtn.disabled = !isEditing;
        changePhotoBtn.classList.toggle('bg-white', isEditing);
        changePhotoBtn.classList.toggle('hover:bg-slate-100', isEditing);
        changePhotoBtn.classList.toggle('text-slate-700', isEditing);
        changePhotoBtn.classList.toggle('cursor-pointer', isEditing);
        changePhotoBtn.classList.toggle('bg-slate-100', !isEditing);
        changePhotoBtn.classList.toggle('text-slate-400', !isEditing);
        changePhotoBtn.classList.toggle('cursor-not-allowed', !isEditing);
    }

    document.getElementById('edit-btn')?.classList.toggle('hidden', isEditing);
    document.getElementById('discard-btn')?.classList.toggle('hidden', !isEditing);
    document.getElementById('save-btn')?.classList.toggle('hidden', !isEditing);
}

function setAddressEditingState(isEditing) {
    document.querySelectorAll('.address-input').forEach((input) => {
        const isLockedReadonly = input.dataset.lockReadonly === 'true';
        const isSelect = input.tagName === 'SELECT';

        if (isEditing) {
            input.readOnly = isLockedReadonly ? true : false;
            input.disabled = isLockedReadonly;
            input.classList.remove('bg-slate-100', 'text-slate-400', 'cursor-not-allowed');
            input.classList.add('bg-white', 'text-slate-700', 'border-primary');
            input.classList.remove('bg-slate-50');

            if (isSelect && !isLockedReadonly) {
                input.disabled = false;
            }
        } else {
            input.readOnly = true;
            input.disabled = true;
            input.classList.remove('bg-white', 'text-slate-700', 'border-primary');
            input.classList.add('bg-slate-100', 'text-slate-400', 'cursor-not-allowed');
        }
    });

    document.getElementById('address-edit-btn')?.classList.toggle('hidden', isEditing);
    document.getElementById('address-discard-btn')?.classList.toggle('hidden', !isEditing);
    document.getElementById('address-save-btn')?.classList.toggle('hidden', !isEditing);
}

function openProfilePhotoModal(file) {
    const modal = document.getElementById('photo-crop-modal');
    const image = document.getElementById('cropper-image');

    if (!modal || !image) {
        return;
    }

    if (selectedProfilePhotoObjectUrl) {
        URL.revokeObjectURL(selectedProfilePhotoObjectUrl);
    }

    selectedProfilePhotoObjectUrl = URL.createObjectURL(file);
    image.src = selectedProfilePhotoObjectUrl;
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    if (profilePhotoCropper) {
        profilePhotoCropper.destroy();
    }

    image.onload = () => {
        profilePhotoCropper = new Cropper(image, {
            aspectRatio: 1,
            viewMode: 1,
            dragMode: 'move',
            guides: false,
            center: false,
            background: false,
            autoCropArea: 1,
            responsive: true,
            preview: '#cropper-preview',
        });
    };
}

function closeProfilePhotoModal() {
    const modal = document.getElementById('photo-crop-modal');
    const input = document.getElementById('profile-photo-input');

    if (profilePhotoCropper) {
        profilePhotoCropper.destroy();
        profilePhotoCropper = null;
    }

    if (selectedProfilePhotoObjectUrl) {
        URL.revokeObjectURL(selectedProfilePhotoObjectUrl);
        selectedProfilePhotoObjectUrl = '';
    }

    if (modal) {
        modal.classList.add('hidden');
    }

    if (input) {
        input.value = '';
    }

    document.body.style.overflow = '';
}

function resetProfilePhotoCrop() {
    profilePhotoCropper?.reset();
}

function updateProfilePhotoPreview(photoUrl) {
    const previewContainer = document.querySelector('.w-24.h-24.rounded-full.border-4.border-white.shadow-sm.bg-slate-200');
    const previewImage = document.getElementById('profile-photo-preview');
    const previewFallback = document.getElementById('profile-photo-fallback');
    const sidebarImage = document.getElementById('profile-sidebar-photo');
    const sidebarInitials = document.getElementById('profile-sidebar-initials');
    const headerImage = document.querySelector('nav img[alt="Foto Profil"]');

    if (previewImage) {
        previewImage.src = photoUrl;
    } else if (previewContainer) {
        previewContainer.innerHTML = `<img id="profile-photo-preview" src="${photoUrl}" alt="Foto Profil" class="w-full h-full object-cover">`;
    }

    if (previewFallback) {
        previewFallback.remove();
    }

    if (sidebarImage) {
        sidebarImage.src = photoUrl;
    } else {
        const sidebarAvatar = document.querySelector('#profile-sidebar-initials')?.parentElement;
        if (sidebarAvatar) {
            const onlineBadge = sidebarAvatar.querySelector('div[title="Online"]');
            sidebarAvatar.innerHTML = `<img id="profile-sidebar-photo" src="${photoUrl}" alt="Foto Profil" class="w-full h-full object-cover">`;
            if (onlineBadge) {
                sidebarAvatar.appendChild(onlineBadge);
            }
        }
    }

    if (sidebarInitials) {
        sidebarInitials.remove();
    }

    if (headerImage) {
        headerImage.src = photoUrl;
    } else {
        window.location.reload();
    }
}

async function uploadProfilePhoto(file) {
    const changePhotoBtn = document.getElementById('change-photo-btn');
    const saveCroppedPhotoBtn = document.getElementById('save-cropped-photo-btn');
    const originalText = changePhotoBtn?.innerHTML || '';
    const originalSaveText = saveCroppedPhotoBtn?.innerHTML || '';

    try {
        if (changePhotoBtn) {
            changePhotoBtn.disabled = true;
            changePhotoBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mengunggah...';
        }
        if (saveCroppedPhotoBtn) {
            saveCroppedPhotoBtn.disabled = true;
            saveCroppedPhotoBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
        }

        const formData = new FormData();
        formData.append('photo', file);

        const response = await fetch('<?= BASE_URL ?>/api/auth/upload-profile-photo.php', {
            method: 'POST',
            credentials: 'same-origin',
            body: formData,
        });
        const result = await response.json();

        if (!response.ok || result.status !== 'success') {
            throw new Error(result.message || 'Upload foto profil gagal.');
        }

        if (result.photo_url) {
            updateProfilePhotoPreview(result.photo_url);
        }
        closeProfilePhotoModal();
    } catch (error) {
        alert(error.message || 'Upload foto profil gagal.');
    } finally {
        if (changePhotoBtn) {
            changePhotoBtn.disabled = false;
            changePhotoBtn.innerHTML = originalText;
        }
        if (saveCroppedPhotoBtn) {
            saveCroppedPhotoBtn.disabled = false;
            saveCroppedPhotoBtn.innerHTML = originalSaveText;
        }
    }
}

async function saveCroppedProfilePhoto() {
    if (!profilePhotoCropper) {
        return;
    }

    const canvas = profilePhotoCropper.getCroppedCanvas({
        width: 640,
        height: 640,
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high',
    });

    if (!canvas) {
        alert('Preview crop belum siap.');
        return;
    }

    const blob = await new Promise((resolve) => {
        canvas.toBlob(resolve, 'image/jpeg', 0.9);
    });

    if (!blob) {
        alert('Gagal menyiapkan foto hasil crop.');
        return;
    }

    const croppedFile = new File([blob], 'profile-photo.jpg', { type: 'image/jpeg' });
    await uploadProfilePhoto(croppedFile);
}

function resetRegionSelect(select, placeholder, keepValue = '') {
    if (!select) {
        return;
    }

    const label = keepValue || placeholder;
    select.innerHTML = '';
    const option = document.createElement('option');
    option.value = keepValue;
    option.textContent = label;
    select.appendChild(option);
    select.value = keepValue;
}

function setRegionLoading(select, placeholder) {
    resetRegionSelect(select, 'Memuat ' + placeholder.toLowerCase() + '...');
}

function fillRegionSelect(select, items, selectedName, placeholder) {
    if (!select) {
        return '';
    }

    select.innerHTML = '';
    const defaultOption = document.createElement('option');
    defaultOption.value = '';
    defaultOption.textContent = placeholder;
    select.appendChild(defaultOption);

    let selectedId = '';
    for (const item of items) {
        const option = document.createElement('option');
        option.value = item.name;
        option.textContent = item.name;
        option.dataset.id = item.id;
        if (selectedName && item.name.toLowerCase() === selectedName.toLowerCase()) {
            option.selected = true;
            selectedId = item.id;
        }
        select.appendChild(option);
    }

    if (!selectedId) {
        select.value = '';
    }

    return selectedId;
}

async function fetchRegions(level, parentId = '') {
    const url = new URL('<?= BASE_URL ?>/api/auth/regions.php', window.location.origin);
    url.searchParams.set('level', level);
    if (parentId) {
        url.searchParams.set('parent_id', parentId);
    }

    const response = await fetch(url.toString(), {
        headers: { 'Accept': 'application/json' }
    });
    const result = await response.json();

    if (!response.ok || result.status !== 'success') {
        throw new Error(result.message || 'Gagal memuat data wilayah.');
    }

    return result.items || [];
}

async function fetchPostalCode({ province = '', city = '', district = '', village = '' } = {}) {
    if (!province || !city) {
        return '';
    }

    const url = new URL('<?= BASE_URL ?>/api/auth/regions.php', window.location.origin);
    url.searchParams.set('level', 'postal_code');
    url.searchParams.set('province', province);
    url.searchParams.set('city', city);
    if (district) {
        url.searchParams.set('district', district);
    }
    if (village) {
        url.searchParams.set('village', village);
    }

    const response = await fetch(url.toString(), {
        headers: { 'Accept': 'application/json' }
    });
    const result = await response.json();

    if (!response.ok || result.status !== 'success') {
        throw new Error(result.message || 'Gagal memuat kode pos.');
    }

    return result.postal_code || '';
}

async function updatePostalCodeField() {
    const postalCodeInput = findPostalCodeInput();
    const provinceSelect = findRegionSelect('province');
    const citySelect = findRegionSelect('city');
    const districtSelect = findRegionSelect('district');
    const villageSelect = findRegionSelect('village');

    if (!postalCodeInput) {
        return;
    }

    const province = provinceSelect?.value?.trim() || '';
    const city = citySelect?.value?.trim() || '';
    const district = districtSelect?.value?.trim() || '';
    const village = villageSelect?.value?.trim() || '';

    if (!province || !city) {
        postalCodeInput.value = '';
        return;
    }

    try {
        postalCodeInput.value = await fetchPostalCode({ province, city, district, village });
    } catch (error) {
        console.error(error);
        postalCodeInput.value = '';
    }
}

async function loadProvinceOptions(selectedName = '') {
    const provinceSelect = findRegionSelect('province');
    setRegionLoading(provinceSelect, 'provinsi');
    const items = await fetchRegions('provinces');
    regionState.provinceId = fillRegionSelect(provinceSelect, items, selectedName, 'Pilih provinsi');
    return regionState.provinceId;
}

async function loadCityOptions(provinceId, selectedName = '') {
    const citySelect = findRegionSelect('city');
    const districtSelect = findRegionSelect('district');
    const villageSelect = findRegionSelect('village');
    const postalCodeInput = findPostalCodeInput();

    resetRegionSelect(districtSelect, 'Pilih kecamatan');
    resetRegionSelect(villageSelect, 'Pilih kelurahan / desa');
    regionState.cityId = '';
    regionState.districtId = '';
    if (postalCodeInput) {
        postalCodeInput.value = '';
    }

    if (!provinceId) {
        resetRegionSelect(citySelect, 'Pilih kota / kabupaten');
        return '';
    }

    setRegionLoading(citySelect, 'kota / kabupaten');
    const items = await fetchRegions('cities', provinceId);
    regionState.cityId = fillRegionSelect(citySelect, items, selectedName, 'Pilih kota / kabupaten');
    return regionState.cityId;
}

async function loadDistrictOptions(cityId, selectedName = '') {
    const districtSelect = findRegionSelect('district');
    const villageSelect = findRegionSelect('village');
    const postalCodeInput = findPostalCodeInput();
    resetRegionSelect(villageSelect, 'Pilih kelurahan / desa');
    regionState.districtId = '';
    if (postalCodeInput) {
        postalCodeInput.value = '';
    }

    if (!cityId) {
        resetRegionSelect(districtSelect, 'Pilih kecamatan');
        return '';
    }

    setRegionLoading(districtSelect, 'kecamatan');
    const items = await fetchRegions('districts', cityId);
    regionState.districtId = fillRegionSelect(districtSelect, items, selectedName, 'Pilih kecamatan');
    return regionState.districtId;
}

async function loadVillageOptions(districtId, selectedName = '') {
    const villageSelect = findRegionSelect('village');
    const postalCodeInput = findPostalCodeInput();
    if (!districtId) {
        resetRegionSelect(villageSelect, 'Pilih kelurahan / desa');
        if (postalCodeInput) {
            postalCodeInput.value = '';
        }
        return '';
    }

    setRegionLoading(villageSelect, 'kelurahan / desa');
    const items = await fetchRegions('villages', districtId);
    return fillRegionSelect(villageSelect, items, selectedName, 'Pilih kelurahan / desa');
}

async function initializeRegionFields() {
    const provinceSelect = findRegionSelect('province');
    const citySelect = findRegionSelect('city');

    try {
        const provinceId = await loadProvinceOptions(regionDefaults.province);
        const cityId = await loadCityOptions(provinceId, regionDefaults.city);
        const districtId = await loadDistrictOptions(cityId, regionDefaults.district);
        await loadVillageOptions(districtId, regionDefaults.village);
        if (!findPostalCodeInput()?.value) {
            await updatePostalCodeField();
        }
    } catch (error) {
        console.error(error);
        resetRegionSelect(provinceSelect, regionDefaults.province || 'Pilih provinsi', regionDefaults.province || '');
        resetRegionSelect(citySelect, regionDefaults.city || 'Pilih kota / kabupaten', regionDefaults.city || '');
        resetRegionSelect(findRegionSelect('district'), regionDefaults.district || 'Pilih kecamatan', regionDefaults.district || '');
        resetRegionSelect(findRegionSelect('village'), regionDefaults.village || 'Pilih kelurahan / desa', regionDefaults.village || '');
    }
}

function enableEdit() {
    setProfileEditingState(true);
}

function discardProfileChanges() {
    closeProfilePhotoModal();
    window.location.reload();
}

function enableAddressEdit() {
    setAddressEditingState(true);
}

function discardAddressChanges() {
    window.location.reload();
}

async function updateProfile(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    const btn = document.getElementById('save-btn');
    const originalText = btn.textContent;

    btn.disabled = true;
    btn.textContent = 'Menyimpan...';

    try {
        const token = localStorage.getItem('em_token');
        const headers = { 'Content-Type': 'application/json' };
        if (token) {
            headers.Authorization = 'Bearer ' + token;
        }

        const response = await fetch('<?= BASE_URL ?>/api/auth/update-profile.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers,
            body: JSON.stringify(data)
        });
        const result = await response.json();

        if (response.ok && result.status === 'success') {
            window.location.reload();
            return;
        }

        alert(result.message || 'Gagal memperbarui profil.');
    } catch (error) {
        alert('Terjadi kesalahan sistem.');
    }

    btn.disabled = false;
    btn.textContent = originalText;
}

async function updateAddress(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    const btn = document.getElementById('address-save-btn');
    const originalText = btn.textContent;

    btn.disabled = true;
    btn.textContent = 'Menyimpan...';

    try {
        const token = localStorage.getItem('em_token');
        const headers = { 'Content-Type': 'application/json' };
        if (token) {
            headers.Authorization = 'Bearer ' + token;
        }

        const response = await fetch('<?= BASE_URL ?>/api/auth/update-profile.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers,
            body: JSON.stringify(data)
        });
        const result = await response.json();

        if (response.ok && result.status === 'success') {
            window.location.reload();
            return;
        }

        alert(result.message || 'Gagal memperbarui alamat.');
    } catch (error) {
        alert('Terjadi kesalahan sistem.');
    }

    btn.disabled = false;
    btn.textContent = originalText;
}

function switchTab(tabId, title, btnElement) {
    document.getElementById('page-title').textContent = title;
    document.querySelectorAll('.tab-content').forEach((tab) => tab.classList.remove('active'));
    document.querySelectorAll('.menu-btn').forEach((btn) => {
        btn.classList.remove('active', 'bg-primaryLight', 'text-primary', 'border', 'border-blue-200');
        btn.classList.add('bg-transparent', 'text-slate-600');
    });
    document.getElementById('tab-' + tabId).classList.add('active');
    btnElement.classList.remove('bg-transparent', 'text-slate-600');
    btnElement.classList.add('active', 'bg-primaryLight', 'text-primary', 'border', 'border-blue-200');
}

function goToProfileTab(tabId, title) {
    const button = document.getElementById('menu-' + tabId);
    if (!button) {
        return;
    }

    switchTab(tabId, title, button);
}

document.addEventListener('DOMContentLoaded', () => {
    initializeRegionFields();
    setProfileEditingState(false);
    setAddressEditingState(false);

    const provinceSelect = findRegionSelect('province');
    const citySelect = findRegionSelect('city');
    const districtSelect = findRegionSelect('district');

    provinceSelect?.addEventListener('change', async (event) => {
        const selectedOption = event.target.selectedOptions[0];
        regionState.provinceId = selectedOption?.dataset.id || '';
        regionDefaults.city = '';
        regionDefaults.district = '';
        regionDefaults.village = '';
        await loadCityOptions(regionState.provinceId, '');
        await updatePostalCodeField();
    });

    citySelect?.addEventListener('change', async (event) => {
        const selectedOption = event.target.selectedOptions[0];
        regionState.cityId = selectedOption?.dataset.id || '';
        regionDefaults.district = '';
        regionDefaults.village = '';
        await loadDistrictOptions(regionState.cityId, '');
        await updatePostalCodeField();
    });

    districtSelect?.addEventListener('change', async (event) => {
        const selectedOption = event.target.selectedOptions[0];
        regionState.districtId = selectedOption?.dataset.id || '';
        regionDefaults.village = '';
        await loadVillageOptions(regionState.districtId, '');
        await updatePostalCodeField();
    });

    findRegionSelect('village')?.addEventListener('change', async () => {
        await updatePostalCodeField();
    });

    document.getElementById('profile-photo-input')?.addEventListener('change', async (event) => {
        const file = event.target.files?.[0];
        if (!file) {
            return;
        }

        openProfilePhotoModal(file);
    });
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
