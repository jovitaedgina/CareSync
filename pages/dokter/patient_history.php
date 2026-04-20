<?php
// pages/dokter/patient_history.php
include '../../includes/header_dokter.php'; 
?>

<div class="p-6 sm:p-8 max-w-7xl mx-auto" x-data="{ 
    // State Navigasi Halaman
    currentView: 'directory', // 'directory' atau 'detail'
    
    // State Direktori (View 1)
    searchPasien: '',
    filterPeriode: 'semua',
    
    // State Detail (View 2) - Seperti sebelumnya
    leftTab: 'semua',
    modalTab: 'riwayat',
    activeRecordId: 1,
    isModalOpen: false,

    // Database Direktori Pasien
    patientsList: [
        { id: 'MED-8819', name: 'Jovita Edgina', gender: 'Perempuan', age: 20, lastVisit: 'Hari Ini', dateVal: '2026-04-20', lastComplaint: 'Jerawat meradang di pipi kanan', lastDiagnosis: 'Acne Vulgaris Grade II', avatar: 'JE', color: 'bg-blue-600' },
        { id: 'MED-8820', name: 'Farras Faishal', gender: 'Laki-laki', age: 21, lastVisit: 'Hari Ini', dateVal: '2026-04-20', lastComplaint: 'Gatal di sela jari kaki', lastDiagnosis: 'Tinea Pedis (Eksim)', avatar: 'FF', color: 'bg-green-600' },
        { id: 'MED-8102', name: 'Zahra Ramadhani', gender: 'Perempuan', age: 21, lastVisit: 'Minggu Ini', dateVal: '2026-04-15', lastComplaint: 'Kontrol rutin mingguan', lastDiagnosis: 'Pemeriksaan Rutin', avatar: 'ZR', color: 'bg-purple-600' },
        { id: 'MED-7754', name: 'Fatcku Rochman', gender: 'Laki-laki', age: 22, lastVisit: 'Bulan Ini', dateVal: '2026-04-02', lastComplaint: 'Batuk berdahak tidak kunjung sembuh', lastDiagnosis: 'Bronkitis Akut', avatar: 'FR', color: 'bg-orange-500' },
        { id: 'MED-6521', name: 'Budi Santoso', gender: 'Laki-laki', age: 45, lastVisit: 'Bulan Lalu', dateVal: '2026-03-10', lastComplaint: 'Mata merah dan perih', lastDiagnosis: 'Konjungtivitis', avatar: 'BS', color: 'bg-teal-500' }
    ],

    // Database Riwayat Kunjungan per Pasien (Dummy untuk View 2)
    records: [
        { id: 1, doctorName: 'dr. Susanti Wulandari, Sp.KK', initial: 'SW', color: 'bg-blue-600', specialty: 'Spesialis Kulit & Kelamin', date: 'Hari Ini', complaint: 'Jerawat meradang dan gatal di pipi kanan', diagnosis: 'Acne Vulgaris Grade II', status: 'selesai', hasPrescription: true, vitals: { bp: '120/80', bs: '95', chol: '180' }, prescription: [{ name: 'Clindamycin 300mg Caps', rule: 'S 2 dd 1 caps' }, { name: 'Benzolac 5% Gel Tube', rule: 'S u.e' }] },
        { id: 2, doctorName: 'dr. Budi Santoso, Sp.M', initial: 'BS', color: 'bg-teal-500', specialty: 'Spesialis Mata', date: '15 Mar 2026', complaint: 'Mata merah dan gatal', diagnosis: 'Konjungtivitis Alergi', status: 'selesai', hasPrescription: false, vitals: { bp: '110/70', bs: '90', chol: '160' }, prescription: [] }
    ],

    get filteredPatients() {
        return this.patientsList.filter(p => {
            const matchName = p.name.toLowerCase().includes(this.searchPasien.toLowerCase()) || p.id.toLowerCase().includes(this.searchPasien.toLowerCase());
            const matchPeriode = this.filterPeriode === 'semua' || p.lastVisit.toLowerCase() === this.filterPeriode.toLowerCase();
            return matchName && matchPeriode;
        });
    },

    get filteredRecords() {
        if (this.leftTab === 'semua') return this.records;
        if (this.leftTab === 'resep') return this.records.filter(r => r.hasPrescription);
        return this.records.filter(r => r.status === this.leftTab);
    },

    get activeRecord() {
        return this.records.find(r => r.id === this.activeRecordId) || this.records[0];
    },

    openDetail() {
        this.currentView = 'detail';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    },

    openModal(id) {
        this.activeRecordId = id;
        this.modalTab = 'riwayat';
        this.isModalOpen = true;
    }
}">

    <div x-show="currentView === 'directory'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
        
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Direktori Rekam Medis</h1>
            <p class="text-gray-500 mt-1">Cari dan kelola riwayat rekam medis seluruh pasien Anda.</p>
        </div>

        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200 mb-6 flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="relative w-full md:w-96">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-3 text-gray-400"></i>
                <input x-model="searchPasien" type="text" placeholder="Cari nama pasien atau No. RM..." class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 transition text-sm">
            </div>
            
            <div class="flex items-center gap-3 w-full md:w-auto">
                <span class="text-sm font-bold text-gray-500"><i class="fa-solid fa-filter"></i> Sortir:</span>
                <select x-model="filterPeriode" class="flex-1 md:w-48 px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 transition text-sm font-semibold text-gray-700">
                    <option value="semua">Semua Waktu</option>
                    <option value="hari ini">Hari Ini</option>
                    <option value="minggu ini">Minggu Ini</option>
                    <option value="bulan ini">Bulan Ini</option>
                </select>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                            <th class="px-6 py-4 font-bold">Pasien & No. RM</th>
                            <th class="px-6 py-4 font-bold">Waktu Kunjungan</th>
                            <th class="px-6 py-4 font-bold">Keluhan Terakhir</th>
                            <th class="px-6 py-4 font-bold">Diagnosis Akhir</th>
                            <th class="px-6 py-4 font-bold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="patient in filteredPatients" :key="patient.id">
                            <tr class="hover:bg-blue-50/30 transition group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div :class="patient.color" class="w-10 h-10 rounded-full text-white flex items-center justify-center font-bold text-xs shadow-sm shrink-0" x-text="patient.avatar"></div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition" x-text="patient.name"></p>
                                            <p class="text-[10px] font-bold text-gray-500 mt-0.5" x-text="patient.id"></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="patient.lastVisit === 'Hari Ini' ? 'bg-green-100 text-green-700 border-green-200' : 'bg-gray-100 text-gray-600 border-gray-200'" class="px-2.5 py-1 rounded-md text-xs font-bold border" x-text="patient.lastVisit"></span>
                                    <p class="text-[10px] text-gray-400 mt-1" x-text="patient.dateVal"></p>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700" x-text="patient.lastComplaint"></td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        <i class="fa-solid fa-stethoscope"></i> <span x-text="patient.lastDiagnosis"></span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button @click="openDetail()" class="text-blue-600 bg-white border border-blue-200 hover:bg-blue-600 hover:text-white rounded-lg px-4 py-2 text-xs font-bold transition">
                                        Buka Rekam Medis
                                    </button>
                                </td>
                            </tr>
                        </template>
                        
                        <template x-if="filteredPatients.length === 0">
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fa-solid fa-magnifying-glass text-3xl mb-3 text-gray-300"></i>
                                    <p class="text-sm">Pasien tidak ditemukan.</p>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div x-show="currentView === 'detail'" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
        
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <button @click="currentView = 'directory'" class="text-gray-400 hover:text-blue-600 transition flex items-center gap-2 bg-white px-3 py-1.5 rounded-lg border border-gray-200 shadow-sm">
                    <i class="fa-solid fa-arrow-left"></i> <span class="text-sm font-bold">Kembali ke Direktori</span>
                </button>
                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight ml-4">Detail Rekam Medis</h1>
            </div>
            <button class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-bold py-2 px-4 rounded-lg shadow-sm transition flex items-center gap-2 text-sm">
                <i class="fa-solid fa-download"></i> Simpan PDF
            </button>
        </div>

        <div class="bg-gradient-to-br from-blue-600 to-blue-500 rounded-3xl shadow-sm overflow-hidden mb-8 relative">
            <i class="fa-solid fa-heart-pulse absolute -right-10 -bottom-10 text-9xl opacity-10"></i>
            
            <div class="p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-6 relative z-10">
                <div class="flex items-center gap-5">
                    <div class="w-20 h-20 rounded-full bg-white text-blue-600 flex items-center justify-center font-black text-3xl shadow-lg border-4 border-blue-200">JE</div>
                    <div class="text-white">
                        <h2 class="text-2xl font-bold mb-1">Jovita Edgina</h2>
                        <p class="text-blue-100 font-medium">Pasien Umum - #MED-8819</p>
                        <p class="text-sm font-medium mt-2 flex items-center gap-4">
                            <span><i class="fa-solid fa-cake-candles mr-1.5 opacity-80"></i> 20 Tahun</span>
                            <span><i class="fa-solid fa-venus mr-1.5 opacity-80"></i> Perempuan</span>
                        </p>
                    </div>
                </div>

                <div class="flex flex-col gap-2 bg-black/10 p-4 rounded-2xl border border-white/10 backdrop-blur-sm">
                    <div class="flex justify-between items-center gap-6">
                        <span class="text-sm font-semibold text-white flex items-center gap-2"><i class="fa-solid fa-triangle-exclamation text-yellow-300"></i> Alergi Aspirin</span>
                        <span class="text-[10px] font-bold bg-yellow-400 text-yellow-900 px-2 py-0.5 rounded uppercase">Tinggi</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-3xl p-6 md:p-8 shadow-sm">
            
            <div class="flex space-x-6 border-b border-gray-200 mb-6 overflow-x-auto">
                <button @click="leftTab = 'semua'" :class="leftTab === 'semua' ? 'border-b-2 border-gray-900 text-gray-900 font-bold' : 'text-gray-500 hover:text-gray-700 font-medium'" class="pb-3 text-sm transition whitespace-nowrap">Semua Riwayat</button>
                <button @click="leftTab = 'resep'" :class="leftTab === 'resep' ? 'border-b-2 border-gray-900 text-gray-900 font-bold' : 'text-gray-500 hover:text-gray-700 font-medium'" class="pb-3 text-sm transition whitespace-nowrap"><i class="fa-solid fa-pills mr-1"></i> Riwayat Resep</button>
            </div>

            <div class="space-y-4">
                <template x-for="record in filteredRecords" :key="record.id">
                    <div @click="openModal(record.id)" class="border border-gray-200 hover:border-blue-300 rounded-2xl p-5 shadow-sm hover:shadow-md hover:bg-blue-50/30 transition cursor-pointer group flex flex-col md:flex-row md:items-center justify-between gap-4">
                        
                        <div class="flex items-center gap-5">
                            <div :class="record.color" class="w-14 h-14 rounded-2xl text-white flex items-center justify-center font-bold text-xl shadow-sm shrink-0" x-text="record.initial"></div>
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs font-bold text-gray-400" x-text="record.date"></span>
                                    <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                    <h3 class="font-bold text-gray-900 group-hover:text-blue-600 transition" x-text="record.doctorName"></h3>
                                </div>
                                <p class="text-sm text-gray-700 mb-2"><span class="font-semibold text-gray-900">Keluhan:</span> <span x-text="record.complaint"></span></p>
                                <div class="flex flex-wrap gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700 border border-gray-200">
                                        <i class="fa-solid fa-stethoscope"></i> <span x-text="record.diagnosis"></span>
                                    </span>
                                    <template x-if="record.hasPrescription">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-green-50 text-green-700 border border-green-200">
                                            <i class="fa-solid fa-pills"></i> Ada Resep
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </div>
                        
                        <div class="hidden md:flex shrink-0">
                            <button class="text-blue-600 bg-blue-50 group-hover:bg-blue-600 group-hover:text-white rounded-xl px-4 py-2 text-sm font-bold transition flex items-center gap-2">
                                Lihat Detail <i class="fa-solid fa-chevron-right text-xs"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
            <div x-show="isModalOpen" x-transition.opacity @click="isModalOpen = false" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

            <div x-show="isModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-2xl border border-gray-100 flex flex-col max-h-[90vh]">
                
                <div class="bg-white px-6 py-4 border-b border-gray-200 flex justify-between items-center sticky top-0 z-10">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2"><i class="fa-solid fa-file-medical text-blue-600"></i> Detail Kunjungan</h3>
                        <p class="text-xs text-gray-500 mt-1 font-medium" x-text="activeRecord.date"></p>
                    </div>
                    <button @click="isModalOpen = false" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-red-100 hover:text-red-500 transition"><i class="fa-solid fa-xmark"></i></button>
                </div>

                <div class="overflow-y-auto p-6 bg-white space-y-6">
                    <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl space-y-3 text-sm">
                        <p><span class="font-bold text-gray-400 uppercase text-xs">Dokter:</span> <span class="font-bold text-gray-900" x-text="activeRecord.doctorName"></span></p>
                        <p><span class="font-bold text-gray-400 uppercase text-xs">Keluhan:</span> <span class="text-gray-800" x-text="activeRecord.complaint"></span></p>
                        <p><span class="font-bold text-gray-400 uppercase text-xs">Diagnosis:</span> <span class="font-bold text-blue-600" x-text="activeRecord.diagnosis"></span></p>
                    </div>

                    <template x-if="activeRecord.hasPrescription">
                        <div class="border border-gray-200 rounded-xl overflow-hidden">
                            <div class="bg-gray-50 p-3 border-b border-gray-200 font-bold text-xs text-gray-800 uppercase">E-Prescription</div>
                            <div class="p-4 space-y-3">
                                <template x-for="(med, idx) in activeRecord.prescription" :key="idx">
                                    <div class="flex items-start gap-3 border-b border-dashed border-gray-200 pb-2 last:border-0 last:pb-0">
                                        <span class="font-bold text-blue-600 text-sm">R/</span>
                                        <div>
                                            <p class="text-sm font-bold text-gray-800" x-text="med.name"></p>
                                            <p class="text-xs text-gray-500 mt-0.5" x-text="med.rule"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        </div>
    </div>

<?php
// include '../../includes/footer.php'; 
?>