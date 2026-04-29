// Ambil base URL secara dinamis dari origin jika memungkinkan, atau gunakan path relatif
const API_BASE = window.location.origin + '/caresync/api';

// ── API helper ──────────────────────────────────────────────
async function api(endpoint, options = {}) {
  const token = localStorage.getItem('em_token');
  const headers = { 'Content-Type': 'application/json', ...(options.headers || {}) };
  if (token) headers['Authorization'] = 'Bearer ' + token;

  const res = await fetch(API_BASE + endpoint, {
    credentials: 'same-origin',
    ...options,
    headers
  });

  let data = null;
  try {
    data = await res.json();
  } catch (error) {
    throw new Error('Respons server tidak valid.');
  }

  if (res.status === 401) {
    localStorage.removeItem('em_token');
    localStorage.removeItem('em_user');
  }

  if (!res.ok || data.status === 'error') {
    throw new Error(data.message || 'Terjadi kesalahan');
  }

  return data;
}

// ── Session helpers ──────────────────────────────────────────
function saveSession(token, user) {
  localStorage.setItem('em_token', token);
  localStorage.setItem('em_user', JSON.stringify(user));
}

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
