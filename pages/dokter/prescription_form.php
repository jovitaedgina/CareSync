<?php
require_once '../../includes/staff_portal_helpers.php';

$doctorUserId = (int) (currentUser()['id'] ?? 0);
$consultationId = (int) ($_GET['consultation'] ?? $_POST['consultation_id'] ?? 0);
$errorMessage = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $items = [];
    $medicineIds = $_POST['medicine_id'] ?? [];
    $quantities = $_POST['qty'] ?? [];
    $instructions = $_POST['instruction'] ?? [];

    foreach ($medicineIds as $index => $medicineId) {
        if ((int) $medicineId <= 0) {
            continue;
        }

        $items[] = [
            'medicine_id' => (int) $medicineId,
            'qty' => (int) ($quantities[$index] ?? 0),
            'instruction' => (string) ($instructions[$index] ?? ''),
        ];
    }

    try {
        saveConsultationPrescription(
            $pdo,
            $consultationId,
            $doctorUserId,
            (string) ($_POST['diagnosis'] ?? ''),
            (string) ($_POST['notes'] ?? ''),
            $items
        );
        $successMessage = 'Diagnosis dan resep berhasil diterbitkan ke sistem apoteker.';
    } catch (Throwable $e) {
        $errorMessage = $e->getMessage();
    }
}

$clinical = $consultationId > 0 ? getConsultationClinicalData($pdo, $consultationId, $doctorUserId) : null;
$medicineCatalog = getMedicineCatalog($pdo);

if (!$clinical) {
    include '../../includes/header_dokter.php';
    echo '<div class="p-8 max-w-4xl mx-auto"><div class="bg-white border border-red-200 text-red-700 rounded-2xl p-6 shadow-sm">Data konsultasi tidak ditemukan atau Anda tidak memiliki akses.</div></div>';
    return;
}

$initialItems = $clinical['prescription']['items'];
if (!$initialItems) {
    $initialItems = [['medicine_id' => 0, 'qty' => 1, 'instruction' => '']];
}

$diagnosisEditable = !empty($clinical['diagnosis_editable']);

include '../../includes/header_dokter.php';
?>

