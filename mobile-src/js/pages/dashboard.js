import { requireAuth, currentUser, doLogout } from '../auth.js';
import { api } from '../api.js';
import { renderSpinner, renderError } from '../ui.js';

function formatRupiah(n) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(n ?? 0);
}

function statsCards(data) {
  const stats = [
    { label: 'Penjualan Hari Ini', value: formatRupiah(data.today_revenue), icon: '💰' },
    { label: 'Penjualan Bulan Ini', value: formatRupiah(data.month_revenue), icon: '📈' },
    { label: 'Total Greenhouse', value: data.total_greenhouses ?? '-', icon: '🌿' },
    { label: 'Total Transaksi', value: data.total_transactions ?? '-', icon: '🧾' },
  ];

  return stats.map(s => `
    <div class="bg-white rounded-2xl border border-stone-100 p-4">
      <div class="text-2xl mb-1">${s.icon}</div>
      <div class="text-xs text-stone-500 mb-1">${s.label}</div>
      <div class="text-base font-semibold text-stone-800">${s.value}</div>
    </div>
  `).join('');
}

function recentSales(sales = []) {
  if (!sales.length) return '<p class="text-sm text-stone-400 text-center py-4">Belum ada transaksi</p>';
  return sales.map(s => `
    <div class="flex justify-between items-center py-2.5 border-b border-stone-50 last:border-0">
      <div>
        <div class="text-sm font-medium text-stone-700">${s.buyer_name ?? '-'}</div>
        <div class="text-xs text-stone-400">${s.greenhouse?.name ?? ''} · ${s.created_at_human ?? ''}</div>
      </div>
      <div class="text-sm font-semibold text-emerald-700">${formatRupiah(s.total)}</div>
    </div>
  `).join('');
}

export async function renderDashboard() {
  if (!requireAuth()) return;

  const user = currentUser();
  const app = document.getElementById('app');

  app.innerHTML = `
    <div class="min-h-screen bg-stone-50 pb-20">
      <!-- Header -->
      <div class="bg-emerald-600 pt-12 pb-6 px-4">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-emerald-200 text-xs">Selamat datang,</p>
            <h1 class="text-white text-lg font-semibold">${user?.name ?? 'Pengguna'}</h1>
            <span class="text-xs bg-emerald-700 text-emerald-100 px-2 py-0.5 rounded-full mt-1 inline-block">${user?.role ?? ''}</span>
          </div>
          <button id="logout-btn" class="bg-emerald-700 text-white text-xs px-3 py-1.5 rounded-xl">Keluar</button>
        </div>
      </div>

      <!-- Stats -->
      <div class="px-4 -mt-2">
        <div id="stats-grid" class="grid grid-cols-2 gap-3 mt-4">
          ${renderSpinner()}
        </div>
      </div>

      <!-- Recent sales -->
      <div class="px-4 mt-5">
        <h2 class="text-sm font-semibold text-stone-700 mb-3">Transaksi Terbaru</h2>
        <div id="recent-sales" class="bg-white rounded-2xl border border-stone-100 px-4">
          ${renderSpinner()}
        </div>
      </div>
    </div>

    <!-- Bottom nav -->
    <nav class="fixed bottom-0 inset-x-0 bg-white border-t border-stone-100 flex">
      <button class="flex-1 py-3 flex flex-col items-center text-emerald-600">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        <span class="text-xs mt-0.5">Dashboard</span>
      </button>
    </nav>
  `;

  document.getElementById('logout-btn').addEventListener('click', doLogout);

  try {
    const data = await api.dashboard();
    document.getElementById('stats-grid').innerHTML = statsCards(data);
    document.getElementById('recent-sales').innerHTML = recentSales(data.recent_sales ?? []);
  } catch {
    document.getElementById('stats-grid').innerHTML = renderError('Gagal memuat data');
    document.getElementById('recent-sales').innerHTML = '';
  }
}
