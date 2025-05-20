# Zerspanungsrechner (Webversion)

Dieses Projekt stellt einen webbasierten Zerspanungsrechner zur Verfügung. Materialien und Schneidplatten können über eine Adminoberfläche verwaltet werden. Die Hauptseite berechnet Schnittwerte und zeigt eine Leistungswarnung unter Berücksichtigung der Untersetzung.

## Features

- Material- und Plattendatenbank (MariaDB)
- Adminbereich mit Bearbeiten/Löschen
- Leistungsanzeige + Warnung ab 80 % (Überlastung ab 95 %)
- Unterstützung für konstante Drehzahl oder Schnittgeschwindigkeit
- Responsive Webdesign mit dunklem CNC-Hintergrund

## Installation

1. **Voraussetzungen**
   - Apache oder Nginx mit PHP
   - MariaDB
   - PHP mysqli-Erweiterung

2. **Datenbank importieren**

```sql
CREATE DATABASE drehbank;
USE drehbank;

CREATE TABLE materialien (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  gruppe VARCHAR(1),
  vc_hss FLOAT,
  vc_hartmetall FLOAT,
  kc FLOAT
);

CREATE TABLE platten (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  typ VARCHAR(50),
  gruppen VARCHAR(20),
  vc FLOAT
);
```

3. **Dateien in deinen Webordner kopieren**  
(z. B. `/var/www/html/drehbank/`)

4. **Datenbankverbindung anpassen in `config.php`**

```php
$host = 'localhost';
$user = 'DEIN_USER';
$pass = 'DEIN_PASSWORT';
$db = 'drehbank';
```

5. **Seite im Browser öffnen**

- Hauptseite: `https://deine-domain/drehbank/zerspanung.html`
- Adminbereich: `https://deine-domain/drehbank/admin.html`

## Backup-Hinweis

Dieses Repository enthält alle Dateien, um die Seite und die Datenbankstruktur jederzeit neu aufzusetzen.


## Admin-Zugang schützen (optional empfohlen)

1. `.htaccess` und `.htpasswd` verwenden:

```bash
sudo apt install apache2-utils
htpasswd -c /var/www/html/drehbank/.htpasswd adminname
```

→ Dann `.htaccess` aktivieren:

```apacheconf
AuthType Basic
AuthName "Adminbereich geschützt"
AuthUserFile /var/www/html/drehbank/.htpasswd
Require valid-user
```

2. Alternativ: Zugriffsschutz per PHP-Login oder Firewall.

## Installation der Datenbankstruktur per Web:

Einfach im Browser aufrufen:
`https://deine-domain/drehbank/install.php`


## Optional: Login-System (statt .htaccess)

1. Benutzer-Tabelle importieren:

```sql
SOURCE users.sql;
```

2. Login starten unter: `login.php`

3. Jede geschützte Seite beginnt mit:
```php
require 'session_check.php';
```

→ Beispiel-Zugang: Benutzer **admin** / Passwort **admin123**

4. Abmelden: `logout.php`
