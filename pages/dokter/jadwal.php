<?php
// pages/dokter/jadwal.php
include '../../includes/header_dokter.php'; 
?>

<div x-data="{ 
    editModalOpen: false,
    days: [
        { name: 'Senin', active: true, start: '09:00', end: '15:00' },
        { name: 'Selasa', active: true, start: '09:00', end: '15:00' },
        { name: 'Rabu', active: true, start: '09:00', end: '15:00' },
        { name: 'Kamis', active: false, start: '09:00', end: '15:00' },
        { name: 'Jumat', active: true, start: '13:00', end: '18:00' },
        { name: 'Sabtu', active: false, start: '09:00', end: '12:00' },
        { name: 'Minggu', active: false, start: '09:00', end: '12:00' }
    ]
}" class="p-6 sm:p-8">

    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Jadwal Praktik Mingguan</h1>
            <p class="text-gray-500 mt-1">Tinjau jadwal operasional Anda secara keseluruhan.</p>
        </div>
        
        <button @click="editModalOpen = true" class="bg-white border border-gray-300 text-gray-700 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-300 font-bold py-2.5 px-5 rounded-lg shadow-sm transition flex items-center justify-center w-full sm:w-auto">
            <i class="fa-solid fa-pen-to-square mr-2"></i> Edit Jadwal
        </button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50/80">
            <div class="flex items-center space-x-4">
                <button class="p-2 rounded-lg hover:bg-gray-200 text-gray-500 transition"><i class="fa-solid fa-chevron-left"></i></button>
                <h2 class="text-lg font-bold text-gray-800">Minggu Ini</h2>
                <button class="p-2 rounded-lg hover:bg-gray-200 text-gray-500 transition"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
            <div class="hidden sm:flex items-center space-x-2 text-sm text-gray-500 font-medium border bg-white px-3 py-1 rounded-full shadow-sm">
                <span class="w-3 h-3 rounded-full bg-blue-500 inline-block shadow-inner"></span> Praktik Reguler Aktif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-7 divide-y md:divide-y-0 md:divide-x divide-gray-200 min-h-[500px]">
            
            <template x-for="(day, index) in days" :key="index">
                <div class="flex flex-col h-full" :class="(day.name === 'Sabtu' || day.name === 'Minggu') ? 'bg-gray-50/50' : ''">
                    <div class="py-3 text-center border-b border-gray-200" :class="(day.name === 'Sabtu' || day.name === 'Minggu') ? 'bg-red-50/30' : 'bg-white'">
                        <span class="block text-xs font-bold uppercase tracking-wider" :class="(day.name === 'Sabtu' || day.name === 'Minggu') ? 'text-red-400' : 'text-gray-500'" x-text="day.name"></span>
                    </div>
                    
                    <div class="p-3 flex-1 relative flex flex-col">
                        
                        <template x-if="day.active">
                            <div class="bg-blue-50 border-l-4 border-blue-500 rounded-r-lg p-3 shadow-sm hover:shadow-md hover:bg-blue-100 transition cursor-pointer mt-2 group">
                                <p class="text-xs font-extrabold text-blue-700" x-text="day.start + ' - ' + day.end"></p>
                                <p class="text-sm font-bold text-gray-800 mt-1">Praktik Reguler</p>
                                <p class="text-xs text-blue-600/80 mt-1 flex items-center font-medium"><i class="fa-solid fa-stethoscope mr-1.5"></i> Poli Kulit</p>
                            </div>
                        </template>

                        <template x-if="!day.active">
                            <div class="flex-1 flex flex-col items-center justify-center text-center opacity-40 mt-10">
                                <i class="fa-solid fa-mug-hot text-gray-400 text-2xl mb-2"></i>
                                <p class="text-xs font-semibold text-gray-500">Tidak ada jadwal</p>
                            </div>
                        </template>

                    </div>
                </div>
            </template>

        </div>
    </div>

    <div x-show="editModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="editModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity"></div>

        <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
            <div x-show="editModalOpen" 
                 @click.away="editModalOpen = false"
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-gray-100">
                
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900" id="modal-title"><i class="fa-solid fa-sliders text-blue-600 mr-2"></i> Konfigurasi Jam Praktik</h3>
                    <button @click="editModalOpen = false" class="text-gray-400 hover:text-red-500 transition"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>

                <div class="px-6 py-6 space-y-4 max-h-[60vh] overflow-y-auto">
                    <template x-for="(day, index) in days" :key="index">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-xl border transition-colors duration-200" :class="day.active ? 'border-blue-200 bg-blue-50/50' : 'border-gray-200 bg-gray-50'">
                            
                            <div class="flex items-center mb-3 sm:mb-0 w-40">
                                <button @click="day.active = !day.active" :class="day.active ? 'bg-blue-600' : 'bg-gray-300'" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2">
                                    <span :class="day.active ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                                </button>
                                <span class="ml-3 font-bold" :class="day.active ? 'text-blue-900' : 'text-gray-500'" x-text="day.name"></span>
                            </div>

                            <div class="flex items-center space-x-3">
                                <input type="time" x-model="day.start" :disabled="!day.active" :class="day.active ? 'bg-white text-gray-900 border-gray-300' : 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed opacity-60'" class="px-3 py-2 rounded-lg border focus:ring-blue-500 focus:border-blue-500 shadow-sm w-32 text-center font-medium">
                                <span class="text-gray-400 font-medium">s/d</span>
                                <input type="time" x-model="day.end" :disabled="!day.active" :class="day.active ? 'bg-white text-gray-900 border-gray-300' : 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed opacity-60'" class="px-3 py-2 rounded-lg border focus:ring-blue-500 focus:border-blue-500 shadow-sm w-32 text-center font-medium">
                            </div>
                        </div>
                    </template>
                </div>

                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button @click="editModalOpen = false" type="button" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition">Batal</button>
                    <button @click="editModalOpen = false" type="button" class="inline-flex w-full justify-center rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow-md hover:bg-blue-700 sm:w-auto transition">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
// include '../../includes/footer.php'; 
?>