<div class="p-6 sm:p-8 max-w-7xl mx-auto">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="consultation_room.php?consultation=<?= (int) $clinical['consultation_id'] ?>" class="text-gray-400 hover:text-blue-600 transition"><i class="fa-solid fa-arrow-left text-xl"></i></a>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Diagnosis & Penerbitan Resep</h1>
            </div>
            <p class="text-gray-500 ml-8">Data pasien, diagnosis, dan daftar obat di bawah ini tersimpan langsung ke tabel klinis.</p>
        </div>
        <div class="text-right ml-8 sm:ml-0">
            <span class="block text-sm text-gray-500 font-medium">Nomor Resep</span>
            <span class="block text-lg font-mono font-bold text-blue-600">#<?= htmlspecialchars($clinical['prescription']['id'] > 0 ? ('RX-' . str_pad((string) $clinical['prescription']['id'], 6, '0', STR_PAD_LEFT)) : 'AUTO') ?></span>
        </div>
    </div>

    <?php if ($errorMessage !== ''): ?>
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 rounded-2xl px-5 py-4 font-medium"><?= htmlspecialchars($errorMessage) ?></div>
    <?php endif; ?>
    <?php if ($successMessage !== ''): ?>
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl px-5 py-4 font-medium"><?= htmlspecialchars($successMessage) ?></div>
    <?php endif; ?>
    <?php if (!$diagnosisEditable): ?>
        <div class="mb-6 bg-amber-50 border border-amber-200 text-amber-700 rounded-2xl px-5 py-4 font-medium">
            <?= htmlspecialchars($clinical['diagnosis_lock_reason']) ?>
        </div>
    <?php endif; ?>

    <form method="post" class="grid grid-cols-1 xl:grid-cols-3 gap-8" x-data="prescriptionFormApp()">
        <input type="hidden" name="consultation_id" value="<?= (int) $clinical['consultation_id'] ?>">

        <div class="xl:col-span-1 space-y-6">
            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-6 shadow-sm">
                <h3 class="text-sm font-bold text-blue-800 uppercase tracking-wider mb-4"><i class="fa-solid fa-user-injured mr-2"></i> Informasi Pasien</h3>
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xl"><?= htmlspecialchars(getInitials($clinical['patient']['name'])) ?></div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-lg"><?= htmlspecialchars($clinical['patient']['name']) ?></h4>
                        <p class="text-sm text-gray-600"><?= htmlspecialchars($clinical['patient']['gender']) ?>, <?= htmlspecialchars($clinical['patient']['age']) ?></p>
                    </div>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between border-b border-blue-200/50 pb-2">
                        <span class="text-gray-500">Alergi Obat</span>
                        <span class="font-bold text-red-500 text-right"><?= htmlspecialchars($clinical['patient']['allergy']) ?></span>
                    </div>
                    <div class="flex justify-between pt-1 gap-3">
                        <span class="text-gray-500">Keluhan Awal</span>
                        <span class="font-medium text-gray-800 text-right"><?= htmlspecialchars($clinical['patient']['complaint']) ?></span>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <div class="mb-5">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Diagnosis Medis <span class="text-red-500">*</span></label>
                    <input name="diagnosis" value="<?= htmlspecialchars($clinical['diagnosis']['description']) ?>" type="text" placeholder="Contoh: Acne Vulgaris Grade II" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm bg-gray-50 disabled:bg-gray-100 disabled:text-gray-400" required <?= $diagnosisEditable ? '' : 'disabled' ?>>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Catatan untuk Pasien / Apoteker</label>
                    <textarea name="notes" rows="5" placeholder="Anjuran istirahat, pantangan makanan, atau catatan farmasi..." class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm bg-gray-50 resize-none disabled:bg-gray-100 disabled:text-gray-400" <?= $diagnosisEditable ? '' : 'disabled' ?>><?= htmlspecialchars($clinical['diagnosis']['notes'] ?: $clinical['prescription']['notes']) ?></textarea>
                </div>
            </div>
        </div>

        <div class="xl:col-span-2">
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden flex flex-col h-full">
                <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-gray-800"><i class="fa-solid fa-pills text-blue-500 mr-2"></i> Rincian Resep Obat</h2>
                    <button @click.prevent="addRow()" type="button" class="text-sm font-bold bg-blue-100 text-blue-700 px-3 py-2 rounded-xl hover:bg-blue-200 transition disabled:opacity-50 disabled:cursor-not-allowed" <?= $diagnosisEditable ? '' : 'disabled' ?>>
                        <i class="fa-solid fa-plus mr-1"></i> Tambah Obat
                    </button>
                </div>

                <div class="p-6 space-y-4 flex-1 bg-gray-50/30">
                    <template x-for="(medicine, index) in medicines" :key="medicine.rowId">
                        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                                <div class="md:col-span-5">
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Nama Obat</label>
                                    <select x-model="medicine.medicineId" :name="`medicine_id[${index}]`" class="w-full px-3 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 bg-white disabled:bg-gray-100 disabled:text-gray-400" required <?= $diagnosisEditable ? '' : 'disabled' ?>>
                                        <option value="0">Pilih obat dari database</option>
                                        <?php foreach ($medicineCatalog as $medicine): ?>
                                            <option value="<?= (int) $medicine['id'] ?>"><?= htmlspecialchars($medicine['name']) ?><?= $medicine['dosage'] !== '' ? ' - ' . htmlspecialchars($medicine['dosage']) : '' ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <p class="text-[11px] text-gray-400 mt-2" x-text="getMedicineMeta(medicine.medicineId)"></p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Qty</label>
                                    <input x-model="medicine.qty" :name="`qty[${index}]`" type="number" min="1" class="w-full px-3 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100 disabled:text-gray-400" required <?= $diagnosisEditable ? '' : 'disabled' ?>>
                                </div>
                                <div class="md:col-span-4">
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Aturan Pakai</label>
                                    <input x-model="medicine.instruction" :name="`instruction[${index}]`" type="text" placeholder="Contoh: 3x1 sesudah makan" class="w-full px-3 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100 disabled:text-gray-400" <?= $diagnosisEditable ? '' : 'disabled' ?>>
                                </div>
                                <div class="md:col-span-1 text-right">
                                    <button @click.prevent="removeRow(index)" type="button" class="w-10 h-10 rounded-xl bg-red-50 text-red-500 hover:bg-red-100 transition disabled:opacity-50 disabled:cursor-not-allowed" :disabled="medicines.length === 1" <?= $diagnosisEditable ? '' : 'disabled' ?>>
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="p-6 border-t border-gray-200 bg-white flex flex-col sm:flex-row justify-between items-center gap-4">
                    <p class="text-sm text-gray-500">
                        <i class="fa-solid fa-circle-info text-blue-500 mr-1"></i> Resep yang diterbitkan akan langsung muncul di dashboard apoteker.
                    </p>
                    <div class="flex gap-3 w-full sm:w-auto">
                        <a href="consultation_room.php?consultation=<?= (int) $clinical['consultation_id'] ?>" class="flex-1 sm:flex-none text-center bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-bold py-2.5 px-6 rounded-lg transition no-underline">
                            Kembali
                        </a>
                        <button type="submit" class="flex-1 sm:flex-none text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-md transition flex items-center justify-center gap-2 disabled:bg-gray-300 disabled:hover:bg-gray-300 disabled:cursor-not-allowed" <?= $diagnosisEditable ? '' : 'disabled' ?>>
                            <i class="fa-solid fa-paper-plane"></i> Simpan Diagnosis & Resep
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function prescriptionFormApp() {
    const catalog = <?= json_encode($medicineCatalog, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    return {
        catalog,
        medicines: <?= json_encode(array_map(static fn(array $item): array => [
            'rowId' => uniqid('row_', true),
            'medicineId' => (int) ($item['medicine_id'] ?? 0),
            'qty' => (int) ($item['qty'] ?? 1),
            'instruction' => (string) ($item['instruction'] ?? ''),
        ], $initialItems), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
        addRow() {
            this.medicines.push({ rowId: Date.now() + Math.random(), medicineId: 0, qty: 1, instruction: '' });
        },
        removeRow(index) {
            if (this.medicines.length === 1) return;
            this.medicines.splice(index, 1);
        },
        getMedicineMeta(id) {
            const item = this.catalog.find(medicine => Number(medicine.id) === Number(id));
            if (!item) return 'Pilih obat untuk melihat stok dan harga.';
            return `Stok ${item.stock} • ${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(item.price)}`;
        }
    };
}
</script>
