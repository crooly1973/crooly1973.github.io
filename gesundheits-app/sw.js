// AUFRÄUM-SERVICE-WORKER (Kill-Switch)
// Zweck: den zuvor installierten, fehlerhaften Service Worker sicher entfernen,
// seinen Zwischenspeicher löschen und die Seite frisch aus dem Netz laden.
// Es wird nichts mehr abgefangen -> die App lädt wieder ganz normal.

self.addEventListener('install', function(){ self.skipWaiting(); });

self.addEventListener('activate', function(e){
  e.waitUntil((async function(){
    try {
      var keys = await caches.keys();
      await Promise.all(keys.map(function(k){ return caches.delete(k); }));   // alten Cache löschen
      await self.registration.unregister();                                    // sich selbst abmelden
      var clients = await self.clients.matchAll({ type: 'window' });
      clients.forEach(function(c){ c.navigate(c.url); });                      // Seiten neu laden
    } catch(err){ /* egal */ }
  })());
});

// Kein 'fetch'-Handler mehr: der Browser lädt alle Dateien direkt vom Server.
