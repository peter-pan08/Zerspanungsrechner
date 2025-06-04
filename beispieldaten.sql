
-- Beispiel-Materialien
INSERT INTO materialien (name, gruppe, vc_hss, vc_hartmetall, kc) VALUES
('1.0037 (P – Baustahl)', 'P', 25, 120, 1800),
('1.4301 (M – Edelstahl)', 'M', 15, 50, 2100),
('AlMg3 (N – Aluminium)', 'N', 180, 300, 400),
('GG25 (K – Gusseisen)', 'K', 25, 90, 1600),
('42CrMo4 (P – Vergütungsstahl)', 'P', 20, 100, 2200),
('Titan (S – Titanlegierung)', 'S', 10, 40, 3200),
('C45 (P – unlegierter Stahl)', 'P', 20, 90, 2000);

-- Beispiel-Schneidplatten
INSERT INTO platten (name, typ, gruppen, vc) VALUES
('VCGT110304 AK H01', 'VCGT110304', 'N', 150),
('VCMT110304 VP15TF', 'VCMT110304', 'P,M,K,N,S,H', 125),
('MRMN200-G NC3030', 'MRMN200-G', 'P,M,K,N,S,H', 165),
('CCMT09T304 UE6020', 'CCMT09T304', 'P,M,K', 130),
('MGMT150408-PM YBC251', 'MGMT150408', 'P,M,K', 140);

-- Beispiel-Fräser
INSERT INTO fraeser (name, typ, zaehne, durchmesser, gruppen, vc, fz) VALUES
('3-Schneider \xC3\x9810', 'VHM-Schaftfräser', 3, 10, 'P,M,K,N', 120, 0.05),
('4-Schneider \xC3\x988', 'HSS-Schaftfräser', 4, 8, 'P,M,K', 60, 0.04);

-- Demo-Benutzer mit Rolle viewer
INSERT IGNORE INTO users (username, password_hash, rolle) VALUES (
  'demo_viewer',
  '$2y$10$2zXHqZQ1BtYV57vmsjCyLu5bY.LQRd5q2hnAqBt0xHTWEpZXmIWeS',
  'viewer'
);
