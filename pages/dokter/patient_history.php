<?php
require_once '../../includes/staff_portal_helpers.php';

$doctorUserId = (int) (currentUser()['id'] ?? 0);
$patientUserId = (int) ($_GET['patient'] ?? 0);
$directory = getDoctorMedicalRecords($pdo, $doctorUserId);
$patientRecord = $patientUserId > 0 ? getDoctorPatientMedicalRecord($pdo, $doctorUserId, $patientUserId) : null;

include '../../includes/header_dokter.php';
?>

<div class="p-6 sm:p-8 max-w-7xl mx-auto">
    <?php if (!$patientRecord): ?>
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Direktori Rekam Medis</h1>
            <p class="text-gray-500 mt-1">Semua data di bawah ini diambil dari konsultasi, diagnosis, dan resep yang pernah Anda tangani.</p>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden" x-data="{ search: '' }">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <div class="relative max-w-md">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-3 text-gray-400"></i>
                    <input x-model="search" type="text" placeholder="Cari nama pasien atau no rekam medis..." class="w-full pl-11 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                            <th class="px-6 py-4 font-bold">Pasien & No. RM</th>
                            <th class="px-6 py-4 font-bold">Kunjungan Terakhir</th>
                            <th class="px-6 py-4 font-bold">Keluhan Terakhir</th>
                            <th class="px-6 py-4 font-bold">Diagnosis Akhir</th>
                            <th class="px-6 py-4 font-bold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if ($directory): ?>
                            <?php foreach ($directory as $patient): ?>
                                <tr x-show="'<?= strtolower(addslashes($patient['name'] . ' ' . $patient['record_code'])) ?>'.includes(search.toLowerCase())" class="hover:bg-blue-50/30 transition group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs shadow-sm shrink-0"><?= htmlspecialchars($patient['avatar']) ?></div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition"><?= htmlspecialchars($patient['name']) ?></p>
                                                <p class="text-[10px] font-bold text-gray-500 mt-0.5"><?= htmlspecialchars($patient['record_code']) ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($patient['last_visit_label']) ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($patient['last_complaint']) ?></td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            <i class="fa-solid fa-stethoscope"></i> <?= htmlspecialchars($patient['last_diagnosis']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="patient_history.php?patient=<?= (int) $patient['patient_user_id'] ?>" class="text-blue-600 bg-white border border-blue-200 hover:bg-blue-600 hover:text-white rounded-lg px-4 py-2 text-xs font-bold transition no-underline">
                                            Buka Rekam Medis
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fa-solid fa-folder-open text-4xl text-gray-300 mb-3 block"></i>
                                    <p class="text-sm">Belum ada data rekam medis pasien untuk dokter ini.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="patient_history.php" class="text-gray-400 hover:text-blue-600 transition flex items-center gap-2 bg-white px-3 py-1.5 rounded-lg border border-gray-200 shadow-sm no-underline">
                    <i class="fa-solid fa-arrow-left"></i> <span class="text-sm font-bold">Kembali ke Direktori</span>
                </a>
                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight ml-4">Detail Rekam Medis</h1>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-600 to-blue-500 rounded-3xl shadow-sm overflow-hidden mb-8 relative">
            <i class="fa-solid fa-heart-pulse absolute -right-10 -bottom-10 text-9xl opacity-10"></i>
            <div class="p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-6 relative z-10">
                <div class="flex items-center gap-5">
                    <div class="w-20 h-20 rounded-full bg-white text-blue-600 flex items-center justify-center font-black text-3xl shadow-lg border-4 border-blue-200"><?= htmlspecialchars($patientRecord['patient']['avatar']) ?></div>
                    <div class="text-white">
                        <h2 class="text-2xl font-bold mb-1"><?= htmlspecialchars($patientRecord['patient']['name']) ?></h2>
                        <p class="text-blue-100 font-medium">Pasien - #<?= htmlspecialchars($patientRecord['patient']['record_code']) ?></p>
                        <p class="text-sm font-medium mt-2 flex items-center gap-4">
                            <span><i class="fa-solid fa-cake-candles mr-1.5 opacity-80"></i> <?= htmlspecialchars((string) $patientRecord['patient']['age']) ?> Tahun</span>
                            <span><i class="fa-solid fa-user mr-1.5 opacity-80"></i> <?= htmlspecialchars($patientRecord['patient']['gender']) ?></span>
                        </p>
                    </div>
                </div>
                <div class="flex flex-col gap-2 bg-black/10 p-4 rounded-2xl border border-white/10 backdrop-blur-sm text-white min-w-[240px]">
                    <div class="flex justify-between items-center gap-6">
                        <span class="text-sm font-semibold">Gol. Darah</span>
                        <span class="text-sm font-bold"><?= htmlspecialchars($patientRecord['patient']['blood_type']) ?></span>
                    </div>
                    <div class="flex justify-between items-center gap-6">
                        <span class="text-sm font-semibold">Berat / Tinggi</span>
                        <span class="text-sm font-bold"><?= htmlspecialchars($patientRecord['patient']['weight']) ?> / <?= htmlspecialchars($patientRecord['patient']['height']) ?></span>
                    </div>
                    <div class="flex justify-between items-center gap-6">
                        <span class="text-sm font-semibold">Alergi</span>
                        <span class="text-sm font-bold text-right"><?= htmlspecialchars($patientRecord['patient']['allergy']) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-3xl p-6 md:p-8 shadow-sm">
            <div class="mb-6 border-b border-gray-200 pb-4">
                <h2 class="text-lg font-bold text-gray-900">Riwayat Konsultasi Pasien</h2>
                <p class="text-sm text-gray-500 mt-1">Diagnosis, catatan, dan resep di bawah ini berasal dari histori klinis yang tersimpan.</p>
            </div>

            <div class="space-y-4">
                <?php foreach ($patientRecord['history'] as $record): ?>
                    <div class="border border-gray-200 rounded-2xl p-5 shadow-sm hover:border-blue-200 transition">
                        <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-xs font-bold text-gray-400"><?= htmlspecialchars($record['date_label']) ?></span>
                                    <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                    <span class="text-xs font-bold <?= $record['status'] === 'Selesai' ? 'text-emerald-600' : ($record['status'] === 'Berjalan' ? 'text-blue-600' : 'text-amber-600') ?>"><?= htmlspecialchars($record['status']) ?></span>
                                </div>
                                <p class="text-sm text-gray-700 mb-2"><span class="font-semibold text-gray-900">Keluhan:</span> <?= htmlspecialchars($record['complaint']) ?></p>
                                <p class="text-sm text-gray-700 mb-2"><span class="font-semibold text-gray-900">Diagnosis:</span> <?= htmlspecialchars($record['diagnosis']) ?></p>
                                <?php if ($record['notes'] !== ''): ?>
                                    <p class="text-sm text-gray-700"><span class="font-semibold text-gray-900">Catatan:</span> <?= htmlspecialchars($record['notes']) ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="flex flex-col gap-2 min-w-[180px]">
                                <?php if ($record['has_prescription']): ?>
                                    <a href="prescription_form.php?consultation=<?= (int) $record['consultation_id'] ?>" class="text-blue-600 bg-blue-50 border border-blue-200 rounded-xl px-4 py-2 text-sm font-bold text-center no-underline">
                                        Lihat / Edit Resep
                                    </a>
                                <?php else: ?>
                                    <a href="prescription_form.php?consultation=<?= (int) $record['consultation_id'] ?>" class="text-emerald-600 bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-2 text-sm font-bold text-center no-underline">
                                        Buat Resep
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if ($record['prescription_items']): ?>
                            <div class="mt-4 pt-4 border-t border-dashed border-gray-200">
                                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Riwayat Resep</div>
                                <div class="space-y-2">
                                    <?php foreach ($record['prescription_items'] as $item): ?>
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                                            <div class="font-bold text-gray-800"><?= htmlspecialchars($item['name']) ?></div>
                                            <div class="text-sm text-gray-500"><?= (int) $item['qty'] ?> item<?= $item['instruction'] !== '' ? ' • ' . htmlspecialchars($item['instruction']) : '' ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
