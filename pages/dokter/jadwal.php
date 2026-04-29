<?php
require_once '../../includes/staff_portal_helpers.php';

$doctor = findDoctorByUserId($pdo, (int) (currentUser()['id'] ?? 0));
$scheduleSaved = false;
$errorMessage = '';

if (!$doctor) {
    include '../../includes/header_dokter.php';
    echo '<div class="p-8 max-w-4xl mx-auto"><div class="bg-white border border-red-200 text-red-700 rounded-2xl p-6 shadow-sm">Profil dokter tidak ditemukan.</div></div>';
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $days = [];
        foreach ($_POST['days'] ?? [] as $index => $row) {
            $days[] = [
                'day_of_week' => (int) ($row['day_of_week'] ?? 0),
                'active' => !empty($row['active']),
                'start' => (string) ($row['start'] ?? '09:00'),
                'end' => (string) ($row['end'] ?? '15:00'),
            ];
        }
        saveDoctorWeeklySchedule($pdo, (int) $doctor['iddokter'], $days);
        $scheduleSaved = true;
    } catch (Throwable $e) {
        $errorMessage = $e->getMessage();
    }
}

$weeklySchedule = getDoctorWeeklySchedule($pdo, (int) $doctor['iddokter']);

include '../../includes/header_dokter.php';
?>

<div x-data="{ editModalOpen: false }" class="p-6 sm:p-8">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Jadwal Praktik Mingguan</h1>
            <p class="text-gray-500 mt-1">Jadwal ini dipakai langsung oleh modul booking pasien untuk menghasilkan slot konsultasi.</p>
        </div>
        <button @click="editModalOpen = true" class="bg-white border border-gray-300 text-gray-700 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-300 font-bold py-2.5 px-5 rounded-lg shadow-sm transition flex items-center justify-center w-full sm:w-auto">
            <i class="fa-solid fa-pen-to-square mr-2"></i> Edit Jadwal
        </button>
    </div>

    <?php if ($scheduleSaved): ?>
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl px-5 py-4 font-medium">Jadwal praktik berhasil diperbarui.</div>
    <?php endif; ?>
    <?php if ($errorMessage !== ''): ?>
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 rounded-2xl px-5 py-4 font-medium"><?= htmlspecialchars($errorMessage) ?></div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50/80">
            <div class="flex items-center space-x-4">
                <h2 class="text-lg font-bold text-gray-800">Jadwal Aktif</h2>
            </div>
            <div class="hidden sm:flex items-center space-x-2 text-sm text-gray-500 font-medium border bg-white px-3 py-1 rounded-full shadow-sm">
                <span class="w-3 h-3 rounded-full bg-blue-500 inline-block shadow-inner"></span> Dipakai untuk booking pasien
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-7 divide-y md:divide-y-0 md:divide-x divide-gray-200 min-h-[420px]">
            <?php foreach ($weeklySchedule as $day): ?>
                <div class="flex flex-col h-full <?= in_array($day['name'], ['Sabtu', 'Minggu'], true) ? 'bg-gray-50/50' : '' ?>">
                    <div class="py-3 text-center border-b border-gray-200 <?= in_array($day['name'], ['Sabtu', 'Minggu'], true) ? 'bg-red-50/30' : 'bg-white' ?>">
                        <span class="block text-xs font-bold uppercase tracking-wider <?= in_array($day['name'], ['Sabtu', 'Minggu'], true) ? 'text-red-400' : 'text-gray-500' ?>"><?= htmlspecialchars($day['name']) ?></span>
                    </div>
                    <div class="p-3 flex-1 relative flex flex-col">
                        <?php if ($day['active']): ?>
                            <div class="bg-blue-50 border-l-4 border-blue-500 rounded-r-lg p-3 shadow-sm mt-2">
                                <p class="text-xs font-extrabold text-blue-700"><?= htmlspecialchars($day['start']) ?> - <?= htmlspecialchars($day['end']) ?></p>
                                <p class="text-sm font-bold text-gray-800 mt-1">Praktik Reguler</p>
                                <p class="text-xs text-blue-600/80 mt-1 flex items-center font-medium"><i class="fa-solid fa-stethoscope mr-1.5"></i> <?= htmlspecialchars($doctor['specialization']) ?></p>
                            </div>
                        <?php else: ?>
                            <div class="flex-1 flex flex-col items-center justify-center text-center opacity-40 mt-10">
                                <i class="fa-solid fa-mug-hot text-gray-400 text-2xl mb-2"></i>
                                <p class="text-xs font-semibold text-gray-500">Tidak ada jadwal</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div x-show="editModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="editModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity"></div>
        <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
            <div x-show="editModalOpen" @click.away="editModalOpen = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-gray-100">
                <form method="post">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900" id="modal-title"><i class="fa-solid fa-sliders text-blue-600 mr-2"></i> Konfigurasi Jam Praktik</h3>
                        <button @click="editModalOpen = false" type="button" class="text-gray-400 hover:text-red-500 transition"><i class="fa-solid fa-xmark text-xl"></i></button>
                    </div>
                    <div class="px-6 py-6 space-y-4 max-h-[60vh] overflow-y-auto">
                        <?php foreach ($weeklySchedule as $index => $day): ?>
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-xl border <?= $day['active'] ? 'border-blue-200 bg-blue-50/50' : 'border-gray-200 bg-gray-50' ?>">
                                <div class="flex items-center mb-3 sm:mb-0 w-40">
                                    <input type="hidden" name="days[<?= $index ?>][day_of_week]" value="<?= (int) $day['day_of_week'] ?>">
                                    <input type="checkbox" name="days[<?= $index ?>][active]" value="1" class="h-5 w-5" <?= $day['active'] ? 'checked' : '' ?>>
                                    <span class="ml-3 font-bold <?= $day['active'] ? 'text-blue-900' : 'text-gray-500' ?>"><?= htmlspecialchars($day['name']) ?></span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <input type="time" name="days[<?= $index ?>][start]" value="<?= htmlspecialchars($day['start']) ?>" class="px-3 py-2 rounded-lg border border-gray-300 shadow-sm w-32 text-center font-medium">
                                    <span class="text-gray-400 font-medium">s/d</span>
                                    <input type="time" name="days[<?= $index ?>][end]" value="<?= htmlspecialchars($day['end']) ?>" class="px-3 py-2 rounded-lg border border-gray-300 shadow-sm w-32 text-center font-medium">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                        <button @click="editModalOpen = false" type="button" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition">Batal</button>
                        <button type="submit" class="inline-flex w-full justify-center rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow-md hover:bg-blue-700 sm:w-auto transition">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
