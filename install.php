<?php
// Erstkonfiguration: Datenbanktabellen anlegen
require 'config.php';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

$queries = [
  "CREATE TABLE IF NOT EXISTS materialien (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    gruppe VARCHAR(1),
    vc_hss FLOAT,
    vc_hartmetall FLOAT,
    kc FLOAT
  )",
  "CREATE TABLE IF NOT EXISTS platten (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    typ VARCHAR(50),
    gruppen VARCHAR(20),
    vc FLOAT
  )"
];

foreach ($queries as $sql) {
  if ($conn->query($sql) === TRUE) {
    echo "OK<br>";
  } else {
    echo "Fehler: " . $conn->error . "<br>";
  }
}
$conn->close();
?>
