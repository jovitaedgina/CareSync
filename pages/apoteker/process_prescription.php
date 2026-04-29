<?php
require_once '../../includes/staff_portal_helpers.php';

$prescriptionId = (int) ($_GET['id'] ?? 0);
$prescription = $prescriptionId > 0 ? getPrescriptionForPharmacist($pdo, $prescriptionId) : null;

include '../../includes/header_apoteker.php';
?>

<div class="p-6 sm:p-8 max-w-7xl mx-auto">
    <?php if (!$prescription): ?>
        <div class="bg-white border border-red-200 text-red-700 rounded-2xl p-6 shadow-sm">Resep tidak ditemukan.</div>
    <?php else: ?>
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="dashboard.php" class="w-10 h-10 bg-white border border-gray-200 rounded-full flex items-center justify-center text-gray-500 hover:text-teal-600 hover:border-teal-300 shadow-sm transition"><i class="fa-solid fa-arrow-left"></i></a>
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Validasi & Penyiapan Resep</h1>
                    <p class="text-sm font-bold text-teal-600 mt-1 uppercase tracking-wider">Nomor Resep: #<?= htmlspecialchars($prescription['code']) ?></p>
                </div>
            </div>
            <div class="bg-amber-50 border border-amber-200 px-4 py-2 rounded-xl flex items-center gap-3 shadow-sm">
                <span class="text-sm font-bold text-amber-700 uppercase tracking-wider"><?= htmlspecialchars($prescription['consultation_status']) ?></span>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <div class="xl:col-span-1 space-y-6">
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm relative overflow-hidden">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">Informasi Pasien</h3>
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-14 h-14 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xl"><?= htmlspecialchars(getInitials($prescription['patient']['name'])) ?></div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-lg"><?= htmlspecialchars($prescription['patient']['name']) ?></h4>
                            <p class="text-sm text-gray-500"><?= htmlspecialchars($prescription['patient']['gender']) ?>, <?= htmlspecialchars($prescription['patient']['age']) ?></p>
                        </div>
                    </div>
                    <div class="space-y-3 text-sm mt-6">
                        <div class="flex justify-between items-center bg-yellow-50 text-yellow-800 p-2 rounded-lg border border-yellow-100">
                            <span class="font-bold text-xs"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Alergi Obat</span>
                            <span class="font-bold text-right"><?= htmlspecialchars($prescription['patient']['allergy']) ?></span>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-500 mb-1">Diagnosis Dokter</span>
                            <span class="font-bold text-gray-800"><?= htmlspecialchars($prescription['diagnosis']) ?></span>
                        </div>
                        <?php if ($prescription['notes'] !== ''): ?>
                            <div>
                                <span class="block text-xs text-gray-500 mb-1">Catatan</span>
                                <span class="font-medium text-gray-700"><?= htmlspecialchars($prescription['notes']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6 shadow-inner">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 border-b border-gray-200 pb-2">Dokter Perujuk</h3>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-bold text-sm"><?= htmlspecialchars(getInitials($prescription['doctor']['name'])) ?></div>
                        <div>
                            <h4 class="font-bold text-gray-800"><?= htmlspecialchars($prescription['doctor']['name']) ?></h4>
                            <p class="text-xs text-gray-500"><?= htmlspecialchars($prescription['doctor']['specialization']) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="xl:col-span-2 space-y-6">
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="bg-teal-600 p-4 text-white flex justify-between items-center">
                        <h2 class="font-bold text-lg"><i class="fa-solid fa-list-check mr-2"></i> Daftar Permintaan Obat</h2>
                        <span class="text-xs font-bold bg-teal-800 px-3 py-1 rounded-full text-teal-100"><?= count($prescription['items']) ?> item</span>
                    </div>

                    <div class="divide-y divide-gray-100">
                        <?php foreach ($prescription['items'] as $med): ?>
                            <div class="p-5 flex flex-col sm:flex-row gap-6 items-start sm:items-center transition-colors hover:bg-gray-50">
                                <div class="flex-1">
                                    <div class="flex items-start gap-2">
                                        <span class="font-bold text-teal-600 mt-0.5 text-sm">R/</span>
                                        <div>
                                            <h4 class="text-base font-bold text-gray-900"><?= htmlspecialchars($med['name']) ?></h4>
                                            <div class="flex items-center gap-3 mt-1 text-sm">
                                                <span class="font-bold text-gray-700 bg-gray-100 px-2 py-0.5 rounded">Qty: <?= (int) $med['qty'] ?></span>
                                                <span class="text-xs text-gray-500">Stok tersedia: <?= (int) $med['stock'] ?></span>
                                            </div>
                                            <?php if ($med['instruction'] !== ''): ?>
                                                <p class="text-xs font-medium text-gray-500 mt-2 bg-yellow-50 border border-yellow-100 p-2 rounded inline-block">Aturan: <?= htmlspecialchars($med['instruction']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="w-full sm:w-48 shrink-0 bg-gray-50 p-3 rounded-xl border border-gray-200">
                                    <div class="flex justify-between items-center">
                                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Harga Satuan</span>
                                        <span class="text-sm font-bold text-gray-800"><?= htmlspecialchars(formatRupiah($med['price'])) ?></span>
                                    </div>
                                    <div class="flex justify-between items-center border-t border-gray-200 pt-2 mt-2">
                                        <span class="text-[10px] font-bold text-gray-500">Subtotal</span>
                                        <span class="text-sm font-bold text-teal-700"><?= htmlspecialchars(formatRupiah($med['subtotal'])) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-2xl shadow-lg p-6 text-white flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="w-full md:w-auto space-y-2">
                        <div class="flex justify-between md:justify-start gap-8 text-sm text-gray-300">
                            <span>Total Obat:</span>
                            <span class="font-mono"><?= htmlspecialchars(formatRupiah($prescription['subtotal'])) ?></span>
                        </div>
                        <div class="flex justify-between md:justify-start gap-8 text-xl font-bold text-teal-400 pt-1">
                            <span>Total Tagihan Saat Ini:</span>
                            <span><?= htmlspecialchars(formatRupiah($prescription['order_total'] > 0 ? $prescription['order_total'] : $prescription['subtotal'])) ?></span>
                        </div>
                    </div>

                    <div class="w-full md:w-auto flex flex-col items-end gap-2">
                        <?php if ($prescription['order_id'] > 0): ?>
                            <a href="<?= BASE_URL ?>/pages/tracking.php?order_id=<?= (int) $prescription['order_id'] ?>" class="w-full md:w-auto px-8 py-3.5 rounded-xl font-bold text-white bg-teal-500 hover:bg-teal-400 transition-all shadow-lg flex items-center justify-center gap-2 no-underline">
                                <i class="fa-solid fa-truck-fast"></i> Buka Tracking Pesanan
                            </a>
                        <?php else: ?>
                            <span class="text-xs text-amber-300 font-medium">Pesanan marketplace untuk resep ini belum terbentuk. Resep sudah tersimpan dan siap dibaca sistem apotek.</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
