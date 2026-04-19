<?php
require_once __DIR__ . '/../includes/config.php';
redirectIfLoggedIn();
$pageTitle = 'Daftar — CareSync';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?></title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
  <script src="<?= BASE_URL ?>/assets/js/app.js"></script>
  <style>
    body { background: var(--gray-50); }
    .auth-wrap { min-height: 100vh; display: grid; grid-template-columns: 1fr 1fr; }
    .auth-left {
      background: linear-gradient(145deg, #0ea898 0%, #1a6bff 100%);
      display: flex; flex-direction: column; justify-content: center;
      padding: 60px; position: relative; overflow: hidden;
    }
    .auth-right { display: flex; align-items: center; justify-content: center; padding: 40px; }
    .auth-card   { width: 100%; max-width: 460px; animation: fadeUp .4s ease; }
    @keyframes fadeUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }

    /* Step indicator */
    .steps { display: flex; align-items: center; gap: 0; margin-bottom: 32px; }
    .step-item { display: flex; align-items: center; gap: 8px; flex: 1; }
    .step-item:last-child { flex: 0; }
    .step-circle {
      width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;
      display: flex; align-items: center; justify-content: center;
      font-size: 13px; font-weight: 700;
      border: 2px solid var(--gray-300); color: var(--gray-400);
      transition: all .3s;
    }
    .step-circle.active { border-color: var(--primary); background: var(--primary); color: #fff; }
    .step-circle.done   { border-color: var(--success); background: var(--success); color: #fff; }
    .step-line { flex: 1; height: 2px; background: var(--gray-200); margin: 0 4px; }
    .step-line.done { background: var(--success); }
    .step-label { font-size: 11px; font-weight: 600; color: var(--gray-400); margin-top: 4px; }

    .step-panel { display: none; }
    .step-panel.active { display: block; animation: fadeUp .3s ease; }

    .otp-inputs { display: flex; gap: 10px; justify-content: center; }
    .otp-input {
      width: 52px; height: 60px; text-align: center;
      font-size: 22px; font-weight: 700; border: 2px solid var(--gray-200);
      border-radius: var(--radius-md); outline: none;
      transition: border-color .2s, box-shadow .2s;
      font-family: var(--font-main);
    }
    .otp-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(26,107,255,.12); }
    .otp-input.filled { border-color: var(--primary); background: var(--primary-light); }

    .strength-bar { height: 4px; background: var(--gray-200); border-radius: 99px; margin-top: 6px; overflow: hidden; }
    .strength-fill { height: 100%; border-radius: 99px; transition: width .3s, background .3s; width: 0; }

    @media (max-width: 768px) {
      .auth-wrap { grid-template-columns: 1fr; }
      .auth-left  { display: none; }
    }
  </style>
</head>
<body>
<div class="auth-wrap">
  <div class="auth-left">
    <div style="position:relative;z-index:1">
      <a href="<?= BASE_URL ?>" style="display:flex;align-items:center;gap:8px;margin-bottom:48px;text-decoration:none">
        <svg width="36" height="36" viewBox="0 0 32 32" fill="none"><rect width="32" height="32" rx="10" fill="rgba(255,255,255,.2)"/><path d="M16 7C16 7 9 11 9 18c0 3.87 3.13 7 7 7s7-3.13 7-7c0-7-7-11-7-11z" fill="white"/><circle cx="16" cy="18" r="3" fill="rgba(255,255,255,.4)"/></svg>
        <span style="font-size:22px;font-weight:800;color:#fff">CareSync</span>
      </a>

      <h2 style="color:#fff;font-size:2rem;margin-bottom:16px;font-family:var(--font-serif);font-style:italic">
        Bergabung dengan<br>jutaan pengguna
      </h2>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:40px">
        <?php foreach ([['50K+','Pasien aktif'],['200+','Dokter terverifikasi'],['99%','Kepuasan pasien'],['24/7','Layanan tersedia']] as [$num,$label]): ?>
        <div style="background:rgba(255,255,255,.12);border-radius:12px;padding:16px">
          <div style="font-size:24px;font-weight:800;color:#fff"><?= $num ?></div>
          <div style="font-size:12px;color:rgba(255,255,255,.7)"><?= $label ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div class="auth-right">
    <div class="auth-card">
      <div style="margin-bottom:24px">
        <h1 style="font-size:1.6rem;margin-bottom:6px">Buat akun baru</h1>
        <p class="text-muted text-sm">Daftar gratis, mulai konsultasi hari ini</p>
      </div>

      <div class="steps">
        <div class="step-item">
          <div class="step-circle active" id="sc-1">1</div>
          <div class="step-line" id="sl-1"></div>
        </div>
        <div class="step-item">
          <div class="step-circle" id="sc-2">2</div>
          <div class="step-line" id="sl-2"></div>
        </div>
        <div class="step-item" style="flex:0">
          <div class="step-circle" id="sc-3">3</div>
        </div>
      </div>

      <div class="step-panel active" id="step-1">
        <h4 style="margin-bottom:18px">Data diri</h4>
        <div class="form-group">
          <label class="form-label">Nama lengkap</label>
          <input type="text" id="reg-name" class="form-input" placeholder="Nama sesuai KTP" required>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
          <div class="form-group">
            <label class="form-label">Jenis kelamin</label>
            <select id="reg-gender" class="form-select">
              <option value="">Pilih</option>
              <option value="L">Laki-laki</option>
              <option value="P">Perempuan</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Tanggal lahir</label>
            <input type="date" id="reg-dob" class="form-input">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Nomor HP</label>
          <input type="tel" id="reg-phone" class="form-input" placeholder="08xxxxxxxxxx">
        </div>
        <button onclick="goStep2()" class="btn btn-primary btn-full">Lanjut</button>
        <p class="text-center text-sm text-muted mt-16">
          Sudah punya akun? <a href="<?= BASE_URL ?>/pages/login.php" style="font-weight:600">Masuk</a>
        </p>
      </div>

      <div class="step-panel" id="step-2">
        <h4 style="margin-bottom:18px">Email & password</h4>
        <div class="form-group">
          <label class="form-label">Email</label>
          <div class="form-input-wrap">
            <svg class="form-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            <input type="email" id="reg-email" class="form-input" placeholder="email@example.com" required>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Password</label>
          <div class="form-input-wrap">
            <svg class="form-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
            <input type="password" id="reg-password" class="form-input" placeholder="Min. 8 karakter" oninput="checkStrength(this.value)">
            <button type="button" class="form-input-action" onclick="togglePassword('reg-password',this)">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
          <div class="strength-bar"><div class="strength-fill" id="strength-fill"></div></div>
          <div id="strength-label" class="form-hint"></div>
        </div>
        <div class="form-group">
          <label class="form-label">Konfirmasi password</label>
          <div class="form-input-wrap">
            <svg class="form-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
            <input type="password" id="reg-confirm" class="form-input" placeholder="Ulangi password">
          </div>
        </div>

        <div style="display:flex;gap:8px">
          <button onclick="goStep(1)" class="btn btn-ghost" style="flex:1">Kembali</button>
          <button onclick="submitRegister()" id="btn-register" class="btn btn-primary" style="flex:2">Daftar & Kirim OTP</button>
        </div>
      </div>

      <div class="step-panel" id="step-3">
        <div style="text-align:center;margin-bottom:28px">
          <div style="width:64px;height:64px;background:var(--primary-light);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          </div>
          <h3>Cek email-mu!</h3>
          <p class="text-muted text-sm mt-8">Kode OTP dikirim ke <strong id="confirm-email"></strong></p>
          <p class="text-sm mt-4" style="color:var(--warning)">Berlaku <span id="reg-countdown" style="font-weight:700">05:00</span></p>
        </div>

        <div class="otp-inputs" id="reg-otp-boxes">
          <input class="otp-input" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]">
          <input class="otp-input" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]">
          <input class="otp-input" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]">
          <input class="otp-input" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]">
          <input class="otp-input" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]">
          <input class="otp-input" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]">
        </div>

        <button onclick="verifyRegOtp()" id="btn-verify-reg" class="btn btn-primary btn-full mt-24" disabled>Verifikasi & Selesai</button>
        <button onclick="resendOtp()" class="btn btn-ghost btn-full mt-8">Kirim ulang OTP</button>
      </div>

    </div>
  </div>
