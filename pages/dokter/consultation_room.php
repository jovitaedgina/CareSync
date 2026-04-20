<?php
// pages/dokter/consultation_room.php
include '../../includes/header_dokter.php'; 
?>

<div class="flex h-[calc(100vh-64px)] overflow-hidden" x-data="{ 
    videoCallOpen: false,
    activeTab: 'aktif',
    searchQuery: '',
    activeChatId: 1,
    newMessage: '',
    videoNewMessage: '',
    
    // Data Dummy Pasien
    chats: [
        { id: 1, name: 'Jovita Edgina', status: 'aktif', time: '10:35', lastMsg: 'Baik dok, untuk salepnya dipakai...', avatar: 'https://ui-avatars.com/api/?name=Jovita+Edgina&background=0D8ABC&color=fff', age: '20 Thn', condition: 'Acne Vulgaris' },
        { id: 2, name: 'Zahra Ramadhani', status: 'antrean', time: 'Menunggu', lastMsg: 'Keluhan: Kontrol rutin mingguan', avatar: 'https://ui-avatars.com/api/?name=Zahra+Ramadhani&background=random', age: '21 Thn', condition: 'Pemeriksaan Rutin' },
        { id: 3, name: 'Farras Faishal', status: 'antrean', time: 'Menunggu', lastMsg: 'Keluhan: Gatal di sela jari', avatar: 'https://ui-avatars.com/api/?name=Farras+Faishal&background=random', age: '21 Thn', condition: 'Eksim' }
    ],
    
    // Data Dummy Riwayat Pesan
    messages: {
        1: [
            { sender: 'system', text: 'Sesi Konsultasi Dimulai (10:30 WIB)', time: '' },
            { sender: 'patient', text: 'Selamat pagi dok. Jerawat saya di bagian pipi kanan semakin meradang dan terasa gatal sejak 2 hari lalu.', time: '10:32', type: 'text' },
            { sender: 'patient', text: 'Ini dok fotonya kondisinya sekarang.', time: '10:33', type: 'image', imgUrl: 'https://placehold.co/400x300/e2e8f0/64748b?text=Foto+Keluhan+Pasien' },
            { sender: 'doctor', text: 'Pagi Jovita. Dari fotonya terlihat ada peradangan aktif. Untuk sementara hindari makanan manis.', time: '10:34', type: 'text' },
            { sender: 'patient', text: 'Baik dok, untuk salepnya dipakai berapa kali sehari ya nanti?', time: '10:35', type: 'text' }
        ],
        2: [
            { sender: 'system', text: 'Pasien masuk ke ruang tunggu.', time: '' },
            { sender: 'patient', text: 'Halo dok, saya mau kontrol rutin mingguan sesuai jadwal.', time: '09:00', type: 'text' }
        ],
        3: [
            { sender: 'system', text: 'Pasien masuk ke ruang tunggu.', time: '' },
            { sender: 'patient', text: 'Dok, sela jari saya gatal-gatal lagi sejak kemarin malam.', time: '09:15', type: 'text' }
        ]
    },

    get filteredChats() {
        return this.chats.filter(chat => 
            chat.status === this.activeTab && 
            chat.name.toLowerCase().includes(this.searchQuery.toLowerCase())
        );
    },

    get activeChatData() {
        return this.chats.find(c => c.id === this.activeChatId) || this.chats[0];
    },

    sendMessage(isFromVideo = false) {
        let msgText = isFromVideo ? this.videoNewMessage : this.newMessage;
        if (msgText.trim() === '') return;
        
        const now = new Date();
        const timeStr = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
        
        this.messages[this.activeChatId].push({
            sender: 'doctor',
            text: msgText,
            time: timeStr,
            type: 'text'
        });
        
        const chatIndex = this.chats.findIndex(c => c.id === this.activeChatId);
        if(chatIndex !== -1) {
            this.chats[chatIndex].lastMsg = msgText;
            this.chats[chatIndex].time = timeStr;
        }

        if(isFromVideo) this.videoNewMessage = '';
        else this.newMessage = '';
        
        this.$nextTick(() => {
            if(this.$refs.chatContainer) this.$refs.chatContainer.scrollTop = this.$refs.chatContainer.scrollHeight;
            if(this.$refs.videoChatContainer) this.$refs.videoChatContainer.scrollTop = this.$refs.videoChatContainer.scrollHeight;
        });
    }
}">

    <div class="w-full md:w-80 lg:w-96 bg-white border-r border-gray-200 flex flex-col flex-shrink-0">
        <div class="p-4 border-b border-gray-100">
            <div class="relative">
                <input x-model="searchQuery" type="text" placeholder="Cari nama pasien..." class="w-full bg-gray-50 text-sm text-gray-700 border border-gray-200 rounded-full pl-10 pr-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-2.5 text-gray-400"></i>
            </div>
        </div>

        <div class="flex text-sm font-medium border-b border-gray-100 bg-gray-50/50">
            <button @click="activeTab = 'aktif'" :class="activeTab === 'aktif' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700'" class="flex-1 py-3 transition">
                Aktif <span class="bg-blue-100 text-blue-600 text-[10px] px-2 py-0.5 rounded-full ml-1" x-text="chats.filter(c => c.status === 'aktif').length"></span>
            </button>
            <button @click="activeTab = 'antrean'" :class="activeTab === 'antrean' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700'" class="flex-1 py-3 transition">
                Antrean <span class="bg-gray-200 text-gray-600 text-[10px] px-2 py-0.5 rounded-full ml-1" x-text="chats.filter(c => c.status === 'antrean').length"></span>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto">
            <template x-if="filteredChats.length === 0">
                <div class="text-center text-gray-400 text-sm mt-10">Tidak ada pasien ditemukan.</div>
            </template>

            <template x-for="chat in filteredChats" :key="chat.id">
                <div @click="activeChatId = chat.id" 
                     :class="activeChatId === chat.id ? 'bg-blue-50/50 border-l-4 border-l-blue-600' : 'border-l-4 border-l-transparent hover:bg-gray-50 cursor-pointer'"
                     class="flex items-center p-4 border-b border-gray-50 transition">
                    <div class="relative">
                        <img :src="chat.avatar" class="w-12 h-12 rounded-full object-cover" :class="chat.status === 'antrean' ? 'grayscale opacity-70' : ''">
                        <template x-if="chat.status === 'aktif'">
                            <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                        </template>
                    </div>
                    <div class="ml-4 flex-1 overflow-hidden">
                        <div class="flex justify-between items-baseline">
                            <h4 class="text-sm font-bold text-gray-900 truncate" x-text="chat.name"></h4>
                            <span class="text-[10px] font-bold" :class="chat.status === 'aktif' ? 'text-blue-600' : 'text-gray-400'" x-text="chat.time"></span>
                        </div>
                        <p class="text-xs text-gray-500 truncate mt-0.5" x-text="chat.lastMsg"></p>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <div class="flex-1 flex flex-col bg-[#F8FAFC]">
        <div class="h-16 bg-white border-b border-gray-200 px-6 flex items-center justify-between shadow-sm z-10">
            <div class="flex items-center gap-4">
                <img :src="activeChatData.avatar" class="w-10 h-10 rounded-full">
                <div>
                    <h2 class="text-sm font-bold text-gray-900" x-text="activeChatData.name"></h2>
                    <p class="text-xs text-gray-500 flex items-center gap-2">
                        <span><i class="fa-solid fa-venus text-pink-400"></i> <span x-text="activeChatData.age"></span></span>
                        <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                        <span x-text="activeChatData.condition"></span>
                    </p>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <div class="bg-red-50 text-red-600 px-3 py-1.5 rounded-lg border border-red-100 flex items-center text-sm font-mono font-bold shadow-sm">
                    <i class="fa-regular fa-clock mr-2"></i> 24:15
                </div>
                <button @click="videoCallOpen = true" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 text-gray-600 hover:bg-blue-100 hover:text-blue-600 transition" title="Mulai Video Call">
                    <i class="fa-solid fa-video"></i>
                </button>
                <a href="prescription_form.php" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-4 py-2 rounded-lg shadow-md transition flex items-center">
                    Akhiri & Buat Resep
                </a>
            </div>
        </div>

        <div x-ref="chatContainer" class="flex-1 overflow-y-auto p-6 space-y-6 scroll-smooth">
            <template x-for="(msg, index) in messages[activeChatId]" :key="index">
                <div>
                    <template x-if="msg.sender === 'system'">
                        <div class="flex justify-center">
                            <span class="bg-yellow-50 border border-yellow-200 text-yellow-800 text-[10px] font-bold px-3 py-1 rounded-full shadow-sm uppercase tracking-wider" x-text="msg.text"></span>
                        </div>
                    </template>

                    <template x-if="msg.sender === 'patient'">
                        <div class="flex items-end gap-2">
                            <img :src="activeChatData.avatar" class="w-8 h-8 rounded-full mb-1">
                            <div class="bg-white border border-gray-200 p-3 rounded-2xl rounded-bl-none shadow-sm max-w-md">
                                <template x-if="msg.type === 'image'">
                                    <img :src="msg.imgUrl" class="rounded-xl mb-2 w-64 object-cover border border-gray-200">
                                </template>
                                <p class="text-sm text-gray-800" x-text="msg.text"></p>
                                <span class="text-[10px] text-gray-400 mt-1 block text-right" x-text="msg.time"></span>
                            </div>
                        </div>
                    </template>

                    <template x-if="msg.sender === 'doctor'">
                        <div class="flex items-end justify-end gap-2">
                            <div class="bg-blue-600 text-white p-3 rounded-2xl rounded-br-none shadow-sm max-w-md">
                                <p class="text-sm" x-text="msg.text"></p>
                                <span class="text-[10px] text-blue-200 mt-1 block text-right">
                                    <span x-text="msg.time"></span> <i class="fa-solid fa-check-double ml-1 text-blue-300"></i>
                                </span>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <div class="bg-white p-4 border-t border-gray-200">
            <div class="flex items-end gap-3 bg-gray-50 border border-gray-200 rounded-2xl p-2 focus-within:ring-2 focus-within:ring-blue-500 transition shadow-inner">
                <button class="p-2 text-gray-400 hover:text-blue-600 transition rounded-full hover:bg-white shrink-0"><i class="fa-solid fa-paperclip text-lg"></i></button>
                <textarea x-model="newMessage" @keydown.enter.prevent="sendMessage(false)" rows="1" placeholder="Ketik balasan untuk pasien..." class="w-full bg-transparent border-none focus:ring-0 text-sm resize-none py-2 max-h-32 text-gray-700 placeholder-gray-400" style="min-height: 40px;"></textarea>
                <button @click="sendMessage(false)" class="w-10 h-10 bg-blue-600 hover:bg-blue-700 text-white rounded-full flex items-center justify-center shadow-md transition shrink-0"><i class="fa-solid fa-paper-plane ml-[-2px]"></i></button>
            </div>
        </div>
    </div>

    <div x-show="videoCallOpen" style="display: none;" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="fixed inset-0 z-[100] flex bg-[#0f172a] text-white"> <div class="flex-1 flex flex-col relative">
            
            <div class="h-16 flex items-center justify-between px-6 bg-gradient-to-b from-[#020617]/80 to-transparent absolute top-0 w-full z-20">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded bg-blue-600 flex items-center justify-center font-bold text-xs shadow-lg">DS</div>
                    <div>
                        <h2 class="text-sm font-bold" x-text="'Pasien: ' + activeChatData.name"></h2>
                        <p class="text-[10px] text-gray-300 flex items-center"><i class="fa-solid fa-circle text-green-500 text-[6px] mr-1.5"></i> Terhubung secara aman</p>
                    </div>
                </div>
                <div class="text-xs font-bold text-gray-300 bg-black/30 px-3 py-1 rounded">HD</div>
            </div>

            <div class="flex-1 relative flex items-center justify-center bg-[#0f172a] overflow-hidden">
                <div class="flex flex-col items-center justify-center text-center z-10">
                    <div class="w-20 h-20 rounded-full border border-gray-600 bg-[#1e293b]/50 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-video text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-300" x-text="'Menghubungkan ke ' + activeChatData.name + '...'"></h3>
                </div>

                <div class="absolute bottom-24 right-6 w-48 h-32 bg-gray-900 rounded-xl border border-gray-700 overflow-hidden shadow-2xl z-20">
                    <img src="https://ui-avatars.com/api/?name=Dokter+Susanti&background=0D8ABC&color=fff&size=300" alt="Dokter" class="w-full h-full object-cover opacity-80">
                </div>
            </div>

            <div class="h-24 bg-gradient-to-t from-[#020617] to-transparent absolute bottom-0 w-full flex items-center justify-center gap-4 z-20 pb-4">
                <button class="w-12 h-12 rounded-full bg-[#1e293b] hover:bg-gray-700 text-white flex items-center justify-center transition border border-gray-600"><i class="fa-solid fa-microphone"></i></button>
                <button class="w-12 h-12 rounded-full bg-[#1e293b] hover:bg-gray-700 text-white flex items-center justify-center transition border border-gray-600"><i class="fa-solid fa-video"></i></button>
                <button class="w-12 h-12 rounded-full bg-[#1e293b] hover:bg-gray-700 text-white flex items-center justify-center transition border border-gray-600"><i class="fa-solid fa-desktop"></i></button>
                <button @click="videoCallOpen = false" class="w-12 h-12 rounded-full bg-red-600 hover:bg-red-700 text-white flex items-center justify-center transition shadow-lg ml-2"><i class="fa-solid fa-phone-slash"></i></button>
            </div>
        </div>

        <div class="w-80 lg:w-96 bg-[#1e293b] border-l border-gray-800 flex flex-col flex-shrink-0 relative z-30 shadow-2xl">
            <div class="p-4 border-b border-gray-800 flex justify-between items-center bg-[#0f172a]/50">
                <h3 class="text-sm font-bold text-white">Live Chat</h3>
                <button @click="videoCallOpen = false" class="text-gray-400 hover:text-white transition"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <div x-ref="videoChatContainer" class="flex-1 overflow-y-auto p-4 space-y-4 scroll-smooth">
                <div class="flex justify-center mb-4">
                    <span class="text-[10px] bg-gray-800/50 text-gray-400 px-3 py-1 rounded-full">Video call dimulai</span>
                </div>
                
                <template x-for="(msg, index) in messages[activeChatId]" :key="index">
                    <div>
                        <template x-if="msg.sender === 'patient'">
                            <div class="flex items-start gap-2">
                                <img :src="activeChatData.avatar" class="w-6 h-6 rounded-full mt-1">
                                <div class="bg-gray-800 border border-gray-700 p-2.5 rounded-xl rounded-tl-none max-w-[85%]">
                                    <p class="text-xs text-gray-200" x-text="msg.text"></p>
                                    <span class="text-[9px] text-gray-500 mt-1 block text-right" x-text="msg.time"></span>
                                </div>
                            </div>
                        </template>

                        <template x-if="msg.sender === 'doctor'">
                            <div class="flex items-start justify-end gap-2">
                                <div class="bg-blue-600 p-2.5 rounded-xl rounded-tr-none max-w-[85%]">
                                    <p class="text-xs text-white" x-text="msg.text"></p>
                                    <span class="text-[9px] text-blue-200 mt-1 block text-right" x-text="msg.time"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <div class="p-4 border-t border-gray-800 bg-[#0f172a]/50">
                <div class="relative flex items-center">
                    <input x-model="videoNewMessage" @keydown.enter.prevent="sendMessage(true)" type="text" placeholder="Tulis pesan..." class="w-full bg-[#0f172a] border border-gray-700 text-sm text-white rounded-full pl-4 pr-10 py-2.5 focus:outline-none focus:border-blue-500 transition placeholder-gray-500">
                    <button @click="sendMessage(true)" class="absolute right-2 w-8 h-8 rounded-full text-gray-400 hover:text-blue-500 flex items-center justify-center transition"><i class="fa-solid fa-paper-plane text-xs"></i></button>
                </div>
            </div>
        </div>

    </div>
    </div>

<?php
// include '../../includes/footer.php'; 
?>