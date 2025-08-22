<?php
require_once 'db.php';

$pdo = getPDO();

header('Content-Type: application/json');

$data = [
  "materialien" => [],
  "platten" => [],
  "fraeser" => []
];

// Materialdaten laden
$mat_stmt = $pdo->query("SELECT id, name, gruppe, vc_hss, vc_hartmetall, kc FROM materialien ORDER BY name");
$data["materialien"] = $mat_stmt->fetchAll(PDO::FETCH_ASSOC);

// Plattendaten laden
$platt_stmt = $pdo->query("SELECT id, name, typ, gruppen, vc FROM platten ORDER BY name");
$data["platten"] = $platt_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fräserdaten laden
$fraes_stmt = $pdo->query("SELECT id, name, typ, durchmesser, zaehne, gruppen, vc, fz FROM fraeser ORDER BY name");
$data["fraeser"] = $fraes_stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);
