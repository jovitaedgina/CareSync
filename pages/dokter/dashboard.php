<?php
// pages/dokter/dashboard.php
include '../../includes/header_dokter.php'; 
?>

<div class="p-6 sm:p-8 space-y-8">

    <div>
        <h1 class="text-2xl font-bold text-gray-900">Selamat bertugas, dr. Susanti! 👋</h1>
        <p class="text-gray-500 mt-1">Berikut adalah ringkasan aktivitas praktik Anda hari ini.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-2xl">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Antrean Hari Ini</p>
                <h3 class="text-2xl font-bold text-gray-900">12 <span class="text-sm font-normal text-gray-500">Pasien</span></h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-green-100 flex items-center justify-center text-green-600 text-2xl">
                <i class="fa-solid fa-check-double"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Konsultasi Selesai</p>
                <h3 class="text-2xl font-bold text-gray-900">8 <span class="text-sm font-normal text-gray-500">Sesi</span></h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 text-2xl">
                <i class="fa-solid fa-prescription-bottle-medical"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Menunggu Resep</p>
                <h3 class="text-2xl font-bold text-gray-900">2 <span class="text-sm font-normal text-gray-500">Tugas</span></h3>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        
        <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h2 class="text-lg font-bold text-gray-800"><i class="fa-solid fa-clipboard-list text-blue-500 mr-2"></i> Antrean Pasien Hari Ini</h2>
                <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-800">Lihat Semua</a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white text-gray-400 text-sm border-b border-gray-100">
                            <th class="px-6 py-4 font-medium">Jam</th>
                            <th class="px-6 py-4 font-medium">Nama Pasien</th>
                            <th class="px-6 py-4 font-medium">Status</th>
                            <th class="px-6 py-4 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm font-semibold text-gray-700">10:30</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">JE</div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-800">Jovita Edgina</p>
                                        <p class="text-xs text-gray-500">Jerawat meradang</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700 border border-yellow-200">Menunggu</span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="consultation_room.php" class="inline-flex items-center justify-center px-3 py-1.5 bg-blue-600 text-white text-xs font-bold rounded-lg hover:bg-blue-700 transition">
                                    <i class="fa-solid fa-comment-medical mr-1.5"></i> Mulai
                                </a>
                            </td>
                        </tr>

                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm font-medium text-gray-500">09:45</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name=Farras+Faishal&background=random" alt="Avatar" class="w-8 h-8 rounded-full">
                                    <div>
                                        <p class="text-sm font-bold text-gray-800">Farras Faishal</p>
                                        <p class="text-xs text-gray-500">Eksim kumat</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 border border-green-200">Selesai</span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="prescription_form.php" class="inline-flex items-center justify-center px-3 py-1.5 bg-white text-blue-600 border border-blue-200 text-xs font-bold rounded-lg hover:bg-blue-50 transition">
                                    <i class="fa-solid fa-pills mr-1.5"></i> Buat Resep
                                </a>
                            </td>
                        </tr>
                        
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm font-medium text-gray-500">09:00</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-bold text-xs">ZR</div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-800">Zahra Ramadhani</p>
                                        <p class="text-xs text-gray-500">Kontrol rutin</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 border border-green-200">Selesai</span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="patient_history.php" class="inline-flex items-center justify-center px-3 py-1.5 bg-gray-100 text-gray-600 text-xs font-bold rounded-lg hover:bg-gray-200 transition">
                                    Lihat Riwayat
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="xl:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <h2 class="text-lg font-bold text-gray-800"><i class="fa-regular fa-clock text-orange-500 mr-2"></i> Jadwal Terdekat</h2>
            </div>
            
            <div class="p-6">
                <div class="relative border-l-2 border-gray-100 ml-3 space-y-8">
                    
                    <div class="relative pl-6">
                        <div class="absolute w-4 h-4 bg-blue-500 rounded-full -left-[9px] top-1 ring-4 ring-white shadow"></div>
                        <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                            <span class="text-xs font-bold text-blue-600 bg-white px-2 py-1 rounded border border-blue-100 mb-2 inline-block">10:30 WIB</span>
                            <h4 class="text-sm font-bold text-gray-900 mt-1">Jovita Edgina</h4>
                            <p class="text-xs text-gray-500 mt-1 flex items-center"><i class="fa-solid fa-video text-gray-400 mr-1.5"></i> Video Call</p>
                        </div>
                    </div>

                    <div class="relative pl-6">
                        <div class="absolute w-3 h-3 bg-gray-300 rounded-full -left-[7px] top-1.5 ring-4 ring-white"></div>
                        <div>
                            <span class="text-xs font-bold text-gray-500">11:15 WIB</span>
                            <h4 class="text-sm font-bold text-gray-800 mt-1">Fatcku Rochman</h4>
                            <p class="text-xs text-gray-500 mt-1 flex items-center"><i class="fa-solid fa-message text-gray-400 mr-1.5"></i> Chat Konsultasi</p>
                        </div>
                    </div>

                    <div class="relative pl-6">
                        <div class="absolute w-3 h-3 bg-gray-300 rounded-full -left-[7px] top-1.5 ring-4 ring-white"></div>
                        <div>
                            <span class="text-xs font-bold text-gray-500">13:00 WIB</span>
                            <h4 class="text-sm font-bold text-gray-800 mt-1">Istirahat Makan Siang</h4>
                            <p class="text-xs text-gray-400 mt-1">Sistem akan otomatis offline.</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

