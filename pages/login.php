<?php
require_once __DIR__ . '/../includes/config.php';
redirectIfLoggedIn();

$pageTitle = 'Masuk — CareSync';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?></title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
  <script src="<?= BASE_URL ?>/assets/js/app.js"></script>
  
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
      tailwind.config = {
          theme: {
              extend: {
                  fontFamily: {
                      sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                  },
                  colors: {
                      primary: '#1D4ED8', // Deeper Blue
                      primaryLight: '#EFF6FF', // Soft blue background
                      accent: '#10B981', // Emerald
                      dark: '#0F172A', // Slate 900
                      textSoft: '#64748B', // Slate 500
                  },
                  boxShadow: {
                      'floating': '0 20px 40px -15px rgba(29, 78, 216, 0.25)',
                  }
              }
          }
      }
  </script>

  <style>
    body { background: #F8FAFC; font-family: 'Plus Jakarta Sans', sans-serif; margin: 0; }
    .auth-wrap {
      min-height: 100vh; display: grid;
      grid-template-columns: 1.1fr 1fr;
    }
    .auth-left {
      /* Gradasi disesuaikan dengan warna tema Dashboard */
      background: linear-gradient(135deg, #1e3a8a 0%, #1D4ED8 50%, #10B981 100%);
      display: flex; flex-direction: column; justify-content: center;
      padding: 5rem; position: relative; overflow: hidden;
    }
    .auth-left::before {
      content: ''; position: absolute; inset: 0;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='20'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .auth-left-content { position: relative; z-index: 10; }
    .auth-blob {
      position: absolute; border-radius: 50%;
      background: rgba(255,255,255,.05); filter: blur(30px);
    }
    .auth-right {
      display: flex; align-items: center; justify-content: center;
      padding: 2rem; background: #F8FAFC;
    }
    .auth-card {
      width: 100%; max-width: 440px;
      animation: fadeUp .6s ease-out forwards;
      background: white; padding: 3rem; border-radius: 2rem;
      box-shadow: 0 10px 40px -10px rgba(0,0,0,0.05);
      border: 1px solid #F1F5F9;
    }
    @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    
    .feature-item {
      display: flex; align-items: flex-start; gap: 16px;
      padding: 16px 0; border-bottom: 1px solid rgba(255,255,255,.1);
    }
    .feature-item:last-child { border-bottom: none; }
    .feature-icon {
      width: 48px; height: 48px; border-radius: 14px;
      background: rgba(255,255,255,.1); backdrop-filter: blur(10px);
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0; transition: transform 0.3s ease;
    }
    .feature-item:hover .feature-icon { transform: scale(1.1) rotate(5deg); }

    /* Integrasi Form Styles dengan gaya modern */
    .form-group { margin-bottom: 1.25rem; }
    .form-label { display: block; font-size: 0.875rem; font-weight: 700; color: #0F172A; margin-bottom: 0.5rem; }
    .form-input-wrap { position: relative; display: flex; align-items: center; }
    .form-input { 
      width: 100%; padding: 0.875rem 1rem 0.875rem 2.75rem; 
      background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 1rem; 
      font-family: inherit; font-size: 0.95rem; font-weight: 600; color: #0F172A; 
      transition: all 0.3s ease; outline: none; 
    }
    .form-input:focus { border-color: #1D4ED8; background: #fff; box-shadow: 0 0 0 4px #EFF6FF; }
    .form-input-icon { position: absolute; left: 1rem; width: 1.25rem; height: 1.25rem; color: #64748B; pointer-events: none; }
    .form-input-action { position: absolute; right: 1rem; background: none; border: none; cursor: pointer; color: #64748B; padding: 0; display: flex; align-items: center; transition: color 0.3s; }
    .form-input-action:hover { color: #1D4ED8; }

    /* Button Styles */
    .btn { 
      font-family: inherit; font-weight: 700; border-radius: 9999px; cursor: pointer; 
      transition: all 0.3s ease; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; border: none; 
    }
    .btn-full { width: 100%; }
    .btn-lg { padding: 1rem 1.5rem; font-size: 1rem; }
    .btn-primary { background: #1D4ED8; color: white; box-shadow: 0 10px 25px -5px rgba(29, 78, 216, 0.3); }
    .btn-primary:hover { background: #1e40af; transform: translateY(-2px); box-shadow: 0 15px 30px -5px rgba(29, 78, 216, 0.4); }
    .btn-outline { background: white; border: 1px solid #E2E8F0; color: #0F172A; padding: 0.875rem 1.5rem; }
    .btn-outline:hover { background: #F8FAFC; border-color: #CBD5E1; }
    .btn-ghost { background: transparent; color: #64748B; padding: 0.875rem 1.5rem; }
    .btn-ghost:hover { background: #F1F5F9; color: #0F172A; }

    .divider { display: flex; align-items: center; margin: 2rem 0; text-align: center; }
    .divider::before, .divider::after { content: ''; flex: 1; border-bottom: 1px solid #E2E8F0; }
    .divider span { padding: 0 1rem; color: #94A3B8; font-size: 0.875rem; font-weight: 600; }

    /* OTP Modern Style */
    .otp-inputs { display: flex; gap: 0.5rem; justify-content: space-between; margin-bottom: 1.5rem; }
    .otp-input {
      width: 3.2rem; height: 3.8rem; text-align: center;
      font-size: 1.5rem; font-weight: 800; border: 2px solid #E2E8F0;
      border-radius: 0.75rem; outline: none; background: #F8FAFC; color: #0F172A;
      transition: all 0.3s ease; font-family: inherit;
    }
    .otp-input:focus { border-color: #1D4ED8; background: #fff; box-shadow: 0 0 0 4px #EFF6FF; transform: translateY(-2px); }
    .otp-input.filled { border-color: #10B981; background: #ECFDF5; color: #047857; }

    @media (max-width: 768px) {
      .auth-wrap { grid-template-columns: 1fr; }
      .auth-left  { display: none; }
      .auth-right { padding: 1rem; align-items: flex-start; padding-top: 3rem; }
      .auth-card { padding: 2rem; border-radius: 1.5rem; box-shadow: none; border: none; background: transparent; }
      .otp-input { width: 2.5rem; height: 3rem; font-size: 1.25rem; }
    }
  </style>
</head>
<body>

<div class="auth-wrap">
  <div class="auth-left">
    <div class="auth-blob" style="width:400px;height:400px;top:-100px;right:-100px"></div>
    <div class="auth-blob" style="width:300px;height:300px;bottom:-50px;left:-80px;background:rgba(16,185,129,0.1)"></div>
    <div class="auth-left-content">
      <a href="<?= BASE_URL ?>" class="flex items-center gap-3 mb-12 no-underline group w-fit">
        <div class="w-11 h-11 bg-primary rounded-xl flex items-center justify-center shadow-soft group-hover:scale-105 group-hover:rotate-3 transition-all duration-300">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12.0002 21.35L10.5502 20.03C5.4002 15.36 2.0002 12.28 2.0002 8.5C2.0002 5.42 4.4202 3 7.5002 3C9.2402 3 10.9102 3.81 12.0002 5.09C13.0902 3.81 14.7602 3 16.5002 3C19.5802 3 22.0002 5.42 22.0002 8.5C22.0002 12.28 18.6002 15.36 13.4502 20.04L12.0002 21.35Z" fill="white"/>
              <path d="M12 17L14 15M12 17L10 15M12 17V11M8 11V13M16 11V13" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <circle cx="12" cy="17" r="6" stroke="#10B981" stroke-width="2"/>
            </svg>
        </div>
        <span class="font-extrabold text-2xl tracking-tighter text-white">Care<span class="text-blue-100">Sync</span></span>
      </a>

      <h2 class="text-white text-3xl md:text-4xl font-extrabold mb-4 leading-tight">
        Kesehatan lebih mudah,<br>di mana saja.
      </h2>
      <p class="text-blue-100 text-base mb-10 leading-relaxed max-w-md">
        Konsultasi dokter, beli obat, dan pantau kesehatanmu dalam satu platform modern.
      </p>

      <div>
        <?php
        $features = [
          ['Konsultasi 24/7', 'Chat atau video call dengan dokter kapan saja', 'M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z'],
          ['Apotek Online',   'Beli obat bebas atau tebus resep digital',        'M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z M9 22V12h6v10'],
          ['Aman & Terpercaya','Data medismu dilindungi sesuai UU PDP',            'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z'],
        ];
        foreach ($features as [$title, $desc, $path]):
        ?>
        <div class="feature-item">
          <div class="feature-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <?php foreach (explode(' M', $path) as $i => $p): ?>
              <path d="<?= $i === 0 ? $p : 'M'.$p ?>"/>
              <?php endforeach; ?>
            </svg>
          </div>
          <div>
            <div class="text-sm font-bold text-white mb-0.5"><?= $title ?></div>
            <div class="text-xs text-blue-200"><?= $desc ?></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div class="auth-right">
    <div class="auth-card">

      <div id="form-login">
        <div class="mb-8">
          <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 mb-2">Selamat datang kembali</h1>
          <p class="text-slate-500 font-medium text-sm">Masuk ke akun CareSync-mu untuk melanjutkan.</p>
        </div>

        <form id="login-form" onsubmit="handleLogin(event)">
          <div class="form-group">
            <label class="form-label">Email</label>
            <div class="form-input-wrap">
              <svg class="form-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
              <input type="email" id="login-email" class="form-input" placeholder="email@example.com" required autocomplete="email">
            </div>
          </div>

          <div class="form-group">
            <div class="flex justify-between items-center mb-2">
              <label class="form-label mb-0">Password</label>
              <a href="<?= BASE_URL ?>/pages/forgot-password.php" class="text-sm font-bold text-primary hover:text-blue-800 transition-colors">Lupa password?</a>
            </div>
            <div class="form-input-wrap">
              <svg class="form-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
              <input type="password" id="login-password" class="form-input" placeholder="••••••••" required autocomplete="current-password">
              <button type="button" class="form-input-action" onclick="togglePassword('login-password', this)">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
          </div>

          <button type="submit" id="btn-login" class="btn btn-primary btn-full btn-lg mt-4">
            Masuk Sekarang
          </button>
        </form>

        <div class="divider"><span>atau masuk dengan OTP email</span></div>

        <button onclick="showOtpLogin()" class="btn btn-outline btn-full">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          Login via OTP
        </button>

        <p class="text-center text-sm text-slate-500 mt-10 font-medium">
          Belum punya akun?
          <a href="<?= BASE_URL ?>/pages/register.php" class="font-bold text-primary hover:text-blue-800 transition-colors ml-1">Daftar sekarang</a>
        </p>
      </div>

      <div id="form-otp-login" class="hidden">
        <button onclick="showPasswordLogin()" class="flex items-center gap-2 bg-transparent border-none cursor-pointer text-slate-500 hover:text-primary font-bold text-sm mb-8 transition-colors p-0">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
          Kembali
        </button>

        <div id="otp-step-1">
          <h2 class="text-2xl font-extrabold text-slate-900 mb-2">Login dengan OTP</h2>
          <p class="text-slate-500 font-medium text-sm mb-8">Masukkan email-mu, kami kirim kode rahasia OTP.</p>

          <div class="form-group">
            <label class="form-label">Email Valid</label>
            <div class="form-input-wrap">
              <svg class="form-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
              <input type="email" id="otp-email" class="form-input" placeholder="email@example.com" required>
            </div>
          </div>
          <button onclick="sendLoginOtp()" id="btn-send-otp" class="btn btn-primary btn-full btn-lg mt-2">Kirim Kode OTP</button>
        </div>

        <div id="otp-step-2" class="hidden">
          <h2 class="text-2xl font-extrabold text-slate-900 mb-2">Masukkan kode OTP</h2>
          <p class="text-slate-500 font-medium text-sm mb-2">Kode dikirim ke <strong id="otp-target-email" class="text-slate-800"></strong></p>
          <p class="text-sm font-medium text-amber-500 mb-8">Berlaku selama <span id="otp-countdown" class="font-bold">05:00</span></p>

          <div class="otp-inputs" id="otp-boxes">
            <input class="otp-input" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]">
            <input class="otp-input" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]">
            <input class="otp-input" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]">
            <input class="otp-input" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]">
            <input class="otp-input" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]">
            <input class="otp-input" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]">
          </div>

          <button onclick="verifyLoginOtp()" id="btn-verify-otp" class="btn btn-primary btn-full btn-lg mt-8" disabled>Verifikasi & Masuk</button>
          <button onclick="sendLoginOtp()" class="btn btn-ghost btn-full mt-2">Kirim ulang kode</button>
        </div>
      </div>

    </div>
  </div>
</div>

<div id="toast-container" class="toast-container"></div>

<script>
const BASE_URL = '<?= BASE_URL ?>';
let otpCountdownInterval = null;

function getDashboardPathByRole(role) {
  const normalizedRole = String(role || '').toLowerCase();
  if (normalizedRole === 'admin') return `${BASE_URL}/pages/admin/dashboard.php`;
  if (normalizedRole === 'apoteker' || normalizedRole === 'pharmacist') return `${BASE_URL}/pages/apoteker/dashboard.php`;
  if (normalizedRole === 'dokter' || normalizedRole === 'doctor') return `${BASE_URL}/pages/dokter/dashboard.php`;
  return `${BASE_URL}/pages/dashboard.php`;
}

function togglePassword(inputId, btn) {
  const inp = document.getElementById(inputId);
  const isPass = inp.type === 'password';
  inp.type = isPass ? 'text' : 'password';
  btn.querySelector('svg').style.opacity = isPass ? '.5' : '1';
  btn.querySelector('svg').style.color = isPass ? '#1D4ED8' : '#64748B';
}

function showOtpLogin() {
  document.getElementById('form-login').classList.add('hidden');
  document.getElementById('form-otp-login').classList.remove('hidden');
}
function showPasswordLogin() {
  document.getElementById('form-login').classList.remove('hidden');
  document.getElementById('form-otp-login').classList.add('hidden');
}

async function handleLogin(e) {
  e.preventDefault();
  const btn = document.getElementById('btn-login');
  setLoading(btn, true, 'Masuk...');
  try {
    const data = await api('/auth/login', {
      method: 'POST',
      body: JSON.stringify({
        email:    document.getElementById('login-email').value,
        password: document.getElementById('login-password').value
      })
    });
    
    saveSession(data.data.token, data.data.user);
    showToast('Berhasil masuk!', 'success');
    setTimeout(() => window.location.href = getDashboardPathByRole(data.data.user?.role), 800);
  } catch (err) {
    showToast(err.message, 'error');
  } finally {
    setLoading(btn, false);
  }
}

async function sendLoginOtp() {
  const email = document.getElementById('otp-email').value;
  if (!email) { showToast('Masukkan email dulu', 'error'); return; }
  
  const btn = document.getElementById('btn-send-otp');
  setLoading(btn, true, 'Mengirim...');
  
  try {
    const response = await api('/auth/send-otp', { method: 'POST', body: JSON.stringify({ email }) });
    
    document.getElementById('otp-step-1').classList.add('hidden');
    document.getElementById('otp-step-2').classList.remove('hidden');
    document.getElementById('otp-target-email').textContent = email;
    startCountdown(300);
    initOtpBoxes();
    
    showToast(response.message, 'success');
  } catch (err) {
    showToast(err.message, 'error');
  } finally {
    setLoading(btn, false);
  }
}

function startCountdown(secs) {
  const el = document.getElementById('otp-countdown');
  if (otpCountdownInterval) {
    clearInterval(otpCountdownInterval);
  }

  el.textContent = '05:00';
  otpCountdownInterval = setInterval(() => {
    secs--;
    const m = String(Math.floor(secs/60)).padStart(2,'0');
    const s = String(secs%60).padStart(2,'0');
    el.textContent = m+':'+s;
    if (secs <= 0) {
      clearInterval(otpCountdownInterval);
      otpCountdownInterval = null;
      el.textContent = 'Kedaluwarsa';
    }
  }, 1000);
}

function initOtpBoxes() {
  const boxes = document.querySelectorAll('#otp-boxes .otp-input');
  boxes.forEach((box, i) => {
    box.value = '';
    box.classList.remove('filled');
    box.oninput = null;
    box.onkeydown = null;

    box.oninput = () => {
      box.classList.toggle('filled', box.value !== '');
      if (box.value && i < boxes.length - 1) boxes[i+1].focus();
      const allFilled = [...boxes].every(b => b.value);
      document.getElementById('btn-verify-otp').disabled = !allFilled;
    };
    box.onkeydown = (e) => {
      if (e.key === 'Backspace' && !box.value && i > 0) boxes[i-1].focus();
    };
  });
  document.getElementById('btn-verify-otp').disabled = true;
  boxes[0].focus();
}

async function verifyLoginOtp() {
  const code = [...document.querySelectorAll('.otp-input')].map(b => b.value).join('');
  const btn = document.getElementById('btn-verify-otp');
  setLoading(btn, true, 'Memverifikasi...');
  try {
    const data = await api('/auth/verify-login-otp', {
      method: 'POST',
      body: JSON.stringify({ email: document.getElementById('otp-email').value, otp: code })
    });
    saveSession(data.data.token, data.data.user);
    
    showToast('Berhasil masuk!', 'success');
    setTimeout(() => window.location.href = getDashboardPathByRole(data.data.user?.role), 800);
  } catch (err) {
    showToast(err.message, 'error');
  } finally {
    setLoading(btn, false);
  }
}

</script>
</body>
</html>
