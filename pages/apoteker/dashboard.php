<?php
require_once '../../includes/staff_portal_helpers.php';

$dashboard = getPharmacistDashboardData($pdo);
$stats = $dashboard['stats'];
$queue = $dashboard['queue'];

include '../../includes/header_apoteker.php';
?>

<div class="p-6 sm:p-8 space-y-8">

    <div>
        <h1 class="text-2xl font-bold text-gray-900">Antrean Resep Hari Ini</h1>
        <p class="text-gray-500 mt-1">Daftar resep di bawah ini diambil dari tabel `Resep`, konsultasi, pasien, dan pemesanan yang sudah tersimpan.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 border-l-4 border-l-red-500">
            <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-red-500 text-xl">
                <i class="fa-solid fa-bell"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Resep Baru Masuk</p>
                <h3 class="text-2xl font-bold text-gray-900"><?= number_format((int) $stats['new'], 0, ',', '.') ?></h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 border-l-4 border-l-yellow-500">
            <div class="w-12 h-12 rounded-full bg-yellow-50 flex items-center justify-center text-yellow-600 text-xl">
                <i class="fa-solid fa-mortar-pestle"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Sedang Disiapkan</p>
                <h3 class="text-2xl font-bold text-gray-900"><?= number_format((int) $stats['processing'], 0, ',', '.') ?></h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 border-l-4 border-l-green-500">
            <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center text-green-600 text-xl">
                <i class="fa-solid fa-bag-shopping"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Siap Diambil</p>
                <h3 class="text-2xl font-bold text-gray-900"><?= number_format((int) $stats['ready'], 0, ',', '.') ?></h3>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden" x-data="{ activeFilter: 'semua' }">
        <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gray-50/50">
            <h2 class="text-lg font-bold text-gray-800"><i class="fa-solid fa-list-check text-teal-600 mr-2"></i> Daftar Antrean</h2>
            <div class="flex items-center gap-2 text-sm font-medium flex-wrap">
                <button @click="activeFilter = 'semua'" :class="activeFilter === 'semua' ? 'bg-gray-800 text-white' : 'bg-white text-gray-600 border hover:bg-gray-50'" class="px-4 py-2 rounded-lg transition shadow-sm">Semua</button>
                <button @click="activeFilter = 'baru'" :class="activeFilter === 'baru' ? 'bg-red-500 text-white' : 'bg-white text-gray-600 border hover:bg-gray-50'" class="px-4 py-2 rounded-lg transition shadow-sm">Baru</button>
                <button @click="activeFilter = 'proses'" :class="activeFilter === 'proses' ? 'bg-yellow-500 text-white' : 'bg-white text-gray-600 border hover:bg-gray-50'" class="px-4 py-2 rounded-lg transition shadow-sm">Diproses</button>
                <button @click="activeFilter = 'siap'" :class="activeFilter === 'siap' ? 'bg-green-500 text-white' : 'bg-white text-gray-600 border hover:bg-gray-50'" class="px-4 py-2 rounded-lg transition shadow-sm">Siap</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white text-gray-400 text-xs uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-4 font-bold">Waktu & No. Resep</th>
                        <th class="px-6 py-4 font-bold">Pasien</th>
                        <th class="px-6 py-4 font-bold">Dokter Perujuk</th>
                        <th class="px-6 py-4 font-bold">Status</th>
                        <th class="px-6 py-4 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php if ($queue): ?>
                        <?php foreach ($queue as $item): ?>
                            <tr class="hover:bg-teal-50/30 transition group" x-show="activeFilter === 'semua' || activeFilter === '<?= $item['filter'] ?>'">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-bold text-gray-900"><?= htmlspecialchars($item['time_label']) ?></p>
                                    <p class="text-[10px] font-mono font-bold text-teal-600 bg-teal-50 px-2 py-0.5 rounded inline-block mt-1">#<?= htmlspecialchars($item['code']) ?></p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-bold text-gray-800"><?= htmlspecialchars($item['patient_name']) ?></p>
                                    <p class="text-xs text-gray-500"><?= htmlspecialchars($item['patient_age']) ?></p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-gray-700"><?= htmlspecialchars($item['doctor_name']) ?></p>
                                    <p class="text-xs text-gray-500"><?= htmlspecialchars($item['specialization']) ?></p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold border <?= htmlspecialchars($item['status_badge']) ?>">
                                        <i class="fa-solid fa-circle text-[8px]"></i> <?= htmlspecialchars($item['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="process_prescription.php?id=<?= (int) $item['id'] ?>" class="inline-flex items-center justify-center px-4 py-2 bg-teal-600 text-white text-xs font-bold rounded-lg hover:bg-teal-700 transition shadow-md">
                                        Buka Resep <i class="fa-solid fa-arrow-right ml-2"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">Belum ada resep yang masuk di database.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
