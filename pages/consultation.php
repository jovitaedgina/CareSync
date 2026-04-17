<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle   = 'Konsultasi — CareSync';
$currentPage = 'booking';
include __DIR__ . '/../includes/header.php';
?>

<style>
  .consult-layout {
    display: grid; grid-template-columns: 300px 1fr;
    height: calc(100vh - var(--nav-h) - 2px); overflow: hidden;
  }

  /* Sidebar daftar konsultasi */
  .consult-sidebar {
    border-right: 1px solid var(--gray-200); background: #fff;
    display: flex; flex-direction: column; overflow: hidden;
  }
  .sidebar-header { padding: 20px; border-bottom: 1px solid var(--gray-100); }
  .consult-list { overflow-y: auto; flex: 1; }
  .consult-item {
    padding: 14px 20px; cursor: pointer; transition: background .15s;
    border-bottom: 1px solid var(--gray-50); display: flex; gap: 10px;
  }
  .consult-item:hover   { background: var(--gray-50); }
  .consult-item.active  { background: var(--primary-light); }
  .consult-item-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--primary); flex-shrink: 0; margin-top: 6px; }

  /* Chat area */
  .chat-area { display: flex; flex-direction: column; background: var(--gray-50); overflow: hidden; }
  .chat-header {
    background: #fff; padding: 14px 24px; border-bottom: 1px solid var(--gray-200);
    display: flex; align-items: center; gap: 14px;
  }
  .chat-messages { flex: 1; overflow-y: auto; padding: 20px 24px; display: flex; flex-direction: column; gap: 12px; }

  .msg-row { display: flex; gap: 8px; align-items: flex-end; max-width: 70%; }
  .msg-row.mine { align-self: flex-end; flex-direction: row-reverse; }
  .msg-bubble {
    padding: 10px 14px; border-radius: 16px; font-size: 14px; line-height: 1.5;
    max-width: 100%; word-break: break-word;
  }
  .msg-bubble.theirs { background: #fff; color: var(--gray-800); border-bottom-left-radius: 4px; box-shadow: var(--shadow-sm); }
  .msg-bubble.mine   { background: var(--primary); color: #fff; border-bottom-right-radius: 4px; }
  .msg-time { font-size: 10px; color: var(--gray-400); margin-top: 2px; }

  .chat-input-area {
    background: #fff; padding: 14px 20px; border-top: 1px solid var(--gray-200);
    display: flex; gap: 10px; align-items: flex-end;
  }
  .chat-textarea {
    flex: 1; resize: none; border: 1.5px solid var(--gray-200); border-radius: var(--radius-lg);
    padding: 10px 14px; font-family: var(--font-main); font-size: 14px; outline: none;
    max-height: 120px; line-height: 1.5; transition: border-color .2s;
  }
  .chat-textarea:focus { border-color: var(--primary); }

  /* Video call overlay */
  .video-overlay {
    position: fixed; inset: 0; z-index: 300;
    background: #0a0a12; display: flex; flex-direction: column;
    animation: fadeIn .3s ease;
  }
  .video-main { flex: 1; position: relative; display: flex; align-items: center; justify-content: center; }
  .video-remote {
    width: 100%; height: 100%; object-fit: cover; background: #1a1a2e;
    display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 12px;
  }
  .video-self {
    position: absolute; bottom: 20px; right: 20px;
    width: 160px; height: 120px; background: #2d2d44; border-radius: 12px;
    display: flex; align-items: center; justify-content: center; border: 2px solid rgba(255,255,255,.15);
  }
  .video-controls {
    display: flex; gap: 12px; justify-content: center; padding: 20px;
    background: rgba(0,0,0,.4);
  }
  .vc-btn {
    width: 52px; height: 52px; border-radius: 50%; border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center; transition: all .2s;
    background: rgba(255,255,255,.15); color: #fff;
  }
  .vc-btn:hover { background: rgba(255,255,255,.25); transform: scale(1.05); }
  .vc-btn.danger { background: var(--danger); }
  .vc-btn.danger:hover { background: #b91c1c; }
  .vc-btn.muted  { background: rgba(255,255,255,.4); }

  /* Connecting animation */
  .connecting-pulse {
    width: 80px; height: 80px; border-radius: 50%;
    background: rgba(26,107,255,.2); position: relative;
    display: flex; align-items: center; justify-content: center;
  }
  .connecting-pulse::before, .connecting-pulse::after {
    content: ''; position: absolute; inset: -16px; border-radius: 50%;
    border: 2px solid rgba(26,107,255,.3); animation: pulse 2s ease-out infinite;
  }
  .connecting-pulse::after { animation-delay: 1s; }
  @keyframes pulse { 0%{transform:scale(.8);opacity:1} 100%{transform:scale(1.5);opacity:0} }

  @media (max-width: 768px) {
    .consult-layout { grid-template-columns: 1fr; }
    .consult-sidebar { display: none; }
  }
</style>

<div class="consult-layout">

  <!-- Sidebar -->
  <div class="consult-sidebar">
    <div class="sidebar-header">
      <h4 style="margin-bottom:10px">Konsultasi Aktif</h4>
      <div style="position:relative">
        <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--gray-400)" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input class="form-input" placeholder="Cari konsultasi..." style="padding-left:34px;height:36px;font-size:13px">
      </div>
    </div>
    <div class="consult-list">
      <?php
      $sessions = [
        [1, 'dr. Susanti W., Sp.KK', 'Kulit & Kelamin', 'Hari ini 10:30', 'Selamat pagi! Ada yang...', true, 'SW', '#1a6bff'],
        [2, 'dr. Budi Santoso, Sp.M', 'Spesialis Mata',  'Kemarin 14:00',  'Baik, hasilnya bagus...', false,'BS', '#0ea898'],
        [3, 'dr. Fenny Nurmahdi',    'Dokter Umum',      '2 hari lalu',    'Minum obat 3x sehari ya', false,'FN', '#7c3aed'],
      ];
      foreach ($sessions as [$id,$name,$spec,$time,$preview,$active,$init,$col]):
      ?>
      <div class="consult-item <?= $active?'active':'' ?>" onclick="openSession(<?= $id ?>,'<?= addslashes($name) ?>','<?= $init ?>','<?= $col ?>')">
        <div style="width:40px;height:40px;border-radius:10px;background:<?= $col ?>;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;color:#fff;flex-shrink:0"><?= $init ?></div>
        <div style="flex:1;min-width:0">
          <div style="display:flex;justify-content:space-between;align-items:center">
            <span style="font-size:13px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:120px"><?= $name ?></span>
            <span style="font-size:10px;color:var(--gray-400);flex-shrink:0;margin-left:4px"><?= $time ?></span>
          </div>
          <div style="font-size:11px;color:var(--gray-400)"><?= $spec ?></div>
          <div style="font-size:12px;color:var(--gray-500);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:2px"><?= $preview ?></div>
        </div>
        <?php if ($active): ?><div class="consult-item-dot"></div><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Chat area -->
  <div class="chat-area">
    <!-- Header chat -->
    <div class="chat-header">
      <div id="chat-avatar" style="width:44px;height:44px;border-radius:12px;background:#1a6bff;display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:800;color:#fff;flex-shrink:0">SW</div>
      <div style="flex:1">
        <div id="chat-name" style="font-size:15px;font-weight:700">dr. Susanti Wulandari, Sp.KK</div>
        <div style="display:flex;align-items:center;gap:6px">
          <span style="width:8px;height:8px;border-radius:50%;background:var(--success)"></span>
          <span style="font-size:12px;color:var(--gray-500)">Online · Spesialis Kulit & Kelamin</span>
        </div>
      </div>
      <div style="display:flex;gap:8px">
        <button onclick="startVideoCall()" class="btn btn-teal btn-sm">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>
          Video Call
        </button>
        <button class="btn btn-ghost btn-icon btn-sm">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
        </button>
      </div>
    </div>

    <!-- Messages -->
    <div class="chat-messages" id="chat-messages">
      <!-- System notice -->
      <div style="text-align:center;padding:8px 0">
        <span style="font-size:12px;background:var(--gray-200);color:var(--gray-500);padding:4px 12px;border-radius:99px">Sesi konsultasi dimulai · Hari ini 10:30</span>
      </div>

      <div class="msg-row">
        <div style="width:32px;height:32px;border-radius:8px;background:#1a6bff;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#fff;flex-shrink:0">SW</div>
        <div>
          <div class="msg-bubble theirs">Selamat pagi! Saya dr. Susanti. Ada keluhan yang ingin dikonsultasikan?</div>
          <div class="msg-time">10:30</div>
        </div>
      </div>

      <div class="msg-row mine">
        <div>
          <div class="msg-bubble mine">Selamat pagi, dok. Saya mau konsultasi masalah kulit.</div>
          <div class="msg-time" style="text-align:right">10:31</div>
        </div>
      </div>

      <div class="msg-row">
        <div style="width:32px;height:32px;border-radius:8px;background:#1a6bff;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#fff;flex-shrink:0">SW</div>
        <div>
          <div class="msg-bubble theirs">Baik, boleh ceritakan keluhan spesifiknya? Seperti di bagian mana, sudah berapa lama, dan ada gejala lain tidak?</div>
          <div class="msg-time">10:32</div>
        </div>
      </div>

      <div class="msg-row mine">
        <div>
          <div class="msg-bubble mine">Ada jerawat meradang di pipi kanan, sudah sekitar 2 minggu. Terasa nyeri dan gatal.</div>
          <div class="msg-time" style="text-align:right">10:33</div>
        </div>
      </div>

      <!-- Typing indicator -->
      <div class="msg-row" id="typing-indicator" style="display:none">
        <div style="width:32px;height:32px;border-radius:8px;background:#1a6bff;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#fff;flex-shrink:0">SW</div>
        <div class="msg-bubble theirs" style="padding:12px 16px">
          <div style="display:flex;gap:4px;align-items:center">
            <span style="width:6px;height:6px;border-radius:50%;background:var(--gray-400);animation:typingDot 1.4s infinite .0s"></span>
            <span style="width:6px;height:6px;border-radius:50%;background:var(--gray-400);animation:typingDot 1.4s infinite .2s"></span>
            <span style="width:6px;height:6px;border-radius:50%;background:var(--gray-400);animation:typingDot 1.4s infinite .4s"></span>
          </div>
        </div>
      </div>
    </div>

    <!-- Input -->
    <div class="chat-input-area">
      <button class="btn btn-ghost btn-icon btn-sm" title="Lampiran">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
      </button>
      <textarea id="msg-input" class="chat-textarea" rows="1" placeholder="Tulis pesan..."
                onkeydown="handleChatKey(event)" oninput="autoResize(this)"></textarea>
      <button onclick="sendMessage()" class="btn btn-primary btn-icon">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
      </button>
    </div>
  </div>
</div>

<!-- Video Call Overlay -->
<div class="video-overlay hidden" id="video-overlay">
  <div class="chat-header" style="padding:14px 24px;background:rgba(0,0,0,.6);border-bottom-color:rgba(255,255,255,.1)">
    <div id="vc-avatar" style="width:40px;height:40px;border-radius:10px;background:#1a6bff;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;color:#fff;flex-shrink:0">SW</div>
    <div>
      <div id="vc-name" style="font-size:14px;font-weight:700;color:#fff">dr. Susanti W., Sp.KK</div>
      <div id="vc-status" style="font-size:12px;color:rgba(255,255,255,.6)">Menghubungkan...</div>
    </div>
    <div style="margin-left:auto;display:flex;align-items:center;gap:8px">
      <span id="vc-timer" style="font-size:13px;font-weight:700;color:#fff;display:none">00:00</span>
      <span style="font-size:11px;background:var(--success);color:#fff;padding:3px 10px;border-radius:99px;font-weight:600">HD</span>
    </div>
  </div>

  <div class="video-main">
    <!-- Remote video (dokter) -->
    <div class="video-remote" id="video-remote">
      <div class="connecting-pulse">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.8)" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>
      </div>
      <div style="color:rgba(255,255,255,.6);font-size:14px" id="vc-connecting-text">Menghubungkan ke dokter...</div>
    </div>

    <!-- Self video -->
    <div class="video-self">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.4)" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
    </div>

    <!-- Chat panel saat video -->
    <div id="vc-chat-panel" style="position:absolute;right:0;top:0;bottom:0;width:280px;background:rgba(0,0,0,.7);display:flex;flex-direction:column;backdrop-filter:blur(8px)">
      <div style="padding:12px 16px;border-bottom:1px solid rgba(255,255,255,.1);font-size:13px;font-weight:700;color:#fff;display:flex;align-items:center;justify-content:space-between">
        Live Chat
        <button onclick="toggleChatPanel()" style="background:none;border:none;cursor:pointer;color:rgba(255,255,255,.5);font-size:18px;line-height:1">×</button>
      </div>
      <div id="vc-messages" style="flex:1;overflow-y:auto;padding:12px;display:flex;flex-direction:column;gap:8px">
        <div style="font-size:11px;text-align:center;color:rgba(255,255,255,.3);margin-bottom:4px">Video call dimulai</div>
        <div>
          <div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:2px">Dokter · 10:35</div>
          <div style="background:rgba(255,255,255,.12);border-radius:10px 10px 10px 2px;padding:8px 10px;font-size:12px;color:#fff">Halo, selamat datang di sesi konsultasi</div>
        </div>
      </div>
      <div style="padding:10px;display:flex;gap:6px">
        <input id="vc-msg-input" style="flex:1;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);border-radius:20px;padding:8px 12px;font-size:12px;color:#fff;outline:none;font-family:var(--font-main)" placeholder="Tulis pesan..." onkeydown="if(event.key==='Enter')sendVCMessage()">
        <button onclick="sendVCMessage()" style="width:32px;height:32px;border-radius:50%;background:var(--primary);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
        </button>
      </div>
    </div>
  </div>

  <div class="video-controls">
    <button class="vc-btn" id="btn-mic" onclick="toggleMic(this)" title="Mute/Unmute">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1a3 3 0 00-3 3v8a3 3 0 006 0V4a3 3 0 00-3-3z"/><path d="M19 10v2a7 7 0 01-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/><line x1="8" y1="23" x2="16" y2="23"/></svg>
    </button>
    <button class="vc-btn" id="btn-cam" onclick="toggleCam(this)" title="Kamera">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>
    </button>
    <button class="vc-btn danger" onclick="endVideoCall()" title="Akhiri">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.68 13.31a16 16 0 003.41 2.6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92v3a2 2 0 01-2.18 2A19.79 19.79 0 0111.19 19a19.5 19.5 0 01-6-6 19.79 19.79 0 01-2.93-8.63A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91"/><line x1="23" y1="1" x2="1" y2="23"/></svg>
    </button>
    <button class="vc-btn" onclick="toggleChatPanel()" title="Chat">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
    </button>
  </div>
