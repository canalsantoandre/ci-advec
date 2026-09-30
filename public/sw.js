// Service Worker minimal para suporte a PWA / Standalone no Portal do Voluntário
self.addEventListener('install', (event) => {
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(clients.claim());
});

self.addEventListener('fetch', (event) => {
  // Pass-through padrão de requisições de rede
  event.respondWith(fetch(event.request).catch(() => {
    return fetch(event.request);
  }));
});
