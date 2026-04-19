// Ambil base URL secara dinamis dari origin jika memungkinkan, atau gunakan path relatif
const API_BASE = window.location.origin + '/caresync/api';

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
  window.location.href = '../pages/login.php';
}
function getUser() {
  try { return JSON.parse(localStorage.getItem('em_user') || '{}'); } catch { return {}; }
}
function isLoggedIn() { return !!localStorage.getItem('em_token'); }
function requireAuth() { if (!isLoggedIn()) window.location.href = '../pages/login.php'; }

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
};