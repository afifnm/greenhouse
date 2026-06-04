import { doLogin } from '../auth.js';
import { navigate } from '../router.js';
import { renderToast } from '../ui.js';

export function renderLogin() {
  document.getElementById('app').innerHTML = `
    <div class="min-h-screen bg-stone-50 flex items-center justify-center p-4">
      <div class="w-full max-w-sm bg-white rounded-2xl shadow-sm border border-stone-100 p-6">
        <div class="text-center mb-6">
          <div class="w-14 h-14 bg-emerald-600 rounded-xl flex items-center justify-center mx-auto mb-3">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h.5A2.5 2.5 0 0020 5.5v-1.65"/>
            </svg>
          </div>
          <h1 class="text-xl font-semibold text-stone-800">Greenhouse</h1>
          <p class="text-sm text-stone-500 mt-1">Masuk ke akun Anda</p>
        </div>

        <form id="login-form" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-stone-700 mb-1">Email</label>
            <input type="email" id="email" required
              class="w-full px-3 py-2 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
              placeholder="email@example.com" />
          </div>
          <div>
            <label class="block text-sm font-medium text-stone-700 mb-1">Password</label>
            <input type="password" id="password" required
              class="w-full px-3 py-2 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
              placeholder="••••••••" />
          </div>
          <button type="submit" id="login-btn"
            class="w-full bg-emerald-600 text-white py-2.5 rounded-xl text-sm font-medium hover:bg-emerald-700 active:bg-emerald-800 transition-colors disabled:opacity-50">
            Masuk
          </button>
        </form>
      </div>
    </div>
  `;

  document.getElementById('login-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('login-btn');
    btn.disabled = true;
    btn.textContent = 'Memuat...';

    try {
      await doLogin(
        document.getElementById('email').value,
        document.getElementById('password').value
      );
      navigate('#/dashboard');
    } catch (err) {
      renderToast(err?.data?.message ?? 'Login gagal', 'error');
      btn.disabled = false;
      btn.textContent = 'Masuk';
    }
  });
}
