# Zerspanungsrechner (Web-App)

Ein interaktiver Zerspanungsrechner mit Material- und Werkzeugdatenbank, Benutzer-Login sowie Exportfunktionen – ideal für CNC-Projekte.

## ✅ Funktionen

- 💠 Material-, Schneidplatten- **und Fräser-Datenbank** (verwaltbar via Admin, speichert den Durchmesser je Fräser)
- 🧮 Zerspanungsrechner für **Drehbank und Fräsen** mit Leistungsberechnung, Schnittdaten, Motorlastanzeige und Warnung
- ✨ Fräsrechner unterstützt Werkzeugdurchmesser sowie wählbare Vorschub-Modi (fz, f oder vf)
- 📤 Export: PDF, Excel (XLSX), CSV
- 🌐 Benutzerverwaltung mit Rollen:
  - `admin`: vollständiger Zugriff
  - `editor`: eingeschränkte Verwaltung
  - `viewer`: nur Nutzung des Rechners
- 🔐 Login-/Logout-System mit Session-Handling
- 📝 Selbstregistrierung über `register.php` (automatisch `viewer`)
- ⚠️ Schutz: Letzter Admin kann nicht gelöscht werden
- 🛠 Webbasierter Installationsassistent (`install.php`)
- 🔑 Login-Pflicht lässt sich über `LOGIN_REQUIRED` in `config.php` steuern
- 🧭 Navigation über alle Seiten integriert
- 🔄 Dropdown für den Vorschubmodus (fz / f / vf) im Rechner
- 👥 Admin-Bereich zum Verwalten von Materialien, Schneidplatten und Fräsern

## 🚀 Installation

1. **Dateien hochladen** nach `/var/www/html/drehbank`
2. **Installer starten**:
   `https://DEIN_SERVER/drehbank/install.php`
3. **Datenbankzugangsdaten eingeben**
4. **Benutzerverwaltung aktivieren?** (Login-Pflicht)
5. **Admin-Benutzer anlegen und Demo-Admin entfernen** (nur bei aktivierter Benutzerverwaltung)
   - Die Einstellung kann später über `LOGIN_REQUIRED` in `config.php` geändert werden

## 🛠️ Erforderliche Erweiterungen

Für den Export werden folgende PHP-Bibliotheken benötigt:

```bash
composer require tecnickcom/tcpdf
composer require phpoffice/phpspreadsheet
```

💡 Falls noch kein `composer` installiert ist:

```bash
sudo apt install composer
```

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

## 🔐 Sicherheit

- `config.php` nach Installation per `chmod 640` schützen
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
