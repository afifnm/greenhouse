export function renderToast(message, type = 'info') {
  const colors = {
    error: 'bg-red-500',
    success: 'bg-emerald-600',
    info: 'bg-stone-700',
  };

  const toast = document.createElement('div');
  toast.className = `fixed bottom-24 left-4 right-4 z-50 px-4 py-3 rounded-xl text-white text-sm font-medium shadow-lg ${colors[type] ?? colors.info} transition-all`;
  toast.textContent = message;
  document.body.appendChild(toast);

  setTimeout(() => toast.remove(), 3000);
}

export function renderSpinner() {
  return `<div class="flex justify-center py-10">
    <div class="w-8 h-8 border-4 border-emerald-200 border-t-emerald-600 rounded-full animate-spin"></div>
  </div>`;
}

export function renderError(message) {
  return `<div class="mx-4 mt-4 bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">${message}</div>`;
}
