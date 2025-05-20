# Zerspanungsrechner (Webversion)

Dieses Projekt stellt einen webbasierten Zerspanungsrechner zur Verfügung. Materialien und Schneidplatten können über eine Adminoberfläche verwaltet werden. Die Hauptseite berechnet Schnittwerte und zeigt eine Leistungswarnung unter Berücksichtigung der Untersetzung.

## Features

- Material- und Plattendatenbank (MariaDB)
- Adminbereich mit Bearbeiten/Löschen
- Leistungsanzeige + Warnung ab 80 % (Überlastung ab 95 %)
- Unterstützung für konstante Drehzahl oder Schnittgeschwindigkeit
- Responsive Webdesign mit dunklem CNC-Hintergrund

## Installation

1. Lade das Projekt auf deinen Webserver
2. Rufe im Browser `install.php` auf und folge den Schritten
3. Danach steht dir der Login zur Verfügung unter `login.php`

## Benutzerverwaltung

- Standardnutzer: `admin`
- Passwort: `admin123`
- Benutzer lassen sich in der DB verwalten (Tabelle `users`)

## 🔐 Rechteverwaltung

Jeder Benutzer in der `users`-Tabelle hat ein Rollenfeld `rolle`, z. B.:

- `admin` → uneingeschränkter Zugriff
- `editor` → eingeschränkter Zugriff (kein Systemupdate)
- `viewer` → nur Leserechte

`$_SESSION['rolle']` wird zur Zugriffskontrolle verwendet.

## 🛠 Update-Modul

Die Datei `update.php` kann verwendet werden, um Systemänderungen oder DB-Strukturprüfungen durchzuführen.

## Sicherheit

- Schütze `config.php` z. B. mit `chmod 640`
- Login-Schutz ersetzt `.htaccess`, aber kann kombiniert werden
