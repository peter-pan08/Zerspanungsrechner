# 📦 Installationsanleitung – Zerspanungsrechner

## 🔧 Voraussetzungen

- Apache oder Nginx mit PHP ≥ 7.4
- MariaDB oder MySQL
- Composer (`sudo apt install composer`)

---

## 📂 Projektbereitstellung

1. Dateien nach `/var/www/html/drehbank` kopieren:
```bash
sudo chown -R www-data:www-data /var/www/html/drehbank
```

2. Optional: Rechte für Konfigdatei:
```bash
chmod 640 /var/www/html/drehbank/config.php
```

---

## 🛠 Webbasierter Installer

1. Browser öffnen:  
   `http://DEIN_SERVER/drehbank/install.php`

2. Datenbankzugangsdaten und App-User anlegen

3. Nach Login mit `admin` / `admin123`:
   - Neuen Admin anlegen
   - Demo-Admin löschen

---

## 📚 Beispieldaten (optional)

```bash
mysql -u root -p drehbank < beispieldaten.sql
```

---



## 📦 Erweiterungen installieren

Zur Nutzung der Exportfunktionen (PDF, Excel) werden zwei PHP-Bibliotheken benötigt.

Wenn du das Projekt über Git oder ZIP herunterlädst, stelle sicher, dass sich im Projektordner `/var/www/html/drehbank` eine Datei `composer.json` befindet. Falls nicht, erstelle sie manuell:

```json
{
  "require": {
    "tecnickcom/tcpdf": "^6.6",
    "phpoffice/phpspreadsheet": "^1.25"
  }
}
```

### 🛠 Composer installieren & ausführen

```bash
cd /var/www/html/drehbank
sudo apt install composer
sudo -u www-data composer install
```

Wenn der Webserver-User (`www-data`) keinen Schreibzugriff hat:

```bash
sudo chown -R $USER:$USER /var/www/html/drehbank
composer install
sudo chown -R www-data:www-data /var/www/html/drehbank
```

Nach der Installation findest du im Ordner `vendor/` die Datei `autoload.php`. Diese wird in den Export-Skripten mit eingebunden:

```php
require_once __DIR__ . '/vendor/autoload.php';
```

Damit sind alle Abhängigkeiten korrekt geladen.


### 🔐 Sicherheitshinweis:

Diese Befehle sollten **nicht als root** ausgeführt werden.

Am besten führst du sie direkt als Webserver-User aus (z. B. `www-data`):

```bash
cd /var/www/html/drehbank
sudo -u www-data composer install
```

Wenn das nicht möglich ist, kannst du auch temporär die Rechte anpassen:

```bash
sudo chown -R $USER:$USER /var/www/html/drehbank
cd /var/www/html/drehbank
composer install
sudo chown -R www-data:www-data /var/www/html/drehbank
```

Das stellt sicher, dass Composer-Dateien im richtigen Besitz bleiben und keine root-Schreibrechte erhalten.


## 🔐 Sicherheit

```bash
chmod 640 config.php
```

---

## 📤 Export verwenden

- Rechne wie gewohnt in `zerspanung.html`
- Werte werden in Session gespeichert
- Rufe `export.php` auf für:
  - 📄 PDF
  - 📊 Excel
  - 📁 CSV

---

## 🧪 Demo-Zugang

| Benutzername | Passwort   | Rolle   |
|--------------|------------|---------|
| demo_admin   | demo123    | admin   |
| demo_viewer  | viewer123  | viewer  |

---

© 2025 – Projekt von [peter-pan08](https://github.com/peter-pan08)
