<?php
require_once '../../includes/staff_portal_helpers.php';

$transactions = getPharmacistTransactionHistory($pdo);
$totalRevenue = array_sum(array_column($transactions, 'total'));
$cancelledCount = count(array_filter($transactions, static fn(array $trx): bool => $trx['status'] === 'Dibatalkan'));

include '../../includes/header_apoteker.php';
?>

<div class="p-6 sm:p-8 max-w-7xl mx-auto" x-data='{ transactions: <?= json_encode($transactions, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>, searchQuery: "", filterStatus: "semua", isModalOpen: false, activeTrx: null,
    get filteredTransactions() {
        return this.transactions.filter(trx => {
            const matchSearch = trx.id.toLowerCase().includes(this.searchQuery.toLowerCase()) || trx.patient.toLowerCase().includes(this.searchQuery.toLowerCase());
            const matchStatus = this.filterStatus === "semua" || trx.status.toLowerCase() === this.filterStatus.toLowerCase();
            return matchSearch && matchStatus;
        });
    },
    formatRupiah(number) {
        return new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0 }).format(number);
    },
    openInvoice(trx) {
        this.activeTrx = trx;
        this.isModalOpen = true;
    }
}'>
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Riwayat Transaksi</h1>
            <p class="text-gray-500 mt-1">Riwayat ini diambil dari pemesanan, pembayaran, dan item obat yang tersimpan di database.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-teal-50 flex items-center justify-center text-teal-600 text-xl"><i class="fa-solid fa-receipt"></i></div>
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Transaksi</p>
                <h3 class="text-2xl font-bold text-gray-900"><?= number_format(count($transactions), 0, ',', '.') ?></h3>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center text-green-600 text-xl"><i class="fa-solid fa-money-bill-wave"></i></div>
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Estimasi Pendapatan</p>
                <h3 class="text-xl font-bold text-green-600"><?= htmlspecialchars(formatRupiah($totalRevenue)) ?></h3>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 border-l-4 border-l-red-500">
            <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-red-600 text-xl"><i class="fa-solid fa-ban"></i></div>
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Dibatalkan</p>
                <h3 class="text-2xl font-bold text-red-600"><?= number_format($cancelledCount, 0, ',', '.') ?></h3>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row gap-4 justify-between items-center bg-gray-50/50">
            <div class="relative w-full md:w-96">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-3 text-gray-400"></i>
                <input x-model="searchQuery" type="text" placeholder="Cari No. Invoice atau Pasien..." class="w-full pl-11 pr-4 py-2 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 transition text-sm shadow-sm">
            </div>
            <div class="flex items-center gap-3 w-full md:w-auto">
                <span class="text-sm font-bold text-gray-500"><i class="fa-solid fa-filter"></i> Status:</span>
                <select x-model="filterStatus" class="flex-1 md:w-48 px-4 py-2 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 transition text-sm font-semibold text-gray-700 shadow-sm">
                    <option value="semua">Semua Status</option>
                    <option value="selesai">Selesai</option>
                    <option value="diproses">Diproses</option>
                    <option value="dibatalkan">Dibatalkan</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white text-gray-400 text-xs uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-4 font-bold">No. Invoice & Tanggal</th>
                        <th class="px-6 py-4 font-bold">Pasien & Dokter</th>
                        <th class="px-6 py-4 font-bold">Total Pembayaran</th>
                        <th class="px-6 py-4 font-bold">Status</th>
                        <th class="px-6 py-4 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <template x-for="trx in filteredTransactions" :key="trx.id">
                        <tr class="hover:bg-teal-50/30 transition group">
                            <td class="px-6 py-4">
                                <p class="text-sm font-mono font-bold text-teal-700 bg-teal-50 px-2 py-0.5 rounded inline-block mb-1" x-text="trx.id"></p>
                                <p class="text-xs text-gray-500"><span x-text="trx.date"></span> • <span x-text="trx.time"></span></p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-gray-900" x-text="trx.patient"></p>
                                <p class="text-xs text-gray-500 mt-0.5"><i class="fa-solid fa-stethoscope text-gray-400 mr-1"></i> <span x-text="trx.doctor"></span></p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-gray-900" x-text="trx.status === 'Dibatalkan' ? '-' : formatRupiah(trx.total)"></p>
                                <p class="text-[10px] text-gray-500 uppercase tracking-wider font-semibold mt-0.5" x-text="trx.payment"></p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold"
                                      :class="trx.status === 'Selesai' ? 'bg-green-100 text-green-700 border border-green-200' : (trx.status === 'Diproses' ? 'bg-amber-100 text-amber-700 border border-amber-200' : 'bg-red-100 text-red-700 border border-red-200')">
                                    <i class="fa-solid" :class="trx.status === 'Selesai' ? 'fa-check' : (trx.status === 'Diproses' ? 'fa-spinner' : 'fa-xmark')"></i>
                                    <span x-text="trx.status"></span>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button @click="openInvoice(trx)" class="text-teal-600 bg-white border border-teal-200 hover:bg-teal-600 hover:text-white rounded-lg px-4 py-2 text-xs font-bold transition">
                                    <i class="fa-solid fa-eye mr-1"></i> Detail
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div x-show="isModalOpen" x-transition.opacity @click="isModalOpen = false" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
        <div x-show="isModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
            <template x-if="activeTrx">
                <div class="flex-1 overflow-y-auto">
                    <div class="bg-gray-50 p-8 border-b border-gray-200 text-center relative">
                        <button @click="isModalOpen = false" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:bg-gray-200 hover:text-gray-700 transition"><i class="fa-solid fa-xmark"></i></button>
                        <h2 class="text-xl font-bold text-gray-800 uppercase tracking-widest mb-1">Detail Transaksi</h2>
                        <p class="text-sm font-mono text-gray-500" x-text="activeTrx.id"></p>
                    </div>
                    <div class="p-8 grid grid-cols-2 gap-6 text-sm border-b border-gray-100">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Data Pasien</p>
                            <p class="font-bold text-gray-900 text-base" x-text="activeTrx.patient"></p>
                            <p class="text-gray-600 mt-1" x-text="'Dokter: ' + activeTrx.doctor"></p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Detail Waktu</p>
                            <p class="font-bold text-gray-800" x-text="activeTrx.date"></p>
                            <p class="text-gray-600 mt-1" x-text="activeTrx.time"></p>
                        </div>
                    </div>
                    <div class="p-8">
                        <table class="w-full text-left text-sm mb-6">
                            <thead>
                                <tr class="border-b border-gray-200 text-gray-500">
                                    <th class="pb-3 font-semibold">Deskripsi Item</th>
                                    <th class="pb-3 font-semibold text-center">Qty</th>
                                    <th class="pb-3 font-semibold text-right">Harga</th>
                                    <th class="pb-3 font-semibold text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="(item, idx) in activeTrx.items" :key="idx">
                                    <tr>
                                        <td class="py-3 font-medium text-gray-800" x-text="item.name"></td>
                                        <td class="py-3 text-center text-gray-600" x-text="item.qty"></td>
                                        <td class="py-3 text-right text-gray-600" x-text="formatRupiah(item.price)"></td>
                                        <td class="py-3 text-right font-bold text-gray-800" x-text="formatRupiah(item.subtotal)"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <div class="w-full max-w-xs ml-auto space-y-2 text-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>Total</span>
                                <span class="font-bold" x-text="formatRupiah(activeTrx.total)"></span>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 pt-1">
                                <span>Metode</span>
                                <span class="font-bold uppercase" x-text="activeTrx.payment"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