</div>

<style>
@keyframes typingDot { 0%,60%,100%{transform:translateY(0)} 30%{transform:translateY(-6px)} }
</style>

<script>
function openSession(id, name, initials, color) {
  document.querySelectorAll('.consult-item').forEach(i => i.classList.remove('active'));
  event.currentTarget.classList.add('active');
  document.getElementById('chat-avatar').textContent = initials;
  document.getElementById('chat-avatar').style.background = color;
  document.getElementById('chat-name').textContent = name;
}

function handleChatKey(e) {
  if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
}
function autoResize(el) {
  el.style.height = 'auto';
  el.style.height = Math.min(el.scrollHeight, 120) + 'px';
}

const autoReplies = [
  'Baik, saya mengerti. Bisa jelaskan lebih detail warna dan tekstur kulitnya?',
  'Apakah ada riwayat alergi sebelumnya?',
  'Dari gejala yang disebutkan, kemungkinan besar ini Acne Vulgaris. Saya akan meresepkan obat yang tepat.',
  'Untuk sementara, hindari memencet jerawat dan gunakan sabun wajah yang lembut ya.',
];
let replyIdx = 0;

function sendMessage() {
  const input = document.getElementById('msg-input');
  const text  = input.value.trim();
  if (!text) return;

  const msgs = document.getElementById('chat-messages');
  const typing = document.getElementById('typing-indicator');

  // Tambah pesan user
  const myMsg = document.createElement('div');
  myMsg.className = 'msg-row mine';
  myMsg.innerHTML = `<div><div class="msg-bubble mine">${escHtml(text)}</div><div class="msg-time" style="text-align:right">${now()}</div></div>`;
  msgs.insertBefore(myMsg, typing);
  input.value = '';
  input.style.height = 'auto';
  scrollBottom();

  // Simulasi balasan dokter
  setTimeout(() => {
    typing.style.display = 'flex';
    scrollBottom();
    setTimeout(() => {
      typing.style.display = 'none';
      const reply = document.createElement('div');
      reply.className = 'msg-row';
      reply.innerHTML = `
        <div style="width:32px;height:32px;border-radius:8px;background:#1a6bff;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#fff;flex-shrink:0">SW</div>
        <div><div class="msg-bubble theirs">${autoReplies[replyIdx++ % autoReplies.length]}</div><div class="msg-time">${now()}</div></div>`;
      msgs.insertBefore(reply, typing);
      scrollBottom();
    }, 1800);
  }, 600);
}

