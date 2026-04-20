<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Staf Internal - CareSync</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased h-screen overflow-hidden flex">

    <div class="hidden lg:flex lg:w-1/2 bg-[#0f172a] relative items-center justify-center flex-col overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80')] bg-cover bg-center opacity-20 mix-blend-luminosity"></div>
        
        <div class="absolute inset-0 bg-gradient-to-b from-blue-900/50 to-[#0f172a]/90"></div>

        <div class="relative z-10 text-center px-12 max-w-2xl">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-blue-600/20 border border-blue-500/30 backdrop-blur-sm mb-8 shadow-2xl">
                <i class="fa-solid fa-notes-medical text-4xl text-blue-400"></i>
            </div>
            <h1 class="text-4xl font-extrabold text-white tracking-tight mb-4">CareSync <span class="text-blue-500">Internal</span></h1>
            <p class="text-lg text-blue-100/80 font-medium leading-relaxed mb-10">
                Sistem Informasi Manajemen Terpadu untuk Pelayanan Medis, Apotek, dan Administrasi Operasional.
            </p>
            
            <div class="inline-flex items-center gap-2 bg-black/30 border border-white/10 px-4 py-2 rounded-full text-xs font-bold text-gray-300 backdrop-blur-md">
                <i class="fa-solid fa-shield-halved text-green-400"></i> Area Terbatas (Restricted Access)
            </div>
        </div>
    </div>

    <div class="w-full lg:w-1/2 flex items-center justify-center bg-white relative">
        
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 rounded-full bg-blue-50 opacity-50 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-indigo-50 opacity-50 blur-3xl"></div>

        <div class="w-full max-w-md px-8 py-10 relative z-10">
            <div class="lg:hidden flex items-center justify-center mb-8">
                <i class="fa-solid fa-notes-medical text-blue-600 text-3xl mr-2"></i>
                <span class="text-2xl font-bold text-gray-900 tracking-tight">Care<span class="text-blue-600">Sync</span></span>
            </div>

            <div class="mb-10 text-center lg:text-left">
                <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-2">Portal Pegawai</h2>
                <p class="text-sm text-gray-500 font-medium">Silakan masuk menggunakan kredensial yang diberikan oleh sistem HRD.</p>
            </div>

            <form action="proses_login_staf.php" method="POST" class="space-y-6">
                
                <div>
                    <label for="email" class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide text-xs">Email Institusi / ID Pegawai</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-user-tie text-gray-400"></i>
                        </div>
                        <input type="text" id="email" name="email" required
                               class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition shadow-sm"
                               placeholder="nama@caresync.com">
                    </div>
                </div>

                <div x-data="{ show: false }">
                    <div class="flex justify-between items-center mb-2">
                        <label for="password" class="block text-sm font-bold text-gray-700 uppercase tracking-wide text-xs">Kata Sandi</label>
                        <a href="#" class="text-xs font-bold text-blue-600 hover:text-blue-800 transition">Lupa Sandi?</a>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-gray-400"></i>
                        </div>
                        <input :type="show ? 'text' : 'password'" id="password" name="password" required
                               class="w-full pl-11 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition shadow-sm"
                               placeholder="••••••••">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition focus:outline-none">
                            <i class="fa-solid" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center">
                    <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded cursor-pointer">
                    <label for="remember-me" class="ml-2 block text-sm text-gray-600 cursor-pointer select-none">
                        Ingat sesi saya di perangkat ini
                    </label>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <i class="fa-solid fa-right-to-bracket mr-2 mt-0.5"></i> Masuk Sistem
                    </button>
                </div>
            </form>

            <div class="mt-10 border-t border-gray-100 pt-6">
                <p class="text-center text-xs text-gray-400">
                    Sistem ini dilindungi oleh rekam pantau aktivitas.<br>
                    Butuh bantuan? <a href="#" class="font-bold text-gray-500 hover:text-blue-600 transition">Hubungi IT Support</a>.
                </p>
            </div>
            
        </div>
    </div>
</body>
</html>