<?php
// pages/admin/patient_data.php
include '../../includes/header_admin.php'; 
?>

<div class="p-6 sm:p-8 space-y-8 max-w-[1400px] mx-auto" x-data="{
    searchQuery: '',
    filterStatus: 'semua',
    isDetailModalOpen: false,
    isAddModalOpen: false,
    activePatient: null,

    // Form Tambah Pasien
    form: { name: '', gender: 'Laki-laki', age: '', phone: '', email: '', address: '' },

    // Database Dummy Pasien
    patientList: [
        { id: 'RM-2604-001', name: 'Jovita Edgina', gender: 'Perempuan', age: 20, phone: '0812-3456-7890', email: 'jovita@email.com', address: 'Jl. Siliwangi No. 12, Tasikmalaya', regDate: '20 Apr 2026', lastVisit: '20 Apr 2026', status: 'Aktif' },
        { id: 'RM-2604-002', name: 'Farras Faishal', gender: 'Laki-laki', age: 21, phone: '0819-8765-4321', email: 'farras@email.com', address: 'Jl. RE Martadinata, Tasikmalaya', regDate: '15 Apr 2026', lastVisit: '20 Apr 2026', status: 'Aktif' },
        { id: 'RM-2603-045', name: 'Zahra Ramadhani', gender: 'Perempuan', age: 21, phone: '0811-1222-3333', email: 'zahra@email.com', address: 'Singaparna, Tasikmalaya', regDate: '10 Mar 2026', lastVisit: '15 Apr 2026', status: 'Aktif' },
        { id: 'RM-2512-102', name: 'Fatcku Rochman', gender: 'Laki-laki', age: 22, phone: '0855-5666-7777', email: 'fatcku@email.com', address: 'Ciawi, Tasikmalaya', regDate: '20 Des 2025', lastVisit: '02 Apr 2026', status: 'Aktif' },
        { id: 'RM-2510-088', name: 'Budi Santoso', gender: 'Laki-laki', age: 45, phone: '0812-9999-8888', email: 'budi.s@email.com', address: 'Kawalu, Tasikmalaya', regDate: '05 Okt 2025', lastVisit: '10 Mar 2026', status: 'Nonaktif' },
        { id: 'RM-2601-012', name: 'Siti Aminah', gender: 'Perempuan', age: 58, phone: '0822-1111-0000', email: '-', address: 'Indihiang, Tasikmalaya', regDate: '12 Jan 2026', lastVisit: '12 Jan 2026', status: 'Aktif' }
    ],

    get filteredPatients() {
        return this.patientList.filter(p => {
            const matchSearch = p.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || p.id.toLowerCase().includes(this.searchQuery.toLowerCase()) || p.phone.includes(this.searchQuery);
            const matchStatus = this.filterStatus === 'semua' || p.status.toLowerCase() === this.filterStatus.toLowerCase();
            return matchSearch && matchStatus;
        });
    },

    openDetailModal(patient) {
        this.activePatient = patient;
        this.isDetailModalOpen = true;
    },

    openAddModal() {
        // Reset form setiap kali modal dibuka
        this.form = { name: '', gender: 'Laki-laki', age: '', phone: '', email: '', address: '' };
        this.isAddModalOpen = true;
    },

    saveNewPatient() {
        if(this.form.name === '' || this.form.phone === '') return; // Validasi sederhana

        // Generate No RM Dummy
        const newIdNumber = String(this.patientList.length + 1).padStart(3, '0');
        const newPatient = {
            id: 'RM-2604-' + newIdNumber,
            name: this.form.name,
            gender: this.form.gender,
            age: this.form.age || 0,
            phone: this.form.phone,
            email: this.form.email || '-',
            address: this.form.address || '-',
            regDate: 'Hari Ini',
            lastVisit: 'Belum Ada',
            status: 'Aktif'
        };

        // Masukkan pasien baru ke urutan teratas array
        this.patientList.unshift(newPatient);
        
        // Tutup Modal
        this.isAddModalOpen = false;
    }
}">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Database Pasien</h1>
            <p class="text-slate-500 mt-1">Pusat data demografi dan status keanggotaan pasien klinik.</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-bold py-2.5 px-5 rounded-xl shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-file-csv text-emerald-600"></i> Export CSV
            </button>
            <button @click="openAddModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-5 rounded-xl shadow-md transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Pasien Baru (Manual)
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-2xl"><i class="fa-solid fa-users"></i></div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Registrasi</p>
                <h3 class="text-2xl font-black text-slate-800" x-text="patientList.length + ' Pasien'"></h3>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 text-2xl"><i class="fa-solid fa-user-check"></i></div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Pasien Aktif</p>
                <h3 class="text-2xl font-black text-slate-800" x-text="patientList.filter(p => p.status === 'Aktif').length"></h3>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 text-2xl"><i class="fa-solid fa-hospital-user"></i></div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Pendaftar Bulan Ini</p>
                <h3 class="text-2xl font-black text-slate-800">2 <span class="text-sm font-medium text-slate-500">Pasien Baru</span></h3>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
        
        <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row gap-4 justify-between items-center bg-slate-50/50">
            <div class="relative w-full md:w-96">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-3 text-slate-400"></i>
                <input x-model="searchQuery" type="text" placeholder="Cari Nama, No. RM, atau No. HP..." class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 transition text-sm shadow-sm">
            </div>
            <div class="flex items-center gap-3 w-full md:w-auto">
                <span class="text-sm font-bold text-slate-500"><i class="fa-solid fa-filter"></i> Status:</span>
                <select x-model="filterStatus" class="flex-1 md:w-48 px-4 py-2.5 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 transition text-sm font-semibold text-slate-700 shadow-sm">
                    <option value="semua">Semua Status</option>
                    <option value="Aktif">Aktif</option>
                    <option value="Nonaktif">Nonaktif (Diblokir)</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white text-slate-400 text-xs uppercase tracking-wider border-b border-slate-200">
                        <th class="px-6 py-4 font-bold">No. RM & Pendaftaran</th>
                        <th class="px-6 py-4 font-bold">Identitas Pasien</th>
                        <th class="px-6 py-4 font-bold">Kontak</th>
                        <th class="px-6 py-4 font-bold">Kunjungan Terakhir</th>
                        <th class="px-6 py-4 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <template x-for="patient in filteredPatients" :key="patient.id">
                        <tr class="hover:bg-indigo-50/30 transition group">
                            
                            <td class="px-6 py-4">
                                <span class="inline-block px-2 py-1 bg-indigo-50 text-indigo-700 font-mono font-bold text-xs rounded border border-indigo-100 mb-1" x-text="patient.id"></span>
                                <p class="text-[10px] font-bold text-slate-500" x-text="'Reg: ' + patient.regDate"></p>
                            </td>
                            
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-slate-900 group-hover:text-indigo-600 transition" x-text="patient.name"></p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs text-slate-500" x-text="patient.gender"></span>
                                    <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                    <span class="text-xs text-slate-500" x-text="patient.age + ' Thn'"></span>
                                    
                                    <span x-show="patient.status === 'Nonaktif'" class="ml-2 text-[8px] bg-rose-100 text-rose-600 px-1.5 py-0.5 rounded font-bold uppercase">Nonaktif</span>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-slate-700 flex items-center gap-2"><i class="fa-solid fa-phone text-slate-400 text-xs"></i> <span x-text="patient.phone"></span></p>
                            </td>

                            <td class="px-6 py-4">
                                <p class="text-sm text-slate-700 font-medium" x-text="patient.lastVisit"></p>
                            </td>

                            <td class="px-6 py-4 text-right space-x-2">
                                <button @click="openDetailModal(patient)" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-indigo-600 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition text-xs font-bold shadow-sm">
                                    Lihat Detail
                                </button>
                            </td>
                        </tr>
                    </template>

                    <template x-if="filteredPatients.length === 0">
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <i class="fa-solid fa-folder-open text-4xl text-slate-300 mb-3 block"></i>
                                <p class="text-sm font-medium">Data pasien tidak ditemukan.</p>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-slate-100 bg-slate-50 flex justify-between items-center text-sm text-slate-500">
            <p>Menampilkan 1 hingga <span x-text="filteredPatients.length"></span> dari <span x-text="patientList.length"></span> data</p>
            <div class="flex gap-1">
                <button class="px-3 py-1 border border-slate-200 bg-white rounded hover:bg-slate-100 disabled:opacity-50" disabled>Prev</button>
                <button class="px-3 py-1 border border-slate-200 bg-indigo-50 text-indigo-600 font-bold rounded">1</button>
                <button class="px-3 py-1 border border-slate-200 bg-white rounded hover:bg-slate-100">Next</button>
            </div>
        </div>
    </div>

    <div x-show="isAddModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div x-show="isAddModalOpen" x-transition.opacity @click="isAddModalOpen = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

        <div x-show="isAddModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden border border-slate-100 flex flex-col">
            
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-user-plus text-indigo-600"></i> Registrasi Pasien Baru
                </h3>
                <button @click="isAddModalOpen = false" class="text-slate-400 hover:text-rose-500 transition w-8 h-8 flex items-center justify-center rounded-full hover:bg-rose-50"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <div class="p-6 space-y-4 overflow-y-auto max-h-[70vh]">
                
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nama Lengkap Sesuai KTP <span class="text-rose-500">*</span></label>
                    <input x-model="form.name" type="text" placeholder="Masukkan nama pasien" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition text-sm">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Jenis Kelamin</label>
                        <select x-model="form.gender" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition text-sm">
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Umur (Tahun)</label>
                        <input x-model.number="form.age" type="number" placeholder="Contoh: 25" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">No. Telepon / WA <span class="text-rose-500">*</span></label>
                        <input x-model="form.phone" type="tel" placeholder="0812-XXXX-XXXX" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Email (Opsional)</label>
                        <input x-model="form.email" type="email" placeholder="pasien@email.com" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Alamat Lengkap</label>
                    <textarea x-model="form.address" rows="3" placeholder="Nama Jalan, RT/RW, Kota..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition text-sm resize-none"></textarea>
                </div>

            </div>

            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
                <button @click="isAddModalOpen = false" class="px-5 py-2.5 text-sm font-bold text-slate-600 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition">Batal</button>
                <button @click="saveNewPatient()" class="px-6 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-save"></i> Simpan Data Pasien
                </button>
            </div>
        </div>
    </div>

    <div x-show="isDetailModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div x-show="isDetailModalOpen" x-transition.opacity @click="isDetailModalOpen = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

        <div x-show="isDetailModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" class="relative bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden border border-slate-100 flex flex-col">
            
            <template x-if="activePatient">
                <div>
                    <div class="bg-gradient-to-r from-indigo-600 to-blue-500 p-6 sm:p-8 text-white relative">
                        <button @click="isDetailModalOpen = false" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-black/20 hover:bg-black/40 transition"><i class="fa-solid fa-xmark"></i></button>
                        
                        <div class="flex items-center gap-5">
                            <img :src="`https://ui-avatars.com/api/?name=${activePatient.name}&background=ffffff&color=4f46e5`" class="w-20 h-20 rounded-2xl shadow-lg border-2 border-white/20">
                            <div>
                                <h2 class="text-2xl font-bold mb-1" x-text="activePatient.name"></h2>
                                <p class="text-indigo-100 font-mono text-sm bg-black/20 inline-block px-2 py-0.5 rounded" x-text="activePatient.id"></p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 sm:p-8 space-y-6 bg-slate-50">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Nomor Telepon / WhatsApp</p>
                                <p class="text-sm font-bold text-slate-800 flex items-center gap-2"><i class="fa-brands fa-whatsapp text-emerald-500"></i> <span x-text="activePatient.phone"></span></p>
                            </div>
                            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Email</p>
                                <p class="text-sm font-bold text-slate-800 flex items-center gap-2"><i class="fa-regular fa-envelope text-slate-400"></i> <span x-text="activePatient.email"></span></p>
                            </div>
                            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Jenis Kelamin & Umur</p>
                                <p class="text-sm font-bold text-slate-800" x-text="activePatient.gender + ', ' + activePatient.age + ' Tahun'"></p>
                            </div>
                            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tanggal Bergabung</p>
                                <p class="text-sm font-bold text-slate-800" x-text="activePatient.regDate"></p>
                            </div>
                            <div class="col-span-1 sm:col-span-2 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Alamat Lengkap</p>
                                <p class="text-sm font-bold text-slate-800"><i class="fa-solid fa-location-dot text-rose-500 mr-1"></i> <span x-text="activePatient.address"></span></p>
                            </div>
                        </div>

                        <div class="border border-rose-200 bg-rose-50 rounded-2xl p-4 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-bold text-rose-800">Status Akun: <span class="uppercase" x-text="activePatient.status"></span></p>
                                <p class="text-xs text-rose-600 mt-0.5">Blokir akun jika pasien melanggar ketentuan klinik.</p>
                            </div>
                            <button class="px-4 py-2 bg-white border border-rose-300 text-rose-600 hover:bg-rose-600 hover:text-white rounded-xl text-xs font-bold transition shadow-sm" x-text="activePatient.status === 'Aktif' ? 'Blokir Pasien' : 'Aktifkan Kembali'"></button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

</div>

<?php
// include '../../includes/footer.php'; 
?>