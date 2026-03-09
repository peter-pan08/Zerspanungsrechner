<?php require 'session_check.php';
include 'header.php';
 ?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Fräsrechner</title>
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
    .top-nav {
      background: #1b263b;
      padding: 10px;
      margin-bottom: 20px;
      border-radius: 8px;
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }
    .top-nav a {
      color: #00b4d8;
      text-decoration: none;
      font-weight: bold;
    }
    .top-nav a:hover { text-decoration: underline; }
    h2 { color: #e0e1dd; }
  </style>
</head>
<body>

  <h2>Fräsrechner</h2>

  <!-- Kernparameter -->
  <label for="material">Material:</label>
  <select id="material" onchange="berechne()"></select>

  <div id="schneidstoffContainer">
    <label for="schneidstoff">Schneidstoff:</label>
    <select id="schneidstoff" onchange="berechne()">
      <option value="hss">HSS</option>
      <option value="hartmetall" selected>Hartmetall</option>
    </select>
  </div>

  <label for="fraeser">Fräser:</label>
  <select id="fraeser" onchange="fraeserGeaendert(); berechne();"></select>

  <label for="modus">Modus:</label>
  <select id="modus" onchange="umschaltenModus(); berechne();">
    <option value="vc" selected>Konstante Schnittgeschwindigkeit</option>
    <option value="n">Konstante Drehzahl</option>
  </select>

  <label for="durchmesser">Werkzeugdurchmesser (mm):</label>
  <input type="number" id="durchmesser" value="100" oninput="berechne()">
  <div id="durchmesserHinweis" style="display:none; margin-top:-6px; margin-bottom:8px; font-size:0.9em; color:#adb5bd;">
    Durchmesser vom ausgewählten Fräser übernommen
  </div>

  <div id="zaehneContainer" style="display:none;">
    <label for="zaehne">Zähnezahl z (nur manuell):</label>
    <input type="number" id="zaehne" min="1" step="1" value="2" oninput="berechne()">
  </div>

  <div id="drehzahlEingabe" style="display:none;">
    <label for="n_manuell">Drehzahl n (1/min):</label>
    <input type="number" id="n_manuell" value="300" oninput="berechne()">
  </div>

  <label for="ap">Zustellung ap (mm):</label>
  <input type="number" id="ap" step="0.01" value="2" oninput="berechne()">

  <label for="ae">Seitliche Zustellung ae (mm):</label>
  <input type="number" id="ae" step="0.01" value="5" oninput="berechne()">

  <label for="feedMode">Vorschubmodus:</label>
  <select id="feedMode" onchange="feedModeChanged(); berechne();">
    <option value="fz" selected>fz (mm/Zahn)</option>
    <option value="f">f (mm/U)</option>
    <option value="vf">vf (mm/min)</option>
  </select>

  <label id="feedLabel" for="feed">Vorschub fz (mm/Zahn):</label>
  <input type="number" id="feed" step="0.01" value="0.05" oninput="berechne()">

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

  <script>
    let materialien = [], fraeser = [];
    const gruppenMap = { P:"Stahl", M:"Edelstahl", K:"Gusseisen", N:"NE-Metalle", S:"Superlegierungen", H:"gehärteter Stahl" };

    async function ladeDaten() {
      const res = await fetch('load.php');
      const data = await res.json();
      materialien = data.materialien;
      fraeser = data.fraeser;
      fuelleMaterialDropdown();
      fuelleFraeserDropdown();
      fraeserGeaendert();
      feedModeChanged();
      berechne();
    }

    function umschaltenModus() {
      document.getElementById('drehzahlEingabe').style.display =
        document.getElementById('modus').value === 'n' ? 'block' : 'none';
    }

    function feedModeChanged() {
      const mode = document.getElementById('feedMode').value;
      const label = document.getElementById('feedLabel');
      if (mode === 'fz') label.textContent = 'Vorschub fz (mm/Zahn):';
      else if (mode === 'f') label.textContent = 'Vorschub f (mm/U):';
      else label.textContent = 'Vorschub vf (mm/min):';
      if (mode === 'fz') {
        const idxRaw = document.getElementById('fraeser').value;
        const idx = parseInt(idxRaw);
        if (!isNaN(idx) && idx >= 0 && fraeser[idx] && fraeser[idx].fz) {
          document.getElementById('feed').value = fraeser[idx].fz;
        }
      }
    }

function berechne() {
  if (!materialien.length) return;
  const motorleistung = parseFloat(document.getElementById('motorleistung').value || '750');
  const untersetzung = parseFloat(document.getElementById('untersetzung').value || '1') || 1;
  const wirkungsgrad = parseFloat(document.getElementById('wirkungsgrad').value || '0.95') || 0.95;
  const d = parseFloat(document.getElementById('durchmesser').value);
  const ap = parseFloat(document.getElementById('ap').value);
  const ae = parseFloat(document.getElementById('ae').value);
  const feed = parseFloat(document.getElementById('feed').value);

  const mat = materialien[parseInt(document.getElementById('material').value)];
  const kc = parseFloat(mat.kc || 0) || 0;
  let tool = null;
  let z = 1;

  const fraeserSel = document.getElementById('fraeser').value;
  const fraeserIdx = parseInt(fraeserSel);
  if (!isNaN(fraeserIdx) && fraeserIdx >= 0) {
    tool = fraeser[fraeserIdx];
    z = tool && tool.zaehne ? tool.zaehne : 1;
  } else {
    const zManual = parseInt(document.getElementById('zaehne').value);
    if (!isNaN(zManual) && zManual > 0) z = zManual;
  }

  // Schneidstoff automatisch aus Fräser ableiten, falls möglich
  let schn = document.getElementById('schneidstoff').value;
  if (!isNaN(fraeserIdx) && fraeserIdx >= 0 && fraeser[fraeserIdx]) {
    const typ = (fraeser[fraeserIdx].typ || '').toLowerCase();
    if (typ.includes('hss')) schn = 'hss';
    else if (typ.includes('vhm') || typ.includes('hartmetall')) schn = 'hartmetall';
  }
  const vc_material = parseFloat((schn === 'hss' ? mat.vc_hss : mat.vc_hartmetall) || 0) || 0;
  const vc_tool = parseFloat((tool && tool.vc) || 0) || 0;
  const vc = vc_tool > 0 ? vc_tool : vc_material;

  let n;
  if (document.getElementById('modus').value === 'vc') {
    n = (d > 0 && vc > 0) ? (1000 * vc) / (Math.PI * d) : 0;
  } else {
    n = parseFloat(document.getElementById('n_manuell').value) || 0;
  }

  const vc_berechnet = (Math.PI * d * n) / 1000; // m/min

  let fz, f, vf;
  const mode = document.getElementById('feedMode').value;
  if (mode === 'fz') {
    fz = feed;
    f = feed * z;
    vf = n * f;
  } else if (mode === 'f') {
    f = feed;
    fz = z ? feed / z : 0;
    vf = n * feed;
  } else {
    vf = feed;
    f = n ? feed / n : 0;
    fz = (n && z) ? feed / (n * z) : 0;
  }

  // Spanvolumen q in mm^3/min und dann in cm^3/min
  const q_mm3 = ap * ae * vf; // mm³/min
  const q = q_mm3 / 1000; // cm³/min

  // Leistungsaufnahme aus spezifischer Schnittkraft und Zeitspanvolumen
  const leistung = (kc * q_mm3) / 60000000; // kW
  const leistungWatt = leistung * 1000;

  // Schnittkraft aus Leistung und Schnittgeschwindigkeit ableiten
  const Fc = vc_berechnet > 0 ? (leistung * 60000) / vc_berechnet : 0; // N

  // Drehmoment an der Spindel
  const drehmomentSpindel = (Fc * d / 2) / 1000; // Nm

  // Motordrehzahl
  const nMot = n * untersetzung;

  // Drehmoment am Motor
  const drehmomentMotor = drehmomentSpindel / untersetzung / wirkungsgrad; // Nm

  // Motorlast (mechanisch am Motor)
  const motorLast = leistungWatt / wirkungsgrad;

  const lastProzent = motorleistung ? (motorLast / motorleistung) * 100 : 0;
  const motordrehmoment = parseFloat(document.getElementById('motordrehmoment').value) || 2.4;
  const drehmomentMotorProzent = motordrehmoment ? (drehmomentMotor / motordrehmoment) * 100 : 0;

  let warnung = '';
  if (lastProzent >= 95 || drehmomentMotorProzent >= 95) {
    warnung = `<div class='over'>⚠️ Überlastung! (${motorLast.toFixed(0)} W = ${lastProzent.toFixed(0)}% von ${motorleistung} W, Drehmoment ${drehmomentMotor.toFixed(2)} Nm = ${drehmomentMotorProzent.toFixed(0)}% von ${motordrehmoment} Nm)</div>`;
  } else if (lastProzent >= 80 || drehmomentMotorProzent >= 80) {
    warnung = `<div class='warn'>⚠️ Leistungs- oder Drehmomentgrenze erreicht (${motorLast.toFixed(0)} W = ${lastProzent.toFixed(0)}% von ${motorleistung} W, Drehmoment ${drehmomentMotor.toFixed(2)} Nm = ${drehmomentMotorProzent.toFixed(0)}% von ${motordrehmoment} Nm)</div>`;
  }

  const gruppenText = tool && tool.gruppen ? tool.gruppen.split(',').map(g => gruppenMap[g]).join(', ') : '-';
  const vcQuelleLabel = vc_tool > 0 ? 'Fraeser' : `Material (${schn === 'hss' ? 'HSS' : 'Hartmetall'})`;

  document.getElementById('ausgabe').innerHTML = `
    <strong>Material:</strong> ${mat.name} (${mat.gruppe} – ${gruppenMap[mat.gruppe]})<br>
    <strong>vc-Quelle:</strong> ${vcQuelleLabel}<br>
    <strong>Schnittgeschwindigkeit:</strong> ${vc_berechnet.toFixed(1)} m/min<br>
    <strong>Spindeldrehzahl:</strong> ${n.toFixed(0)} U/min<br>
    <strong>Motordrehzahl:</strong> ${nMot.toFixed(0)} U/min (Untersetzung ${untersetzung})<br>
    <strong>Vorschubgeschwindigkeit:</strong> ${vf.toFixed(0)} mm/min<br>
    <strong>Spanvolumen:</strong> ${q.toFixed(2)} cm³/min<br>
    <strong>Leistungsaufnahme (Spindel):</strong> ${leistung.toFixed(2)} kW<br>
    <strong>Motorlast:</strong> ${motorLast.toFixed(0)} W (${lastProzent.toFixed(0)}% von ${motorleistung} W, Wirkungsgrad ${wirkungsgrad})<br>
    <strong>Schnittkraft:</strong> ${Fc.toFixed(0)} N<br>
    <strong>Drehmoment (Spindel):</strong> ${drehmomentSpindel.toFixed(1)} Nm<br>
    <strong>Drehmoment (Motor):</strong> ${drehmomentMotor.toFixed(2)} Nm (${drehmomentMotorProzent.toFixed(0)}% von ${motordrehmoment} Nm)<br><br>
    <strong>Fräser:</strong> ${tool ? `${tool.name} (${tool.typ}) – für ${gruppenText}, vc ${tool.vc} m/min` : 'Manueller Fräser'}
    ${warnung}
  `;

  document.getElementById('ausgabe').style.display = 'block';
  document.getElementById('exportLink').style.display = 'block';

  fetch('session_export.php', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({
      material: mat.name,
      fraeser: tool ? tool.name : 'Manuell',
      vc: vc_berechnet.toFixed(1),
      fz,
      f,
      ap,
      ae,
      D: d,
      n: n.toFixed(0), // Spindeldrehzahl
      nMot: nMot.toFixed(0), // Motordrehzahl
      untersetzung: untersetzung, // Untersetzung
      wirkungsgrad: wirkungsgrad, // Wirkungsgrad
      vf: vf.toFixed(0), // Vorschubgeschwindigkeit
      pc: leistung.toFixed(2), // Leistungsaufnahme (kW)
      motorLast: motorLast.toFixed(0), // Motorlast (W)
      Fc: Fc.toFixed(0), // Schnittkraft (N)
      md_spindel: drehmomentSpindel.toFixed(1), // Drehmoment Spindel (Nm)
      md_motor: drehmomentMotor.toFixed(2), // Drehmoment Motor (Nm)
      motordrehmoment: motordrehmoment, // Motordrehmoment (Nm)
      drehmomentMotorProzent: drehmomentMotorProzent.toFixed(0), // Motordrehmoment-Auslastung (%)
    })
  });
}

  function fuelleMaterialDropdown() {
      const sel = document.getElementById('material'); sel.innerHTML = '';
      materialien.forEach((m, i) => sel.innerHTML += `<option value="${i}">${m.name} (${m.gruppe} – ${gruppenMap[m.gruppe]})</option>`);
    }

  function fuelleFraeserDropdown() {
      const sel = document.getElementById('fraeser'); sel.innerHTML = '';
      sel.innerHTML += `<option value="-1">Manueller Fräser</option>`;
      fraeser.forEach((p, i) => sel.innerHTML += `<option value="${i}">${p.name} (${p.typ}) – für ${p.gruppen.split(',').map(g=>gruppenMap[g]).join(', ')}</option>`);
  }

  function fraeserGeaendert() {
      const selVal = document.getElementById('fraeser').value;
      const idx = parseInt(selVal);
      const durchmesserInput = document.getElementById('durchmesser');
      const dHinweis = document.getElementById('durchmesserHinweis');
      const szContainer = document.getElementById('schneidstoffContainer');
      const zContainer = document.getElementById('zaehneContainer');

      if (!isNaN(idx) && idx >= 0 && fraeser[idx]) {
        // Fräser aus DB gewählt
        const fr = fraeser[idx];
        if (fr.durchmesser) {
          durchmesserInput.value = fr.durchmesser;
          durchmesserInput.disabled = true;
          dHinweis.style.display = 'block';
        } else {
          durchmesserInput.disabled = false;
          dHinweis.style.display = 'none';
        }
        // Schneidstoff automatisch ableiten und Dropdown ausblenden (falls erkennbar)
        const typ = (fr.typ || '').toLowerCase();
        if (typ.includes('hss')) {
          document.getElementById('schneidstoff').value = 'hss';
          szContainer.style.display = 'none';
        } else if (typ.includes('vhm') || typ.includes('hartmetall')) {
          document.getElementById('schneidstoff').value = 'hartmetall';
          szContainer.style.display = 'none';
        } else {
          szContainer.style.display = 'block';
        }
        // Standard fz übernehmen
        if (fr.fz && document.getElementById('feedMode').value === 'fz') {
          document.getElementById('feed').value = fr.fz;
        }
        // z aus DB verwenden, manuelle Eingabe ausblenden
        zContainer.style.display = 'none';
      } else {
        // Manueller Fräser
        durchmesserInput.disabled = false;
        dHinweis.style.display = 'none';
        szContainer.style.display = 'block';
        zContainer.style.display = 'block';
      }
  }

  window.onload = ladeDaten;
  </script>
</body>
</html>
