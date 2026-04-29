<?php
require_once '../../includes/header_dokter.php';

ensureUserProfilePhotoSchema($pdo);

$doctorUserId = (int) (currentUser()['id'] ?? 0);
$doctorStmt = $pdo->prepare(
    "SELECT
        u.id,
        u.nama,
        u.email,
        u.phone,
        u.profile_photo,
        d.idDokter,
        COALESCE(d.spesialisasi, 'Dokter Umum') AS specialization,
        COALESCE(d.nomorSTR, '') AS license
     FROM users u
     INNER JOIN Dokter d ON d.id_user = u.id
     WHERE u.id = :user_id
     LIMIT 1"
);
$doctorStmt->execute([':user_id' => $doctorUserId]);
$doctor = $doctorStmt->fetch();

if (!$doctor) {
    echo '<div class="p-8 max-w-4xl mx-auto"><div class="bg-white border border-red-200 text-red-700 rounded-2xl p-6 shadow-sm">Profil dokter tidak ditemukan.</div></div>';
    include '../../includes/footer.php';
    return;
}

$statsStmt = $pdo->prepare(
    "SELECT
        COUNT(sk.idKonsultasi) AS total_consultations,
        COUNT(DISTINCT sk.idPasien) AS total_patients,
        SUM(CASE WHEN sk.status = 'Selesai' THEN 1 ELSE 0 END) AS completed_consultations
     FROM SesiKonsultasi sk
     WHERE sk.idDokter = :doctor_id"
);
$statsStmt->execute([':doctor_id' => (int) $doctor['iddokter']]);
$doctorStats = $statsStmt->fetch() ?: [];

$profilePhotoUrl = getUserProfilePhotoUrl((string) ($doctor['profile_photo'] ?? ''));
$doctorInitials = getInitials((string) ($doctor['nama'] ?? 'Dokter'));
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>

<style>
    .doctor-field {
        transition: all .2s ease-in-out;
    }

    .doctor-field.is-locked {
        background-color: #f3f4f6;
        color: #9ca3af;
        cursor: not-allowed;
    }

    .doctor-field.is-unlocked {
        background-color: #fff;
        color: #1f2937;
        border-color: #3b82f6;
    }

    .cropper-view-box,
    .cropper-face {
        border-radius: 9999px;
    }
</style>

