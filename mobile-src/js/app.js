import { route, initRouter, navigate } from './router.js';
import { isAuthenticated } from './auth.js';
import { renderLogin } from './pages/login.js';
import { renderDashboard } from './pages/dashboard.js';

// Listen for forced logout (401)
window.addEventListener('auth:logout', () => {
  navigate('#/login');
});

route('#/login', () => {
  if (isAuthenticated()) return navigate('#/dashboard');
  renderLogin();
});

route('#/dashboard', renderDashboard);

// Start
initRouter();
