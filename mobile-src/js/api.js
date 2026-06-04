// Base URL Laravel API — ganti sesuai environment
const API_BASE = import.meta.env.VITE_API_URL ?? 'http://localhost:8000/api/v1';

function getToken() {
  return localStorage.getItem('sanctum_token');
}

async function request(method, path, body = null) {
  const headers = { 'Content-Type': 'application/json', Accept: 'application/json' };
  const token = getToken();
  if (token) headers['Authorization'] = `Bearer ${token}`;

  const res = await fetch(`${API_BASE}${path}`, {
    method,
    headers,
    body: body ? JSON.stringify(body) : null,
  });

  if (res.status === 401) {
    localStorage.removeItem('sanctum_token');
    window.dispatchEvent(new Event('auth:logout'));
    throw new Error('Unauthenticated');
  }

  const data = await res.json().catch(() => ({}));
  if (!res.ok) throw { status: res.status, data };
  return data;
}

export const api = {
  get: (path) => request('GET', path),
  post: (path, body) => request('POST', path, body),
  put: (path, body) => request('PUT', path, body),
  patch: (path, body) => request('PATCH', path, body),
  delete: (path) => request('DELETE', path),

  login: (email, password) => request('POST', '/login', { email, password }),
  logout: () => request('POST', '/logout'),
  me: () => request('GET', '/me'),
  dashboard: () => request('GET', '/dashboard'),
};
