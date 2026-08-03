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

### ⚪ Etappe 2 – Aus dem Mockup wird eine echte Web-App
- [ ] Grundlagen HTML / CSS / JavaScript verstehen (die 3 Bausteine jeder Web-App)
- [ ] Die Klick-Buttons mit echter Logik verbinden
- [ ] Daten speichern (erst im Browser, später in einer kleinen Datenbank)
- **Was du hier lernst:** Wie eine App „von innen" funktioniert.

### ⚪ Etappe 3 – PWA: installierbar machen
- [ ] „Manifest" + „Service Worker" hinzufügen (macht die Web-App installierbar & offline-fähig)
- [ ] Auf dem eigenen Handy testen (Icon auf den Homescreen legen)
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