<div class="bg-gray-50 min-h-screen pb-12 pt-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Manajemen Profil</h1>
                <p class="text-gray-500 mt-1">Kelola informasi dokter yang tampil di portal staf dan pencarian pasien.</p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" id="edit-profile-btn" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-5 rounded-xl shadow-sm transition">
                    <i class="fa-solid fa-pen-to-square"></i>
                    Edit Profil
                </button>
                <button type="button" id="discard-profile-btn" class="hidden inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 px-5 rounded-xl transition">
                    <i class="fa-solid fa-rotate-left"></i>
                    Discard Changes
                </button>
                <button type="submit" form="doctor-profile-form" id="save-profile-btn" class="hidden inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-5 rounded-xl shadow-sm transition">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Simpan
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <div class="xl:col-span-2 space-y-8">
                <form id="doctor-profile-form" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8" novalidate>
                    <h2 class="text-xl font-bold text-gray-800 border-b pb-4 mb-6">
                        <i class="fa-solid fa-user-doctor text-blue-500 mr-2"></i>
                        Informasi Pribadi & Profesi
                    </h2>

                    <div class="flex flex-col sm:flex-row gap-8 mb-8">
                        <div class="flex-shrink-0 flex flex-col items-center">
                            <div id="doctor-photo-shell" class="w-32 h-32 rounded-full bg-blue-100 border-4 border-white shadow-md flex items-center justify-center overflow-hidden relative text-3xl font-extrabold text-blue-700">
                                <?php if ($profilePhotoUrl !== ''): ?>
                                <img id="doctor-photo-preview" src="<?= htmlspecialchars($profilePhotoUrl) ?>" alt="Foto Dokter" class="w-full h-full object-cover">
                                <?php else: ?>
                                <span id="doctor-photo-fallback"><?= htmlspecialchars($doctorInitials) ?></span>
                                <?php endif; ?>
                            </div>
                            <input type="file" id="doctor-photo-input" accept="image/jpeg,image/png,image/webp" class="hidden">
                            <button type="button" id="change-photo-btn" class="mt-3 inline-flex items-center gap-2 text-sm font-semibold px-4 py-2 rounded-lg border border-slate-200 bg-slate-100 text-slate-400 cursor-not-allowed transition" disabled>
                                <i class="fa-solid fa-camera"></i>
                                Ubah Foto
                            </button>
                            <p class="text-xs text-slate-400 mt-2 text-center max-w-[180px]">Foto hanya bisa diubah saat mode edit aktif.</p>
                        </div>

                        <div class="flex-grow grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="sm:col-span-2">
                                <label for="nama" class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap (Sesuai Gelar)</label>
                                <input type="text" id="nama" name="nama" value="<?= htmlspecialchars((string) $doctor['nama']) ?>" class="doctor-field is-locked w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm" placeholder="Contoh: dr. Budi Santoso, Sp.M" readonly disabled>
                            </div>

                            <div>
                                <label for="spesialisasi" class="block text-sm font-semibold text-gray-700 mb-1">Spesialisasi</label>
                                <input type="text" id="spesialisasi" name="spesialisasi" value="<?= htmlspecialchars((string) $doctor['specialization']) ?>" class="doctor-field is-locked w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm" placeholder="Contoh: Dokter Umum" readonly disabled>
                            </div>

                            <div>
                                <label for="nomor_str" class="block text-sm font-semibold text-gray-700 mb-1">Nomor STR</label>
                                <input type="text" id="nomor_str" name="nomor_str" value="<?= htmlspecialchars((string) $doctor['license']) ?>" class="doctor-field is-locked w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm" placeholder="Masukkan nomor STR" readonly disabled>
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email Aktif</label>
                                <input type="email" id="email" name="email" value="<?= htmlspecialchars((string) $doctor['email']) ?>" class="doctor-field is-locked w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm" readonly disabled>
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-semibold text-gray-700 mb-1">Nomor Telepon (WhatsApp)</label>
                                <input type="text" id="phone" name="phone" value="<?= htmlspecialchars((string) ($doctor['phone'] ?? '')) ?>" class="doctor-field is-locked w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm" placeholder="08xxxxxxxxxx" readonly disabled>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="xl:col-span-1 space-y-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-8">
                    <div class="h-24 bg-gradient-to-r from-blue-500 to-cyan-400"></div>

                    <div class="px-6 pb-6 relative">
                        <div class="flex justify-center -mt-12 mb-4">
                            <div id="doctor-card-photo-shell" class="w-24 h-24 rounded-full border-4 border-white shadow-lg overflow-hidden bg-white flex items-center justify-center text-xl font-extrabold text-blue-700">
                                <?php if ($profilePhotoUrl !== ''): ?>
                                <img id="doctor-card-photo" src="<?= htmlspecialchars($profilePhotoUrl) ?>" alt="Preview" class="w-full h-full object-cover">
                                <?php else: ?>
                                <span id="doctor-card-fallback"><?= htmlspecialchars($doctorInitials) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="text-center">
                            <h3 id="doctor-card-name" class="text-xl font-bold text-gray-900"><?= htmlspecialchars((string) $doctor['nama']) ?></h3>
                            <p id="doctor-card-specialization" class="text-blue-600 font-medium text-sm mt-1"><?= htmlspecialchars((string) $doctor['specialization']) ?></p>

                            <div class="mt-4 flex items-center justify-center space-x-2 text-sm text-gray-500">
                                <i class="fa-regular fa-id-card"></i>
                                <span id="doctor-card-license">STR: <?= htmlspecialchars((string) ($doctor['license'] !== '' ? $doctor['license'] : '-')) ?></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-3 mt-6">
                            <div class="bg-slate-50 rounded-xl p-3 border border-slate-100 text-center">
                                <div class="text-xs text-slate-500">Konsultasi</div>
                                <div class="text-lg font-extrabold text-slate-900"><?= number_format((int) ($doctorStats['total_consultations'] ?? 0), 0, ',', '.') ?></div>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-3 border border-slate-100 text-center">
                                <div class="text-xs text-slate-500">Pasien</div>
                                <div class="text-lg font-extrabold text-slate-900"><?= number_format((int) ($doctorStats['total_patients'] ?? 0), 0, ',', '.') ?></div>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-3 border border-slate-100 text-center">
                                <div class="text-xs text-slate-500">Selesai</div>
                                <div class="text-lg font-extrabold text-slate-900"><?= number_format((int) ($doctorStats['completed_consultations'] ?? 0), 0, ',', '.') ?></div>
                            </div>
                        </div>

                        <div class="mt-6 bg-blue-50 rounded-xl p-4 border border-blue-100">
                            <h4 class="text-xs font-bold text-blue-800 uppercase tracking-wider mb-2">Tampilan Pasien</h4>
                            <p class="text-sm text-blue-700">Kartu ini menggambarkan informasi dokter yang akan muncul saat pasien menelusuri layanan konsultasi.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="photo-crop-modal" class="hidden fixed inset-0 z-[70] bg-slate-950/70 px-4 py-6">
    <div class="max-w-4xl mx-auto h-full flex items-center justify-center">
        <div class="bg-white w-full rounded-3xl shadow-2xl overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900">Sesuaikan Foto Profil</h3>
                    <div class="text-xs text-slate-500 mt-1">Atur posisi foto, gunakan reset kalau perlu, lalu simpan saat hasilnya sudah pas.</div>
                </div>
                <button type="button" id="close-photo-modal-btn" class="w-10 h-10 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 transition border-0">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_280px] gap-0">
                <div class="bg-slate-100 p-6 min-h-[420px] flex items-center justify-center">
                    <img id="cropper-image" alt="Preview Crop" class="max-w-full">
                </div>
                <div class="p-6 border-l border-slate-100 flex flex-col gap-5">
                    <div>
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Preview</div>
                        <div class="w-40 h-40 rounded-full overflow-hidden border-4 border-slate-100 bg-slate-50 mx-auto">
                            <div id="cropper-preview" class="w-full h-full overflow-hidden"></div>
                        </div>
                    </div>

                    <div class="space-y-3 mt-auto">
                        <button type="button" id="reset-photo-crop-btn" class="w-full inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-4 py-3 rounded-xl text-sm transition border-0">
                            <i class="fa-solid fa-arrows-rotate"></i>
                            Reset Crop
                        </button>
                        <button type="button" id="save-cropped-photo-btn" class="w-full inline-flex items-center justify-center gap-2 bg-blue-600 text-white hover:bg-blue-700 font-bold px-4 py-3 rounded-xl text-sm transition border-0">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Simpan Foto
                        </button>
                        <button type="button" id="discard-photo-btn" class="w-full inline-flex items-center justify-center gap-2 bg-white text-slate-600 hover:bg-slate-50 font-bold px-4 py-3 rounded-xl text-sm transition border border-slate-200">
                            <i class="fa-solid fa-xmark"></i>
                            Discard
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let isDoctorProfileEditing = false;
let profilePhotoCropper = null;
let selectedPhotoObjectUrl = '';

