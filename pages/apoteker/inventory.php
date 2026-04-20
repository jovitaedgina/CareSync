<?php
// pages/apoteker/inventory.php
include '../../includes/header_apoteker.php'; 
?>

<div class="p-6 sm:p-8 max-w-7xl mx-auto" x-data="{
    searchQuery: '',
    filterCategory: 'semua',
    isModalOpen: false,
    modalTitle: 'Tambah Obat Baru',
    
    // Form State
    form: { id: '', name: '', category: '', stock: 0, unit: 'Tablet', price: 0, minStock: 10 },

    // Database Dummy Inventaris (15 Item Realistis)
    inventory: [
        { id: 'OBT-001', name: 'Paracetamol 500mg', category: 'Obat Bebas', stock: 150, unit: 'Tablet', price: 1500, minStock: 20 },
        { id: 'OBT-002', name: 'Amoxicillin 500mg', category: 'Obat Resep (Keras)', stock: 8, unit: 'Kapsul', price: 3000, minStock: 15 },
        { id: 'OBT-003', name: 'Benzolac 5% Gel', category: 'Obat Resep', stock: 5, unit: 'Tube', price: 45000, minStock: 10 },
        { id: 'OBT-004', name: 'Cetirizine 10mg', category: 'Obat Bebas Terbatas', stock: 80, unit: 'Tablet', price: 2500, minStock: 20 },
        { id: 'OBT-005', name: 'Clindamycin 300mg', category: 'Obat Resep (Keras)', stock: 120, unit: 'Kapsul', price: 2500, minStock: 30 },
        { id: 'OBT-006', name: 'Ibuprofen 400mg', category: 'Obat Bebas Terbatas', stock: 45, unit: 'Tablet', price: 2000, minStock: 30 },
        { id: 'OBT-007', name: 'Promag Hydrotalcite', category: 'Obat Bebas', stock: 200, unit: 'Tablet', price: 1000, minStock: 50 },
        { id: 'OBT-008', name: 'Omeprazole 20mg', category: 'Obat Resep (Keras)', stock: 25, unit: 'Kapsul', price: 1500, minStock: 30 },
        { id: 'OBT-009', name: 'OBH Combi Plus 60ml', category: 'Obat Bebas Terbatas', stock: 40, unit: 'Botol', price: 18500, minStock: 15 },
        { id: 'OBT-010', name: 'Betadine Sol 15ml', category: 'Obat Bebas', stock: 60, unit: 'Botol', price: 12000, minStock: 20 },
        { id: 'OBT-011', name: 'Cefadroxil 500mg', category: 'Obat Resep (Keras)', stock: 10, unit: 'Kapsul', price: 4000, minStock: 25 },
        { id: 'OBT-012', name: 'Vitamin C IPI 50mg', category: 'Obat Bebas', stock: 85, unit: 'Botol', price: 6000, minStock: 20 },
        { id: 'OBT-013', name: 'Dexamethasone 0.5mg', category: 'Obat Resep (Keras)', stock: 300, unit: 'Tablet', price: 500, minStock: 50 },
        { id: 'OBT-014', name: 'Acyclovir 5% Cream', category: 'Obat Resep (Keras)', stock: 12, unit: 'Tube', price: 15000, minStock: 15 },
        { id: 'OBT-015', name: 'Sanmol Syrup 60ml', category: 'Obat Bebas', stock: 35, unit: 'Botol', price: 22000, minStock: 10 }
    ],

    get filteredInventory() {
        return this.inventory.filter(item => {
            const matchSearch = item.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || item.id.toLowerCase().includes(this.searchQuery.toLowerCase());
            const matchCategory = this.filterCategory === 'semua' || item.category.includes(this.filterCategory);
            return matchSearch && matchCategory;
        });
    },

    formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
    },

    openAddModal() {
        // Otomatis bikin ID OBT-016 dst..
        const nextId = 'OBT-' + String(this.inventory.length + 1).padStart(3, '0');
        this.modalTitle = 'Tambah Obat Baru';
        this.form = { id: nextId, name: '', category: 'Obat Bebas', stock: 0, unit: 'Tablet', price: 0, minStock: 10 };
        this.isModalOpen = true;
    },

    openEditModal(item) {
        this.modalTitle = 'Edit Data Obat';
        this.form = { ...item }; 
        this.isModalOpen = true;
    }
}">

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Manajemen Stok Obat</h1>
            <p class="text-gray-500 mt-1">Pantau ketersediaan fisik obat dan atur harga jual.</p>
        </div>
        <button @click="openAddModal()" class="bg-teal-600 hover:bg-teal-700 text-white font-bold py-2.5 px-5 rounded-lg shadow-md transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Tambah Obat Baru
        </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-teal-50 flex items-center justify-center text-teal-600 text-xl"><i class="fa-solid fa-boxes-stacked"></i></div>
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Item Obat</p>
                <h3 class="text-2xl font-bold text-gray-900" x-text="inventory.length"></h3>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 border-l-4 border-l-red-500">
            <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-red-600 text-xl"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Stok Kritis / Habis</p>
                <h3 class="text-2xl font-bold text-red-600" x-text="inventory.filter(i => i.stock <= i.minStock).length"></h3>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 border-l-4 border-l-blue-500">
            <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 text-xl"><i class="fa-solid fa-file-prescription"></i></div>
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Obat Resep (Keras)</p>
                <h3 class="text-2xl font-bold text-gray-900" x-text="inventory.filter(i => i.category.includes('Resep')).length"></h3>
            </div>
        </div>
    </div>

    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200 mb-6 flex flex-col md:flex-row gap-4 justify-between items-center">
        <div class="relative w-full md:w-96">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-3 text-gray-400"></i>
            <input x-model="searchQuery" type="text" placeholder="Cari nama atau kode obat..." class="w-full pl-11 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 transition text-sm shadow-sm">
        </div>
        <div class="flex items-center gap-3 w-full md:w-auto">
            <span class="text-sm font-bold text-gray-500"><i class="fa-solid fa-filter"></i> Kategori:</span>
            <select x-model="filterCategory" class="flex-1 md:w-48 px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 transition text-sm font-semibold text-gray-700 shadow-sm">
                <option value="semua">Semua Kategori</option>
                <option value="Bebas">Obat Bebas</option>
                <option value="Resep">Obat Resep / Keras</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        
        <template x-for="item in filteredInventory" :key="item.id">
            <div class="bg-white rounded-2xl transition-all duration-200 hover:-translate-y-1 hover:shadow-xl relative overflow-hidden flex flex-col border"
                 :class="item.stock <= item.minStock ? 'border-red-300 shadow-[0_0_15px_rgba(239,68,68,0.15)]' : 'border-gray-200 shadow-sm'">
                
                <div class="p-4 border-b flex justify-between items-start" :class="item.stock <= item.minStock ? 'bg-red-50/50 border-red-100' : 'bg-gray-50/50 border-gray-100'">
                    <span class="text-[10px] font-bold px-2 py-1 rounded truncate max-w-[80%]"
                          :class="item.category.includes('Resep') ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700'"
                          x-text="item.category"></span>
                    <button @click="openEditModal(item)" class="text-gray-400 hover:text-teal-600 transition shrink-0" title="Edit Obat">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                </div>

                <div class="p-6 flex-1 flex flex-col justify-center text-center relative">
                    <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center text-2xl mb-4 transition-colors"
                         :class="item.stock <= item.minStock ? 'bg-red-100 text-red-500' : 'bg-teal-50 text-teal-500'">
                        <i class="fa-solid fa-capsules" x-show="item.unit === 'Kapsul' || item.unit === 'Tablet'"></i>
                        <i class="fa-solid fa-prescription-bottle" x-show="item.unit === 'Botol'"></i>
                        <i class="fa-solid fa-eye-dropper" x-show="item.unit === 'Tube'"></i>
                    </div>
                    
                    <h3 class="text-base font-extrabold text-gray-900 mb-1 leading-tight" x-text="item.name"></h3>
                    <p class="text-[10px] font-mono text-gray-400 mb-3" x-text="item.id"></p>
                    
                    <div>
                        <p class="text-xl font-black" :class="item.stock <= item.minStock ? 'text-red-600' : 'text-teal-600'" x-text="formatRupiah(item.price)"></p>
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider font-bold" x-text="'PER ' + item.unit"></p>
                    </div>
                </div>

                <div class="p-4 border-t" :class="item.stock <= item.minStock ? 'bg-red-50 border-red-200' : 'bg-white border-gray-100'">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs font-bold text-gray-500">Sisa Stok:</span>
                        <span class="text-sm font-black" :class="item.stock <= item.minStock ? 'text-red-600' : 'text-gray-900'" x-text="item.stock + ' ' + item.unit"></span>
                    </div>
                    
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2 overflow-hidden">
                        <div class="h-1.5 rounded-full transition-all duration-500" 
                             :class="item.stock <= item.minStock ? 'bg-red-500' : (item.stock > item.minStock * 2 ? 'bg-teal-500' : 'bg-yellow-400')"
                             :style="`width: ${Math.min((item.stock / (item.minStock * 3)) * 100, 100)}%`"></div>
                    </div>

                    <template x-if="item.stock <= item.minStock">
                         <div class="text-[10px] font-bold text-red-600 mt-2 flex items-center justify-center gap-1.5 animate-pulse bg-red-100/50 py-1 rounded">
                             <i class="fa-solid fa-triangle-exclamation"></i> Stok Kritis (Minimal: <span x-text="item.minStock"></span>)
                         </div>
                    </template>
                </div>
            </div>
        </template>

        <template x-if="filteredInventory.length === 0">
            <div class="col-span-full py-16 text-center text-gray-500 bg-white rounded-2xl border border-gray-200 border-dashed">
                <i class="fa-solid fa-box-open text-5xl text-gray-300 mb-4 block"></i>
                <p class="text-base font-bold text-gray-600">Obat tidak ditemukan.</p>
                <p class="text-sm mt-1">Coba sesuaikan kata kunci atau filter kategori.</p>
            </div>
        </template>

    </div>

    <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div x-show="isModalOpen" x-transition.opacity @click="isModalOpen = false" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"></div>

        <div x-show="isModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden border border-gray-100">
            
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-900" x-text="modalTitle"></h3>
                <button @click="isModalOpen = false" class="text-gray-400 hover:text-red-500 transition"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Obat</label>
                        <input x-model="form.name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none">
                    </div>
                    
                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kategori</label>
                        <select x-model="form.category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none">
                            <option>Obat Bebas</option>
                            <option>Obat Bebas Terbatas</option>
                            <option>Obat Resep (Keras)</option>
                        </select>
                    </div>

                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Harga Jual (Rp)</label>
                        <input x-model.number="form.price" type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none">
                    </div>

                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Stok Awal / Saat Ini</label>
                        <div class="flex gap-2">
                            <input x-model.number="form.stock" type="number" class="w-2/3 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none">
                            <select x-model="form.unit" class="w-1/3 px-2 py-2 border border-gray-300 rounded-lg outline-none text-xs">
                                <option>Tablet</option>
                                <option>Kapsul</option>
                                <option>Tube</option>
                                <option>Botol</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1 flex items-center gap-1">Batas Minimal <i class="fa-solid fa-circle-info text-gray-400" title="Sistem akan memberi peringatan jika stok di bawah angka ini"></i></label>
                        <input x-model.number="form.minStock" type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none">
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
                <button @click="isModalOpen = false" class="px-5 py-2.5 text-sm font-bold text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                <button @click="isModalOpen = false" class="px-5 py-2.5 text-sm font-bold text-white bg-teal-600 rounded-lg hover:bg-teal-700 shadow-md flex items-center gap-2">
                    <i class="fa-solid fa-save"></i> Simpan Data
                </button>
            </div>
        </div>
    </div>

</div>

<?php
// include '../../includes/footer.php'; 
?>