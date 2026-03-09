# Zerspanungsrechner (Web-App)

Ein interaktiver Zerspanungsrechner mit Material- und Werkzeugdatenbank, Benutzer-Login sowie Exportfunktionen – ideal für CNC-Projekte.

## ✅ Funktionen

- 💠 Material-, Schneidplatten- **und Fräser-Datenbank** (verwaltbar via Admin, speichert den Durchmesser je Fräser)
- 🧮 Zerspanungsrechner für **Drehbank und Fräsen** mit Leistungsberechnung, Schnittdaten, Motorlastanzeige und Warnung
- ✨ Fräsrechner unterstützt Werkzeugdurchmesser sowie wählbare Vorschub-Modi (fz, f oder vf)
- ✨ Fräsen: vereinfachte Eingabe mit „Manueller Fräser“, automatischer Schneidstoff-Erkennung (HSS/VHM) und optionalen Maschinenparametern
- 📤 Export: PDF, Excel (XLSX), CSV
- 🌐 Benutzerverwaltung mit Rollen:
  - `admin`: vollständiger Zugriff
  - `editor`: eingeschränkte Verwaltung
  - `viewer`: nur Nutzung des Rechners
- 🔐 Login-/Logout-System mit Session-Handling
- 📝 Selbstregistrierung über `register.php` (automatisch `viewer`)
- ⚠️ Schutz: Letzter Admin kann nicht gelöscht werden
- 🛠 Webbasierter Installationsassistent (`install.php`)
- 🔑 Login-Pflicht lässt sich über die Einstellungen (`settings.php`) oder `LOGIN_REQUIRED` in `config.php` steuern
- 🧭 Navigation über alle Seiten integriert
- 🔄 Dropdown für den Vorschubmodus (fz / f / vf) im Rechner
- 👥 Admin-Bereich zum Verwalten von Materialien, Schneidplatten und Fräsern

## 🚀 Installation

### Systempakete installieren

```bash
sudo apt update
sudo apt install apache2 mariadb-server php php-mbstring php-xml php-gd php-zip php-mysql
```

**Benötigte PHP-Erweiterungen:** `mbstring`, `xml`, `zip`, `gd` – erforderlich für TCPDF und PhpSpreadsheet.

### Webserver konfigurieren

- **Apache**: VirtualHost auf `/var/www/html/drehbank` verweisen, ggf. `AllowOverride All` aktivieren.
- **Nginx**: Serverblock mit `root /var/www/html/drehbank;` und PHP-FPM-Einbindung erstellen.

1. **Dateien hochladen** nach `/var/www/html/drehbank`
2. **Datenbank und Benutzer anlegen**:
```sql
CREATE DATABASE drehbank;
CREATE USER 'drehuser'@'localhost' IDENTIFIED BY 'starkes-passwort';
GRANT ALL PRIVILEGES ON drehbank.* TO 'drehuser'@'localhost';
FLUSH PRIVILEGES;
```
   Diese Zugangsdaten müssen im Web‑Installer eingetragen werden.
3. **Installer starten**:
   `https://DEIN_SERVER/drehbank/install.php`
   - Legt `config.php` automatisch an, wenn die Datei fehlt. Kopiere also **nicht** `config.example.php` vorher oder lösche/benenne vorhandene Kopien um, damit der Installer neue Zugangsdaten schreiben kann.
   - Optional kannst du nach der Installation `git update-index --skip-worktree config.php` verwenden, wenn die Datei lokal weiter versioniert werden soll.
4. **Datenbankzugangsdaten (z. B. `drehuser`) eingeben**
5. **Benutzerverwaltung aktivieren?** (Login-Pflicht)
6. **Admin-Benutzer anlegen und Demo-Admin entfernen** (nur bei aktivierter Benutzerverwaltung)
   - Die Einstellung kann später über die Einstellungen (`settings.php`) oder `LOGIN_REQUIRED` in `config.php` geändert werden
7. *(Fallback)* Sollte der Installer nicht genutzt werden können, kopiere `config.example.php` manuell zu `config.php` und passe die Werte an.

