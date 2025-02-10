const CACHE_NAME = 'getsportnews-v1';
const urlsToCache = [
  '/',
  '/css/style.css',
  '/images/fifa18.jpg',
  // Adaugă aici alte resurse care trebuie cache-uite
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(urlsToCache))
  );
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => response || fetch(event.request))
  );
});
