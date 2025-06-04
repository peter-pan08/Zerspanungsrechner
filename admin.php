<?php
  define('REQUIRE_SESSION', true);
  $pageTitle = 'Zerspanungsrechner';
  include 'header.php';
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Admin: Material- und Schneidplatten-Datenbank</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      font-family: sans-serif;
      margin: 20px;
      max-width: 1000px;
      background-color: #0d1b2a;
      color: #e0e1dd;
      background-image: url('A_digital_vector_illustration_depicts_a_CNC_lathe_.png');
      background-repeat: no-repeat;
      background-size: 60%;
      background-position: center;
      background-attachment: fixed;
      background-blend-mode: overlay;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
      background-color: #1b263b;
    }
    th, td {
      border: 1px solid #415a77;
      padding: 8px;
      text-align: left;
    }
    th {
      background: #1b263b;
      color: #e0e1dd;
    }
    input, select, button {
      padding: 5px;
      margin: 5px;
      background: #415a77;
      color: #e0e1dd;
      border: 1px solid #778da9;
    }
    input::placeholder, select {
      color: #e0e1dd;
    }
    .checkboxes label {
      display: inline-block;
      margin-right: 10px;
    }
    button {
      background-color: #00b4d8;
      border: none;
      cursor: pointer;
    }
    button:hover {
      background-color: #0096c7;
    }
    h1, h2 {
      color: #e0e1dd;
    }
  </style>
