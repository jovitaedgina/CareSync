<?php
// pages/admin/manage_staff.php
include '../../includes/header_admin.php'; 
?>

<div class="p-6 sm:p-8 space-y-8 max-w-[1400px] mx-auto" x-data="{
    searchQuery: '',
    filterRole: 'semua',
    isModalOpen: false,
    modalTitle: 'Tambah Staf Baru',
    
    // State Formulir
    form: { id: '', name: '', email: '', role: 'Dokter', specialty: '', status: 'Aktif' },

    // Database Dummy Staf Klinik
    staffList: [
        { id: 'EMP-001', name: 'dr. Susanti Wulandari, Sp.KK', email: 'susanti.w@caresync.com', role: 'Dokter', specialty: 'Poli Kulit & Kelamin', status: 'Aktif', avatarColor: '4f46e5' },
        { id: 'EMP-002', name: 'dr. Budi Santoso, Sp.M', email: 'budi.s@caresync.com', role: 'Dokter', specialty: 'Poli Mata', status: 'Aktif', avatarColor: '0ea5e9' },
        { id: 'EMP-003', name: 'Rina M., S.Farm', email: 'rina.m@caresync.com', role: 'Apoteker', specialty: 'Instalasi Farmasi', status: 'Aktif', avatarColor: '0d9488' },
        { id: 'EMP-004', name: 'dr. Fenny Nurmahdi', email: 'fenny.n@caresync.com', role: 'Dokter', specialty: 'Poli Umum', status: 'Aktif', avatarColor: '10b981' },
        { id: 'EMP-005', name: 'Farras Faishal', email: 'farras.it@caresync.com', role: 'Admin', specialty: 'IT Support', status: 'Aktif', avatarColor: '64748b' },
        { id: 'EMP-006', name: 'dr. Herman Santika', email: 'herman.s@caresync.com', role: 'Dokter', specialty: 'Poli Gigi', status: 'Cuti', avatarColor: 'f59e0b' }
    ],

    get filteredStaff() {
        return this.staffList.filter(staff => {
            const matchSearch = staff.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || staff.email.toLowerCase().includes(this.searchQuery.toLowerCase());
            const matchRole = this.filterRole === 'semua' || staff.role.toLowerCase() === this.filterRole.toLowerCase();
            return matchSearch && matchRole;
        });
    },

    openAddModal() {
        this.modalTitle = 'Registrasi Staf Baru';
        this.form = { id: 'EMP-00' + (this.staffList.length + 1), name: '', email: '', role: 'Dokter', specialty: '', status: 'Aktif' };
        this.isModalOpen = true;
    },

    openEditModal(staff) {
        this.modalTitle = 'Edit Data Staf';
        this.form = { ...staff }; 
        this.isModalOpen = true;
    }
}">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Manajemen Staf & Akses</h1>
            <p class="text-slate-500 mt-1">Kelola akun pegawai, hak akses, dan penugasan departemen.</p>
        </div>
        <button @click="openAddModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-md transition flex items-center gap-2">
            <i class="fa-solid fa-user-plus"></i> Tambah Staf
        </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600"><i class="fa-solid fa-users"></i></div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Pegawai</p>
                <h3 class="text-xl font-bold text-slate-800" x-text="staffList.length"></h3>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600"><i class="fa-solid fa-user-doctor"></i></div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Dokter Aktif</p>
                <h3 class="text-xl font-bold text-slate-800" x-text="staffList.filter(s => s.role === 'Dokter').length"></h3>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-teal-50 flex items-center justify-center text-teal-600"><i class="fa-solid fa-mortar-pestle"></i></div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Apoteker</p>
                <h3 class="text-xl font-bold text-slate-800" x-text="staffList.filter(s => s.role === 'Apoteker').length"></h3>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4 border-b-4 border-b-amber-400">
            <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center text-amber-600"><i class="fa-solid fa-mug-hot"></i></div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Sedang Cuti</p>
                <h3 class="text-xl font-bold text-slate-800" x-text="staffList.filter(s => s.status === 'Cuti').length"></h3>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
        
        <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row gap-4 justify-between items-center bg-slate-50/50">
            <div class="relative w-full md:w-96">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-3 text-slate-400"></i>
                <input x-model="searchQuery" type="text" placeholder="Cari nama atau email staf..." class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 transition text-sm shadow-sm">
            </div>
            <div class="flex items-center gap-3 w-full md:w-auto">
                <span class="text-sm font-bold text-slate-500"><i class="fa-solid fa-filter"></i> Role:</span>
                <select x-model="filterRole" class="flex-1 md:w-48 px-4 py-2.5 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 transition text-sm font-semibold text-slate-700 shadow-sm">
                    <option value="semua">Semua Jabatan</option>
                    <option value="Dokter">Dokter</option>
                    <option value="Apoteker">Apoteker</option>
                    <option value="Admin">Admin / IT</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white text-slate-400 text-xs uppercase tracking-wider border-b border-slate-200">
                        <th class="px-6 py-4 font-bold">Profil Pegawai</th>
                        <th class="px-6 py-4 font-bold">Jabatan & Departemen</th>
                        <th class="px-6 py-4 font-bold">Kontak (Email)</th>
                        <th class="px-6 py-4 font-bold text-center">Status</th>
                        <th class="px-6 py-4 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <template x-for="staff in filteredStaff" :key="staff.id">
                        <tr class="hover:bg-slate-50/50 transition group">
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <img :src="`https://ui-avatars.com/api/?name=${staff.name}&background=${staff.avatarColor}&color=fff`" class="w-10 h-10 rounded-full object-cover shadow-sm">
                                    <div>
                                        <p class="text-sm font-bold text-slate-900 group-hover:text-indigo-600 transition" x-text="staff.name"></p>
                                        <p class="text-[10px] font-mono text-slate-500 mt-0.5" x-text="staff.id"></p>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold mb-1"
                                      :class="{
                                          'bg-blue-100 text-blue-700': staff.role === 'Dokter',
                                          'bg-teal-100 text-teal-700': staff.role === 'Apoteker',
                                          'bg-slate-200 text-slate-700': staff.role === 'Admin'
                                      }" x-text="staff.role"></span>
                                <p class="text-xs text-slate-600 font-medium" x-text="staff.specialty"></p>
                            </td>

                            <td class="px-6 py-4">
                                <p class="text-sm text-slate-600" x-text="staff.email"></p>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold"
                                      :class="staff.status === 'Aktif' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-amber-50 text-amber-600 border border-amber-200'">
                                    <i class="fa-solid" :class="staff.status === 'Aktif' ? 'fa-circle-check' : 'fa-clock'"></i>
                                    <span x-text="staff.status"></span>
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right space-x-2">
                                <button @click="openEditModal(staff)" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-indigo-600 hover:border-indigo-300 transition inline-flex items-center justify-center shadow-sm" title="Edit Data">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-rose-600 hover:border-rose-300 transition inline-flex items-center justify-center shadow-sm" title="Hapus Akun">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </td>
                        </tr>
                    </template>

                    <template x-if="filteredStaff.length === 0">
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <i class="fa-solid fa-users-slash text-4xl text-slate-300 mb-3 block"></i>
                                <p class="text-sm font-medium">Data staf tidak ditemukan.</p>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div x-show="isModalOpen" x-transition.opacity @click="isModalOpen = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

        <div x-show="isModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden border border-slate-100 flex flex-col">
            
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-id-badge text-indigo-600"></i> <span x-text="modalTitle"></span>
                </h3>
                <button @click="isModalOpen = false" class="text-slate-400 hover:text-rose-500 transition w-8 h-8 flex items-center justify-center rounded-full hover:bg-rose-50"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <div class="p-6 space-y-5 overflow-y-auto max-h-[70vh]">
                
                <div class="bg-indigo-50 text-indigo-800 text-xs p-3 rounded-xl border border-indigo-100 flex gap-3">
                    <i class="fa-solid fa-circle-info mt-0.5"></i>
                    <p>Kata sandi bawaan (default) untuk staf baru adalah <b>Caresync123!</b>. Staf dapat mengubahnya setelah berhasil login.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nama Lengkap & Gelar</label>
                    <input x-model="form.name" type="text" placeholder="Cth: dr. Budi Santoso, Sp.M" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Email Institusi (Login ID)</label>
                    <input x-model="form.email" type="email" placeholder="nama@caresync.com" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition text-sm">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Jabatan / Role</label>
                        <select x-model="form.role" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition text-sm">
                            <option value="Dokter">Dokter</option>
                            <option value="Apoteker">Apoteker</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Status Akun</label>
                        <select x-model="form.status" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition text-sm">
                            <option value="Aktif">Aktif</option>
                            <option value="Cuti">Cuti</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div x-show="form.role === 'Dokter'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Spesialisasi / Poli</label>
                    <input x-model="form.specialty" type="text" placeholder="Cth: Poli Mata" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition text-sm">
                </div>

                <div x-show="form.role !== 'Dokter'">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Departemen</label>
                    <input x-model="form.specialty" type="text" placeholder="Cth: Instalasi Farmasi" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition text-sm">
                </div>

            </div>

            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
                <button @click="isModalOpen = false" class="px-5 py-2.5 text-sm font-bold text-slate-600 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition">Batal</button>
                <button @click="isModalOpen = false" class="px-6 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-save"></i> Simpan Data Staf
                </button>
            </div>
        </div>
    </div>

</div>

<?php
// include '../../includes/footer.php'; 
?>