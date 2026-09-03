# Lernpfad: Von der Idee zur eigenen App im App Store

> Dieser Pfad ist unser gemeinsamer Fahrplan. Ich (Claude) erkläre dir bei jedem
> Schritt, **was** wir tun und **warum** – in kleinen, verständlichen Häppchen.
> Wir gehen das in deinem Tempo an. Kein Vorwissen nötig.

**Stand:** 3. August 2026
**Wo wir gerade stehen:** 🟢 Etappe 1 (Konzept & Mockup) – fast fertig

---

## Das große Bild: 3 Wege zur „echten" App

Es gibt grob drei Wege, aus unserem Web-Mockup eine echte App zu machen.
Sie bauen aufeinander auf – man kann klein anfangen und wachsen.

| Weg | Was es ist | Aufwand | App Store möglich? |
|---|---|---|---|
| **A) PWA** (Web-App zum Installieren) | Unsere HTML-Seite wird „installierbar" – Icon auf dem Homescreen, funktioniert offline | 🟢 gering | ⚠️ Nur eingeschränkt (Apple listet PWAs kaum) |
| **B) Web-App verpacken** (z.B. mit *Capacitor*) | Unser Web-Code wird in eine echte iOS-/Android-App „eingewickelt" | 🟡 mittel | ✅ Ja – App Store & Google Play |
| **C) Native App** (*Flutter* oder *React Native*) | Von Grund auf mit App-Technik gebaut, tiefste Geräte-Integration | 🔴 hoch | ✅ Ja |

**Meine Empfehlung für dich (Lernender):** **A → B.**
Wir machen erst eine **PWA** (baut direkt auf dem auf, was wir schon haben),
und wenn du dann in den App Store willst, **verpacken** wir dieselbe App mit
*Capacitor*. So lernst du Schritt für Schritt und wirfst nichts weg.
Weg C heben wir uns auf, falls wir später sehr tiefe Funktionen brauchen.

---

## Die Etappen (unser Fahrplan)

### 🟢 Etappe 1 – Konzept & Mockup  *(fast fertig)*
- [x] Idee & Bereiche festgelegt
- [x] Anklickbares Mockup gebaut (`index.html`)
- [x] Konzept-Notiz angelegt (`KONZEPT.md`)
- [ ] Echte Inhalte einfüllen (Vitalwerte, Präparate, Gelenke)
- **Was du hier lernst:** Wie man eine App-Idee strukturiert und visualisiert.

### 🟢 Etappe 2 – Aus dem Mockup wird eine echte Web-App  *(läuft)*
- [x] **Lektion 1:** Die 3 Bausteine verstehen (HTML, CSS, JavaScript) → `lernen/lektion-1.html`
- [x] **Erstes echtes Feature:** Nahrungsergänzung wird lokal gespeichert (localStorage)
      + Tageszähler zählt live mit → die App „merkt" sich jetzt Eingaben ✅
- [x] **Gelenke echt:** Schmerzwert (0–10) pro Gelenk speichern + Wärme/Sauna & Bewegung ✅
- [x] **Training echt:** Übungen speichern + Wochenzählung (2×/Woche, Mo–So-Streifen) ✅
- [x] **Erste Auswertung (Stufe 1):** 7-Tage-Schmerztrend pro Gelenk ✅
- [x] **Wissen echt & filterbar:** kuratierte Links aus `wissen.json`, Suche/Themen/Archiv,
      Favoriten (max. 5 Videos + 5 Artikel), monatliche Auto-Aktualisierung ✅
- [x] **Vitalwerte echt:** Blutdruck, Ruhepuls & Gewicht selbst eintragen + 30-Tage-Verlauf
      (Blutdruck/Ruhepuls/Gewicht umschaltbar) — dieselbe Anzeige, in die später Fitbit einfließt ✅
- [ ] Eigenes Datenmodell / Struktur der gespeicherten Daten sauber aufsetzen
- **Was du hier lernst:** Wie eine App „von innen" funktioniert und Daten behält.

### ⚪ Fitbit-Anbindung (eigener Meilenstein, nach dem Fundament)
- [ ] **Google-Cloud-Projekt** anlegen + OAuth-Zugangsdaten (Client-ID) — **nur Oliver kann das**
- [ ] Anmelde-/Erlaubnis-Ablauf (Google OAuth 2.0) einbauen
- [ ] Werte abholen und in denselben `vital.*`-Speicher wie die manuelle Eingabe schreiben
- [ ] Kleiner Server-Baustein prüfen (unsere App ist rein statisch; Google-Token-Tausch braucht meist ein Backend)

**Stand der Technik (recherchiert am 3. Sept. 2026):**
- ⚠️ Die **alte Fitbit-Web-API wird zum 30. September 2026 abgeschaltet.** Neu-Anbindungen
  müssen die **Google Health API** (Google-Login, Google OAuth 2.0) nutzen; alte Tokens gelten
  nicht weiter, jede Nutzerin/jeder Nutzer muss neu zustimmen.
