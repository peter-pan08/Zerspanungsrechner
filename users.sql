-- Zerspanungsrechner: vollständige Datenbankstruktur

CREATE DATABASE IF NOT EXISTS drehbank;
USE drehbank;

CREATE TABLE IF NOT EXISTS materialien (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  gruppe VARCHAR(1),
  vc_hss FLOAT,
  vc_hartmetall FLOAT,
  kc FLOAT
);

CREATE TABLE IF NOT EXISTS platten (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  typ VARCHAR(50),
  gruppen VARCHAR(20),
  vc FLOAT
);

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  rolle VARCHAR(20) DEFAULT 'admin'
);

-- Testadmin: admin / admin123
INSERT IGNORE INTO users (username, password_hash, rolle) VALUES (
  'admin',
  '$2y$10$QIxmuI6KVmX.XvlzQbPyl.TcSTj5iNuQiZL91gzN6k5bmkx.2mHLy',
  'admin'
);