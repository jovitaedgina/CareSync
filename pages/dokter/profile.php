<?php
include '../../includes/header_dokter.php';
?>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<div class="bg-gray-50 min-h-screen pb-12 pt-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Manajemen Profil & Jadwal</h1>
            <p class="text-gray-500 mt-1">Kelola informasi publik Anda dan atur jam praktik untuk pasien.</p>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            
            <div class="xl:col-span-2 space-y-8">
                
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                    <h2 class="text-xl font-bold text-gray-800 border-b pb-4 mb-6"><i class="fa-solid fa-user-doctor text-blue-500 mr-2"></i> Informasi Pribadi & Profesi</h2>
                    
                    <div class="flex flex-col sm:flex-row gap-8 mb-8">
                        <div class="flex-shrink-0 flex flex-col items-center">
                            <div class="w-32 h-32 rounded-full bg-blue-100 border-4 border-white shadow-md flex items-center justify-center overflow-hidden relative group cursor-pointer">
                                <img src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" alt="Foto Dokter" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-200">
                                    <i class="fa-solid fa-camera text-white text-2xl"></i>
                                </div>
                            </div>
                            <button class="mt-3 text-sm text-blue-600 font-semibold hover:text-blue-800">Ubah Foto</button>
                        </div>

                        <div class="flex-grow grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap (Sesuai Gelar)</label>
                                <input type="text" value="dr. Susanti Wulandari, Sp.KK" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm" placeholder="Contoh: dr. Budi Santoso, Sp.M">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Spesialisasi</label>
                                <select class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm bg-white">
                                    <option value="">Pilih Spesialisasi</option>
                                    <option value="Dokter Umum">Dokter Umum</option>
                                    <option value="Spesialis Kulit & Kelamin" selected>Spesialis Kulit & Kelamin (Sp.KK)</option>
                                    <option value="Spesialis Penyakit Dalam">Spesialis Penyakit Dalam (Sp.PD)</option>
                                    <option value="Spesialis Mata">Spesialis Mata (Sp.M)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor STR</label>
                                <input type="text" value="33211002213456789" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm" placeholder="Masukkan 16 Digit STR">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Email Aktif</label>
                                <input type="email" value="susanti.w@caresync.com" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Telepon (WhatsApp)</label>
                                <input type="text" value="081234567890" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm">
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-md transition duration-200">
                            Simpan Profil
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8" x-data="{
                    days: [
                        { name: 'Senin', active: true, start: '09:00', end: '15:00' },
                        { name: 'Selasa', active: true, start: '09:00', end: '15:00' },
                        { name: 'Rabu', active: true, start: '09:00', end: '15:00' },
                        { name: 'Kamis', active: false, start: '09:00', end: '15:00' },
                        { name: 'Jumat', active: true, start: '13:00', end: '18:00' },
                        { name: 'Sabtu', active: false, start: '09:00', end: '12:00' },
                        { name: 'Minggu', active: false, start: '09:00', end: '12:00' }
                    ]
                }">
                    <h2 class="text-xl font-bold text-gray-800 border-b pb-4 mb-6"><i class="fa-solid fa-calendar-check text-green-500 mr-2"></i> Jadwal Praktik Reguler</h2>
                    
                    <div class="space-y-4">
                        <template x-for="(day, index) in days" :key="index">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-xl border transition-colors duration-200" :class="day.active ? 'border-blue-200 bg-blue-50' : 'border-gray-200 bg-gray-50'">
                                
                                <div class="flex items-center mb-3 sm:mb-0 w-40">
                                    <button @click="day.active = !day.active" :class="day.active ? 'bg-blue-600' : 'bg-gray-300'" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2">
                                        <span :class="day.active ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                                    </button>
                                    <span class="ml-3 font-semibold" :class="day.active ? 'text-blue-900' : 'text-gray-500'" x-text="day.name"></span>
                                </div>

                                <div class="flex items-center space-x-3">
                                    <input type="time" x-model="day.start" :disabled="!day.active" :class="day.active ? 'bg-white text-gray-900 border-gray-300' : 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed'" class="px-3 py-2 rounded-lg border focus:ring-blue-500 focus:border-blue-500 shadow-sm w-32 text-center">
                                    <span class="text-gray-500 font-medium">s/d</span>
                                    <input type="time" x-model="day.end" :disabled="!day.active" :class="day.active ? 'bg-white text-gray-900 border-gray-300' : 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed'" class="px-3 py-2 rounded-lg border focus:ring-blue-500 focus:border-blue-500 shadow-sm w-32 text-center">
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="flex justify-end mt-8">
                        <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-md transition duration-200">
                            Simpan Jadwal
                        </button>
                    </div>
                </div>

            </div>

            <div class="xl:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-8">
                    <div class="h-24 bg-gradient-to-r from-blue-500 to-cyan-400"></div>
                    
                    <div class="px-6 pb-6 relative">
                        <div class="flex justify-center -mt-12 mb-4">
                            <div class="w-24 h-24 rounded-full border-4 border-white shadow-lg overflow-hidden bg-white">
                                <img src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" alt="Preview">
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <h3 class="text-xl font-bold text-gray-900">dr. Susanti Wulandari, Sp.KK</h3>
                            <p class="text-blue-600 font-medium text-sm mt-1">Spesialis Kulit & Kelamin</p>
                            
                            <div class="mt-4 flex items-center justify-center space-x-2 text-sm text-gray-500">
                                <i class="fa-regular fa-id-card"></i>
                                <span>STR: 33211002213456789</span>
                            </div>
                        </div>

                        <div class="mt-6 bg-blue-50 rounded-xl p-4 border border-blue-100">
                            <h4 class="text-xs font-bold text-blue-800 uppercase tracking-wider mb-2">Tampilan Pasien</h4>
                            <p class="text-sm text-blue-700">Ini adalah kartu profil yang akan dilihat oleh pasien saat mencari dokter di halaman Booking.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

