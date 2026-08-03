# VITARA – Gesundheits- & Sport-App · Konzept

> Dies ist unsere gemeinsame Konzept-Notiz. Hier sammeln wir alle Ideen, Entscheidungen
> und deine echten Daten, damit über die Sitzungen hinweg nichts verloren geht.
> Der anklickbare Prototyp liegt in `gesundheits-app/index.html`.
> Den Fahrplan „von der Idee zur App im App Store" findest du in `LERNPFAD.md`.

**Stand:** 3. August 2026
**Status:** Konzeptphase (Mockup steht, Inhalte werden gefüllt)
**Ziel-Plattform:** Handy-App (iPhone/Android) – Web-Vorschau vorhanden

---

## 1. Die Idee in einem Satz

Ein persönliches Gesundheits-Cockpit, das **Vitalwerte**, **Nahrungsergänzung** und
**Gelenke** an einem Ort bündelt – ergänzt um einen **Wissens-Bereich** mit aktueller
Forschung und Übungsvideos.

> ⚠️ Kein Medizinprodukt. Die App unterstützt und begleitet, ersetzt aber keine
> ärztliche oder physiotherapeutische Beratung.

---

## 2. Die Bereiche der App (Stand Mockup)

| Bereich | Zweck | Status |
|---|---|---|
| **Übersicht** | Tages-Score + wichtigste Werte auf einen Blick | ✅ im Mockup |
| **Vitalwerte** | Blutdruck, Puls, HRV, Gewicht, Schlaf, SpO₂ mit Trends & Zielbereich | ✅ im Mockup (vertieft) |
| **Nahrungsergänzung** | Einnahme-Plan nach Tageszeit, Abhaken, Vorrats-Warnung | ✅ im Mockup |
| **Gelenke** | Körperkarte, Schmerz-Tagebuch (0–10), Beweglichkeit | ✅ im Mockup |
| **Wissen** | Forschung (Arthrose, HWS) + kuratierte YouTube-Übungen | ✅ im Mockup (Beispielinhalte) |
| **Profil** | Geräte verbinden, Ziele, Arzt-Bericht (PDF), Datenschutz | ✅ im Mockup |

---

## 3. Meine echten Daten (bitte ausfüllen – füllen wir gemeinsam)

### 3.1 Vitalwerte 📈  *(mein Fokus-Bereich)*

**Mein Tracker:** Fitbit Charge 6 → Werte sollen von dort übernommen werden.
Blutdruck und Gewicht gebe ich manuell ein.

| Wert | Tracken? | Quelle | Orientierung / Zielbereich* |
|---|---|---|---|
| Herzfrequenz (Puls) | ✅ ja | Fitbit | Kontext-abhängig (Ruhe/Aktiv) |
| Ruheherzfrequenz | ✅ ja | Fitbit | grob 50–70 bpm (individuell) |
| Herzfrequenzvariabilität (HRV) | ✅ ja | Fitbit | eigener Basiswert, Trend zählt |
| Atemfrequenz | ✅ ja | Fitbit | grob 12–20 / min in Ruhe |
| Schlaf (Dauer, Phasen, Score) | ✅ ja | Fitbit | Ziel z.B. 7–8 h (persönlich) |
| Aktivzonenminuten | ✅ ja | Fitbit | Ziel z.B. 150+ / Woche |
| Blutdruck | ✅ ja | **manuell** | mit Arzt abstimmen (Orientierung < 130/80) |
| Gewicht | ✅ ja | **manuell** | persönliches Ziel offen |
| **Empfohlen, da Charge 6 misst:** | | | |
| SpO₂ (Sauerstoffsättigung) | ⭐ Vorschlag | Fitbit | grob 95–100 % |
| Hauttemperatur-Abweichung | ⭐ Vorschlag | Fitbit | Abweichung vom eigenen Mittel |
| Schritte / Distanz | ⭐ Vorschlag | Fitbit | Ziel z.B. 8.000–10.000 Schritte |
| Cardio-Fitness (VO₂max-Schätzung) | ⭐ Vorschlag | Fitbit | Trend nach oben |
| Stress-Management-Score | ⭐ Vorschlag | Fitbit | höher = entspannter |

*\*Orientierungswerte sind allgemein und ersetzen keine ärztliche Beurteilung.
Deine persönlichen Zielbereiche stimmen wir/du mit dem Arzt ab.*

**Technische Notiz (für später):** Fitbit-Daten holen wir über die offizielle
Fitbit-Schnittstelle (Web-API) bzw. auf Android über „Health Connect". Das ist
ein eigener Lernschritt in **Etappe 4** – jetzt nur vormerken, noch nichts tun.

### 3.2 Nahrungsergänzung 💊

Meine echten Präparate, Dosierung und Einnahmezeit:

| Präparat | Dosierung | Wann (morgens/mittags/abends) | Wofür |
|---|---|---|---|
| _z.B. Vitamin D3+K2_ | _offen_ | _offen_ | _offen_ |
| | | | |

### 3.3 Gelenke 🦵

Welche Gelenke sind betroffen und wie?

| Gelenk / Bereich | Beschwerde | Seit wann | Was hilft bisher |
|---|---|---|---|
| _z.B. rechtes Knie_ | _offen_ | _offen_ | _offen_ |
| _HWS / Nacken_ | _offen_ | _offen_ | _offen_ |
| | | | |

### 3.4 Wissens-Themen 🔎

Themen, die mich besonders interessieren (für Forschung & Videos):
- Arthrose
- HWS-Syndrom (Halswirbelsäule)
- Schulterschmerzen
- Nackenschmerzen
- Hüftschmerzen
- _weitere?_

---

## 4. Entscheidungen (was wir schon festgelegt haben)

- ✅ Name des Konzepts: **VITARA** (Arbeitstitel, änderbar)
- ✅ Drei Kernbereiche + Wissens-Bereich
- ✅ Farb-Logik: Grün = Vitalwerte · Bernstein = Nahrungsergänzung · Koralle = Gelenke
- ✅ Ziel: Handy-App; vorerst als Web-Mockup zum Durchklicken
- ✅ Projekt liegt im Repo unter `gesundheits-app/` auf eigenem Branch (noch nicht öffentlich)

---

## 5. Offene Fragen / nächste Schritte

- [x] Echte Vitalwerte + Quellen eintragen (Abschnitt 3.1) → Fitbit Charge 6 + manuell
- [ ] Persönliche Zielbereiche (Blutdruck, Gewicht, Schlaf) mit Arzt abstimmen
- [ ] Echte Präparate-Liste aufnehmen (Abschnitt 3.2)
- [ ] Betroffene Gelenke konkretisieren (Abschnitt 3.3)
- [ ] Entscheiden: Bleibt der Name „VITARA"?
- [ ] Etappe 4: Fitbit-Daten anbinden (Web-API / Health Connect)
- [ ] Später: Woher kommen die echten Forschungs-Artikel & Videos?

---

## 6. Ideen-Parkplatz 💡

Platz für spontane Einfälle, die uns unterwegs kommen:

- _…_
