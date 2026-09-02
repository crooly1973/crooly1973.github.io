// Service Worker für VITARA – macht die App offline-fähig.
// Speichert die App-Dateien im Browser-Cache und liefert sie ohne Internet aus.
// Deine Eingaben liegen im localStorage und sind ohnehin offline verfügbar.

const CACHE = 'vitara-v2';                 // Version erhöht -> alter (fehlerhafter) Cache wird gelöscht
const ASSETS = [
  './index.html',                          // NUR direkte Datei-URLs (keine Weiterleitungen!)
  './manifest.webmanifest',
  './apple-touch-icon-180-v2.png',
  './icon-192-v2.png',
  './icon-512-v2.png',
  './favicon-32-v2.png'
];

// 1) Installation: wichtige Dateien in den Cache legen
self.addEventListener('install', function(e){
  e.waitUntil(
    caches.open(CACHE).then(function(c){ return c.addAll(ASSETS); })
      .then(function(){ return self.skipWaiting(); })
  );
});

// 2) Aktivierung: alte Cache-Versionen aufräumen und sofort übernehmen
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
  if(new URL(req.url).origin !== self.location.origin) return;  // nur eigene Dateien

  // Beim Öffnen der App (Navigation): die Datei DIREKT laden (kein Redirect-Problem),
  // bei Internet die neueste Version, ohne Internet aus dem Cache.
  if(req.mode === 'navigate'){
    e.respondWith((async function(){
      try {
        var res = await fetch('./index.html', { cache: 'no-store' });
        if(res && res.ok){
          var c = await caches.open(CACHE);
          c.put('./index.html', res.clone());
          return res;
        }
      } catch(err){ /* offline -> unten Cache */ }
      var cached = await caches.match('./index.html');
      return cached || new Response('Offline und noch nichts gespeichert.', { headers: { 'Content-Type': 'text/plain; charset=utf-8' } });
    })());
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
