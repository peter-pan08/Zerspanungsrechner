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

Im Projektordner:

```bash
composer require tecnickcom/tcpdf
composer require phpoffice/phpspreadsheet
```

---

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