## 🧑‍💻 Deploy-Checkliste (Server)

- `config.php` bleibt lokal: Wird über `require_config.php` geladen und ist per `.gitignore` ausgeschlossen. Niemals committen.
- Abhängigkeiten installieren: `composer install --no-dev --optimize-autoloader`
- PHP-Erweiterungen: `php-mbstring`, `php-xml`, `php-zip`, `php-gd`, `php-mysql`
- Dateirechte (Beispiel): `chown -R www-data:www-data /var/www/html/Zerspanungsrechner && chmod 640 /var/www/html/Zerspanungsrechner/config.php`
- Nach Updates ggf. `update.php` im Browser aufrufen (z. B. neue DB-Spalten).

### Update bestehender Server-Installationen

1) Optional Backup der lokalen Konfig: `cp config.php config.php.bak-$(date +%F)`
2) Pull ausführen: `git pull`
3) Abhängigkeiten aktualisieren: `composer install --no-dev --optimize-autoloader`
4) Installer/Updater prüfen: `update.php` bei Bedarf ausführen

Hinweis zu `composer.lock`:
- Die Datei ist versioniert und sollte NICHT ignoriert werden. Verwende auf dem Server `composer install`, nicht `composer update`.

### Troubleshooting: Git-Pull mit alter, getrackter config.php

Falls ein altes Deployment `config.php` noch getrackt hatte und `git pull` blockiert:

- Änderungen sichern und Arbeitsverzeichnis bereinigen:
  - `cp config.php config.php.bak-$(date +%F)`
  - `git reset --hard HEAD`
- Lokale Konfig zurücklegen: `cp config.php.bak-<DATUM> config.php`
- Prüfen, dass `config.php` ignoriert wird: `git check-ignore -v config.php` (soll `.gitignore` melden)
- Abhängigkeiten installieren: `composer install --no-dev --optimize-autoloader`

## 🛠️ Erforderliche Erweiterungen

Für den Export werden die in `composer.json` definierten PHP-Bibliotheken benötigt.
Installiere sie mit:

```bash
composer install
```

💡 Falls noch kein `composer` installiert ist:

```bash
sudo apt install composer
```
Anschließend im Projektordner `composer install` ausführen. Die installierten
Abhängigkeiten landen im Ordner `vendor/`. Der `vendor/`-Ordner bleibt in der Versionskontrolle ignoriert,
`composer.lock` wird hingegen mit versioniert, um reproduzierbare Installationen sicherzustellen.
Für Bibliotheken kann die Lock-Datei entfallen, für Anwendungen wie diese sollte sie jedoch versioniert werden.

## 📂 Dateien & Seiten

| Datei                | Beschreibung                             |
|----------------------|------------------------------------------|
| `install.php`        | Webbasierter Installer                   |
| `login.php`          | Login                                     |
| `logout.php`         | Abmelden                                  |
| `register.php`       | Registrierung (viewer)                   |
| `profil.php`         | Passwort ändern                           |
| `admin_user.php`     | Benutzerverwaltung                        |
| `admin.php`          | Admin-Oberfläche für Materialien, Platten & Fräser |
| `zerspanung.php`     | Rechner für Drehbank                      |
| `fraesen.php`        | Rechner für Fräsen                        |
| `export.php`         | Export-Auswahlseite                      |
| `export_csv.php`     | Export als CSV                           |
| `export_excel.php`   | Export als XLSX                          |
| `export_pdf.php`     | Export als PDF                           |
| `session_export.php` | Speichert Berechnungsdaten für Export    |
### Vorschubmodus wählen

Direkt beim Eingabefeld für den Vorschub findest du ein Dropdown zur Wahl des Modus `fz`, `f` oder `vf`. Der Labeltext passt sich entsprechend an und der eingegebene Wert wird als Vorschub pro Zahn, pro Umdrehung oder als Vorschubgeschwindigkeit interpretiert. Wählst du `fz` und änderst den Fräser, wird automatisch der empfohlene fz-Wert aus der Datenbank geladen. Der Export speichert alle drei Werte und verwendet den aktuell aktiven Modus.


## 🧰 Fräsen – vereinfachte Eingabe

