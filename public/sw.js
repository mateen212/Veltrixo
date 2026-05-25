// Veltrixo PWA Service Worker
// Enables offline access for riders and caches delivery lists

const CACHE_NAME    = 'veltrixo-v1';
const RUNTIME_CACHE = 'veltrixo-runtime-v1';

// Assets to pre-cache on install
const PRECACHE_URLS = [
    '/',
    '/offline.html',
];

// ── Install ──────────────────────────────────────────────────────────────────
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(PRECACHE_URLS);
        }).then(() => self.skipWaiting())
    );
});

// ── Activate ─────────────────────────────────────────────────────────────────
self.addEventListener('activate', (event) => {
    const currentCaches = [CACHE_NAME, RUNTIME_CACHE];
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return cacheNames.filter((c) => !currentCaches.includes(c));
        }).then((cachesToDelete) => {
            return Promise.all(cachesToDelete.map((c) => caches.delete(c)));
        }).then(() => self.clients.claim())
    );
});

// ── Fetch ─────────────────────────────────────────────────────────────────────
self.addEventListener('fetch', (event) => {
    const url = new URL(event.request.url);

    // Skip non-GET and cross-origin requests
    if (event.request.method !== 'GET' || url.origin !== location.origin) {
        return;
    }

    // Skip Reverb WebSocket and Inertia navigation requests
    if (url.pathname.startsWith('/app/') || event.request.headers.get('X-Inertia')) {
        return;
    }

    // Network-first for rider delivery endpoints (stay fresh)
    if (url.pathname.startsWith('/rider/')) {
        event.respondWith(networkFirst(event.request));
        return;
    }

    // Cache-first for static assets (build/)
    if (url.pathname.startsWith('/build/')) {
        event.respondWith(cacheFirst(event.request));
        return;
    }

    // Stale-while-revalidate for everything else
    event.respondWith(staleWhileRevalidate(event.request));
});

// ── Background Sync — queue offline delivery status updates ──────────────────
self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-delivery-updates') {
        event.waitUntil(syncDeliveryUpdates());
    }
});

async function syncDeliveryUpdates() {
    const db     = await openIDB();
    const tx     = db.transaction('pendingUpdates', 'readwrite');
    const store  = tx.objectStore('pendingUpdates');
    const items  = await storeGetAll(store);

    for (const item of items) {
        try {
            await fetch(item.url, {
                method:  item.method,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': item.csrf },
                body:    JSON.stringify(item.body),
            });
            await store.delete(item.id);
        } catch {
            // Will retry on next sync
        }
    }

    return tx.done;
}

// ── Helpers ───────────────────────────────────────────────────────────────────
async function networkFirst(request) {
    try {
        const response = await fetch(request);
        const cache    = await caches.open(RUNTIME_CACHE);
        cache.put(request, response.clone());
        return response;
    } catch {
        return caches.match(request) || caches.match('/offline.html');
    }
}

async function cacheFirst(request) {
    const cached = await caches.match(request);
    if (cached) return cached;
    const response = await fetch(request);
    const cache    = await caches.open(RUNTIME_CACHE);
    cache.put(request, response.clone());
    return response;
}

async function staleWhileRevalidate(request) {
    const cache  = await caches.open(RUNTIME_CACHE);
    const cached = await cache.match(request);
    const fresh  = fetch(request).then((response) => {
        cache.put(request, response.clone());
        return response;
    }).catch(() => null);
    return cached || await fresh || caches.match('/offline.html');
}

function openIDB() {
    return new Promise((resolve, reject) => {
        const req = indexedDB.open('veltrixo-offline', 1);
        req.onupgradeneeded = (e) => {
            e.target.result.createObjectStore('pendingUpdates', { keyPath: 'id', autoIncrement: true });
        };
        req.onsuccess  = (e) => resolve(e.target.result);
        req.onerror    = (e) => reject(e.target.error);
    });
}

function storeGetAll(store) {
    return new Promise((resolve, reject) => {
        const req = store.getAll();
        req.onsuccess = (e) => resolve(e.target.result);
        req.onerror   = (e) => reject(e.target.error);
    });
}
