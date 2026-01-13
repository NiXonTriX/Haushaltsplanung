# Datenbankschema anwenden

So spielst du das aktuelle Schema (`db/schema.sql`) in eine MySQL/MariaDB-Instanz ein.

## Voraussetzungen
- MySQL 8.0+ oder MariaDB (CLI-Client `mysql` verfügbar)
- Ein Benutzer mit Berechtigung zum Erstellen von Tabellen in der Ziel-Datenbank

## Schritte
1. **Datenbank auswählen oder anlegen**
   ```sql
   CREATE DATABASE IF NOT EXISTS haushaltsplaner CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   USE haushaltsplaner;
   ```
2. **Schema einspielen** (aus dem Repo-Wurzelverzeichnis)
   ```bash
   mysql -u <user> -p haushaltsplaner < db/schema.sql
   ```
   Alternativ im `mysql`-Prompt:
   ```sql
   SOURCE /pfad/zum/repo/db/schema.sql;
   ```
3. **(Optional) Prüfen, ob alles angelegt wurde**
   ```sql
   SHOW TABLES;
   DESCRIBE bookings;
   ```

## Hinweise
- Beträge werden in Cent gespeichert (BIGINT) mit 3-stelliger Währung (Standard EUR).
- Die Kategorien sind zweistufig; die View `vw_category_levels` hilft, tiefer geschachtelte Einträge zu finden.
- Fremdschlüssel löschen abhängige Daten meist nicht, sondern setzen sie auf `NULL`, damit Historie erhalten bleibt; `booking_links` wird bei Löschungen der referenzierten Buchungen mit gelöscht.

---

## DB-Klasse (db.php)

Zusätzlich gibt es eine kleine Singleton-DB-Klasse (`db/db.php`) zur einfachen Wiederverwendung einer PDO-Verbindung.

Kurz:
- Unterstützt MySQL und SQLite
- Singleton-Pattern: `DB::getInstance()` liefert die Instanz
- `getConnection()` liefert das `PDO`-Objekt

Beispiel (Konstanten-Variante):

```php
define('DB_HOST','127.0.0.1');
define('DB_NAME','haushalt');
define('DB_USER','root');
define('DB_PASS','');
define('DB_CHARSET','utf8mb4');
require_once __DIR__ . '/db.php';
$pdo = DB::getInstance()->getConnection();
```

Oder beim ersten Aufruf konfigurieren:

```php
require_once __DIR__ . '/db.php';
$pdo = DB::getInstance([
  'driver' => 'mysql',
  'host' => '127.0.0.1',
  'dbname' => 'haushalt',
  'user' => 'root',
  'pass' => '',
  'charset' => 'utf8mb4',
])->getConnection();
```

Es gibt ein kurzes Beispielskript: `db/example_usage.php`.
