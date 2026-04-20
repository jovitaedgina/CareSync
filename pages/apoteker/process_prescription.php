<?php
// pages/apoteker/process_prescription.php
include '../../includes/header_apoteker.php'; 
?>

<div class="p-6 sm:p-8 max-w-7xl mx-auto" x-data="{
    medicines: [
        { id: 1, name: 'Clindamycin 300mg Caps', qty: 15, unit: 'Kapsul', rule: 'S 2 dd 1 caps (Habiskan)', price: 2500, checked: false },
        { id: 2, name: 'Benzolac 5% Gel Tube', qty: 1, unit: 'Tube', rule: 'S u.e (Oles tipis malam hari)', price: 45000, checked: false }
    ],
    serviceFee: 10000,
    
    // Fungsi hitung subtotal harga obat
    get subtotal() {
        return this.medicines.reduce((sum, item) => sum + (item.price * item.qty), 0);
    },
    
    // Fungsi hitung grand total
    get total() {
        return this.subtotal + this.serviceFee;
    },
    
    // Format angka ke Rupiah
    formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
    },

    // Cek apakah semua obat sudah dicentang/disiapkan
    get isReadyToSubmit() {
        return this.medicines.every(m => m.checked);
    }
}">

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="dashboard.php" class="w-10 h-10 bg-white border border-gray-200 rounded-full flex items-center justify-center text-gray-500 hover:text-teal-600 hover:border-teal-300 shadow-sm transition"><i class="fa-solid fa-arrow-left"></i></a>
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Validasi & Penyiapan Resep</h1>
                <p class="text-sm font-bold text-teal-600 mt-1 uppercase tracking-wider">Nomor Resep: #RX-241103-001</p>
            </div>
        </div>
        <div class="bg-red-50 border border-red-200 px-4 py-2 rounded-xl flex items-center gap-3 shadow-sm">
            <span class="relative flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
            </span>
            <span class="text-sm font-bold text-red-700 uppercase tracking-wider">Menunggu Validasi</span>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

        <div class="xl:col-span-1 space-y-6">
            
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-teal-50 rounded-bl-full -z-10"></div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">Informasi Pasien</h3>
                
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xl">JE</div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-lg">Jovita Edgina</h4>
                        <p class="text-sm text-gray-500">Perempuan, 20 Tahun</p>
                    </div>
                </div>

                <div class="space-y-3 text-sm mt-6">
                    <div class="flex justify-between items-center bg-yellow-50 text-yellow-800 p-2 rounded-lg border border-yellow-100">
                        <span class="font-bold text-xs"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Alergi Obat</span>
                        <span class="font-bold">Aspirin</span>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-500 mb-1">Diagnosis Dokter</span>
                        <span class="font-bold text-gray-800">Acne Vulgaris Grade II</span>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6 shadow-inner">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 border-b border-gray-200 pb-2">Dokter Perujuk</h3>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-bold text-sm">SW</div>
                    <div>
                        <h4 class="font-bold text-gray-800">dr. Susanti Wulandari, Sp.KK</h4>
                        <p class="text-xs text-gray-500">Poli Kulit & Kelamin</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="xl:col-span-2 space-y-6">
            
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="bg-teal-600 p-4 text-white flex justify-between items-center">
                    <h2 class="font-bold text-lg"><i class="fa-solid fa-list-check mr-2"></i> Daftar Permintaan Obat</h2>
                    <span class="text-xs font-bold bg-teal-800 px-3 py-1 rounded-full text-teal-100">Ceklis obat yang sudah disiapkan</span>
                </div>

                <div class="divide-y divide-gray-100">
                    <template x-for="(med, index) in medicines" :key="med.id">
                        <div class="p-5 flex flex-col sm:flex-row gap-6 items-start sm:items-center transition-colors" :class="med.checked ? 'bg-teal-50/30' : 'hover:bg-gray-50'">
                            
                            <div class="shrink-0 pt-1 sm:pt-0">
                                <input type="checkbox" x-model="med.checked" class="w-6 h-6 text-teal-600 bg-gray-100 border-gray-300 rounded focus:ring-teal-500 cursor-pointer transition">
                            </div>

                            <div class="flex-1">
                                <div class="flex items-start gap-2">
                                    <span class="font-bold text-teal-600 mt-0.5 text-sm">R/</span>
                                    <div>
                                        <h4 class="text-base font-bold text-gray-900" :class="med.checked ? 'line-through text-gray-400' : ''" x-text="med.name"></h4>
                                        <div class="flex items-center gap-3 mt-1 text-sm">
                                            <span class="font-bold text-gray-700 bg-gray-100 px-2 py-0.5 rounded" x-text="'Qty: ' + med.qty + ' ' + med.unit"></span>
                                        </div>
                                        <p class="text-xs font-medium text-gray-500 mt-2 bg-yellow-50 border border-yellow-100 p-2 rounded inline-block" x-text="'Aturan: ' + med.rule"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="w-full sm:w-48 shrink-0 bg-gray-50 p-3 rounded-xl border border-gray-200">
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Harga Satuan (Rp)</label>
                                <input type="number" x-model.number="med.price" class="w-full px-3 py-2 text-sm font-bold text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 mb-2">
                                <div class="flex justify-between items-center border-t border-gray-200 pt-2">
                                    <span class="text-[10px] font-bold text-gray-500">Subtotal:</span>
                                    <span class="text-sm font-bold text-teal-700" x-text="formatRupiah(med.price * med.qty)"></span>
                                </div>
                            </div>

                        </div>
                    </template>
                </div>
            </div>

            <div class="bg-gray-800 rounded-2xl shadow-lg p-6 text-white flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="w-full md:w-auto space-y-2">
                    <div class="flex justify-between md:justify-start gap-8 text-sm text-gray-300">
                        <span>Total Obat:</span>
                        <span class="font-mono" x-text="formatRupiah(subtotal)"></span>
                    </div>
                    <div class="flex justify-between md:justify-start gap-8 text-sm text-gray-300 border-b border-gray-600 pb-2">
                        <span>Biaya Racik/Layanan:</span>
                        <span class="font-mono" x-text="formatRupiah(serviceFee)"></span>
                    </div>
                    <div class="flex justify-between md:justify-start gap-8 text-xl font-bold text-teal-400 pt-1">
                        <span>Total Tagihan:</span>
                        <span x-text="formatRupiah(total)"></span>
                    </div>
                </div>

                <div class="w-full md:w-auto flex flex-col items-end gap-2">
                    <button :disabled="!isReadyToSubmit" :class="isReadyToSubmit ? 'bg-teal-500 hover:bg-teal-400' : 'bg-gray-600 cursor-not-allowed text-gray-400'" class="w-full md:w-auto px-8 py-3.5 rounded-xl font-bold text-white transition-all shadow-lg flex items-center justify-center gap-2">
                        <i class="fa-solid fa-check-double"></i> Selesai & Kirim Tagihan
                    </button>
                    <p x-show="!isReadyToSubmit" class="text-xs text-red-400 font-medium">*Centang semua obat terlebih dahulu</p>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
// include '../../includes/footer.php'; 
?>