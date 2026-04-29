<?php
require_once '../../includes/staff_portal_helpers.php';

$report = getAdminReportData($pdo);
$transactions = $report['transactions'];

include '../../includes/header_admin.php';
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="p-6 sm:p-8 space-y-8 max-w-[1400px] mx-auto" x-data='{
    searchQuery: "",
    filterKategori: "semua",
    transactions: <?= json_encode($transactions, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
    get filteredTransactions() {
        return this.transactions.filter(trx => {
            const matchSearch = trx.id.toLowerCase().includes(this.searchQuery.toLowerCase()) || trx.patient.toLowerCase().includes(this.searchQuery.toLowerCase());
            const matchCategory = this.filterKategori === "semua" || trx.category === this.filterKategori;
            return matchSearch && matchCategory;
        });
    },
    formatRupiah(number) {
        return new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0 }).format(number);
    },
    initChart() {
        new Chart(this.$refs.financeChart, {
            type: "bar",
            data: {
                labels: <?= json_encode($report['chart']['labels']) ?>,
                datasets: [
                    { label: "Layanan Medis", data: <?= json_encode($report['chart']['medical']) ?>, backgroundColor: "#4f46e5", borderRadius: 4 },
                    { label: "Penjualan Apotek", data: <?= json_encode($report['chart']['pharmacy']) ?>, backgroundColor: "#10b981", borderRadius: 4 }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: "top", labels: { usePointStyle: true, padding: 20 } } }, scales: { y: { beginAtZero: true, grid: { borderDash: [4,4] }, ticks: { callback: value => "Rp " + (value / 1000) + "k" } }, x: { grid: { display: false } } } }
        });
    }
}' x-init="initChart()">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Laporan Keuangan</h1>
            <p class="text-slate-500 mt-1">Laporan ini diambil dari konsultasi medis dan transaksi apotek yang sudah tersimpan.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-6 sm:p-8 shadow-lg text-white relative overflow-hidden">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Total Pendapatan</p>
                <h3 class="text-4xl font-black text-white mb-6"><?= htmlspecialchars(formatRupiah($report['total_medical'] + $report['total_pharmacy'])) ?></h3>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm"><div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 mb-3"><i class="fa-solid fa-stethoscope"></i></div><p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Poli Medis</p><p class="text-lg font-bold text-slate-800"><?= htmlspecialchars(formatRupiah($report['total_medical'])) ?></p></div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm"><div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600 mb-3"><i class="fa-solid fa-pills"></i></div><p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Apotek</p><p class="text-lg font-bold text-slate-800"><?= htmlspecialchars(formatRupiah($report['total_pharmacy'])) ?></p></div>
            </div>
        </div>
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 shadow-sm border border-slate-200 flex flex-col">
            <h2 class="text-lg font-bold text-slate-800 mb-1">Grafik Pendapatan Harian</h2>
            <p class="text-xs text-slate-500 mb-6">Perbandingan pemasukan dari layanan medis dan apotek.</p>
            <div class="flex-1 min-h-[250px] w-full relative"><canvas x-ref="financeChart"></canvas></div>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
        <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row gap-4 justify-between items-center bg-slate-50/50">
            <h2 class="text-lg font-bold text-slate-800 md:w-1/3"><i class="fa-solid fa-list-ul text-indigo-500 mr-2"></i> Rincian Arus Kas Masuk</h2>
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                <div class="relative w-full sm:w-64"><i class="fa-solid fa-magnifying-glass absolute left-4 top-2.5 text-slate-400 text-sm"></i><input x-model="searchQuery" type="text" placeholder="Cari Inv / Pasien..." class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 transition text-sm shadow-sm"></div>
                <select x-model="filterKategori" class="w-full sm:w-48 px-4 py-2 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 transition text-sm font-semibold text-slate-700 shadow-sm"><option value="semua">Semua Sumber</option><option value="Layanan Medis">Poli Layanan Medis</option><option value="Apotek">Farmasi / Apotek</option></select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead><tr class="bg-white text-slate-400 text-xs uppercase tracking-wider border-b border-slate-200"><th class="px-6 py-4 font-bold">Waktu & No. Transaksi</th><th class="px-6 py-4 font-bold">Sumber Dana</th><th class="px-6 py-4 font-bold">Keterangan / Pasien</th><th class="px-6 py-4 font-bold">Metode</th><th class="px-6 py-4 font-bold text-right">Nominal Masuk</th></tr></thead>
                <tbody class="divide-y divide-slate-50">
                    <template x-for="trx in filteredTransactions" :key="trx.id">
                        <tr class="hover:bg-indigo-50/30 transition group">
                            <td class="px-6 py-4"><p class="text-[10px] font-bold text-slate-500 mb-1" x-text="trx.date + ' • ' + trx.time"></p><span class="inline-block px-2 py-0.5 bg-slate-100 text-slate-700 font-mono font-bold text-xs rounded border border-slate-200" x-text="trx.id"></span></td>
                            <td class="px-6 py-4"><span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded text-[10px] font-bold" :class="trx.category === 'Layanan Medis' ? 'bg-indigo-50 text-indigo-700' : 'bg-emerald-50 text-emerald-700'"><i class="fa-solid" :class="trx.category === 'Layanan Medis' ? 'fa-stethoscope' : 'fa-pills'"></i><span x-text="trx.category"></span></span></td>
                            <td class="px-6 py-4"><p class="text-sm font-bold text-slate-900" x-text="trx.patient"></p><p class="text-xs text-slate-500 mt-0.5" x-text="trx.detail"></p></td>
                            <td class="px-6 py-4"><p class="text-xs font-bold text-slate-600 uppercase" x-text="trx.method"></p></td>
                            <td class="px-6 py-4 text-right"><p class="text-sm font-black text-slate-800" x-text="'+' + formatRupiah(trx.amount)"></p></td>
                        </tr>
                    </template>
                </tbody>
                <tfoot class="bg-slate-50 border-t-2 border-slate-200"><tr><td colspan="4" class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Total Nominal Filtered:</td><td class="px-6 py-4 text-right text-lg font-black text-indigo-700" x-text="formatRupiah(filteredTransactions.reduce((sum, t) => sum + t.amount, 0))"></td></tr></tfoot>
            </table>
        </div>
    </div>
</div>
