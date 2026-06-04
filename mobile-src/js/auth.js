import { api } from './api.js';
import { navigate } from './router.js';

export function isAuthenticated() {
  return !!localStorage.getItem('sanctum_token');
}

export function requireAuth() {
  if (!isAuthenticated()) {
    navigate('#/login');
    return false;
  }
  return true;
}

export async function doLogin(email, password) {
  const data = await api.login(email, password);
  localStorage.setItem('sanctum_token', data.token);
  localStorage.setItem('user', JSON.stringify(data.user));
  return data.user;
}

export async function doLogout() {
  try { await api.logout(); } catch {}
  localStorage.removeItem('sanctum_token');
  localStorage.removeItem('user');
  navigate('#/login');
}

export function currentUser() {
  try { return JSON.parse(localStorage.getItem('user')); } catch { return null; }
}