const lockedFieldClasses = ['is-locked', 'bg-gray-100', 'text-gray-400', 'cursor-not-allowed'];
const unlockedFieldClasses = ['is-unlocked', 'bg-white', 'text-gray-700'];

function getDoctorFields() {
    return Array.from(document.querySelectorAll('.doctor-field'));
}

function setDoctorProfileEditingState(isEditing) {
    isDoctorProfileEditing = isEditing;

    getDoctorFields().forEach((field) => {
        field.readOnly = !isEditing;
        field.disabled = !isEditing;
        field.classList.remove(...lockedFieldClasses, ...unlockedFieldClasses);

        if (isEditing) {
            field.classList.add(...unlockedFieldClasses);
        } else {
            field.classList.add(...lockedFieldClasses);
        }
    });

    const editBtn = document.getElementById('edit-profile-btn');
    const discardBtn = document.getElementById('discard-profile-btn');
    const saveBtn = document.getElementById('save-profile-btn');
    const changePhotoBtn = document.getElementById('change-photo-btn');

    editBtn?.classList.toggle('hidden', isEditing);
    discardBtn?.classList.toggle('hidden', !isEditing);
    saveBtn?.classList.toggle('hidden', !isEditing);

    if (changePhotoBtn) {
        changePhotoBtn.disabled = !isEditing;
        changePhotoBtn.classList.toggle('bg-slate-100', !isEditing);
        changePhotoBtn.classList.toggle('text-slate-400', !isEditing);
        changePhotoBtn.classList.toggle('cursor-not-allowed', !isEditing);
        changePhotoBtn.classList.toggle('border-slate-200', !isEditing);
        changePhotoBtn.classList.toggle('bg-blue-50', isEditing);
        changePhotoBtn.classList.toggle('text-blue-700', isEditing);
        changePhotoBtn.classList.toggle('border-blue-200', isEditing);
        changePhotoBtn.classList.toggle('hover:bg-blue-100', isEditing);
        changePhotoBtn.classList.toggle('cursor-pointer', isEditing);
    }
}

