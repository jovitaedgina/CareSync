<?php
// pages/admin/reports.php
include '../../includes/header_admin.php'; 
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="p-6 sm:p-8 space-y-8 max-w-[1400px] mx-auto" x-data="{
    searchQuery: '',
    filterKategori: 'semua',
    filterBulan: 'April 2026',

    // Database Dummy Laporan Keuangan
    transactions: [
        { id: 'INV-MED-2604-01', date: '20 Apr 2026', time: '10:30 WIB', patient: 'Jovita Edgina', category: 'Layanan Medis', detail: 'Konsultasi dr. Susanti (Sp.KK)', method: 'QRIS', amount: 150000 },
        { id: 'INV-APT-2604-01', date: '20 Apr 2026', time: '10:45 WIB', patient: 'Jovita Edgina', category: 'Apotek', detail: 'Tebus Resep #RX-241103', method: 'QRIS', amount: 82500 },
        { id: 'INV-MED-2604-02', date: '20 Apr 2026', time: '11:00 WIB', patient: 'Farras Faishal', category: 'Layanan Medis', detail: 'Konsultasi dr. Susanti (Sp.KK)', method: 'Tunai', amount: 150000 },
        { id: 'INV-APT-2604-02', date: '20 Apr 2026', time: '11:15 WIB', patient: 'Farras Faishal', category: 'Apotek', detail: 'Tebus Resep #RX-241104', method: 'Tunai', amount: 35000 },
        { id: 'INV-MED-2604-03', date: '19 Apr 2026', time: '09:00 WIB', patient: 'Budi Santoso', category: 'Layanan Medis', detail: 'Konsultasi dr. Budi (Sp.M)', method: 'Debit BCA', amount: 200000 },
        { id: 'INV-APT-2604-03', date: '19 Apr 2026', time: '09:20 WIB', patient: 'Budi Santoso', category: 'Apotek', detail: 'Pembelian Obat Bebas', method: 'Tunai', amount: 45000 },
        { id: 'INV-MED-2604-04', date: '18 Apr 2026', time: '14:00 WIB', patient: 'Zahra Ramadhani', category: 'Layanan Medis', detail: 'Konsultasi dr. Fenny (Umum)', method: 'QRIS', amount: 100000 },
        { id: 'INV-APT-2604-04', date: '18 Apr 2026', time: '14:30 WIB', patient: 'Zahra Ramadhani', category: 'Apotek', detail: 'Tebus Resep #RX-241098', method: 'QRIS', amount: 125000 }
    ],

    get filteredTransactions() {
        return this.transactions.filter(trx => {
            const matchSearch = trx.id.toLowerCase().includes(this.searchQuery.toLowerCase()) || trx.patient.toLowerCase().includes(this.searchQuery.toLowerCase());
            const matchCategory = this.filterKategori === 'semua' || trx.category === this.filterKategori;
            return matchSearch && matchCategory;
        });
    },

    formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
    },

    get totalMedis() {
        return this.transactions.filter(t => t.category === 'Layanan Medis').reduce((sum, t) => sum + t.amount, 0);
    },

    get totalApotek() {
        return this.transactions.filter(t => t.category === 'Apotek').reduce((sum, t) => sum + t.amount, 0);
    },

    initChart() {
        new Chart(this.$refs.financeChart, {
            type: 'bar',
            data: {
                labels: ['16 Apr', '17 Apr', '18 Apr', '19 Apr', '20 Apr'],
                datasets: [
                    {
                        label: 'Layanan Medis',
                        data: [500000, 750000, 100000, 200000, 300000],
                        backgroundColor: '#4f46e5', // Indigo
                        borderRadius: 4
                    },
                    {
                        label: 'Penjualan Apotek',
                        data: [350000, 420000, 125000, 45000, 117500],
                        backgroundColor: '#10b981', // Emerald
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { usePointStyle: true, padding: 20 } }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { borderDash: [4, 4] },
                        ticks: { callback: function(value) { return 'Rp ' + (value/1000) + 'k'; } }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    }
}" x-init="initChart()">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Laporan Keuangan</h1>
            <p class="text-slate-500 mt-1">Pantau arus kas masuk dari layanan konsultasi medis dan penjualan apotek.</p>
        </div>
        <div class="flex items-center gap-3">
            <select x-model="filterBulan" class="px-4 py-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 transition text-sm font-bold text-slate-700 shadow-sm outline-none">
                <option>April 2026</option>
                <option>Maret 2026</option>
                <option>Februari 2026</option>
            </select>
            <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-5 rounded-xl shadow-md transition flex items-center gap-2">
                <i class="fa-solid fa-file-pdf"></i> Unduh PDF
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-1 space-y-6">
            
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-6 sm:p-8 shadow-lg text-white relative overflow-hidden">
                <i class="fa-solid fa-wallet absolute -right-6 -bottom-6 text-8xl text-white/5 pointer-events-none"></i>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Total Pendapatan (April)</p>
                <h3 class="text-4xl font-black text-white mb-6" x-text="formatRupiah(totalMedis + totalApotek)"></h3>
                <div class="inline-flex items-center gap-2 bg-emerald-500/20 border border-emerald-500/30 px-3 py-1.5 rounded-lg text-emerald-400 text-xs font-bold backdrop-blur-sm">
                    <i class="fa-solid fa-arrow-trend-up"></i> Naik 15% dari bulan lalu
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                    <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 mb-3"><i class="fa-solid fa-stethoscope"></i></div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Poli Medis</p>
                    <p class="text-lg font-bold text-slate-800" x-text="formatRupiah(totalMedis)"></p>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                    <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600 mb-3"><i class="fa-solid fa-pills"></i></div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Apotek</p>
                    <p class="text-lg font-bold text-slate-800" x-text="formatRupiah(totalApotek)"></p>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 bg-white rounded-3xl p-6 shadow-sm border border-slate-200 flex flex-col">
            <h2 class="text-lg font-bold text-slate-800 mb-1">Grafik Pendapatan Harian</h2>
            <p class="text-xs text-slate-500 mb-6">Perbandingan proporsi pemasukan dari Poli Medis dan Farmasi.</p>
            <div class="flex-1 min-h-[250px] w-full relative">
                <canvas x-ref="financeChart"></canvas>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
        
        <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row gap-4 justify-between items-center bg-slate-50/50">
            <h2 class="text-lg font-bold text-slate-800 md:w-1/3"><i class="fa-solid fa-list-ul text-indigo-500 mr-2"></i> Rincian Arus Kas Masuk</h2>
            
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                <div class="relative w-full sm:w-64">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-2.5 text-slate-400 text-sm"></i>
                    <input x-model="searchQuery" type="text" placeholder="Cari Inv / Pasien..." class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 transition text-sm shadow-sm">
                </div>
                <select x-model="filterKategori" class="w-full sm:w-48 px-4 py-2 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 transition text-sm font-semibold text-slate-700 shadow-sm">
                    <option value="semua">Semua Sumber</option>
                    <option value="Layanan Medis">Poli Layanan Medis</option>
                    <option value="Apotek">Farmasi / Apotek</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white text-slate-400 text-xs uppercase tracking-wider border-b border-slate-200">
                        <th class="px-6 py-4 font-bold">Waktu & No. Transaksi</th>
                        <th class="px-6 py-4 font-bold">Sumber Dana</th>
                        <th class="px-6 py-4 font-bold">Keterangan / Pasien</th>
                        <th class="px-6 py-4 font-bold">Metode</th>
                        <th class="px-6 py-4 font-bold text-right">Nominal Masuk</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <template x-for="trx in filteredTransactions" :key="trx.id">
                        <tr class="hover:bg-indigo-50/30 transition group">
                            
                            <td class="px-6 py-4">
                                <p class="text-[10px] font-bold text-slate-500 mb-1" x-text="trx.date + ' • ' + trx.time"></p>
                                <span class="inline-block px-2 py-0.5 bg-slate-100 text-slate-700 font-mono font-bold text-xs rounded border border-slate-200" x-text="trx.id"></span>
                            </td>
                            
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded text-[10px] font-bold"
                                      :class="trx.category === 'Layanan Medis' ? 'bg-indigo-50 text-indigo-700' : 'bg-emerald-50 text-emerald-700'">
                                    <i class="fa-solid" :class="trx.category === 'Layanan Medis' ? 'fa-stethoscope' : 'fa-pills'"></i>
                                    <span x-text="trx.category"></span>
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-slate-900" x-text="trx.patient"></p>
                                <p class="text-xs text-slate-500 mt-0.5" x-text="trx.detail"></p>
                            </td>

                            <td class="px-6 py-4">
                                <p class="text-xs font-bold text-slate-600 uppercase" x-text="trx.method"></p>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <p class="text-sm font-black text-slate-800" x-text="'+' + formatRupiah(trx.amount)"></p>
                            </td>
                        </tr>
                    </template>

                    <template x-if="filteredTransactions.length === 0">
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <i class="fa-solid fa-receipt text-4xl text-slate-300 mb-3 block"></i>
                                <p class="text-sm font-medium">Data transaksi tidak ditemukan pada periode/filter ini.</p>
                            </td>
                        </tr>
                    </template>
                </tbody>
                <tfoot class="bg-slate-50 border-t-2 border-slate-200">
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Total Nominal Filtered:</td>
                        <td class="px-6 py-4 text-right text-lg font-black text-indigo-700" x-text="formatRupiah(filteredTransactions.reduce((sum, t) => sum + t.amount, 0))"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>

<?php
// include '../../includes/footer.php'; 
?>