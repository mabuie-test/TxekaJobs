self.addEventListener('install', (event) => {
    event.waitUntil(caches.open('txekajobs-v1').then((cache) => cache.addAll(['/','/manifest.json'])));
});

self.addEventListener('fetch', (event) => {
    event.respondWith(
        caches.match(event.request).then((response) => response || fetch(event.request))
    );
});
