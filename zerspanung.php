<?php require 'session_check.php'; ?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Zerspanungsrechner</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      font-family: sans-serif;
      margin: 20px;
      background-color: #0a0f14;
      color: #e0e1dd;
    }
    .calculator-form {
      display: grid;
      grid-template-columns: 1fr;
      gap: 10px;
      max-width: 600px;
      margin-bottom: 20px;
    }
    label {
      font-weight: bold;
    }
    input, select {
      width: 100%;
      padding: 6px;
      background: #415a77;
      color: #e0e1dd;
      border: 1px solid #778da9;
    }
    .note {
      font-size: 0.9em;
      color: #c0c1c2;
      margin-top: -8px;
      margin-bottom: 8px;
    }
    .result {
      background: #1b263b;
      padding: 10px;
      border: 1px solid #415a77;
      max-width: 600px;
      margin-bottom: 20px;
    }
    .warn { color: #ffba08; font-weight: bold; }
    .over { color: #ff4d6d; font-weight: bold; }
    .export-btn a {
      display: inline-block;
      padding: 10px 20px;
      background: #00b4d8;
      color: black;
      text-decoration: none;
      font-weight: bold;
      border-radius: 6px;
    }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>
  <h2>Zerspanungsrechner</h2>
  <div class="calculator-form">
    <label for="motorleistung">Motorleistung (Watt):</label>
    <input type="number" id="motorleistung" value="750" oninput="berechne()">

    <label for="untersetzung">Untersetzung (z. B. 1.5 = 1.5:1):</label>
    <input type="number" id="untersetzung" step="0.1" value="1" oninput="berechne()">

    <label for="material">Material:</label>
    <select id="material" onchange="berechne()"></select>

    <label for="schneidstoff">Schneidstoff:</label>
    <select id="schneidstoff" onchange="berechne()">
      <option value="hss">HSS</option>
      <option value="hartmetall" selected>Hartmetall</option>
    </select>

    <label for="schneidplatte">Schneidplatte:</label>
    <select id="schneidplatte" onchange="berechne()"></select>

    <label for="modus">Modus:</label>
    <select id="modus" onchange="umschaltenModus(); berechne();">
      <option value="vc" selected>Konstante Schnittgeschwindigkeit</option>
      <option value="n">Konstante Drehzahl</option>
    </select>

    <label for="durchmesser">Werkstückdurchmesser (mm):</label>
    <input type="number" id="durchmesser" value="100" oninput="berechne()">

    <div id="drehzahlEingabe" style="display:none">
      <label for="n_manuell">Drehzahl n (1/min):</label>
      <input type="number" id="n_manuell" value="300" oninput="berechne()">
    </div>

    <label for="ap">Zustellung ap (mm, Radiusmaß):</label>
    <input type="number" id="ap" step="0.01" value="0.5" oninput="berechne()">
    <small class="note">Die Zustellung ist der Radius, nicht der Durchmesser.</small>

    <label for="f">Vorschub f (mm/U):</label>
    <input type="number" id="f" step="0.01" value="0.15" oninput="berechne()">
  </div>

  <div class="result" id="ausgabe"></div>
  <div id="exportLink" class="export-btn" style="display:none;"><a href="export.php" target="_blank">📤 Ergebnis exportieren</a></div>

  <script>
    let materialien = [], platten = [];
    const gruppenMap = { P:'Stahl', M:'Edelstahl', K:'Gusseisen', N:'NE-Metalle', S:'Superlegierungen', H:'gehärteter Stahl' };

    async function ladeDaten() {
      const res = await fetch('load.php');
      const data = await res.json();
      materialien = data.materialien;
      platten = data.platten;
      fuelleMaterialDropdown();
      fuellePlattenDropdown();
      berechne();
    }

    function berechne() {
      if (!materialien.length) return;
      const mat = materialien[+document.getElementById('material').value];
      const schn = document.getElementById('schneidstoff').value;
      const modus = document.getElementById('modus').value;
      const d = +document.getElementById('durchmesser').value;
      const ap = +document.getElementById('ap').value;
      const f = +document.getElementById('f').value;
      const b = 2 * ap;
      const vcEmpf = schn==='hss'?mat.vc_hss:mat.vc_hartmetall;
      let n = modus==='vc'?(1000*vcEmpf)/(Math.PI*d):+document.getElementById('n_manuell').value;
      const vf = n*f;
      const kc = mat.kc;
      const Fc = kc*ap*b*0.8;
      const M = (Fc*(d/2))/1000;
      const PkW = (M*n)/9550;
      const P_W = PkW*1000;
      const motorLeistung = +document.getElementById('motorleistung').value;
      const lastPct = (P_W/motorLeistung)*100;
      let warnung='';
      if(lastPct>=95) warnung=`<div class='over'>⚠️ Überlastung! (${P_W.toFixed(0)} W = ${lastPct.toFixed(0)}% von ${motorLeistung} W)</div>`;
      else if(lastPct>=80) warnung=`<div class='warn'>⚠️ Leistungsgrenze erreicht (${P_W.toFixed(0)} W = ${lastPct.toFixed(0)}% von ${motorLeistung} W)</div>`;
      const plate = platten[+document.getElementById('schneidplatte').value];
      const grpText = plate.gruppen.split(',').map(g=>gruppenMap[g]).join(', ');
      document.getElementById('ausgabe').innerHTML =
        `<strong>Material:</strong> ${mat.name} (${mat.gruppe} – ${gruppenMap[mat.gruppe]})<br>`+
        `<strong>Schnittgeschwindigkeit:</strong> ${vcEmpf.toFixed(1)} m/min<br>`+
        `<strong>Drehzahl (Spindel):</strong> ${n.toFixed(0)} U/min<br>`+
        `<strong>Vorschubgeschwindigkeit:</strong> ${vf.toFixed(0)} mm/min<br>`+
        `<strong>Spanvolumen:</strong> ${(ap*f*vcEmpf/1000).toFixed(2)} cm³/min<br>`+
        `<strong>Schnittkraft:</strong> ${Fc.toFixed(0)} N<br>`+
        `<strong>Drehmoment:</strong> ${M.toFixed(1)} Nm<br>`+
        `<strong>Leistungsaufnahme (Spindel):</strong> ${PkW.toFixed(2)} kW<br>`+
        `<strong>Motorlast:</strong> ${P_W.toFixed(0)} W (${lastPct.toFixed(0)}%)<br>`+
        `<strong>Ausgewählte Schneidplatte:</strong> ${plate.name} – ${grpText}<br>`+
        warnung;
      document.getElementById('exportLink').style.display='block';
    }

    function fuelleMaterialDropdown() {
      const sel=document.getElementById('material');sel.innerHTML='';
      materialien.forEach((m,i)=>sel.innerHTML+=`<option value='${i}'>${m.name} (${m.gruppe} – ${gruppenMap[m.gruppe]})</option>`);
    }
    function fuellePlattenDropdown() {
      const sel=document.getElementById('schneidplatte');sel.innerHTML='';
      platten.forEach((p,i)=>sel.innerHTML+=`<option value='${i}'>${p.name} (${p.typ}) – für ${p.gruppen.split(',').map(g=>gruppenMap[g]).join(', ')}</option>`);
    }
    function umschaltenModus() {
      document.getElementById('drehzahlEingabe').style.display=document.getElementById('modus').value==='n'?'block':'none';
    }
    window.onload=ladeDaten;
  </script>
</body>
</html>
