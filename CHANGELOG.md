# Changelog

Alle wesentlichen Änderungen am Gyro-PHP Framework, chronologisch nach Phasen geordnet.

## [Phase 8] – 2026-03-05

### Hinzugefügt
- **CLI-Tool (`bin/gyro`):** Neues Kommandozeilen-Werkzeug für Gyro-PHP:
  - `gyro help` — Verfügbare Kommandos anzeigen
  - `gyro model:list` — Alle DAO-Modelle mit Tabellennamen, Feldern und Primary Keys auflisten
  - `gyro model:show <table>` — Detailliertes Schema eines Modells anzeigen (Felder, Typen, Defaults, Relations, CREATE TABLE SQL)
  - `gyro db:sync` — Model-Schema mit der Datenbank vergleichen und ALTER TABLE SQL generieren
- **CLI-Kernel** (`gyro/core/cli/clikernel.cls.php`): Command-Routing, Argument-Parsing, farbige Ausgabe
- **CLICommand** Basisklasse für eigene Kommandos
- **CLITable** ASCII-Tabellenrenderer für formatierte CLI-Ausgabe
- **CLI-Bootstrap** (`gyro/core/cli/bootstrap.cli.php`): Framework-Initialisierung ohne HTTP-Kontext
- **Model-Discovery:** Automatische Erkennung aller DAO-Klassen in Core, Modules und Contributions
- **Schema-Introspection:** Liest `create_table_object()` und generiert CREATE TABLE / ALTER TABLE SQL
- **33 neue Tests** für CLI-Komponenten (CLITable, CLIKernel, ModelShowCommand)

### Ergebnis
- 287 Tests, 1066 Assertions (alle grün, 0 Deprecations)

---

## [Phase 7] – 2026-03-05

### Hinzugefügt
- **`.env` Konfiguration:** Neuer `Env`-Loader (`gyro/core/lib/helpers/env.cls.php`) ermöglicht
  Environment-Konfiguration über `.env`-Dateien. Alle `APP_*` Variablen aus der `.env`-Datei
  werden automatisch als PHP-Konstanten definiert — vollständig rückwärtskompatibel.
- **`.env.example`:** Referenzdatei mit allen verfügbaren Konfigurationsvariablen.
- **11 neue Tests** für den Env-Loader (`tests/core/EnvTest.php`).
- **PHPStan Baseline** (`phpstan-baseline.neon`): 1262 bekannte Fehler getracked,
  neue Fehler werden sofort gemeldet.

### Geändert
- **PHPStan Level 1 → 2:** Strengere statische Analyse mit Baseline-Strategie.
- **Composer Classmap entfernt:** Die `autoload.classmap` Konfiguration wurde entfernt,
  da sie einen Pfadkonflikt mit dem Framework-eigenen `Load::directories()` verursachte
  (`include_once` erkannte die gleiche Datei unter verschiedenen Pfaden nicht als identisch).
- **`start.php`:** Lädt jetzt `.env` vor `constants.inc.php` (nur wenn `APP_INCLUDE_ABSPATH` definiert ist).
- **`.gitignore`:** `.env` hinzugefügt.

### Behoben
- **PHP 8.4 Deprecation Warnings:** 3 dynamische Properties gefixt:
  - `DAOStudentsTest::$modificationdate` als explizite Property deklariert
  - `Url::$url` als explizite Property deklariert (verwendet in `__sleep`/`__wakeup`)

### Ergebnis
- 254 Tests, 985 Assertions (alle grün, 0 Deprecations)
- PHPStan Level 2: keine neuen Fehler

---

## [Phase 6] – 2026-03-05

### Hinzugefügt
- **Typed Properties** in 12 Interface-Implementierungen (16 Properties total):
  - `DBResultSet`, `DBResultSetMysql`, `DBResultSetSphinx`, `DBResultSetCountSphinx`
  - `CacheDBImpl`, `CacheFileImpl`, `FileCacheItem`, `ACPuCacheItem`, `MemcacheCacheItem`
  - `ConverterChain`, `ConverterHtmlTidy`, `ConverterUnidecode`
- **`DB::execute_prepared()`** und **`DB::query_prepared()`** — statische Wrapper für
  Prepared Statements auf der DB-Klasse. Vereinfacht die Nutzung gegenüber dem direkten
  Driver-Zugriff.
- **PHPStan Level 1** eingerichtet (`phpstan.neon.dist`).

---

## [Phase 5] – 2026-03-05

### Entfernt
- **`cache.xcache`** — XCache ist seit PHP 7.0 nicht mehr verfügbar (8 Dateien).
- **`javascript.cleditor`** — CLEditor ist seit Jahren abandoned (~36 Dateien).
- **`javascript.wymeditor`** — WYMeditor ist seit Jahren abandoned (~79 Dateien).

### Hinzugefügt
- Weitere SimpleTest → PHPUnit Migrationen.
- PHPDoc für ausgewählte public APIs.