</div>

<div id="toast-container" class="toast-container"></div>

<script>
const BASE_URL = '<?= BASE_URL ?>';
let currentStep = 1;
let regInterval = null;

function goStep(n) {
  document.getElementById('step-' + currentStep).classList.remove('active');
  document.getElementById('sc-' + currentStep).classList.remove('active');
  if (n > currentStep) document.getElementById('sc-' + currentStep).classList.add('done');
  if (currentStep < 3) { const sl = document.getElementById('sl-' + currentStep); if(sl && n>currentStep) sl.classList.add('done'); }
  currentStep = n;
  document.getElementById('step-' + n).classList.add('active');
  document.getElementById('sc-' + n).classList.add('active');
}

function goStep2() {
  const name = document.getElementById('reg-name').value.trim();
  if (!name) { showToast('Nama wajib diisi', 'error'); return; }
  goStep(2);
}

function togglePassword(id, btn) {
  const inp = document.getElementById(id);
  inp.type = inp.type === 'password' ? 'text' : 'password';
  btn.querySelector('svg').style.opacity = inp.type === 'text' ? '.4' : '1';
}

function checkStrength(val) {
  const fill = document.getElementById('strength-fill');
  const label = document.getElementById('strength-label');
  let score = 0;
  if (val.length >= 8) score++;
  if (/[A-Z]/.test(val)) score++;
  if (/[0-9]/.test(val)) score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;
  const levels = [['0%','',''],['25%','var(--danger)','Lemah'],['50%','var(--warning)','Cukup'],['75%','var(--primary)','Kuat'],['100%','var(--success)','Sangat kuat']];
  fill.style.width = levels[score][0];
  fill.style.background = levels[score][1];
  label.textContent = levels[score][2];
  label.style.color = levels[score][1];
}

