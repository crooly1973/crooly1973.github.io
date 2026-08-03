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
| **Training** | Krafttraining 2×/Woche, Übungen, Wochenziel, Fortschritt | ✅ im Mockup |
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
| **Zusätzlich (Charge 6 misst sie ohnehin):** | | | |
| SpO₂ (Sauerstoffsättigung) | ✅ ja | Fitbit | grob 95–100 % |
| Hauttemperatur-Abweichung | ✅ ja | Fitbit | Abweichung vom eigenen Mittel |
| Schritte / Distanz | ✅ ja | Fitbit | Ziel z.B. 8.000–10.000 Schritte |
| Cardio-Fitness (VO₂max-Schätzung) | ✅ ja | Fitbit | Trend nach oben |
| Stress-Management-Score | ✅ ja | Fitbit | höher = entspannter |

*\*Orientierungswerte sind allgemein und ersetzen keine ärztliche Beurteilung.
Deine persönlichen Zielbereiche stimmen wir/du mit dem Arzt ab.*

**Technische Notiz (für später):** Fitbit-Daten holen wir über die offizielle
Fitbit-Schnittstelle (Web-API) bzw. auf Android über „Health Connect". Das ist
ein eigener Lernschritt in **Etappe 4** – jetzt nur vormerken, noch nichts tun.

### 3.2 Nahrungsergänzung 💊

Meine echten Präparate, sortiert nach Tageszeit:

**🌅 Morgens**

| Präparat | Marke / Form | Dosierung | Einnahme | Wofür (allgemein) |
|---|---|---|---|---|
| Vitamin D3 + K2 | natural elements (MK-7, Tabletten) | 1 Tablette | morgens | Knochen & Immunsystem |
| Kreatin Monohydrat | WeightWorld (Tabletten) | 3000 mg pro Portion | morgens (zusammen mit D3) | Kraft & Sport |
| Vitamin B12 | Cosphera (Spray) | 1 Sprühstoß = 25 µg (Methylcobalamin) | morgens nach dem Zähneputzen | Nerven & Energie |

**🌙 Abends**

| Präparat | Marke / Form | Dosierung | Einnahme | Wofür (allgemein) |
|---|---|---|---|---|
| Magnesium | Verla N (Dragées) | 40 mg Magnesium / Tablette (Citrat 205 mg + Glutamat 90 mg) | abends vor dem Schlafen | Muskeln & Entspannung |

*Angaben laut Verpackung/deinen Fotos. „Wofür" ist allgemein beschrieben –
keine Heilaussagen. Dosierung ggf. später präzisieren.*

### 3.3 Gelenke 🦵

**Auffälliges Muster:** Fast alle Beschwerden sind auf der **rechten Körperseite**.

| Gelenk / Bereich | Beschwerde | Seit wann | Was hilft |
|---|---|---|---|
| Rechte Hüfte | **Künstliches Hüftgelenk** (Hüft-OP) | Mai 2025 | Bewegung, Wärme |
| Nacken / HWS | Wiederkehrende Schmerzen | fortlaufend | Bewegung, Sauna / Wärme |
| Rechte Schulter | Schmerz, wechselnd (mal mehr, mal weniger) | fortlaufend | Bewegung, Wärme |
| Rechtes Sprunggelenk | Schmerzen | fortlaufend | Bewegung, Wärme |

**Beschwerdebild allgemein:**
- Vor allem **Schmerzen** in Schulter, Hals/Nacken/HWS.
- **Morgens leichte Steifigkeit**, aber gut im Griff.
- **Was gut hilft:** Bewegung und **Sauna / Wärme** (Wärme tut besonders gut).

**Ideen für die App (aus diesen Angaben abgeleitet):**
- „Rechte Seite"-Ansicht in der Körperkarte hervorheben.
- Schnell-Eintrag „Wärme/Sauna gemacht" + Wirkung dokumentieren.
- Sanfte Beweglichkeits-/Mobilisationsübungen für Nacken, Schulter, Hüfte.
- Rücksicht auf künstliche Hüfte: Übungen, die dafür geeignet sind (mit Physio/Arzt abstimmen).

### 3.4 Wissens-Themen 🔎

Themen, die mich besonders interessieren (für Forschung & Videos):
- Arthrose
- HWS-Syndrom (Halswirbelsäule)
- Schulterschmerzen
- Nackenschmerzen
- Hüftschmerzen
- _weitere?_

### 3.5 Sport / Training 🏋️

**Rhythmus:** Krafttraining **2× pro Woche**, viel mit eigenem Körpergewicht + Hanteln.

| Übung | Art | Vorgabe (Startwert) | Hinweis |
|---|---|---|---|
| Liegestütz | Körpergewicht (erhöht, an Badewannenkante) | 3 Sätze × 50 | gelenkschonend (schräg) |
| Kniebeugen | Körpergewicht | 3 Sätze × 15 | auf Hüftprothese achten |
| Bizeps-Curls | Hanteln (2×5 kg) | 3 Sätze × 12 | — |
| Schulterdrücken | Hanteln | 3 Sätze × 10 | **schulterschonend** (re. Schulter) |

**App-Funktionen (im Mockup):**
- Wochenübersicht mit Ziel „2 Einheiten" und Fortschritt.
- Übungen abhaken, „Workout starten".
- Fortschritts-Diagramm (z.B. max. Wiederholungen über Zeit).
- Verknüpfung mit Gelenken: Übungen schulter-/hüftschonend markiert.

*Sätze/Wiederholungen/Gewichte sind Startwerte – gern später an deinen Stand anpassen.*

---

## 4. Entscheidungen (was wir schon festgelegt haben)

- ✅ Name des Konzepts: **VITARA** (Arbeitstitel, änderbar)
- ✅ Drei Kernbereiche + Wissens- & Trainings-Bereich
- ✅ **Design-Richtung:** hell, **Koralle + Periwinkle** (Mix aus Richtung A & B),
  mit handgezeichneten Akzenten, sanfter Bewegung und KI-Tipp oben (statt Grün)
- ✅ Farb-Logik: Koralle = Brand/Gelenke · Periwinkle = Vitalwerte · Bernstein = Nahrungsergänzung
- ✅ Ziel: Handy-App; vorerst als Web-Mockup zum Durchklicken
- ✅ Projekt liegt im Repo unter `gesundheits-app/` auf eigenem Branch (noch nicht öffentlich)

---

## 5. Offene Fragen / nächste Schritte

- [x] Echte Vitalwerte + Quellen eintragen (Abschnitt 3.1) → Fitbit Charge 6 + manuell
- [ ] Persönliche Zielbereiche (Blutdruck, Gewicht, Schlaf) mit Arzt abstimmen
- [x] Echte Präparate-Liste aufnehmen (Abschnitt 3.2) → D3+K2, Kreatin, B12, Magnesium
- [x] Betroffene Gelenke konkretisieren (Abschnitt 3.3) → Hüfte re. (Prothese), HWS/Nacken, Schulter re., Sprunggelenk re.
- [x] **Etappe 1 (Konzept & Inhalte) abgeschlossen! 🎉**
- [ ] Entscheiden: Bleibt der Name „VITARA"?
- [ ] Etappe 4: Fitbit-Daten anbinden (Web-API / Health Connect)
- [ ] Später: Woher kommen die echten Forschungs-Artikel & Videos?

---

## 6. Ideen-Parkplatz 💡

Platz für spontane Einfälle, die uns unterwegs kommen:

- _…_
