// Service Worker für VITARA – macht die App offline-fähig.
// Er speichert die App-Dateien im Browser-Cache und liefert sie aus,
// wenn kein Internet da ist. Deine eingetragenen Daten liegen separat
// im localStorage und sind ohnehin offline verfügbar.

const CACHE = 'vitara-v1';                 // Name/Version des Zwischenspeichers
const ASSETS = [
  './',
  './index.html',
  './manifest.webmanifest',
  './apple-touch-icon-180-v2.png',
  './icon-192-v2.png',
  './icon-512-v2.png',
  './favicon-32-v2.png'
];

// 1) Installation: alle wichtigen Dateien einmalig in den Cache legen
self.addEventListener('install', function(e){
  e.waitUntil(
    caches.open(CACHE).then(function(c){ return c.addAll(ASSETS); })
      .then(function(){ return self.skipWaiting(); })
  );
});

// 2) Aktivierung: alte Cache-Versionen aufräumen
self.addEventListener('activate', function(e){
  e.waitUntil(
    caches.keys().then(function(keys){
      return Promise.all(keys.filter(function(k){ return k !== CACHE; })
        .map(function(k){ return caches.delete(k); }));
    }).then(function(){ return self.clients.claim(); })
  );
});

// 3) Anfragen abfangen
self.addEventListener('fetch', function(e){
  var req = e.request;
  if(req.method !== 'GET') return;
  var url = new URL(req.url);
  if(url.origin !== self.location.origin) return;   // nur eigene Dateien behandeln

  // Beim Öffnen der App (Navigation): erst Netzwerk (immer die neueste Version),
  // bei fehlendem Internet aus dem Cache.
  if(req.mode === 'navigate'){
    e.respondWith(
      fetch(req).then(function(res){
        var copy = res.clone();
        caches.open(CACHE).then(function(c){ c.put(req, copy); });
        return res;
      }).catch(function(){
        return caches.match(req).then(function(r){ return r || caches.match('./index.html'); });
      })
    );
    return;
  }

  // Sonstige Dateien (Icons, Manifest): erst Cache, dann Netzwerk.
  e.respondWith(
    caches.match(req).then(function(cached){
      return cached || fetch(req).then(function(res){
        var copy = res.clone();
        caches.open(CACHE).then(function(c){ c.put(req, copy); });
        return res;
      }).catch(function(){ return cached; });
    })
  );
});