- Fräser-Auswahl: Zusätzlich zu den gespeicherten Werkzeugen gibt es die Option `Manueller Fräser`.
  - Datenbank-Fräser: Der Durchmesser wird aus der DB übernommen und das Feld gesperrt. Die Zähnezahl `z` kommt aus der DB.
  - Manueller Fräser: Durchmesser und Zähnezahl `z` sind editierbar.
- Schneidstoff: Wird automatisch aus dem Fräser-Typ erkannt (z. B. `HSS` oder `VHM/Hartmetall`).
  - Wenn eindeutig erkannt, blendet sich das Schneidstoff-Dropdown aus.
  - Ist der Typ nicht eindeutig, bleibt das Dropdown sichtbar und frei wählbar.
- Maschinenparameter: Motorleistung, Motordrehmoment, Untersetzung und Wirkungsgrad sind in einem aufklappbaren Bereich `Maschinenparameter (optional)` gruppiert.
- Komfort: Bei Modus `fz` wird – falls vorhanden – der empfohlene fz-Wert des ausgewählten Fräsers automatisch übernommen.

## 📐 Berechnungsmodell (Drehen vs. Fräsen)

Die Berechnung wurde vereinheitlicht, damit die Angaben zwischen Dreh- und Fräsrechner schlüssig bleiben.

- **Drehen**
  - `n = (1000 * vc) / (pi * D)`
  - `vf = n * f`
  - `Fc = kc * ap * f`
  - `Pc = (Fc * vc) / 60000` (kW)
- **Fräsen**
  - `n = (1000 * vc) / (pi * D)`
  - `vf = n * f` oder `vf = n * z * fz`
  - `Q = ap * ae * vf` (Zeitspanvolumen in mm^3/min)
  - `Pc = (kc * Q) / 60000000` (kW)
  - `Fc = (Pc * 60000) / vc`

Hinweise:
- Im Fräsrechner wird `vc` bevorzugt aus dem Fräserdatensatz genommen, wenn dort ein Wert hinterlegt ist, sonst aus dem Material (HSS/Hartmetall).
- Die Motorlast berücksichtigt weiterhin Untersetzung und Wirkungsgrad separat.


## 📦 Beispieldaten

Beinhaltet Beispiel-Materialien, Schneidplatten und Fräser. Importieren über:

```bash
mysql -u root -p drehbank < beispieldaten.sql
```

## 🔄 Update bestehender Installationen

Führe bei bestehenden Setups nach dem Update auf diese Version `update.php` aus:

1. Wenn die Benutzerverwaltung aktiv ist, melde dich als `admin` an.
2. Rufe `https://DEIN_SERVER/drehbank/update.php` auf.
3. Klicke auf **Update ausführen**, um die neue Spalte `durchmesser` in der Tabelle `fraeser` anzulegen.

Hinweis: `config.php` wird jetzt durch `.gitignore` ausgeschlossen. Falls du sie
noch im Repository hast, entferne sie mit `git rm --cached config.php`,
committe diese Änderung und ziehe danach die neue Version.

## 🔐 Sicherheit

- `config.php` nach Installation per `chmod 640` schützen
- `DEBUG=false` in `config.php` deaktiviert die Fehlerausgabe und wird für Produktionssysteme empfohlen
- Standard-Adminkonto `admin/admin123` löschen
- Admin darf nicht gelöscht werden, wenn letzter seiner Rolle

## 🤝 Mitmachen

Fehler, Wünsche oder eigene Erweiterungen?  
→ GitHub Pull Requests und Issues willkommen!

---

© 2025 – Projekt von [peter-pan08](https://github.com/peter-pan08)


## 🧪 Live-Demo

Eine öffentliche, gesicherte Demoversion ist hier verfügbar:

🔗 [Zerspanungsrechner Demo ansehen](https://dryba.com/Zerspanungsrechner/index.php)

USR:admin
PWD:admin123
- ✔️ Alle Funktionen testbar
- 🚫 Kein Löschen oder Bearbeiten in der Demo möglich
- 🔐 Demo-Modus aktiv und geschützt
