# Zerspanungsrechner (Web-App)

Ein interaktiver Zerspanungsrechner mit Materialdatenbank, Schneidplattenverwaltung, Benutzer-Login und Adminsteuerung – ideal für CNC-Projekte.

## ✅ Funktionen

- 💠 Material- und Schneidplatten-Datenbank (verwaltbar via Admin)
- 🧮 Zerspanungsrechner mit Leistungsberechnung und Warnanzeige
- 🌐 Benutzerverwaltung mit Rollen:
  - `admin`: vollständiger Zugriff
  - `editor`: eingeschränkte Verwaltung
  - `viewer`: nur Nutzung des Rechners
- 🔐 Login-/Logout-System mit Session-Handling
- 📋 Eigene Passwortänderung über `profil.php`
- 📝 Selbstregistrierung über `register.php` (automatisch `viewer`)
- ⚠️ Schutz: Letzter Admin kann nicht gelöscht werden
- 🧭 Navigation über alle Seiten integriert
- 🛠 Webbasierter Installationsassistent (`install.php`)

## 🚀 Installation

1. Alles ins Webverzeichnis kopieren (z. B. `/var/www/html/drehbank`)
2. Im Browser aufrufen: `https://DEIN_SERVER/drehbank/install.php`
3. Zugangsdaten eingeben – DB, Benutzer, Passwort
4. Nach erfolgreicher Einrichtung mit `admin` / `admin123` einloggen
5. Neuen Admin anlegen und Demo-Admin löschen (WICHTIG!)

## 📂 Dateien & Seiten

| Datei               | Beschreibung                       |
|---------------------|------------------------------------|
| `install.php`       | Schritt-für-Schritt Installer      |
| `login.php`         | Login-Seite                        |
| `logout.php`        | Abmelden                           |
| `register.php`      | Registrierung als viewer           |
| `profil.php`        | Passwort ändern                    |
| `admin_user.php`    | Benutzerverwaltung (Admin)         |
| `admin.html`        | Admin-Bereich (Material, Platten)  |
| `zerspanung.html`   | Hauptrechner für Schnittdaten      |

## 🔐 Sicherheitshinweise

- `config.php` nach der Installation mit `chmod 640` sichern
- Admin-Benutzer `admin` (Passwort: `admin123`) nach Einrichtung löschen
- Nur Admins dürfen Benutzer löschen oder Rollen ändern

## 📦 Beispieldaten

Eine Datei `beispieldaten.sql` mit Materialien, Platten und Demo-Nutzer ist enthalten.

Import z. B. über:
```bash
mysql -u root -p drehbank < beispieldaten.sql
```

## 🤝 Mitmachen

Vorschläge, Bugs oder Features? → Pull Requests oder Issues auf GitHub willkommen!

---

© 2025 – Projekt von [peter-pan08](https://github.com/peter-pan08)