</head>
<body>

  <h1>Adminbereich: Material- und Schneidplattenverwaltung</h1>

  <h2>Materialien verwalten</h2>
  <form id="materialForm">
    <input type="hidden" id="mat_id">
    <input type="text" placeholder="Materialname" id="mat_name">
    <input type="text" placeholder="Typ" id="mat_typ">
    <select id="mat_gruppe">
      <option value="P">P – Stahl</option>
      <option value="M">M – Edelstahl</option>
      <option value="K">K – Gusseisen</option>
      <option value="N">N – NE-Metalle</option>
      <option value="S">S – Superlegierungen</option>
      <option value="H">H – gehärteter Stahl</option>
    </select>
    <input type="number" placeholder="vc HSS" id="mat_vc_hss">
    <input type="number" placeholder="vc Hartmetall" id="mat_vc_hm">
    <input type="number" placeholder="kc (N/mm²)" id="mat_kc">
    <button type="button" onclick="saveMaterial()">Speichern</button>
  </form>
  <table id="materialTable">
    <thead><tr><th>Name</th><th>Typ</th><th>Gruppe</th><th>vc HSS</th><th>vc HM</th><th>kc</th><th>Aktion</th></tr></thead>
    <tbody></tbody>
  </table>

  <h2>Schneidplatten verwalten</h2>
  <form id="platteForm">
    <input type="hidden" id="platt_id">
    <input type="text" placeholder="Bezeichnung (z.B. VCMT110304)" id="platt_name">
    <input type="text" placeholder="ISO-Typ (z.B. VCMT)" id="platt_typ">
    <input type="text" placeholder="Materialeinsatz" id="platt_mat">
    <input type="number" placeholder="Empf. vc" id="platt_vc">
    <div class="checkboxes">
      <label><input type="checkbox" value="P"> P</label>
      <label><input type="checkbox" value="M"> M</label>
      <label><input type="checkbox" value="K"> K</label>
      <label><input type="checkbox" value="N"> N</label>
      <label><input type="checkbox" value="S"> S</label>
      <label><input type="checkbox" value="H"> H</label>
    </div>
    <button type="button" onclick="savePlatte()">Speichern</button>
  </form>
  <table id="plattenTable">
    <thead><tr><th>Bezeichnung</th><th>ISO-Typ</th><th>Einsatz</th><th>vc</th><th>Gruppen</th><th>Aktion</th></tr></thead>
    <tbody></tbody>
  </table>

  <script>
    let materialEditId = null;
    let plattenEditId = null;

    async function loadData() {
      const res = await fetch('load.php');
      const data = await res.json();
      renderMaterialTable(data.materialien);
      renderPlattenTable(data.platten);
    }

    async function saveMaterial() {
      const formData = new FormData();
      if (materialEditId) formData.append('id', materialEditId);
      formData.append('name', document.getElementById("mat_name").value);
      formData.append('typ', document.getElementById("mat_typ").value);
      formData.append('gruppe', document.getElementById("mat_gruppe").value);
      formData.append('vc_hss', document.getElementById("mat_vc_hss").value);
      formData.append('vc_hartmetall', document.getElementById("mat_vc_hm").value);
      formData.append('kc', document.getElementById("mat_kc").value);
      await fetch('save_material.php', { method: 'POST', body: formData });
      materialEditId = null;
      document.getElementById("materialForm").reset();
      loadData();
    }

    async function deleteMaterial(id) {
      await fetch('delete_material.php', {
        method: 'POST',
        body: new URLSearchParams({ id })
      });
      loadData();
    }

    function editMaterial(mat) {
      materialEditId = mat.id;
      document.getElementById("mat_name").value = mat.name;
      document.getElementById("mat_typ").value = mat.typ;
      document.getElementById("mat_gruppe").value = mat.gruppe;
      document.getElementById("mat_vc_hss").value = mat.vc_hss;
      document.getElementById("mat_vc_hm").value = mat.vc_hartmetall;
      document.getElementById("mat_kc").value = mat.kc;
    }

    function renderMaterialTable(materialien) {
      const tbody = document.querySelector("#materialTable tbody");
      tbody.innerHTML = "";
      materialien.forEach(mat => {
        tbody.innerHTML += `<tr><td>${mat.name}</td><td>${mat.typ}</td><td>${mat.gruppe}</td><td>${mat.vc_hss}</td><td>${mat.vc_hartmetall}</td><td>${mat.kc}</td><td><button onclick='editMaterial(${JSON.stringify(mat)})'>Bearbeiten</button> <button onclick="deleteMaterial(${mat.id})">Löschen</button></td></tr>`;
      });
    }

    async function savePlatte() {
      const formData = new FormData();
      if (plattenEditId) formData.append('id', plattenEditId);
      formData.append('name', document.getElementById("platt_name").value);
      formData.append('typ', document.getElementById("platt_typ").value);
      formData.append('material', document.getElementById("platt_mat").value);
      formData.append('vc', document.getElementById("platt_vc").value);
      const gruppen = Array.from(document.querySelectorAll("input[type=checkbox]:checked")).map(cb => cb.value).join(',');
      formData.append('gruppen', gruppen);
      await fetch('save_platte.php', { method: 'POST', body: formData });
      plattenEditId = null;
      document.getElementById("platteForm").reset();
      loadData();
    }

    async function deletePlatte(id) {
      await fetch('delete_platte.php', {
        method: 'POST',
        body: new URLSearchParams({ id })
      });
      loadData();
    }

    function editPlatte(p) {
      plattenEditId = p.id;
      document.getElementById("platt_name").value = p.name;
      document.getElementById("platt_typ").value = p.typ;
      document.getElementById("platt_mat").value = p.material;
      document.getElementById("platt_vc").value = p.vc;
      document.querySelectorAll(".checkboxes input").forEach(cb => {
        cb.checked = p.gruppen?.split(',').includes(cb.value);
      });
    }

    function renderPlattenTable(platten) {
      const tbody = document.querySelector("#plattenTable tbody");
      tbody.innerHTML = "";
      platten.forEach(p => {
        tbody.innerHTML += `<tr><td>${p.name}</td><td>${p.typ}</td><td>${p.material}</td><td>${p.vc}</td><td>${p.gruppen}</td><td><button onclick='editPlatte(${JSON.stringify(p)})'>Bearbeiten</button> <button onclick="deletePlatte(${p.id})">Löschen</button></td></tr>`;
      });
    }

    window.onload = loadData;
  </script>
</body>
</html>