---

## [Phase 4] – 2026-03-05

### Hinzugefügt
- **Type Declarations** in 5 Core-Interfaces und allen Implementierungen:
  - `IDBResultSet` (3 Impl.), `ISessionHandler` (4 Impl.), `ICachePersister` (5 Impl.),
    `IConverter` (12+ Impl.), `IHashAlgorithm` (6 Impl.)
  - Union Types: `array|false`, `string|false`, `int|false`, `ICacheItem|false`, `mixed`
- **Structured Logging** (PSR-3 kompatibel) in `Logger`:
  - Neue Methoden: `Logger::emergency()`, `::alert()`, `::critical()`, `::error()`,
    `::warning()`, `::notice()`, `::info()`, `::debug()`
  - Context-Interpolation: `Logger::error('User {user} failed', ['user' => $name])`
  - JSON-Ausgabe pro Level (z.B. `error-2026-03-05.log`)
  - Exception-Support mit automatischem Stack-Trace
  - Konfigurierbares Minimum-Level: `Logger::set_min_level(Logger::WARNING)`

### Nicht geändert
- `IDBDriver` Type Declarations zurückgestellt (Sphinx-Driver hat fehlende Methoden).
- Namespace-Migration (PSR-4) zurückgestellt (zu großer Breaking Change).

---

## [Phase 3] – 2026-03-05

### Verbessert
- **Session-Security:**
  - `session.cookie_secure = 1` wird bei HTTPS automatisch gesetzt
  - `session.cookie_httponly = true` fest konfiguriert
  - `session.cookie_samesite = Lax` konfiguriert
  - Veralteter PHP < 7.3 `setcookie()` Fallback entfernt
- **CSRF-Token Validierung:** Strikter Vergleich `===` statt `==` in
  `FormHandler::validate()`.

### Geprüft (keine Änderung nötig)
- CSRF-Token-System: Bereits robust (random_bytes, Session-gebunden, DB-gestützt, Einmal-Tokens).
- Input-Handling: Core nutzt `PageData`/`TracedArray`, kein direkter `$_POST`/`$_GET` Zugriff.

---

## [Phase 2] – 2026-03-05

### Hinzugefügt
- **Composer** (`composer.json`): PHPUnit 10.5 als Dev-Dependency, PHP >=8.0.
- **PHPUnit Setup:** `phpunit.xml.dist`, `tests/bootstrap.php`, Test-Verzeichnisstruktur.
- **Prepared Statements** im MySQL-Driver:
  - `$driver->execute_prepared('INSERT INTO t (col) VALUES (?)', ['value'])`
  - `$driver->query_prepared('SELECT * FROM t WHERE id = ?', [42])`
  - Automatische Typerkennung der Parameter (`detect_param_types()`)
- **IDBDriver Interface:** Um `execute_prepared()` und `query_prepared()` erweitert.
- **SimpleTest → PHPUnit Migration** gestartet: `ArrayTest`, `StringTest`, `ValidationTest`.
- `.gitignore`: `/vendor/` hinzugefügt.

### Nicht geändert
- Bestehende `execute()`/`query()` Methoden bleiben unverändert (Rückwärtskompatibilität).
  Sie verwenden weiterhin `mysqli_real_escape_string()`.

---

## [Phase 1] – 2026-03-05

### Behoben (PHP 8.x Kompatibilität)
- **`common.cls.php`:** `preprocess_input()` als No-op implementiert, `transcribe()` entfernt
  (Magic Quotes gibt es seit PHP 7.4 nicht mehr).
- **`start.php`:** `E_ALL | E_STRICT` → `E_ALL` (E_STRICT ist seit PHP 8.0 Teil von E_ALL).
  PHP 5.3 Kompatibilitäts-Check (`defined('E_DEPRECATED')`) entfernt.
- **`cast.cls.php`:** `isset($value->__toString)` → `method_exists($value, '__toString')`
  (PHP 8.0 wirft bei `isset()` auf Magic Methods einen Fehler).

### Verbessert (Sicherheit)
- **Passwort-Hashing:** Default von MD5/PHPass auf **bcrypt** umgestellt:
  - `password_hash()` mit `PASSWORD_BCRYPT`, Cost-Factor 12
  - Neuer Hash-Algorithmus `bcryp` in `contributions/usermanagement/`
  - Automatische Migration: Bestehende Hashes werden beim nächsten Login transparent
    auf bcrypt aktualisiert
- **HTTP Security Headers:**
  - `X-Content-Type-Options: nosniff`
  - `X-Frame-Options: SAMEORIGIN`
  - `Referrer-Policy: strict-origin-when-cross-origin`
  - `Permissions-Policy` (restriktiv)
  - Alle mit `override=false` — Applikationen können sie überschreiben
- **Timing-safe Vergleiche:** `hash_equals()` in MD5 und SHA1 Hash-Klassen.
