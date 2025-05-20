CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  rolle VARCHAR(20) DEFAULT 'admin'
);
-- Beispiel-Admin-User: Benutzername admin / Passwort admin123
INSERT INTO users (username, password_hash) VALUES (
  'admin',
  '$2y$10$QIxmuI6KVmX.XvlzQbPyl.TcSTj5iNuQiZL91gzN6k5bmkx.2mHLy'
);
