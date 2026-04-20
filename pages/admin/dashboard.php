<?php
// pages/admin/dashboard.php
include '../../includes/header_admin.php'; 
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="p-6 sm:p-8 space-y-8 max-w-[1400px] mx-auto" x-data="{
    // Filter rentang waktu (dummy)
    timeRange: 'Bulan Ini',
    
    initCharts() {
        // Grafik Tren Pendapatan (Line Chart)
        new Chart(this.$refs.revenueChart, {
            type: 'line',
            data: {
                labels: ['1 Apr', '5 Apr', '10 Apr', '15 Apr', '20 Apr'],
                datasets: [{
                    label: 'Pendapatan (Juta Rp)',
                    data: [12, 19, 15, 25, 22],
                    borderColor: '#4f46e5', // Indigo-600
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#4f46e5',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [4, 4] } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Grafik Kunjungan Poli (Doughnut Chart)
        new Chart(this.$refs.poliChart, {
            type: 'doughnut',
            data: {
                labels: ['Poli Kulit', 'Poli Mata', 'Poli Umum', 'Poli Gigi'],
                datasets: [{
                    data: [45, 25, 20, 10],
                    backgroundColor: ['#4f46e5', '#0ea5e9', '#10b981', '#f59e0b'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
                },
                cutout: '75%'
            }
        });
    }
}" x-init="initCharts()">

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Dashboard Analitik</h1>
            <p class="text-slate-500 mt-1">Pantau performa klinik, tren pendapatan, dan efisiensi operasional.</p>
        </div>
        <div class="flex items-center gap-3 bg-white p-1.5 rounded-xl border border-slate-200 shadow-sm">
            <button @click="timeRange = 'Hari Ini'" :class="timeRange === 'Hari Ini' ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-500 hover:text-slate-700'" class="px-4 py-2 rounded-lg text-sm transition">Hari Ini</button>
            <button @click="timeRange = 'Bulan Ini'" :class="timeRange === 'Bulan Ini' ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-500 hover:text-slate-700'" class="px-4 py-2 rounded-lg text-sm transition">Bulan Ini</button>
            <button @click="timeRange = 'Tahun Ini'" :class="timeRange === 'Tahun Ini' ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-500 hover:text-slate-700'" class="px-4 py-2 rounded-lg text-sm transition">Tahun Ini</button>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 text-xl"><i class="fa-solid fa-wallet"></i></div>
                <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full flex items-center gap-1"><i class="fa-solid fa-arrow-up"></i> 12.5%</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Pendapatan</p>
                <h3 class="text-3xl font-black text-slate-800">Rp 45,2 Jt</h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600 text-xl"><i class="fa-solid fa-user-plus"></i></div>
                <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full flex items-center gap-1"><i class="fa-solid fa-arrow-up"></i> 4.2%</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Pasien Baru</p>
                <h3 class="text-3xl font-black text-slate-800">128 <span class="text-sm font-medium text-slate-500">orang</span></h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-full bg-sky-50 flex items-center justify-center text-sky-600 text-xl"><i class="fa-solid fa-calendar-check"></i></div>
                <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded-full">Stabil</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Kunjungan Harian</p>
                <h3 class="text-3xl font-black text-slate-800">42 <span class="text-sm font-medium text-slate-500">pasien/hari</span></h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-full bg-rose-50 flex items-center justify-center text-rose-600 text-xl"><i class="fa-solid fa-pills"></i></div>
                <span class="text-xs font-bold text-rose-600 bg-rose-50 px-2 py-1 rounded-full flex items-center gap-1"><i class="fa-solid fa-arrow-down"></i> 1.5%</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Resep Ditebus</p>
                <h3 class="text-3xl font-black text-slate-800">890 <span class="text-sm font-medium text-slate-500">item</span></h3>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex flex-col">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Tren Pendapatan Klinik</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Pendapatan kotor dari Layanan Medis & Apotek</p>
                </div>
                <button class="w-8 h-8 rounded-full border border-slate-200 flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:border-indigo-300 transition"><i class="fa-solid fa-ellipsis-vertical"></i></button>
            </div>
            <div class="flex-1 min-h-[300px] relative w-full">
                <canvas x-ref="revenueChart"></canvas>
            </div>
        </div>

        <div class="lg:col-span-1 bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex flex-col">
            <div class="mb-4">
                <h2 class="text-lg font-bold text-slate-800">Distribusi Pasien</h2>
                <p class="text-xs text-slate-500 mt-0.5">Berdasarkan Poli yang dituju</p>
            </div>
            <div class="flex-1 relative w-full flex items-center justify-center min-h-[250px]">
                <canvas x-ref="poliChart"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none mt-[-30px]">
                    <span class="text-2xl font-black text-slate-800">1,248</span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Total Pasien</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Performa Dokter (Top 3)</h2>
                <a href="reports.php" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">Lihat Lengkap</a>
            </div>
            <div class="p-6 space-y-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <img src="https://ui-avatars.com/api/?name=Susanti+W&background=4f46e5&color=fff" class="w-10 h-10 rounded-full object-cover">
                            <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-amber-400 border-2 border-white rounded-full flex items-center justify-center text-[8px] text-white font-bold">1</span>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-900">dr. Susanti Wulandari, Sp.KK</p>
                            <p class="text-xs text-slate-500">Poli Kulit & Kelamin</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-slate-900">342</p>
                        <p class="text-[10px] text-slate-400">Pasien/bln</p>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <img src="https://ui-avatars.com/api/?name=Budi+Santoso&background=0ea5e9&color=fff" class="w-10 h-10 rounded-full object-cover">
                            <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-slate-300 border-2 border-white rounded-full flex items-center justify-center text-[8px] text-white font-bold">2</span>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-900">dr. Budi Santoso, Sp.M</p>
                            <p class="text-xs text-slate-500">Poli Mata</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-slate-900">215</p>
                        <p class="text-[10px] text-slate-400">Pasien/bln</p>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <img src="https://ui-avatars.com/api/?name=Fenny+N&background=10b981&color=fff" class="w-10 h-10 rounded-full object-cover">
                            <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-amber-700 border-2 border-white rounded-full flex items-center justify-center text-[8px] text-white font-bold">3</span>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-900">dr. Fenny Nurmahdi</p>
                            <p class="text-xs text-slate-500">Poli Umum</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-slate-900">198</p>
                        <p class="text-[10px] text-slate-400">Pasien/bln</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Top Selling Item (Apotek)</h2>
            </div>
            <div class="flex-1 overflow-x-auto p-2">
                <table class="w-full text-left">
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3">
                                <p class="font-bold text-slate-800">Benzolac 5% Gel</p>
                                <p class="text-[10px] text-slate-500">Obat Resep</p>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <p class="font-bold text-indigo-600">85 Tube</p>
                                <p class="text-[10px] text-slate-400">Terjual mgg ini</p>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3">
                                <p class="font-bold text-slate-800">Paracetamol 500mg</p>
                                <p class="text-[10px] text-slate-500">Obat Bebas</p>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <p class="font-bold text-indigo-600">320 Tablet</p>
                                <p class="text-[10px] text-slate-400">Terjual mgg ini</p>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3">
                                <p class="font-bold text-slate-800">Cetirizine 10mg</p>
                                <p class="text-[10px] text-slate-500">Obat Bebas Terbatas</p>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <p class="font-bold text-indigo-600">145 Tablet</p>
                                <p class="text-[10px] text-slate-400">Terjual mgg ini</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php
// include '../../includes/footer.php'; 
?>