function triggerDoctorPhotoPicker() {
    if (!isDoctorProfileEditing) {
        return;
    }

    document.getElementById('doctor-photo-input')?.click();
}

function openDoctorPhotoModal(file) {
    const modal = document.getElementById('photo-crop-modal');
    const image = document.getElementById('cropper-image');

    if (!modal || !image) {
        return;
    }

    if (selectedPhotoObjectUrl) {
        URL.revokeObjectURL(selectedPhotoObjectUrl);
    }

    selectedPhotoObjectUrl = URL.createObjectURL(file);
    image.src = selectedPhotoObjectUrl;
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

function closeDoctorPhotoModal() {
    const modal = document.getElementById('photo-crop-modal');
    const input = document.getElementById('doctor-photo-input');

    if (profilePhotoCropper) {
        profilePhotoCropper.destroy();
        profilePhotoCropper = null;
    }

    if (selectedPhotoObjectUrl) {
        URL.revokeObjectURL(selectedPhotoObjectUrl);
        selectedPhotoObjectUrl = '';
    }

    if (modal) {
        modal.classList.add('hidden');
    }

    if (input) {
        input.value = '';
    }

    document.body.style.overflow = '';
}

function resetDoctorPhotoCrop() {
    profilePhotoCropper?.reset();
}

function updateDoctorPhotoPreview(photoUrl) {
    const photoShell = document.getElementById('doctor-photo-shell');
    const cardPhotoShell = document.getElementById('doctor-card-photo-shell');
    const photoPreview = document.getElementById('doctor-photo-preview');
    const cardPhoto = document.getElementById('doctor-card-photo');
    const photoFallback = document.getElementById('doctor-photo-fallback');
    const cardFallback = document.getElementById('doctor-card-fallback');

    if (photoPreview) {
        photoPreview.src = photoUrl;
    } else if (photoShell) {
        photoShell.innerHTML = `<img id="doctor-photo-preview" src="${photoUrl}" alt="Foto Dokter" class="w-full h-full object-cover">`;
    }

    if (cardPhoto) {
        cardPhoto.src = photoUrl;
    } else if (cardPhotoShell) {
        cardPhotoShell.innerHTML = `<img id="doctor-card-photo" src="${photoUrl}" alt="Preview" class="w-full h-full object-cover">`;
    }

    photoFallback?.remove();
    cardFallback?.remove();

    const headerAvatarText = document.querySelector('header .w-8.h-8.rounded-full');
    if (headerAvatarText && headerAvatarText.tagName === 'DIV') {
        headerAvatarText.innerHTML = `<img src="${photoUrl}" alt="Foto Profil" class="w-full h-full object-cover rounded-full">`;
        headerAvatarText.classList.remove('bg-blue-100', 'text-blue-700', 'border', 'border-blue-200');
        headerAvatarText.classList.add('overflow-hidden', 'bg-white');
    }
}

async function uploadDoctorPhoto(file) {
    const changePhotoBtn = document.getElementById('change-photo-btn');
    const savePhotoBtn = document.getElementById('save-cropped-photo-btn');
    const originalChangeText = changePhotoBtn?.innerHTML || '';
    const originalSaveText = savePhotoBtn?.innerHTML || '';

    try {
        if (changePhotoBtn) {
            changePhotoBtn.disabled = true;
            changePhotoBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mengunggah...';
        }
        if (savePhotoBtn) {
            savePhotoBtn.disabled = true;
            savePhotoBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
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
            updateDoctorPhotoPreview(result.photo_url);
        }

        closeDoctorPhotoModal();
    } catch (error) {
        alert(error.message || 'Upload foto profil gagal.');
    } finally {
        if (changePhotoBtn) {
            changePhotoBtn.disabled = !isDoctorProfileEditing;
            changePhotoBtn.innerHTML = originalChangeText;
        }
        if (savePhotoBtn) {
            savePhotoBtn.disabled = false;
            savePhotoBtn.innerHTML = originalSaveText;
        }
        setDoctorProfileEditingState(isDoctorProfileEditing);
    }
}

async function saveDoctorCroppedPhoto() {
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

    const croppedFile = new File([blob], 'doctor-profile-photo.jpg', { type: 'image/jpeg' });
    await uploadDoctorPhoto(croppedFile);
}

function syncDoctorCardPreview() {
    const nameField = document.getElementById('nama');
    const specializationField = document.getElementById('spesialisasi');
    const licenseField = document.getElementById('nomor_str');

    const doctorCardName = document.getElementById('doctor-card-name');
    const doctorCardSpecialization = document.getElementById('doctor-card-specialization');
    const doctorCardLicense = document.getElementById('doctor-card-license');

    if (doctorCardName && nameField) {
        doctorCardName.textContent = nameField.value.trim() || 'Dokter CareSync';
    }
    if (doctorCardSpecialization && specializationField) {
        doctorCardSpecialization.textContent = specializationField.value.trim() || 'Dokter Umum';
    }
    if (doctorCardLicense && licenseField) {
        doctorCardLicense.textContent = 'STR: ' + (licenseField.value.trim() || '-');
    }
}

async function updateDoctorProfile(event) {
    event.preventDefault();

    const saveBtn = document.getElementById('save-profile-btn');
    const originalText = saveBtn?.innerHTML || '';

    const payload = {
        nama: document.getElementById('nama')?.value.trim() || '',
        spesialisasi: document.getElementById('spesialisasi')?.value.trim() || '',
        nomor_str: document.getElementById('nomor_str')?.value.trim() || '',
        email: document.getElementById('email')?.value.trim() || '',
        phone: document.getElementById('phone')?.value.trim() || '',
    };

    try {
        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
        }

        const response = await fetch('<?= BASE_URL ?>/api/dokter/update-profile.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
        });
        const result = await response.json();

        if (!response.ok || result.status !== 'success') {
            throw new Error(result.message || 'Profil dokter gagal diperbarui.');
        }

        window.location.reload();
    } catch (error) {
        alert(error.message || 'Profil dokter gagal diperbarui.');
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        }
    }
}

