const API_BASE = 'http://localhost/caresync/api';

// ── API helper ──────────────────────────────────────────────
async function api(endpoint, options = {}) {
  const token = localStorage.getItem('em_token');
  const headers = { 'Content-Type': 'application/json', ...(options.headers || {}) };
  if (token) headers['Authorization'] = 'Bearer ' + token;

  try {
    const res = await fetch(API_BASE + endpoint, { ...options, headers });
    const data = await res.json();
    if (!res.ok || data.status === 'error') throw new Error(data.message || 'Terjadi kesalahan');
    return data;
  } catch (err) {
    throw err;
  }
}

// ── Session helpers ──────────────────────────────────────────
function saveSession(token, user) {
  localStorage.setItem('em_token', token);
  localStorage.setItem('em_user', JSON.stringify(user));
}
function clearSession() {
  localStorage.removeItem('em_token');
  localStorage.removeItem('em_user');
  window.location.href = '/caresync/pages/login.php';
}
function getUser() {
  try { return JSON.parse(localStorage.getItem('em_user') || '{}'); } catch { return {}; }
}
function isLoggedIn() { return !!localStorage.getItem('em_token'); }
function requireAuth() { if (!isLoggedIn()) window.location.href = '/caresync/pages/login.php'; }

// ── Toast ────────────────────────────────────────────────────
function showToast(message, type = 'info', duration = 3500) {
  let container = document.getElementById('toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container';
    document.body.appendChild(container);
  }
  const icons = { success: '✓', error: '✕', info: 'ℹ' };
  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.innerHTML = `<span style="font-size:16px">${icons[type]||icons.info}</span><span>${message}</span>`;
  container.appendChild(toast);
  setTimeout(() => { toast.style.animation = 'slideIn .3s ease reverse'; setTimeout(() => toast.remove(), 300); }, duration);
}

// ── Loading button state ─────────────────────────────────────
function setLoading(btn, loading, text = '') {
  if (loading) {
    btn.dataset.original = btn.innerHTML;
    btn.classList.add('loading');
    btn.innerHTML = `<span class="spinner"></span>${text}`;
  } else {
    btn.classList.remove('loading');
    btn.innerHTML = btn.dataset.original || btn.innerHTML;
  }
}

// ── Format helpers ───────────────────────────────────────────
function formatRupiah(amount) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
}
function formatDate(dateStr) {
  return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
}
function formatTime(dateStr) {
  return new Date(dateStr).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
}
function timeAgo(dateStr) {
  const diff = Math.floor((Date.now() - new Date(dateStr)) / 1000);
  if (diff < 60) return 'Baru saja';
  if (diff < 3600) return Math.floor(diff / 60) + ' menit lalu';
  if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu';
  return formatDate(dateStr);
}

// ── Mock data (dipakai saat backend belum siap) ──────────────
const MOCK = {
  user: { id: 1, name: 'Siti Aminah', email: 'siti@example.com', role: 'pasien', avatar: null },

  doctors: [
    { id: 1, name: 'dr. Susanti Wulandari, Sp.KK', specialty: 'Spesialis Kulit & Kelamin', rating: 4.9, exp: 5, patients: 320, price: 45000, available: true, avatar: null },
    { id: 2, name: 'dr. Budi Santoso, Sp.M',        specialty: 'Spesialis Mata',            rating: 4.7, exp: 8, patients: 280, price: 60000, available: true,  avatar: null },
    { id: 3, name: 'dr. Fenny Nurmahdi',             specialty: 'Dokter Umum',               rating: 4.8, exp: 3, patients: 210, price: 30000, available: false, avatar: null },
    { id: 4, name: 'dr. Ika Syafitri, Sp.PD',        specialty: 'Spesialis Penyakit Dalam',  rating: 4.6, exp: 6, patients: 195, price: 55000, available: true,  avatar: null },
  ],

  specialties: ['Semua', 'Dokter Umum', 'Spesialis Kulit & Kelamin', 'Spesialis Mata', 'Spesialis Penyakit Dalam', 'THT'],

  consultations: [
    { id: 1, doctor: 'dr. Susanti Wulandari, Sp.KK', specialty: 'Spesialis Kulit', date: '2024-11-24', status: 'selesai', complaint: 'Jerawat meradang dan gatal di pipi kanan', diagnosis: 'Acne Vulgaris Grade II' },
    { id: 2, doctor: 'dr. Budi Santoso, Sp.M',        specialty: 'Spesialis Mata',  date: '2024-10-15', status: 'selesai', complaint: 'Mata merah dan gatal',                    diagnosis: 'Konjungtivitis Alergi' },
  ],

  slots: ['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00'],
};
