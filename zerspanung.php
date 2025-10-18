<?php require 'session_check.php';
include 'header.php';
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Drehrechner</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      font-family: sans-serif;
      margin: 20px;
      background-color: #0a0f14;
      color: #e0e1dd;
      background-image: url('A_digital_vector_illustration_depicts_a_CNC_lathe_.png');
      background-repeat: no-repeat;
      background-size: contain;
      background-position: center center;
      background-attachment: fixed;
      background-blend-mode: multiply;
    }
    @media (max-width: 600px) {
      body { margin: 10px; }
      input, select, button { font-size: 1.1em; }
    }
    label { font-weight: bold; display: block; margin-top: 10px; }
    input, select, button {
      padding: 6px;
      width: 100%;
      margin-bottom: 10px;
      background: #415a77;
      color: #e0e1dd;
      border: 1px solid #778da9;
    }
    .result {
      margin-top: 20px;
      background: #1b263b;
      padding: 10px;
      border: 1px solid #415a77;
      white-space: pre-wrap;
      display: none;
    }
    .warn { color: #ffba08; font-weight: bold; margin-top: 10px; }
    .over { color: #ff4d6d; font-weight: bold; margin-top: 10px; }
    h2 { color: #e0e1dd; }
  </style>
  <script>
    let materialien = [], platten = [];
    const gruppenMap = { P:"Stahl", M:"Edelstahl", K:"Gusseisen", N:"NE-Metalle", S:"Superlegierungen", H:"gehärteter Stahl" };

    async function ladeDaten() {
      const res = await fetch('load.php');
      const data = await res.json();
      materialien = data.materialien;
      platten = data.platten;
      fuelleMaterialDropdown();
      fuellePlattenDropdown();
      vcQuelleGeaendert();
      modusGeaendert();
      berechne();
    }

    function fuelleMaterialDropdown() {
      const sel = document.getElementById('material');
      sel.innerHTML = '';
      materialien.forEach((m, i) => sel.innerHTML += `<option value="${i}">${m.name} (${m.gruppe} – ${gruppenMap[m.gruppe]})</option>`);
    }

    function fuellePlattenDropdown() {
      const sel = document.getElementById('platte');
      sel.innerHTML = '';
      sel.innerHTML += `<option value="-1">Keine Platte (manuell)</option>`;
      platten.forEach((p, i) => sel.innerHTML += `<option value="${i}">${p.name} (${p.typ}) – für ${p.gruppen.split(',').map(g=>gruppenMap[g]).join(', ')}</option>`);
    }

    function modusGeaendert() {
      const m = document.getElementById('modus').value;
      document.getElementById('drehzahlEingabe').style.display = (m === 'n') ? 'block' : 'none';
    }

    function vcQuelleGeaendert() {
      const q = document.getElementById('vcQuelle').value;
      const schnC = document.getElementById('schneidstoffContainer');
      // Schneidstoff ist nur für Quelle "Material" relevant
      schnC.style.display = (q === 'material') ? 'block' : 'none';
    }

    function platteGeaendert() {
      const idx = parseInt(document.getElementById('platte').value);
      const vcSel = document.getElementById('vcQuelle');
      // Wenn Platte eine vc hat, bleibt "Material" Standard, aber Auswahl "Platte" möglich
      if (!isNaN(idx) && idx >= 0 && platten[idx] && platten[idx].vc) {
        // optional könnte man hier automatisch auf "Platte" umschalten
        // vcSel.value = 'platte';
      }
      berechne();
    }

    function feedModeChanged() {
      const mode = document.getElementById('feedMode').value;
      const label = document.getElementById('feedLabel');
      if (mode === 'f') label.textContent = 'Vorschub f (mm/U):';
      else label.textContent = 'Vorschub vf (mm/min):';
      berechne();
    }

    function berechne() {
      if (!materialien.length) return;

      const mat = materialien[parseInt(document.getElementById('material').value)] || {};
      const plIdx = parseInt(document.getElementById('platte').value);
      const pl = (!isNaN(plIdx) && plIdx >= 0) ? platten[plIdx] : null;
      const vcQuelle = document.getElementById('vcQuelle').value; // 'material' | 'platte'

      const D = parseFloat(document.getElementById('durchmesser').value) || 0;
      const ap = parseFloat(document.getElementById('ap').value) || 0;
      const feedInput = parseFloat(document.getElementById('feed').value) || 0;
      const feedMode = document.getElementById('feedMode').value; // 'f' | 'vf'

      const motorleistung = parseFloat(document.getElementById('motorleistung').value || '750');
      const untersetzung = parseFloat(document.getElementById('untersetzung').value || '1') || 1;
      const wirkungsgrad = parseFloat(document.getElementById('wirkungsgrad').value || '0.95') || 0.95;
      const motordrehmoment = parseFloat(document.getElementById('motordrehmoment').value || '2.4') || 2.4;

      // Schneidstoff nur relevant für vc-Quelle "Material"
      let schn = document.getElementById('schneidstoff').value; // 'hss' | 'hartmetall'
      if (vcQuelle === 'platte' && pl) {
        schn = 'hartmetall'; // gängige Annahme für Schneidplatten
      }

      // vc bestimmen
      const vc_material = (schn === 'hss') ? (mat.vc_hss || 0) : (mat.vc_hartmetall || 0);
      const vc_platte = pl && pl.vc ? pl.vc : 0;
      const vc = (vcQuelle === 'platte' && vc_platte > 0) ? vc_platte : vc_material;

      // Drehzahl bestimmen
      let n;
      if (document.getElementById('modus').value === 'vc') {
        n = (D > 0) ? (1000 * vc) / (Math.PI * D) : 0;
      } else {
        n = parseFloat(document.getElementById('n_manuell').value) || 0;
      }
      const vc_berechnet = (Math.PI * D * n) / 1000; // m/min

      // Vorschub ableiten
      let f, vf;
      if (feedMode === 'f') {
        f = feedInput;               // mm/U
        vf = n * f;                  // mm/min
      } else {
        vf = feedInput;              // mm/min
        f = n ? (vf / n) : 0;        // mm/U
      }

      // Kräfte/Leistung
      const kc = mat.kc || 0;           // N/mm^2
      const Fc = kc * ap * f;           // N  (A = ap * f)
      const leistung = (Fc * vc_berechnet) / 60000; // kW
      const leistungWatt = leistung * 1000;
      const drehmomentSpindel = (Fc * D / 2) / 1000; // Nm
      const nMot = n * untersetzung;                 // 1/min
      const drehmomentMotor = drehmomentSpindel / untersetzung / wirkungsgrad; // Nm
      const motorLast = leistungWatt / wirkungsgrad; // W
      const lastProzent = motorleistung ? (motorLast / motorleistung) * 100 : 0;
      const drehmomentMotorProzent = motordrehmoment ? (drehmomentMotor / motordrehmoment) * 100 : 0;

      // Spanvolumen (mm³/min) – informativ
      const q_mm3 = ap * f * n * Math.PI * D / 1000; // entspricht Prüf-Formel
      const q_cm3 = q_mm3 / 1000;

      let warnung = '';
      if (lastProzent >= 95 || drehmomentMotorProzent >= 95) {
        warnung = `<div class='over'>⚠️ Überlastung! (${motorLast.toFixed(0)} W = ${lastProzent.toFixed(0)}% von ${motorleistung} W, Drehmoment ${drehmomentMotor.toFixed(2)} Nm = ${drehmomentMotorProzent.toFixed(0)}% von ${motordrehmoment} Nm)</div>`;
      } else if (lastProzent >= 80 || drehmomentMotorProzent >= 80) {
        warnung = `<div class='warn'>⚠️ Leistungs- oder Drehmomentgrenze erreicht (${motorLast.toFixed(0)} W = ${lastProzent.toFixed(0)}% von ${motorleistung} W, Drehmoment ${drehmomentMotor.toFixed(2)} Nm = ${drehmomentMotorProzent.toFixed(0)}% von ${motordrehmoment} Nm)</div>`;
      }

      const plGruppen = pl && pl.gruppen ? pl.gruppen.split(',').map(g => gruppenMap[g]).join(', ') : '-';
      const vcQuelleLabel = (vcQuelle === 'platte' && vc_platte > 0) ? 'Platte' : `Material (${schn === 'hss' ? 'HSS' : 'Hartmetall'})`;

      document.getElementById('ausgabe').innerHTML = `
        <strong>Material:</strong> ${mat.name || '-'} (${mat.gruppe || '-'} – ${gruppenMap[mat.gruppe] || '-'})<br>
        <strong>vc-Quelle:</strong> ${vcQuelleLabel}<br>
        <strong>Schnittgeschwindigkeit:</strong> ${vc_berechnet.toFixed(1)} m/min<br>
        <strong>Spindeldrehzahl:</strong> ${n.toFixed(0)} U/min<br>
        <strong>Motordrehzahl:</strong> ${nMot.toFixed(0)} U/min (Untersetzung ${untersetzung})<br>
        <strong>Vorschub f:</strong> ${f.toFixed(3)} mm/U<br>
        <strong>Vorschubgeschwindigkeit vf:</strong> ${vf.toFixed(0)} mm/min<br>
        <strong>Spanvolumen:</strong> ${q_cm3.toFixed(2)} cm³/min<br>
        <strong>Leistungsaufnahme (Spindel):</strong> ${leistung.toFixed(2)} kW<br>
        <strong>Motorlast:</strong> ${motorLast.toFixed(0)} W (${lastProzent.toFixed(0)}% von ${motorleistung} W, Wirkungsgrad ${wirkungsgrad})<br>
        <strong>Schnittkraft:</strong> ${Fc.toFixed(0)} N<br>
        <strong>Drehmoment (Spindel):</strong> ${drehmomentSpindel.toFixed(2)} Nm<br>
        <strong>Drehmoment (Motor):</strong> ${drehmomentMotor.toFixed(2)} Nm (${drehmomentMotorProzent.toFixed(0)}% von ${motordrehmoment} Nm)<br><br>
        <strong>Platte:</strong> ${pl ? `${pl.name} (${pl.typ}) – für ${plGruppen}${pl.vc ? `, vc ${pl.vc} m/min` : ''}` : 'Keine'}
        ${warnung}
      `;

      document.getElementById('ausgabe').style.display = 'block';
      document.getElementById('exportLink').style.display = 'block';

      // Exportdaten an Session schicken
      fetch('session_export.php', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({
          material: mat.name,
          platte: pl ? pl.name : 'Keine',
          vc: vc_berechnet.toFixed(1),
          f: f.toFixed(3),
          ap: ap,
          D: D,
          n: n.toFixed(0),
          nMot: nMot.toFixed(0),
          untersetzung: untersetzung,
          wirkungsgrad: wirkungsgrad,
          vf: vf.toFixed(0),
          pc: leistung.toFixed(2),
          motorLast: motorLast.toFixed(0),
          Fc: Fc.toFixed(0),
          md_spindel: drehmomentSpindel.toFixed(2),
          md_motor: drehmomentMotor.toFixed(2),
          motordrehmoment: motordrehmoment,
          drehmomentMotorProzent: drehmomentMotorProzent.toFixed(0),
          feedMode: feedMode
        })
      });
    }

    window.onload = ladeDaten;
  </script>
</head>
<body>
  <h2>Drehrechner</h2>

  <!-- Kernparameter -->
  <label for="material">Material:</label>
  <select id="material" onchange="berechne()"></select>

  <label for="platte">Schneidplatte:</label>
  <select id="platte" onchange="platteGeaendert()"></select>

  <label for="vcQuelle">vc-Quelle:</label>
  <select id="vcQuelle" onchange="vcQuelleGeaendert(); berechne();">
    <option value="material" selected>Material (HSS/Hartmetall)</option>
    <option value="platte">Platte (falls vc vorhanden)</option>
  </select>

  <div id="schneidstoffContainer">
    <label for="schneidstoff">Schneidstoff:</label>
    <select id="schneidstoff" onchange="berechne()">
      <option value="hss">HSS</option>
      <option value="hartmetall" selected>Hartmetall</option>
    </select>
  </div>

  <label for="modus">Modus:</label>
  <select id="modus" onchange="modusGeaendert(); berechne();">
    <option value="vc" selected>Konstante Schnittgeschwindigkeit</option>
    <option value="n">Konstante Drehzahl</option>
  </select>

  <div id="drehzahlEingabe" style="display:none;">
    <label for="n_manuell">Drehzahl n (1/min):</label>
    <input type="number" id="n_manuell" value="300" oninput="berechne()">
  </div>

  <label for="durchmesser">Durchmesser D (mm):</label>
  <input type="number" id="durchmesser" value="100" oninput="berechne()">

  <label for="ap">Zustellung ap (mm):</label>
  <input type="number" id="ap" step="0.01" value="0.5" oninput="berechne()">

  <label for="feedMode">Vorschubmodus:</label>
  <select id="feedMode" onchange="feedModeChanged();">
    <option value="f" selected>f (mm/U)</option>
    <option value="vf">vf (mm/min)</option>
  </select>

  <label id="feedLabel" for="feed">Vorschub f (mm/U):</label>
  <input type="number" id="feed" step="0.01" value="0.20" oninput="berechne()">

  <!-- Maschinenparameter (optional) -->
  <details id="maschinenDetails" style="margin-top:6px;">
    <summary style="cursor:pointer; user-select:none;">Maschinenparameter (optional)</summary>
    <div style="margin-top:10px;">
      <label for="motorleistung">Motorleistung (Watt):</label>
      <input type="number" id="motorleistung" value="750" oninput="berechne()">

      <label for="motordrehmoment">Motordrehmoment (Nm):</label>
      <input type="number" id="motordrehmoment" step="0.01" min="0" value="2.4" oninput="berechne()">

      <label for="untersetzung">Untersetzung (z. B. 1.5 = 1.5:1):</label>
      <input type="number" id="untersetzung" step="0.1" value="1" oninput="berechne()">

      <label for="wirkungsgrad">Getriebewirkungsgrad (z.B. 0.95):</label>
      <input type="number" id="wirkungsgrad" step="0.01" min="0.7" max="1" value="0.95" oninput="berechne()">
    </div>
  </details>

  <!-- Ausgabe -->
  <div class="result" id="ausgabe"></div>

  <!-- Export-Button -->
  <div id="exportLink" style="display:none; margin-top:20px;">
    <a href="export.php" target="_blank" style="background:#00b4d8; color:black; padding:10px 20px; text-decoration:none; font-weight:bold; border-radius:6px;">📤 Ergebnis exportieren</a>
  </div>

</body>
</html>
