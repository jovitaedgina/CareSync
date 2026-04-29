<?php
require_once '../../includes/staff_portal_helpers.php';

$dashboard = getAdminDashboardData($pdo);
$stats = $dashboard['stats'];
$revenueChart = $dashboard['revenue_chart'];
$distribution = $dashboard['distribution'];
$topDoctors = $dashboard['top_doctors'];
$topItems = $dashboard['top_items'];

include '../../includes/header_admin.php';

function deltaBadgeClass(string $direction): string
{
    return match ($direction) {
        'up' => 'text-emerald-600 bg-emerald-50',
        'down' => 'text-rose-600 bg-rose-50',
        default => 'text-slate-500 bg-slate-100',
    };
}

function deltaIcon(string $direction): string
{
    return match ($direction) {
        'up' => 'fa-arrow-up',
        'down' => 'fa-arrow-down',
        default => 'fa-minus',
    };
}
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div
    class="p-6 sm:p-8 space-y-8 max-w-[1400px] mx-auto"
    x-data='{
        initCharts() {
            new Chart(this.$refs.revenueChart, {
                type: "line",
                data: {
                    labels: <?= json_encode($revenueChart['labels']) ?>,
                    datasets: [{
                        label: "Pendapatan (Rp)",
                        data: <?= json_encode($revenueChart['values']) ?>,
                        borderColor: "#4f46e5",
                        backgroundColor: "rgba(79, 70, 229, 0.1)",
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: "#ffffff",
                        pointBorderColor: "#4f46e5",
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

            new Chart(this.$refs.poliChart, {
                type: "doughnut",
                data: {
                    labels: <?= json_encode($distribution['labels']) ?>,
                    datasets: [{
                        data: <?= json_encode($distribution['values']) ?>,
                        backgroundColor: ["#4f46e5", "#0ea5e9", "#10b981", "#f59e0b", "#f43f5e"],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: "bottom", labels: { usePointStyle: true, padding: 20 } }
                    },
                    cutout: "75%"
                }
            });
        }
    }'
    x-init="initCharts()"
>

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Dashboard Analitik</h1>
            <p class="text-slate-500 mt-1">Seluruh ringkasan di halaman ini sekarang diambil langsung dari database CareSync.</p>
        </div>
        <div class="bg-white px-4 py-3 rounded-xl border border-slate-200 shadow-sm text-sm font-semibold text-slate-600">
            Update terakhir: <?= date('d M Y H:i') ?> WIB
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 text-xl"><i class="fa-solid fa-wallet"></i></div>
                <span class="text-xs font-bold px-2 py-1 rounded-full flex items-center gap-1 <?= deltaBadgeClass($stats['revenue']['delta']['direction']) ?>">
                    <i class="fa-solid <?= deltaIcon($stats['revenue']['delta']['direction']) ?>"></i> <?= htmlspecialchars($stats['revenue']['delta']['label']) ?>
                </span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Pendapatan Bulan Ini</p>
                <h3 class="text-3xl font-black text-slate-800"><?= htmlspecialchars($stats['revenue']['value']) ?></h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600 text-xl"><i class="fa-solid fa-user-plus"></i></div>
                <span class="text-xs font-bold px-2 py-1 rounded-full flex items-center gap-1 <?= deltaBadgeClass($stats['patients']['delta']['direction']) ?>">
                    <i class="fa-solid <?= deltaIcon($stats['patients']['delta']['direction']) ?>"></i> <?= htmlspecialchars($stats['patients']['delta']['label']) ?>
                </span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Pasien Baru Bulan Ini</p>
                <h3 class="text-3xl font-black text-slate-800"><?= htmlspecialchars($stats['patients']['value']) ?> <span class="text-sm font-medium text-slate-500">orang</span></h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-full bg-sky-50 flex items-center justify-center text-sky-600 text-xl"><i class="fa-solid fa-calendar-check"></i></div>
                <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded-full">Hari Ini</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Kunjungan Harian</p>
                <h3 class="text-3xl font-black text-slate-800"><?= htmlspecialchars($stats['visits_today']['value']) ?> <span class="text-sm font-medium text-slate-500">pasien</span></h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-full bg-rose-50 flex items-center justify-center text-rose-600 text-xl"><i class="fa-solid fa-pills"></i></div>
                <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded-full">Bulan Ini</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Item Apotek Terjual</p>
                <h3 class="text-3xl font-black text-slate-800"><?= htmlspecialchars($stats['prescription_items']['value']) ?> <span class="text-sm font-medium text-slate-500">item</span></h3>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex flex-col">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Tren Pendapatan 7 Hari Terakhir</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Berdasarkan transaksi tabel `Pemesanan`</p>
                </div>
            </div>
            <div class="flex-1 min-h-[300px] relative w-full">
                <canvas x-ref="revenueChart"></canvas>
            </div>
        </div>

        <div class="lg:col-span-1 bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex flex-col">
            <div class="mb-4">
                <h2 class="text-lg font-bold text-slate-800">Distribusi Kunjungan</h2>
                <p class="text-xs text-slate-500 mt-0.5">Berdasarkan spesialisasi dokter</p>
            </div>
            <div class="flex-1 relative w-full flex items-center justify-center min-h-[250px]">
                <canvas x-ref="poliChart"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none mt-[-30px]">
                    <span class="text-2xl font-black text-slate-800"><?= number_format((int) $distribution['total'], 0, ',', '.') ?></span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Total Kunjungan</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Performa Dokter</h2>
            </div>
            <div class="p-6 space-y-5">
                <?php if ($topDoctors): ?>
                    <?php foreach ($topDoctors as $index => $doctor): ?>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold"><?= htmlspecialchars(getInitials($doctor['nama'])) ?></div>
                                    <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-amber-400 border-2 border-white rounded-full flex items-center justify-center text-[8px] text-white font-bold"><?= $index + 1 ?></span>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900"><?= htmlspecialchars($doctor['nama']) ?></p>
                                    <p class="text-xs text-slate-500"><?= htmlspecialchars($doctor['specialization']) ?></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-slate-900"><?= number_format((int) $doctor['total_patients'], 0, ',', '.') ?></p>
                                <p class="text-[10px] text-slate-400">konsultasi/bln</p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-sm text-slate-500">Belum ada data konsultasi dokter bulan ini.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Top Selling Item (Apotek)</h2>
            </div>
            <div class="flex-1 overflow-x-auto p-2">
                <table class="w-full text-left">
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <?php if ($topItems): ?>
                            <?php foreach ($topItems as $item): ?>
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-4 py-3">
                                        <p class="font-bold text-slate-800"><?= htmlspecialchars($item['nama']) ?></p>
                                        <p class="text-[10px] text-slate-500">Dari transaksi bulan ini</p>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <p class="font-bold text-indigo-600"><?= number_format((int) $item['total_sold'], 0, ',', '.') ?> item</p>
                                        <p class="text-[10px] text-slate-400">terjual</p>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td class="px-4 py-4 text-slate-500" colspan="2">Belum ada transaksi obat bulan ini.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