function discardDoctorProfileChanges() {
    closeDoctorPhotoModal();
    window.location.reload();
}

document.addEventListener('DOMContentLoaded', () => {
    setDoctorProfileEditingState(false);

    document.getElementById('edit-profile-btn')?.addEventListener('click', () => {
        setDoctorProfileEditingState(true);
    });

    document.getElementById('discard-profile-btn')?.addEventListener('click', discardDoctorProfileChanges);
    document.getElementById('doctor-profile-form')?.addEventListener('submit', updateDoctorProfile);
    document.getElementById('change-photo-btn')?.addEventListener('click', triggerDoctorPhotoPicker);
    document.getElementById('close-photo-modal-btn')?.addEventListener('click', closeDoctorPhotoModal);
    document.getElementById('discard-photo-btn')?.addEventListener('click', closeDoctorPhotoModal);
    document.getElementById('reset-photo-crop-btn')?.addEventListener('click', resetDoctorPhotoCrop);
    document.getElementById('save-cropped-photo-btn')?.addEventListener('click', saveDoctorCroppedPhoto);

    document.getElementById('doctor-photo-input')?.addEventListener('change', (event) => {
        const file = event.target.files?.[0];
        if (!file) {
            return;
        }

        openDoctorPhotoModal(file);
    });

    ['nama', 'spesialisasi', 'nomor_str'].forEach((fieldId) => {
        document.getElementById(fieldId)?.addEventListener('input', syncDoctorCardPreview);
    });
});
</script>

<?php include '../../includes/footer.php'; ?>
