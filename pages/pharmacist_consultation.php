<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle   = 'Konsultasi Apoteker — CareSync';
$currentPage = 'marketplace'; // Tetap di lingkup Apotek/Marketplace

// Suntikkan Tailwind CSS khusus untuk halaman ini
$extraHead = '
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: { sans: [\'"Plus Jakarta Sans"\', \'sans-serif\'] },
                colors: {
                    primary: \'#1D4ED8\', primaryLight: \'#EFF6FF\',
                    accent: \'#10B981\', dark: \'#0F172A\', textSoft: \'#64748B\'
                },
                boxShadow: { 
                    \'sm\': \'0 2px 4px rgba(0,0,0,0.02)\',
                    \'md\': \'0 10px 25px -5px rgba(0,0,0,0.05)\',
                    \'floating\': \'0 20px 40px -15px rgba(29, 78, 216, 0.15)\' 
                }
            }
        }
    }
</script>
<style>
    body { background-color: #F8FAFC; margin: 0; }
    .consult-layout {
        display: grid; grid-template-columns: 350px 1fr;
        height: calc(100vh - 80px); overflow: hidden;
        padding: 24px; gap: 24px;
    }
    
    /* Mengkustomisasi scrollbar */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #94A3B8; }

    /* Chat Area Background */
    .chat-area::before {
        content: \'\'; position: absolute; inset: 0; opacity: 0.4; pointer-events: none;
        background-image: radial-gradient(#94A3B8 1px, transparent 1px); background-size: 24px 24px; z-index: 0;
    }
    
    .msg-row { display: flex; gap: 12px; align-items: flex-end; max-width: 75%; animation: slideUp 0.3s ease-out; }
    @keyframes slideUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .msg-row.mine { align-self: flex-end; flex-direction: row-reverse; }

    /* Responsif */
    @media (max-width: 768px) {
        .consult-layout { grid-template-columns: 1fr; padding: 10px; gap: 10px; border-radius: 0; }
        .consult-sidebar { display: none; }
    }
</style>
';

include __DIR__ . '/../includes/header.php';

/* * MOCK DATA KONSULTASI APOTEKER */
$pharmacists = [
    [
        'id' => 1, 
        'name' => 'apt. Rina Melati, S.Farm.', 
        'sipa' => 'SIPA. 199203/2021/001', 
        'time' => 'Baru saja', 
        'preview' => 'Cara pakainya dioleskan...', 
        'active' => true, 
        'init' => 'RM', 
        'col' => '#10B981' // Emerald Green untuk Apoteker
    ],
    [
        'id' => 2, 
        'name' => 'apt. Dimas Anggara, S.Farm.', 
        'sipa' => 'SIPA. 198805/2019/042', 
        'time' => 'Kemarin', 
        'preview' => 'Bisa ditebus setengah dulu kak', 
        'active' => false, 
        'init' => 'DA', 
        'col' => '#0EA898' // Teal
    ],
];
?>

<div class="consult-layout font-sans">

    <div class="consult-sidebar bg-white rounded-3xl border border-slate-100 shadow-md flex flex-col overflow-hidden z-10">
        <div class="p-6 border-b border-slate-50">
            <h4 class="text-xl font-extrabold text-slate-800 mb-4 m-0 flex items-center gap-2">
                <i class="fa-solid fa-pills text-accent"></i> Chat Apoteker
            </h4>
            <div class="relative">
                <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" placeholder="Cari obrolan..." class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-accent focus:ring-4 focus:ring-emerald-50 rounded-xl py-2.5 pl-11 pr-4 text-sm font-semibold text-slate-700 outline-none transition-all duration-300">
            </div>
        </div>
        
        <div class="flex-1 overflow-y-auto p-4 flex flex-col gap-2">
            <?php foreach ($pharmacists as $p): ?>
            <div class="consult-item p-4 rounded-2xl cursor-pointer transition-all duration-300 border border-transparent flex gap-3 items-center <?= $p['active'] ? 'bg-emerald-50 border-emerald-100 shadow-inner' : 'bg-white hover:bg-slate-50 hover:border-slate-200 shadow-sm' ?>" 
                 onclick="openSession(<?= $p['id'] ?>, '<?= addslashes($p['name']) ?>', '<?= $p['init'] ?>', '<?= $p['col'] ?>', '<?= $p['sipa'] ?>', this)">
                
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-base font-extrabold flex-shrink-0 shadow-sm" style="background: <?= $p['col'] ?>;">
                    <?= $p['init'] ?>
                </div>
                
                <div class="flex-1 min-w-0 flex flex-col justify-center">
                    <div class="flex justify-between items-center mb-0.5">
                        <span class="font-bold text-slate-800 text-sm truncate max-w-[140px]"><?= $p['name'] ?></span>
                        <span class="text-[10px] font-bold text-slate-400 flex-shrink-0 ml-1"><?= $p['time'] ?></span>
                    </div>
                    <div class="text-[10px] font-bold text-emerald-600 bg-emerald-100/50 px-1.5 py-0.5 rounded w-fit mb-1 border border-emerald-200/50">Apoteker</div>
                    <div class="text-xs font-medium text-slate-500 truncate"><?= $p['preview'] ?></div>
                </div>
                <?php if ($p['active']): ?><div class="w-2.5 h-2.5 rounded-full bg-accent flex-shrink-0 shadow-[0_0_0_3px_white]"></div><?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="chat-area bg-white rounded-3xl shadow-md border border-slate-100 flex flex-col relative overflow-hidden">
        
        <div class="bg-white/90 backdrop-blur-md p-5 border-b border-slate-100 flex items-center gap-4 relative z-10">
            <div id="chat-avatar" class="w-12 h-12 rounded-xl bg-accent flex items-center justify-center text-white text-base font-extrabold flex-shrink-0 shadow-[0_4px_12px_rgba(16,185,129,0.3)]">RM</div>
            <div class="flex-1">
                <div id="chat-name" class="font-extrabold text-slate-800 text-[17px] mb-0.5">apt. Rina Melati, S.Farm.</div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-accent shadow-[0_0_0_3px_#D1FAE5]"></span>
                    <span class="text-[11px] font-bold text-slate-500">Online · <span id="chat-sipa">SIPA. 199203/2021/001</span></span>
                </div>
            </div>
            <div class="flex gap-2">
                <button class="w-10 h-10 rounded-full bg-slate-50 text-slate-500 hover:bg-slate-100 hover:text-primary flex items-center justify-center border-none cursor-pointer transition-colors duration-200">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-6 md:p-8 flex flex-col gap-5 relative z-[1]" id="chat-messages">
            
            <div class="text-center py-2">
                <span class="text-[10px] font-bold bg-white text-slate-400 px-4 py-1.5 rounded-full shadow-sm border border-slate-100 uppercase tracking-wider">Hari ini</span>
            </div>

            <div class="msg-row">
                <div class="w-10 h-10 rounded-xl bg-accent flex items-center justify-center text-white text-xs font-extrabold flex-shrink-0 shadow-sm" id="msg-avatar-pharmacist">RM</div>
                <div>
                    <div class="bg-white text-slate-800 p-4 text-[13px] font-medium leading-relaxed max-w-full break-words shadow-sm rounded-[20px_20px_20px_4px] border border-slate-100">
                        Halo kak Jovita! Saya apoteker Rina. Ada yang bisa saya bantu terkait resep atau obat yang ingin ditebus?
                    </div>
                    <div class="text-[10px] text-slate-400 mt-1.5 font-bold ml-1">10:45</div>
                </div>
            </div>

            <div class="msg-row mine">
                <div>
                    <div class="bg-primary text-white p-4 text-[13px] font-medium leading-relaxed max-w-full break-words shadow-[0_4px_12px_rgba(29,78,216,0.25)] rounded-[20px_20px_4px_20px]">
                        Halo kak, saya mau tanya soal resep salep Benzolac CL. Itu cara pakainya gmn ya? Apakah boleh ditimpa skincare lain?
                    </div>
                    <div class="text-[10px] text-slate-400 mt-1.5 font-bold mr-1 text-right">10:46 <i class="fa-solid fa-check-double text-blue-400 ml-1"></i></div>
                </div>
            </div>

            <div class="msg-row" id="typing-indicator" style="display:none;">
                <div class="w-10 h-10 rounded-xl bg-accent flex items-center justify-center text-white text-xs font-extrabold flex-shrink-0 shadow-sm">RM</div>
                <div class="bg-white p-4 shadow-sm rounded-[20px_20px_20px_4px] border border-slate-100 flex gap-1.5 items-center h-[52px]">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-[typingDot_1.4s_infinite_0s]"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-[typingDot_1.4s_infinite_0.2s]"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-[typingDot_1.4s_infinite_0.4s]"></span>
                </div>
            </div>
            
        </div>

        <div class="bg-white/90 backdrop-blur-md p-4 md:p-6 border-t border-slate-100 flex gap-3 items-end relative z-10">
            <button class="w-12 h-12 rounded-full bg-slate-50 hover:bg-slate-100 text-slate-500 flex items-center justify-center border-none cursor-pointer transition-colors duration-200 flex-shrink-0" title="Lampirkan Resep/Foto">
                <i class="fa-solid fa-paperclip"></i>
            </button>
            <textarea id="msg-input" class="flex-1 resize-none bg-slate-50 border border-slate-200 rounded-3xl p-3.5 px-5 font-sans text-[13px] font-medium text-slate-800 outline-none transition-all focus:bg-white focus:border-accent focus:ring-4 focus:ring-emerald-50 max-h-[120px]" rows="1" placeholder="Ketik pesan untuk apoteker..." onkeydown="handleChatKey(event)" oninput="autoResize(this)"></textarea>
            <button onclick="sendMessage()" class="w-12 h-12 rounded-full bg-accent hover:bg-emerald-600 text-white flex items-center justify-center border-none cursor-pointer shadow-md active:scale-95 transition-all duration-300 flex-shrink-0">
                <i class="fa-solid fa-paper-plane text-sm ml-[-2px]"></i>
            </button>
        </div>

    </div>
</div>

<style>
@keyframes typingDot { 0%, 60%, 100% { transform: translateY(0); } 30% { transform: translateY(-4px); } }
</style>

<script>
// Pindah Sesi Chat Apoteker
function openSession(id, name, initials, color, sipa, el) {
    // Reset status aktif di UI sidebar
    document.querySelectorAll('.consult-item').forEach(item => {
        item.classList.remove('bg-emerald-50', 'border-emerald-100', 'shadow-inner');
        item.classList.add('bg-white', 'hover:bg-slate-50', 'hover:border-slate-200', 'shadow-sm');
        const dot = item.querySelector('.w-2\\.5');
        if(dot) dot.remove();
    });
    
    // Set item yang diklik jadi aktif
    el.classList.remove('bg-white', 'hover:bg-slate-50', 'hover:border-slate-200', 'shadow-sm');
    el.classList.add('bg-emerald-50', 'border-emerald-100', 'shadow-inner');
    el.insertAdjacentHTML('beforeend', '<div class="w-2.5 h-2.5 rounded-full bg-accent flex-shrink-0 shadow-[0_0_0_3px_white]"></div>');

    // Update Header Chat
    const avatar = document.getElementById('chat-avatar');
    avatar.textContent = initials;
    avatar.style.background = color;
    avatar.style.boxShadow = `0 4px 12px ${color}60`; // Glow effect
    document.getElementById('chat-name').textContent = name;
    document.getElementById('chat-sipa').textContent = sipa;

    // Update Avatar di dalam bubble chat
    document.getElementById('msg-avatar-pharmacist').textContent = initials;
    document.getElementById('msg-avatar-pharmacist').style.background = color;
}

function handleChatKey(e) {
    if (e.key === 'Enter' && !e.shiftKey) { 
        e.preventDefault(); 
        sendMessage(); 
    }
}

function autoResize(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 120) + 'px';
}

// Auto-reply Bot (Khusus Apoteker)
const pharmacistReplies = [
    'Untuk Benzolac CL, pastikan wajah sudah dibersihkan dulu ya kak. Oleskan tipis saja hanya di titik jerawatnya.',
    'Sebaiknya beri jeda sekitar 15-20 menit sebelum menimpa dengan skincare lain (seperti pelembap). Hindari pemakaian serum yang mengandung AHA/BHA atau Retinol bersamaan dengan Benzolac ini ya.',
    'Iya benar kak. Jika kulit terasa sangat kering atau mengelupas, pemakaiannya bisa dikurangi jadi 2 hari sekali saja.',
    'Ada lagi yang ingin ditanyakan terkait obat lainnya kak Jovita?'
];
let replyIdx = 0;

function sendMessage() {
    const input = document.getElementById('msg-input');
    const text  = input.value.trim();
    if (!text) return;

    const msgs = document.getElementById('chat-messages');
    const typing = document.getElementById('typing-indicator');

    // Inject pesan user (Jovita)
    const myMsg = document.createElement('div');
    myMsg.className = 'msg-row mine';
    myMsg.innerHTML = `
        <div>
            <div class="bg-primary text-white p-4 text-[13px] font-medium leading-relaxed max-w-full break-words shadow-[0_4px_12px_rgba(29,78,216,0.25)] rounded-[20px_20px_4px_20px]">
                ${escHtml(text)}
            </div>
            <div class="text-[10px] text-slate-400 mt-1.5 font-bold mr-1 text-right">${now()} <i class="fa-solid fa-check text-slate-300 ml-1"></i></div>
        </div>`;
    msgs.insertBefore(myMsg, typing);
    
    input.value = '';
    input.style.height = 'auto';
    scrollBottom();

    // Simulasi Apoteker Mengetik & Membalas
    setTimeout(() => {
        typing.style.display = 'flex';
        scrollBottom();
        
        // Ubah centang satu jadi centang dua biru
        const ticks = myMsg.querySelectorAll('.fa-check');
        ticks.forEach(t => {
            t.classList.remove('fa-check', 'text-slate-300');
            t.classList.add('fa-check-double', 'text-blue-400');
        });

        setTimeout(() => {
            typing.style.display = 'none';
            const reply = document.createElement('div');
            reply.className = 'msg-row';
            
            // Ambil inisial & warna aktif saat ini dari header
            const currentInit = document.getElementById('chat-avatar').textContent;
            const currentCol = document.getElementById('chat-avatar').style.background;

            reply.innerHTML = `
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white text-xs font-extrabold flex-shrink-0 shadow-sm" style="background:${currentCol}">${currentInit}</div>
                <div>
                    <div class="bg-white text-slate-800 p-4 text-[13px] font-medium leading-relaxed max-w-full break-words shadow-sm rounded-[20px_20px_20px_4px] border border-slate-100">
                        ${pharmacistReplies[replyIdx++ % pharmacistReplies.length]}
                    </div>
                    <div class="text-[10px] text-slate-400 mt-1.5 font-bold ml-1">${now()}</div>
                </div>`;
            msgs.insertBefore(reply, typing);
            scrollBottom();
        }, 1500 + Math.random() * 1000); // Random delay 1.5s - 2.5s
    }, 500);
}

function scrollBottom() {
    const msgs = document.getElementById('chat-messages');
    msgs.scrollTop = msgs.scrollHeight;
}
function escHtml(s) { return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
function now() { return new Date().toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'}); }

// Scroll ke bawah saat pertama kali diload
document.addEventListener('DOMContentLoaded', scrollBottom);
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>