- ⚠️ **„Live/Echtzeit" gibt es bei Fitbit nicht.** Die Uhr synchronisiert periodisch (Minuten
  bis Stunden); die API liefert immer nur die **zuletzt synchronisierten** Werte.
- 🩺 Für Gesundheitsdaten prüft Google die App strenger — das kann dauern.
- Quellen: developers.google.com/health/migration · community.fitbit.com (Web-API-Phase-out).

### 🟢 Etappe 3 – PWA: installierbar & offline  *(erledigt)*
- [x] Manifest + eigenes App-Icon → auf dem iPhone-Home-Bildschirm getestet ✅
- [x] Service Worker hinzugefügt → App startet & läuft **offline** (`sw.js`) ✅
- **Was du hier lernst:** Was eine Web-App von einer „App" unterscheidet.

### ⚪ Etappe 4 – In eine echte App verpacken (Capacitor)
- [ ] Projekt mit Capacitor einrichten
- [ ] Auf Handy-Simulator und echtem Gerät testen
- [ ] Ggf. Gesundheitsdaten anbinden (Apple Health / Google Health Connect)
- **Was du hier lernst:** Wie aus Web-Code eine Store-fähige App wird.

### ⚪ Etappe 5 – Veröffentlichung im App Store / Google Play
- [ ] Entwickler-Konten anlegen
- [ ] App-Icon, Screenshots, Beschreibung, Datenschutzerklärung
- [ ] Test über TestFlight (Apple) / internen Test (Google)
- [ ] Einreichen & durch die Prüfung („App Review")
- **Was du hier lernst:** Den kompletten Veröffentlichungs-Prozess.

---

## Was du für den App Store realistisch brauchst

Damit es keine Überraschungen gibt – hier die echten Rahmenbedingungen:

**Kosten**
- **Apple Developer Program:** 99 US-Dollar **pro Jahr** (Pflicht, um im App Store zu veröffentlichen).
- **Google Play:** einmalig ca. 25 US-Dollar (nur einmal, nicht jährlich).
- Provision: Apple/Google behalten bei *bezahlten* Apps/Käufen 15–30 % ein.
  Für eine kostenlose App fällt das **nicht** an.

**Technik**
- Für **iOS/Apple** braucht man zum Bauen einen **Mac** mit dem Programm **Xcode**.
  → Falls du keinen Mac hast: Es gibt Cloud-Dienste, die das Bauen übernehmen –
  klären wir, wenn wir so weit sind. Kein Grund, jetzt etwas zu kaufen.
- Für **Android** reicht ein normaler PC/Windows.

**Besonderheit Gesundheits-App** 🩺
- Gesundheitsdaten sind **besonders sensibel**. In Deutschland/EU gilt die **DSGVO**.
  → Wir speichern Daten möglichst **lokal auf dem Gerät** und gehen sparsam damit um.
- Apple & Google prüfen Gesundheits-Apps **strenger**. Wichtig sind klare
  **Hinweise** („ersetzt keine ärztliche Beratung") und Ehrlichkeit bei dem, was die App kann.
- Keine Heilversprechen, keine Diagnosen – die App ist ein **Begleiter**, kein Arzt.

---

## Wichtig zum Mitnehmen 💚

- Wir müssen **nicht alles auf einmal** können. Jede Etappe ist ein eigenes kleines Lernpaket.
- Alles, was wir in Etappe 1–3 bauen, **verwenden wir in Etappe 4–5 weiter**. Nichts ist umsonst.
- Du brauchst **jetzt** noch nichts kaufen und keinen Mac. Erst ganz am Ende (Etappe 5).
- Bei jedem Schritt erkläre ich dir die neuen Begriffe. Frag **immer** nach, wenn etwas unklar ist.

---

## Quellen (Stand August 2026)
- Apple Developer Program – Kosten & Anforderungen: siehe App-Store-Guides 2026
- Xcode/Mac-Anforderung für iOS-Einreichungen: Apple Developer Forum

---

## 🌐 Live-Adressen der App (Stand August 2026)

Die App ist an **zwei** Adressen erreichbar – beide aktualisieren sich automatisch,
sobald wir etwas auf `main` veröffentlichen:

1. **GitHub Pages (Subdomain):** `https://gesundheit.oliver-rock.de/gesundheits-app/`
   → aktualisiert sich automatisch bei jeder Veröffentlichung.
2. **IONOS-Webspace (eigene Domain):** `https://oliver-rock.de/gesundheitsapp/`
   → wird per GitHub-Action (SFTP) automatisch mit hochgeladen.

**Automatischer IONOS-Upload:** `.github/workflows/deploy-ionos.yml`
(Zugangsdaten liegen sicher in den GitHub-Secrets, neues SFTP-Konto `u72279346-github`).
