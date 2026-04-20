<?php
// pages/dokter/prescription_form.php
include '../../includes/header_dokter.php'; 
?>

<div class="p-6 sm:p-8 max-w-7xl mx-auto" x-data="{
    diagnosis: '',
    notes: '',
    
    // Database Dummy Obat (Nanti Farras bisa isi ini dari MySQL)
    medicineDB: [
        'Benzoyl Peroxide 5% Gel (Tube 15g)',
        'Clindamycin 1% Gel (Tube 10g)',
        'Doxycycline 100mg (Kapsul)',
        'Paracetamol 500mg (Tablet)',
        'Amoxicillin 500mg (Kapsul)',
        'Ibuprofen 400mg (Tablet)',
        'Cetirizine 10mg (Tablet)',
        'Salicylic Acid 2% (Botol 50ml)'
    ],

    // Array dinamis untuk menampung daftar obat
    medicines: [
        { id: 1, name: '', dosage: '', qty: '', instruction: '' }
    ],
    
    // Fungsi tambah baris obat
    addMedicine() {
        this.medicines.push({ id: Date.now(), name: '', dosage: '', qty: '', instruction: '' });
    },
    
    // Fungsi hapus baris obat
    removeMedicine(id) {
        if(this.medicines.length > 1) {
            this.medicines = this.medicines.filter(m => m.id !== id);
        }
    }
}">

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="consultation_room.php" class="text-gray-400 hover:text-blue-600 transition"><i class="fa-solid fa-arrow-left text-xl"></i></a>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Penerbitan Resep Digital</h1>
            </div>
            <p class="text-gray-500 ml-8">Isi diagnosis dan daftar obat untuk diserahkan ke Apoteker.</p>
        </div>
        <div class="text-right ml-8 sm:ml-0">
            <span class="block text-sm text-gray-500 font-medium">Nomor Resep</span>
            <span class="block text-lg font-mono font-bold text-blue-600">#RX-241103-001</span>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        
        <div class="xl:col-span-1 space-y-6">
            
            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-6 shadow-sm">
                <h3 class="text-sm font-bold text-blue-800 uppercase tracking-wider mb-4"><i class="fa-solid fa-user-injured mr-2"></i> Informasi Pasien</h3>
                <div class="flex items-center gap-4 mb-4">
                    <img src="https://ui-avatars.com/api/?name=Jovita+Edgina&background=0D8ABC&color=fff" class="w-14 h-14 rounded-full border-2 border-white shadow-sm">
                    <div>
                        <h4 class="font-bold text-gray-900 text-lg">Jovita Edgina</h4>
                        <p class="text-sm text-gray-600">Perempuan, 20 Tahun</p>
                    </div>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between border-b border-blue-200/50 pb-2">
                        <span class="text-gray-500">Alergi Obat</span>
                        <span class="font-bold text-red-500">Tidak Ada</span>
                    </div>
                    <div class="flex justify-between pt-1">
                        <span class="text-gray-500">Keluhan Awal</span>
                        <span class="font-medium text-gray-800 text-right">Jerawat meradang<br>di pipi kanan</span>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <div class="mb-5">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Diagnosis Medis <span class="text-red-500">*</span></label>
                    <input x-model="diagnosis" type="text" placeholder="Contoh: Acne Vulgaris Grade II" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm bg-gray-50">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Catatan untuk Pasien (Opsional)</label>
                    <textarea x-model="notes" rows="4" placeholder="Anjuran istirahat, pantangan makanan, dll..." class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm bg-gray-50 resize-none"></textarea>
                </div>
            </div>
        </div>

        <div class="xl:col-span-2">
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden flex flex-col h-full">
                
                <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-gray-800"><i class="fa-solid fa-pills text-blue-500 mr-2"></i> Rincian Resep Obat</h2>
                    <span class="text-xs font-bold bg-blue-100 text-blue-700 px-3 py-1 rounded-full" x-text="medicines.length + ' Obat Ditambahkan'"></span>
                </div>

                <div class="p-6 space-y-6 flex-1 bg-gray-50/30">
                    
                    <template x-for="(medicine, index) in medicines" :key="medicine.id">
                        <div class="relative bg-white border border-gray-200 rounded-xl p-5 shadow-sm hover:shadow-md transition group">
                            
                            <template x-if="medicines.length > 1">
                                <button @click="removeMedicine(medicine.id)" type="button" class="absolute -top-3 -right-3 w-8 h-8 bg-red-100 hover:bg-red-500 text-red-500 hover:text-white rounded-full flex items-center justify-center transition shadow-sm border border-white">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </template>

                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                
                                <div class="md:col-span-5" x-data="{ open: false, search: '' }">
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Nama Obat <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div @click="open = !open" @click.away="open = false" 
                                             class="w-full px-3 py-2.5 rounded-lg border border-gray-300 bg-white focus-within:ring-2 focus-within:ring-blue-500 transition cursor-pointer flex justify-between items-center shadow-sm min-h-[46px]">
                                            <span x-text="medicine.name || '-- Cari Nama Obat --'" :class="medicine.name ? 'text-gray-900 font-semibold' : 'text-gray-400'"></span>
                                            <i class="fa-solid fa-chevron-down text-gray-400 transition-transform text-xs" :class="open ? 'rotate-180' : ''"></i>
                                        </div>

                                        <div x-show="open" style="display: none;" 
                                             x-transition:enter="transition ease-out duration-100"
                                             x-transition:enter-start="transform opacity-0 scale-95"
                                             x-transition:enter-end="transform opacity-100 scale-100"
                                             class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl overflow-hidden">
                                            
                                            <div class="p-2 border-b border-gray-100 bg-gray-50">
                                                <div class="relative">
                                                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-gray-400 text-sm"></i>
                                                    <input x-model="search" type="text" placeholder="Ketik nama obat..." 
                                                           class="w-full pl-9 pr-3 py-1.5 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                            </div>
                                            
                                            <ul class="max-h-48 overflow-y-auto">
                                                <template x-for="med in medicineDB.filter(m => m.toLowerCase().includes(search.toLowerCase()))" :key="med">
                                                    <li @click="medicine.name = med; open = false; search = ''" 
                                                        class="px-4 py-2.5 hover:bg-blue-50 cursor-pointer text-sm text-gray-700 border-b border-gray-50 last:border-0 transition"
                                                        x-text="med"></li>
                                                </template>
                                                <template x-if="medicineDB.filter(m => m.toLowerCase().includes(search.toLowerCase())).length === 0">
                                                    <li class="px-4 py-3 text-sm text-gray-500 text-center italic"><i class="fa-solid fa-box-open mr-1"></i> Obat tidak ditemukan di apotek</li>
                                                </template>
                                            </ul>
                                        </div>
                                    </div>
                                    
                                    <input type="hidden" name="medicine_name[]" :value="medicine.name">
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Dosis <span class="text-red-500">*</span></label>
                                    <input x-model="medicine.dosage" type="text" placeholder="Cth: 2 x 1 Tablet" class="w-full px-3 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 transition shadow-sm">
                                </div>

                                <div class="md:col-span-4">
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Jumlah (Qty) <span class="text-red-500">*</span></label>
                                    <div class="flex items-center gap-2">
                                        <input x-model="medicine.qty" type="number" min="1" placeholder="0" class="w-20 px-3 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 transition text-center shadow-sm">
                                        <select class="flex-1 px-3 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 transition text-gray-600 text-sm shadow-sm bg-white">
                                            <option>Tube</option>
                                            <option>Strip</option>
                                            <option>Tablet</option>
                                            <option>Botol</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="md:col-span-12">
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Aturan Pakai / Keterangan</label>
                                    <input x-model="medicine.instruction" type="text" placeholder="Cth: Dioleskan tipis pada jerawat setelah cuci muka, sebelum tidur." class="w-full px-3 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 transition bg-yellow-50/30 shadow-sm">
                                </div>
                            </div>
                        </div>
                    </template>

                    <button @click="addMedicine()" type="button" class="w-full border-2 border-dashed border-blue-300 text-blue-600 hover:bg-blue-50 hover:border-blue-400 font-bold py-3 rounded-xl transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-plus"></i> Tambah Obat Lain
                    </button>

                </div>

                <div class="p-6 border-t border-gray-200 bg-white flex flex-col sm:flex-row justify-between items-center gap-4">
                    <p class="text-sm text-gray-500">
                        <i class="fa-solid fa-circle-info text-blue-500 mr-1"></i> Resep akan langsung dikirim ke sistem antrean <b>Apoteker</b>.
                    </p>
                    <div class="flex gap-3 w-full sm:w-auto">
                        <a href="consultation_room.php" class="flex-1 sm:flex-none text-center bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-bold py-2.5 px-6 rounded-lg transition">
                            Batal
                        </a>
                        <button class="flex-1 sm:flex-none text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-md transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-paper-plane"></i> Terbitkan Resep
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<?php
// include '../../includes/footer.php'; 
?>