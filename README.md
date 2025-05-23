# Zerspanungsrechner (Web-App)

Ein interaktiver Zerspanungsrechner mit Materialdatenbank, Schneidplattenverwaltung, Benutzer-Login, Exportfunktionen – ideal für CNC-Projekte.

## ✅ Funktionen

- 💠 Material- und Schneidplatten-Datenbank (verwaltbar via Admin)
- 🧮 Zerspanungsrechner mit Leistungsberechnung, Schnittdaten, Motorlastanzeige und Warnung
- 📤 Export: PDF, Excel (XLSX), CSV
- 🌐 Benutzerverwaltung mit Rollen:
  - `admin`: vollständiger Zugriff
  - `editor`: eingeschränkte Verwaltung
  - `viewer`: nur Nutzung des Rechners
- 🔐 Login-/Logout-System mit Session-Handling
- 📝 Selbstregistrierung über `register.php` (automatisch `viewer`)
- ⚠️ Schutz: Letzter Admin kann nicht gelöscht werden
- 🛠 Webbasierter Installationsassistent (`install.php`)
- 🧭 Navigation über alle Seiten integriert

## 🚀 Installation

1. **Dateien hochladen** nach `/var/www/html/drehbank`
2. **Installer starten**:  
   `https://DEIN_SERVER/drehbank/install.php`
3. **Datenbankzugangsdaten eingeben**
4. **Admin-Benutzer anlegen und Demo-Admin entfernen**

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
| `admin.html`         | Admin-Oberfläche für Materialien/Platten |
| `zerspanung.html`    | Hauptrechner mit Berechnung              |
| `export.php`         | Export-Auswahlseite                      |
| `export_csv.php`     | Export als CSV                           |
| `export_excel.php`   | Export als XLSX                          |
| `export_pdf.php`     | Export als PDF                           |
| `session_export.php` | Speichert Berechnungsdaten für Export    |

## 📦 Beispieldaten

Importieren über:

```bash
mysql -u root -p drehbank < beispieldaten.sql
```

## 🔐 Sicherheit

- `config.php` nach Installation per `chmod 640` schützen
- Standard-Adminkonto `admin/admin123` löschen
- Admin darf nicht gelöscht werden, wenn letzter seiner Rolle

## 🤝 Mitmachen

Fehler, Wünsche oder eigene Erweiterungen?  
→ GitHub Pull Requests und Issues willkommen!

---

© 2025 – Projekt von [peter-pan08](https://github.com/peter-pan08)
