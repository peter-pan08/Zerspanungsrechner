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
2. **Installer starten**:
   `http://DEIN_SERVER/drehbank/install.php`
   - Erstellt `config.php`, falls sie fehlt. Kopiere also keine Example-Datei vorab oder lösche/benenne vorhandene Kopien, damit der Installer neue Zugangsdaten schreiben kann.
   - Wer die Datei lokal weiter versionieren möchte, kann danach `git update-index --skip-worktree config.php` verwenden.

3. Optional: Rechte für Konfigdatei:
```bash
chmod 640 /var/www/html/drehbank/config.php
```

---

## 🛠 Webbasierter Installer

1. Browser öffnen:
   `http://DEIN_SERVER/drehbank/install.php`

2. Datenbankzugangsdaten und App-User anlegen
3. Benutzerverwaltung aktivieren? (setzt `LOGIN_REQUIRED` in `config.php`)

Hinweis: Die Tabelle `fraeser` enthält jetzt die Spalte `durchmesser` zur Ablage des Werkzeug-Ø. Der Installer legt diese Spalte automatisch an.
4. Nach Login mit `admin` / `admin123` (nur wenn Benutzerverwaltung aktiv ist):
   - Neuen Admin anlegen
   - Demo-Admin löschen

Die Einstellung kann später über die Einstellungen (`settings.php`) oder `LOGIN_REQUIRED` in `config.php` angepasst werden.

*(Fallback)* Sollte der Installer nicht zur Verfügung stehen, kopiere `config.example.php` manuell zu `config.php` und passe die Werte an.

---

## 📚 Beispieldaten (optional)

Die Datei `beispieldaten.sql` enthält Beispiel-Materialien, Schneidplatten und Fräser.
Importiere sie mit:

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

- Rechne im gewünschten Modus:
  - `zerspanung.php` für Drehbank
  - `fraesen.php` für Fräsen
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