async function submitRegister() {
  const nama = document.getElementById('reg-name').value;
  const email = document.getElementById('reg-email').value;
  const password = document.getElementById('reg-password').value;
  const confirm  = document.getElementById('reg-confirm').value;

  if (!email || !password) { showToast('Email dan password wajib diisi', 'error'); return; }
  if (password !== confirm) { showToast('Password tidak cocok', 'error'); return; }
  if (password.length < 8)  { showToast('Password minimal 8 karakter', 'error'); return; }

  const btn = document.getElementById('btn-register');
  setLoading(btn, true, 'Mengirim OTP...');
  
  try {
    // 1. Panggil API kirim OTP (Mengecek email & mengirim email PHPMailer)
    const response = await api('/auth/send-register-otp', { 
      method: 'POST', 
      body: JSON.stringify({ nama, email }) 
    });
    
    // 2. Kalau sukses, baru lanjut ke form OTP (Step 3)
    setLoading(btn, false);
    document.getElementById('confirm-email').textContent = email;
    startRegCountdown(300);
    goStep(3);
    initRegOtpBoxes();
    showToast(response.message, 'success');
  } catch (err) {
    // Kalau gagal (misal email sudah ada), tampilkan error dari backend
    setLoading(btn, false);
    showToast(err.message, 'error'); 
  }
}

function startRegCountdown(secs) {
  const el = document.getElementById('reg-countdown');
  if (regInterval) clearInterval(regInterval);
  regInterval = setInterval(() => {
    secs--;
    if (secs <= 0) {
      clearInterval(regInterval);
      el.textContent = 'Kedaluwarsa';
      return;
    }
    el.textContent = String(Math.floor(secs/60)).padStart(2,'0') + ':' + String(secs%60).padStart(2,'0');
  }, 1000);
}

function initRegOtpBoxes() {
  const boxes = document.querySelectorAll('#reg-otp-boxes .otp-input');
  boxes.forEach((box, i) => {
    box.value = '';
    box.classList.remove('filled');
    box.addEventListener('input', () => {
      box.classList.toggle('filled', !!box.value);
      if (box.value && i < boxes.length - 1) boxes[i+1].focus();
      document.getElementById('btn-verify-reg').disabled = ![...boxes].every(b => b.value);
    });
    box.addEventListener('keydown', e => { if (e.key==='Backspace' && !box.value && i>0) boxes[i-1].focus(); });
  });
  boxes[0].focus();
}

async function verifyRegOtp() {
  const code = [...document.querySelectorAll('#reg-otp-boxes .otp-input')].map(b=>b.value).join('');
  const btn  = document.getElementById('btn-verify-reg');
  setLoading(btn, true, 'Memverifikasi...');

  // Gabungkan semua data diri + password + OTP untuk dikirim ke backend
  const payload = {
    nama: document.getElementById('reg-name').value,
    email: document.getElementById('reg-email').value,
    password: document.getElementById('reg-password').value,
    gender: document.getElementById('reg-gender').value,
    dob: document.getElementById('reg-dob').value,
    phone: document.getElementById('reg-phone').value,
    otp: code
  };
  
  try {
    // 3. Panggil API Registrasi (Memvalidasi OTP dan memasukkan ke DB)
    const response = await api('/auth/register', {
      method: 'POST',
      body: JSON.stringify(payload)
    });
    
    // 4. Simpan session token asli & redirect ke dashboard
    saveSession(response.data.token, response.data.user);
    showToast('Pendaftaran berhasil!', 'success');
    setTimeout(() => window.location.href = BASE_URL + '/pages/dashboard.php', 900);
  } catch (err) {
    // Kalau OTP salah
    setLoading(btn, false);
    showToast(err.message, 'error');
  }
}

async function resendOtp() {
  const email = document.getElementById('reg-email').value;
  const nama = document.getElementById('reg-name').value;
  try {
    const response = await api('/auth/send-register-otp', {
      method: 'POST',
      body: JSON.stringify({ nama, email })
    });
    showToast(response.message, 'success');
    startRegCountdown(300);
  } catch (err) {
    showToast(err.message, 'error');
  }
}
</script>
</body>
</html>