function scrollBottom() {
  const msgs = document.getElementById('chat-messages');
  msgs.scrollTop = msgs.scrollHeight;
}
function escHtml(s) { return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
function now() { return new Date().toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'}); }

// Video call
let vcInterval = null, vcSeconds = 0;

function startVideoCall() {
  document.getElementById('video-overlay').classList.remove('hidden');
  document.body.style.overflow = 'hidden';
  // Simulasi connected setelah 3 detik
  setTimeout(() => {
    document.getElementById('vc-connecting-text').textContent = 'Terhubung dengan dokter';
    document.getElementById('vc-status').textContent = 'Terhubung';
    document.getElementById('vc-timer').style.display = 'inline';
    const remote = document.getElementById('video-remote');
    remote.style.background = '#1a2a4a';
    // Mulai timer
    vcInterval = setInterval(() => {
      vcSeconds++;
      const m = String(Math.floor(vcSeconds/60)).padStart(2,'0');
      const s = String(vcSeconds%60).padStart(2,'0');
      document.getElementById('vc-timer').textContent = m+':'+s;
    }, 1000);
  }, 3000);
}

function endVideoCall() {
  document.getElementById('video-overlay').classList.add('hidden');
  document.body.style.overflow = '';
  clearInterval(vcInterval);
  vcSeconds = 0;
  document.getElementById('vc-timer').style.display = 'none';
  document.getElementById('vc-status').textContent = 'Menghubungkan...';
  document.getElementById('vc-connecting-text').textContent = 'Menghubungkan ke dokter...';
  showToast('Sesi video call selesai', 'info');
}

function toggleMic(btn) { btn.classList.toggle('muted'); }
function toggleCam(btn) { btn.classList.toggle('muted'); }

function toggleChatPanel() {
  const panel = document.getElementById('vc-chat-panel');
  panel.style.display = panel.style.display === 'none' ? 'flex' : 'none';
}

function sendVCMessage() {
  const input = document.getElementById('vc-msg-input');
  const text  = input.value.trim();
  if (!text) return;
  const msgs = document.getElementById('vc-messages');
  const div  = document.createElement('div');
  div.innerHTML = `<div style="text-align:right"><div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:2px">Kamu · ${now()}</div><div style="background:var(--primary);border-radius:10px 10px 2px 10px;padding:8px 10px;font-size:12px;color:#fff;display:inline-block;max-width:90%">${escHtml(text)}</div></div>`;
  msgs.appendChild(div);
  msgs.scrollTop = msgs.scrollHeight;
  input.value = '';
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
