// Service Worker minimal para suporte a PWA / Standalone no Portal do Voluntário
self.addEventListener('install', (event) => {
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(clients.claim());
});

self.addEventListener('fetch', (event) => {
  // Deixa as requisições fluírem normalmente pela rede sem interceptação invasiva
  return;
});

