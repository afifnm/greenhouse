// Minimal hash router
const routes = {};

export function route(hash, fn) {
  routes[hash] = fn;
}

export function navigate(hash) {
  window.location.hash = hash;
}

export function initRouter() {
  function dispatch() {
    const hash = window.location.hash || '#/login';
    const handler = routes[hash];
    if (handler) handler();
    else if (routes['#/404']) routes['#/404']();
  }
  window.addEventListener('hashchange', dispatch);
  dispatch();
}
