const CACHE_NAME = 'kang-naryo-v1.0.0';

// Daftar aset inti yang di-pre-cache untuk loading instan
const PRECACHE_ASSETS = [
  '/',
  '/pos',
  '/menu',
  '/manifest.json',
  '/assets/images/icon-192.png',
  '/assets/images/icon-512.png',
  'https://cdn.tailwindcss.com',
  'https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js',
  'https://cdn.jsdelivr.net/npm/dexie@3.2.4/dist/dexie.min.js',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
  'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap'
];

// Install Event: Cache essential assets
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      console.log('[SW] Pre-caching offline essentials');
      // Gunakan addAll dengan toleransi gagal parsial agar tidak menggagalkan install jika CDN offline
      return Promise.allSettled(
        PRECACHE_ASSETS.map((url) =>
          cache.add(url).catch((err) => console.warn('[SW] Could not pre-cache:', url, err))
        )
      );
    }).then(() => self.skipWaiting())
  );
});

// Activate Event: Bersihkan cache versi lama
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((name) => {
          if (name !== CACHE_NAME) {
            console.log('[SW] Deleting old cache:', name);
            return caches.delete(name);
          }
        })
      );
    }).then(() => self.clients.claim())
  );
});

// Fetch Event: Network-first untuk API & dynamic routes, Cache-first / Stale-while-revalidate untuk static assets
self.addEventListener('fetch', (event) => {
  const url = new URL(event.request.url);

  // Abaikan non-GET requests (misalnya POST pembayaran, update data)
  if (event.request.method !== 'GET') {
    return;
  }

  // Khusus API orders / payment: Network only agar data pembayaran selalu valid & realtime
  if (url.pathname.startsWith('/api/payment') || url.pathname.startsWith('/api/orders/cash')) {
    return;
  }

  // Khusus API products & categories: Stale-While-Revalidate agar kasir instan tanpa blank
  if (url.pathname.startsWith('/api/products') || url.pathname.startsWith('/api/categories')) {
    event.respondWith(
      caches.open(CACHE_NAME).then(async (cache) => {
        const cachedResponse = await cache.match(event.request);
        const fetchPromise = fetch(event.request).then((networkResponse) => {
          if (networkResponse && networkResponse.status === 200) {
            cache.put(event.request, networkResponse.clone());
          }
          return networkResponse;
        }).catch(() => cachedResponse);

        return cachedResponse || fetchPromise;
      })
    );
    return;
  }

  // Static Assets (CDN, Images, CSS, JS, Fonts): Cache-First dengan fallback ke network
  if (
    url.hostname.includes('cdn') ||
    url.hostname.includes('cdnjs') ||
    url.hostname.includes('fonts') ||
    url.pathname.match(/\.(png|jpg|jpeg|webp|svg|css|js|woff2|woff|ttf)$/)
  ) {
    event.respondWith(
      caches.match(event.request).then((cachedResponse) => {
        if (cachedResponse) {
          return cachedResponse;
        }
        return fetch(event.request).then((networkResponse) => {
          if (networkResponse && networkResponse.status === 200) {
            const responseToCache = networkResponse.clone();
            caches.open(CACHE_NAME).then((cache) => {
              cache.put(event.request, responseToCache);
            });
          }
          return networkResponse;
        });
      })
    );
    return;
  }

  // HTML Pages: Network first dengan fallback ke Cache
  event.respondWith(
    fetch(event.request)
      .then((networkResponse) => {
        if (networkResponse && networkResponse.status === 200) {
          const responseToCache = networkResponse.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(event.request, responseToCache);
          });
        }
        return networkResponse;
      })
      .catch(() => caches.match(event.request))
  );
});
