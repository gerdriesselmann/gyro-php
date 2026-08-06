# Gyro-PHP Upgrade-Leitfaden

Dieser Leitfaden richtet sich an Entwickler, die eine bestehende Gyro-PHP Applikation auf die
aktuelle Version aktualisieren. Er erklärt, was sich geändert hat, was automatisch funktioniert
und wo manuelles Eingreifen nötig ist.

> **Kurzversion:** Die meisten Änderungen sind rückwärtskompatibel. Bestehende Applikationen
> laufen ohne Anpassungen weiter. Die wichtigste Änderung betrifft das Passwort-Hashing
> (automatische Migration) und die neue `.env`-Unterstützung (optional).

---

## Inhaltsverzeichnis

1. [Voraussetzungen](#1-voraussetzungen)
2. [Schnellstart](#2-schnellstart)
3. [Was passiert automatisch](#3-was-passiert-automatisch)
4. [Neue Features nutzen](#4-neue-features-nutzen)
5. [Breaking Changes](#5-breaking-changes)
6. [Datenbank-Updates](#6-datenbank-updates)
7. [Entfernte Module](#7-entfernte-module)
8. [Für Entwickler](#8-für-entwickler)
9. [FAQ](#9-faq)

---

## 1. Voraussetzungen

| Anforderung | Mindestversion | Empfohlen |
|-------------|---------------|-----------|
| PHP | 8.0 | 8.2+ |
| MySQL/MariaDB | 5.7 | 8.0+ |
| Composer | 2.x | 2.x |

**Neu:** PHP 7.x wird **nicht mehr unterstützt**. Das Framework benötigt PHP >= 8.0.

### Composer installieren (falls noch nicht vorhanden)

```bash
# Im Projektverzeichnis
composer install
```

Dies installiert die Entwicklungstools (PHPUnit, PHPStan). Für Produktionsserver:

```bash
composer install --no-dev
```

---

## 2. Schnellstart

```bash
# 1. Code aktualisieren
git pull

# 2. Composer Dependencies installieren
composer install

# 3. (Optional) .env einrichten
cp .env.example .env
# .env anpassen

# 4. Testen
./vendor/bin/phpunit        # Unit-Tests
./vendor/bin/phpstan analyse # Statische Analyse
```

**Das war's.** Bestehende Applikationen laufen ohne weitere Änderungen.

---

## 3. Was passiert automatisch

### Passwort-Hashing: Automatische Migration

**Vorher:** Passwörter wurden mit MD5, SHA1 oder PHPass gehasht.
**Jetzt:** Neue Passwörter verwenden **bcrypt** (`password_hash()` mit Cost 12).

**Was passiert mit bestehenden Nutzern?**
- Bestehende Passwort-Hashes bleiben gültig und funktionieren weiterhin.
- Der Login-Prozess erkennt den alten Hash-Typ am `hash_type`-Feld in der Datenbank.
- **Keine Zwangs-Migration:** Nutzer können sich weiterhin mit ihren alten Passwörtern anmelden.
- Neue Passwörter (Registrierung, Passwort-Änderung) verwenden automatisch bcrypt.

**Kein Handlungsbedarf** — außer Sie möchten bestehende Hashes aktiv migrieren
(nicht empfohlen; passiert bei der nächsten Passwort-Änderung automatisch).

### Security Headers

Diese HTTP-Headers werden jetzt automatisch gesetzt:

| Header | Wert |
|--------|------|
| `X-Content-Type-Options` | `nosniff` |
| `X-Frame-Options` | `SAMEORIGIN` |
| `Referrer-Policy` | `strict-origin-when-cross-origin` |
| `Permissions-Policy` | restriktiv |

Alle Headers verwenden `override=false`. **Wenn Ihre Applikation eigene Headers setzt,
haben diese Vorrang.** Die Framework-Defaults greifen nur, wenn kein eigener Wert definiert ist.

### Session-Security

Sessions verwenden jetzt automatisch:
- `httponly = true` (Cookie nicht per JavaScript zugänglich)
- `secure = true` bei HTTPS-Verbindungen
- `samesite = Lax`

**Kein Handlungsbedarf** — es sei denn, Ihre Applikation benötigt JavaScript-Zugriff auf
Session-Cookies (unwahrscheinlich und nicht empfohlen).

### PHP 8.x Kompatibilität

Folgende PHP 8.x Inkompatibilitäten wurden behoben:
- `get_magic_quotes_gpc()` (entfernt seit PHP 7.4)
- `E_STRICT` als separate Konstante (Teil von `E_ALL` seit PHP 8.0)
- `isset()` auf Magic Methods (wirft Fehler seit PHP 8.0)

**Kein Handlungsbedarf** — diese Fixes betreffen nur den Framework-Core.

---

## 4. Neue Features nutzen

### 4.1 Environment-Konfiguration (.env)

Statt Konfigurationswerte direkt in PHP-Dateien als Konstanten zu definieren, können Sie
jetzt eine `.env`-Datei verwenden. **Das ist optional** — die bisherige Methode funktioniert
weiterhin.

#### Bisheriger Ansatz (funktioniert weiterhin)

```php
// In Ihrer config.php / index.php (vor dem Framework-Include)
define('APP_DB_HOST', '127.0.0.1');
define('APP_DB_NAME', 'mydb');
define('APP_DB_USER', 'root');
define('APP_DB_PASSWORD', 'secret');
define('APP_TESTMODE', false);
```

#### Neuer Ansatz mit .env

```bash
# .env (im Projektverzeichnis, NICHT in Git committen!)
APP_DB_HOST=127.0.0.1
APP_DB_NAME=mydb
APP_DB_USER=root
APP_DB_PASSWORD=secret
APP_TESTMODE=false
```

**Vorteile der .env-Variante:**
- Keine Passwörter im Quellcode
- Einfacher Wechsel zwischen Umgebungen (Dev/Staging/Prod)
- `.env` ist in `.gitignore` eingetragen — wird nicht versehentlich committet
- `.env.example` dient als Referenz für neue Teammitglieder

**Reihenfolge der Konfiguration:**
1. Konstanten, die Ihre Applikation vor dem Framework-Include definiert, haben Vorrang
2. `.env`-Werte werden nur definiert, wenn die Konstante noch nicht existiert
3. `constants.inc.php` setzt Defaults für alles, was noch nicht definiert ist

**Type-Casting in .env:**
- `true` / `false` → PHP `bool`
- Ganzzahlen → PHP `int`
- Dezimalzahlen → PHP `float`
- Alles andere → PHP `string`

**Direkte Nutzung im Code (optional):**
```php
// Über die Env-Klasse (gibt keinen Fehler wenn .env nicht geladen)
$host = Env::get('DB_HOST', 'localhost');

// Über die Konstante (wie gewohnt)
$host = APP_DB_HOST;
```

**Einschränkung:** Die `.env`-Datei muss im Verzeichnis `APP_INCLUDE_ABSPATH` liegen
(typischerweise das Projektverzeichnis). `APP_INCLUDE_ABSPATH` muss definiert sein,
bevor `start.php` inkludiert wird.

### 4.2 Prepared Statements

Für neue Datenbankzugriffe stehen Prepared Statements zur Verfügung:

```php
// Über die DB-Klasse (empfohlen)
DB::execute_prepared('INSERT INTO users (name, email) VALUES (?, ?)', ['Max', 'max@example.com']);
$result = DB::query_prepared('SELECT * FROM users WHERE id = ?', [42]);

// Über den Driver direkt
$driver->execute_prepared('UPDATE users SET name = ? WHERE id = ?', ['Max', 42]);
$result = $driver->query_prepared('SELECT * FROM users WHERE email = ?', ['max@example.com']);
```

**Bestehender Code funktioniert weiterhin** — die alten `DB::execute()` und `DB::query()`
Methoden mit `mysqli_real_escape_string()` bleiben erhalten. Eine schrittweise Migration
auf Prepared Statements wird empfohlen.

### 4.3 Structured Logging

Der Logger unterstützt jetzt PSR-3 kompatible Log-Levels:

```php
// Statt:
Logger::log('Benutzer konnte sich nicht anmelden');

// Jetzt möglich:
Logger::error('Login fehlgeschlagen für {user}', ['user' => $username]);
Logger::warning('Langsame Query: {ms}ms', ['ms' => $duration]);
Logger::info('Benutzer {user} angemeldet', ['user' => $username]);
Logger::debug('Cache-Hit für Key {key}', ['key' => $cache_key]);

// Mit Exception (inkl. Stack-Trace im Log)
try {
    // ...
} catch (Exception $e) {
    Logger::error('Fehler bei Verarbeitung', ['exception' => $e]);
}

// Minimum-Level setzen (filtert weniger wichtige Meldungen)
Logger::set_min_level(Logger::WARNING); // Nur WARNING und höher loggen
```

**Log-Dateien:** Pro Level eine separate JSON-Datei (z.B. `error-2026-03-05.log`).
Der alte `Logger::log()` bleibt kompatibel und schreibt weiterhin im CSV-Format.

### 4.4 CLI-Tool (`bin/gyro`)

Ein neues Kommandozeilen-Werkzeug ermöglicht die Verwaltung des Frameworks ohne Browser:

```bash
# Alle verfügbaren Kommandos anzeigen
./bin/gyro help

# Alle DAO-Modelle auflisten
./bin/gyro model:list
./bin/gyro model:list --verbose    # Mit Feldzahl, Relations, Quellmodul

# Detailliertes Schema eines Modells anzeigen
./bin/gyro model:show users        # Felder, Typen, Defaults, Relations, CREATE TABLE SQL

# Datenbank mit Model-Schema vergleichen
./bin/gyro db:sync                 # Zeigt ALTER TABLE SQL (Dry Run)
./bin/gyro db:sync --execute       # Führt die Änderungen aus
./bin/gyro db:sync --table=users   # Nur eine Tabelle prüfen
```

**Kein Handlungsbedarf** — das CLI-Tool ist ein reiner Neuzugang ohne Auswirkung auf
bestehenden Code. Es nutzt die vorhandene Model-Introspection (`create_table_object()`)
und generiert SQL aus den bestehenden DAO-Definitionen.

**Eigene Kommandos schreiben:**
```php
class MyCommand extends CLICommand {
    public function get_name(): string { return 'my:task'; }
    public function get_description(): string { return 'Mein Kommando'; }
    public function execute(array $args): int {
        $this->success('Fertig!');
        return 0;
    }
}
```

---

## 5. Breaking Changes

### Minimale Breaking Changes

Die folgenden Änderungen können in seltenen Fällen bestehenden Code betreffen:

| Änderung | Betrifft | Aktion |
|----------|----------|--------|
| PHP >= 8.0 erforderlich | Alle auf PHP 7.x | PHP aktualisieren |
| Default-Hash ist `bcryp` statt `pas3p` | Usermanagement | Nur neue Accounts betroffen |
| `E_STRICT` nicht mehr separat | Error-Handler | Nur wenn explizit auf E_STRICT geprüft wird |
| CSRF: `===` statt `==` | FormHandler | Nur bei nicht-String Token-Vergleich (sehr unwahrscheinlich) |

### Interface-Änderungen

Wenn Ihre Applikation eigene Implementierungen dieser Interfaces hat, müssen Sie
Type Declarations ergänzen:

- **`IDBResultSet`** — Return Types in `fetch()`, `get_row_count()`, `get_status()`
- **`ISessionHandler`** — Return Types in Session-Methoden
- **`ICachePersister`** — Return Types in Cache-Methoden
- **`IConverter`** — Return Types in `encode()`, `decode()`
- **`IHashAlgorithm`** — Parameter- und Return Types in `hash()`, `check()`

**Beispiel:**

```php
// Vorher:
class MyConverter implements IConverter {
    public function encode($value, $params = array()) { /* ... */ }
}

// Nachher — mit den Type Declarations aus dem Interface:
class MyConverter implements IConverter {
    public function encode($value, array $params = array()): string { /* ... */ }
}
```

Prüfen Sie die aktuellen Interface-Dateien in `gyro/core/lib/interfaces/` für
die exakten Signaturen.

### IDBDriver Interface

`IDBDriver` wurde um zwei optionale Methoden erweitert:

```php
public function execute_prepared(string $sql, array $params = array()): int|false;
public function query_prepared(string $sql, array $params = array()): IDBResultSet|false;
```

**Wenn Sie einen eigenen DB-Driver implementiert haben** (nicht den mitgelieferten
MySQL-Driver), müssen Sie diese Methoden ergänzen. Der mitgelieferte Sphinx-Driver
hat diese Methoden derzeit noch nicht implementiert.

---

## 6. Datenbank-Updates

### Usermanagement: `hash_type` Feld

Falls Sie das Usermanagement-Modul verwenden und von einer sehr alten Version kommen,
stellen Sie sicher, dass das `hash_type`-Feld in der `users`-Tabelle existiert:

```sql
-- Nur nötig bei Upgrade von Version < 0.5.1
ALTER TABLE users ADD COLUMN hash_type VARCHAR(5) NOT NULL DEFAULT 'bcryp' AFTER password;
```

Wenn Sie das Systemupdate-Modul verwenden, wurde dieses SQL automatisch ausgeführt.

---

## 7. Entfernte Module

Die folgenden Module wurden entfernt, da sie nicht mehr gepflegt werden oder
mit aktuellen PHP-Versionen nicht mehr funktionieren:

| Modul | Grund | Alternative |
|-------|-------|-------------|
| `cache.xcache` | XCache seit PHP 7.0 nicht mehr verfügbar | `cache.acpu` (APCu) oder `cache.file` |
| `javascript.cleditor` | CLEditor seit Jahren abandoned | TinyMCE, CKEditor, Quill |
| `javascript.wymeditor` | WYMeditor seit Jahren abandoned | TinyMCE, CKEditor, Quill |

**Wenn Ihre Applikation diese Module verwendet:**
- Für `cache.xcache`: Wechseln Sie auf `cache.acpu` (APCu) oder `cache.file`.
  Die `ICachePersister`-Schnittstelle ist identisch.
- Für die Editor-Module: Integrieren Sie einen modernen WYSIWYG-Editor über das
  bestehende Widget-System oder als eigenständiges JavaScript-Modul.

---

## 8. Für Entwickler

### Tests ausführen

```bash
# Alle Tests
./vendor/bin/phpunit

# Nur Core-Tests
./vendor/bin/phpunit --testsuite core

# Einzelnen Test
./vendor/bin/phpunit tests/core/StringTest.php

# Mit Deprecation-Details
./vendor/bin/phpunit --display-deprecations
```

### Statische Analyse

```bash
# PHPStan ausführen (Level 2 mit Baseline)
./vendor/bin/phpstan analyse

# Ohne Cache (bei Problemen)
./vendor/bin/phpstan analyse --clear-result-cache
```

PHPStan Level 2 ist konfiguriert mit einer Baseline von 1262 bekannten Fehlern.
**Neue Fehler werden sofort gemeldet** — bestehende sind in `phpstan-baseline.neon`
getracked und können schrittweise behoben werden.

### Eigene Tests schreiben

```php
// tests/core/MyTest.php
<?php
use PHPUnit\Framework\TestCase;

class MyTest extends TestCase {
    public function test_example() {
        $this->assertEquals('HELLO', strtoupper('hello'));
    }
}
```

Der Test-Bootstrap (`tests/bootstrap.php`) lädt den Framework-Core automatisch.
Ein Mock-DB-Driver ist als Default-Connection registriert — kein echte Datenbankverbindung nötig.

### Prepared Statements in bestehendem Code einführen

Schritt-für-Schritt Migration:

```php
// 1. Finden Sie existierende Queries:
$result = DB::query("SELECT * FROM users WHERE email = '" . DB::escape($email) . "'");

// 2. Ersetzen Sie durch Prepared Statements:
$result = DB::query_prepared('SELECT * FROM users WHERE email = ?', [$email]);

// Bei INSERT/UPDATE/DELETE:
// Vorher:
DB::execute("DELETE FROM sessions WHERE id = '" . DB::escape($id) . "'");
// Nachher:
DB::execute_prepared('DELETE FROM sessions WHERE id = ?', [$id]);
```

---

## 9. FAQ

### Muss ich sofort alles umstellen?

**Nein.** Alle Änderungen sind rückwärtskompatibel. Sie können schrittweise migrieren:
- Zuerst: PHP 8.0+ sicherstellen und `composer install` ausführen
- Dann: Optional `.env` einrichten
- Später: Queries auf Prepared Statements umstellen
- Irgendwann: Logger auf Structured Logging umstellen

### Meine Applikation definiert eigene APP_*-Konstanten vor dem Framework-Include. Funktioniert das noch?

**Ja, genau wie vorher.** Ihre Konstanten haben immer Vorrang. Die `.env`-Datei
definiert Konstanten nur, wenn sie noch nicht existieren.

### Was passiert, wenn keine .env-Datei existiert?

**Nichts.** Das Framework verhält sich exakt wie vorher. Die `.env`-Unterstützung
ist vollständig optional.

### Ich habe einen eigenen Cache-Backend. Was muss ich tun?

Wenn Sie `ICachePersister` implementieren, ergänzen Sie die Return Type Declarations
laut Interface-Definition. Die Logik Ihrer Implementierung muss nicht geändert werden.

### Können wir PHPStan-Level weiter erhöhen?

Ja. Die Baseline-Strategie erlaubt es, das Level zu erhöhen, ohne alle bestehenden
Fehler sofort fixen zu müssen. Arbeiten Sie die Baseline schrittweise ab und erhöhen
Sie dann das Level.

### Meine Tests schlagen mit "Cannot redeclare class" fehl

Dies passiert, wenn Composer's Classmap-Autoloader und das Framework-eigene
`Load::directories()` die gleiche Datei über verschiedene Pfade laden. Stellen
Sie sicher, dass in `composer.json` **keine** `autoload.classmap` für `gyro/core/`
oder `contributions/` konfiguriert ist.
