# Gyro-PHP Framework – Projektanalyse & Memory

> Letzte Aktualisierung: 2026-03-05 (Phase 8 abgeschlossen)

## Projektübersicht

- **Framework:** Gyro-PHP, eigenes PHP-Webframework (seit 2004, PHP 4 → PHP 5 Rewrite 2005)
- **Aktueller Stand:** Läuft auf PHP 8.x mit Safeguards, Code-Stil ist PHP 5.x Ära
- **Composer** für Dev-Dependencies (PHPUnit, PHPStan), kein PSR-4, kein Namespace-System
- **Test-Framework:** PHPUnit 10.5 (primär, 287 Tests) + SimpleTest 1.1.0 (Legacy, abandoned)
- **CLI-Tool:** `bin/gyro` (Phase 8) — model:list, model:show, db:sync
- **Statische Analyse:** PHPStan Level 2 mit Baseline (1262 bekannte Fehler getracked)
- **Environment:** `.env` Support (Phase 7), rückwärtskompatibel mit `APP_*` Konstanten

## Verzeichnisstruktur

```
bin/                             # CLI-Werkzeuge
  gyro                           # CLI Entry Point (Phase 8)
gyro/                          # Framework-Core
  core/
    config.cls.php             # Zentrale Config (281 Zeilen, 100+ Konstanten)
    start.php                  # Bootstrap/Entry Point
    cli/                       # CLI-Kernel, Commands, Helpers (Phase 8)
    controller/base/           # Basis-Controller & Routing
    model/base/                # DB-Abstraktionsschicht
    model/drivers/mysql/       # MySQL-Driver (nur mysqli_real_escape_string)
    lib/components/            # Core-Komponenten (Logger, HTTP, etc.)
    lib/helpers/               # Hilfsklassen (String, Array, Cast, etc.)
    lib/interfaces/            # Interface-Definitionen
    view/base/                 # View-Layer
  modules/                     # Framework-Module
    simpletest/                # Test-Framework + Tests
    cache.*/                   # Cache-Backends (memcache, xcache, acpu, file, mysql)
    mime/, json/, mail/, etc.  # Diverse Module
contributions/                 # Erweiterungen/Plugins (60+ Module)
  usermanagement/              # User-Verwaltung (bcrypt Default seit Phase 1)
  lib.geocalc/                 # Geo-Berechnungen
  scheduler/, gsitemap/, etc.  # Diverse Beiträge
```

## Statistiken

| Metrik | Wert |
|--------|------|
| Core-Klassen | 239 (.cls.php, .model.php, .facade.php) |
| PHPUnit-Tests | 287 Tests, 1066 Assertions (65 Test-Dateien) |
| SimpleTest (Legacy) | 57 Dateien (größtenteils nach PHPUnit portiert) |
| Testabdeckung | ~50%+ (Phase 7: massive Erweiterung) |
| PHPDoc-Abdeckung | ~15-20% |
| TODO/FIXME/HACK | 14 Marker |
| Contributions | 57+ Module (3 tote entfernt in Phase 5) |
| PHPStan | Level 2, Baseline mit 1262 bekannten Fehlern |

## Sicherheitsprobleme

### ✅ GEFIXT: Passwort-Hashing
- Default von MD5/PHPass auf **bcrypt** umgestellt (`password_hash(PASSWORD_BCRYPT, cost 12)`)
- Neuer Hash-Algorithmus: `contributions/usermanagement/behaviour/commands/users/hashes/bcryp.hash.php`
- Timing-safe Vergleiche in MD5/SHA1 Klassen (`hash_equals()`)
- Auto-Upgrade: Alte Hashes werden beim nächsten Login automatisch migriert

### ✅ GEFIXT: HTTP Security Headers
- X-Content-Type-Options, X-Frame-Options, Referrer-Policy, Permissions-Policy
- Gesetzt in `pageviewbase.cls.php` mit `override=false`

### ✅ GEFIXT: Prepared Statements
- **Driver:** `execute_prepared()` und `query_prepared()` in `dbdriver.mysql.php` (Phase 2)
- **DB-Klasse:** `DB::execute_prepared()` und `DB::query_prepared()` Wrapper (Phase 6)
- Legacy `execute()`/`query()` nutzen weiterhin `mysqli_real_escape_string()` (Rückwärtskompatibilität)
- **Nächster Schritt:** Schrittweise Migration bestehender Queries auf Prepared Statements

### ✅ GEFIXT: Session-Konfiguration
- `httponly`, `secure` (bei HTTPS), `samesite=Lax` auf Session-Cookies konfiguriert

## ✅ PHP 8.x Kompatibilität (GEFIXT)

- `common.cls.php`: `preprocess_input()` → No-op (Magic Quotes seit PHP 7.4 weg)
- `start.php`: `E_ALL | E_STRICT` → `E_ALL`, PHP 5.3 Compat-Check entfernt
- `cast.cls.php`: `isset($value->__toString)` → `method_exists($value, '__toString')`
- `mb_*` Funktionen: NULL-Parameter teilweise gefixt (bereits vor Phase 1)

## Architektur-Schwächen

### Typ-System (Phase 4 + Phase 6)
- Interfaces mit Type Declarations versehen (Phase 4)
- Typed Properties in Interface-Implementierungen (Phase 6)
- Kein Einsatz von Enums, Attributes, Match, Readonly etc.

### Kein Namespace-System
- Alle Klassen im globalen Namespace
- Namenskonventionen statt Namespaces: `DAO*`, `*Controller`, `*Facade`
- Eigenes Autoloading statt PSR-4

### ✅ Logger modernisiert (Phase 4)
- **Datei:** `gyro/core/lib/components/logger.cls.php`
- PSR-3 kompatible Log-Levels (emergency → debug)
- Context-Interpolation (`{placeholder}` Syntax)
- JSON-Ausgabe für strukturierte Logs, CSV für Legacy `log()`
- Exception-Support mit Stack-Traces
- Konfigurierbares Minimum-Level via `Logger::set_min_level()`

### ✅ Environment-Konfiguration (Phase 7)
- **Datei:** `gyro/core/lib/helpers/env.cls.php`
- `.env` Datei-Loader mit automatischer `APP_*` Konstanten-Definition
- Rückwärtskompatibel: Ohne `.env` funktioniert alles wie bisher
- Integration in `start.php`: Lädt `.env` vor `constants.inc.php`
- `.env.example` mit allen verfügbaren Konfigurationsvariablen
- Type-Casting: `true`/`false` → bool, Zahlen → int/float
- Keine externe Dependency (kein vlucas/phpdotenv nötig)

### Konfigurations-Schwächen (teilweise behoben)
- ✅ `.env` Support für Environment-abhängige Konfiguration (Phase 7)
- Hardcoded Timeouts: `$timeout_sec = 30` (HTTP), `$max_age = 600` (Cache)
- Magic Numbers: Port 443 für HTTPS, ASCII-Codes `10`/`13`, Email-Limit `64`
- String-basierte Konstanten-Lookup (flexibel aber nicht typsicher)

## Veraltete/Tote Module

### ✅ Entfernt in Phase 5
- `cache.xcache` – XCache seit PHP 7 tot (8 Dateien)
- `javascript.cleditor` – CLEditor abandoned (~36 Dateien)
- `javascript.wymeditor` – WYMeditor abandoned (~79 Dateien)

### Noch vorhanden, prüfen
- `cache.acpu` – APCu noch aktiv, nur entfernen wenn Server kein APCu nutzt
- SimpleTest 1.1.0 – abandoned seit 2012, PHPUnit parallel eingerichtet
- Mehrere CSS-Präprozessor-Module (`css.sass`, `css.yaml`, `css.postcss`)

## Modernisierungsplan (Phasen)

### Phase 1: Sicherheit & Lauffähigkeit (KRITISCH) ✅ ERLEDIGT
- [x] PHP 8.x Fatal Errors fixen (`get_magic_quotes_gpc`, `E_STRICT`, `isset(__toString)`)
- [x] Passwort-Hashing: MD5 → `password_hash()` mit bcrypt (neuer `bcryp` Hash-Algorithmus)
- [x] HTTP Security Headers einführen (X-Content-Type-Options, X-Frame-Options, Referrer-Policy, Permissions-Policy)
- [x] Timing-safe Vergleiche in MD5/SHA1 Hash-Klassen (`hash_equals()`)

#### Phase 1 Details
- `common.cls.php`: `preprocess_input()` → No-op, `transcribe()` entfernt (Magic Quotes seit PHP 7.4 weg)
- `start.php`: `E_ALL | E_STRICT` → `E_ALL`, `defined('E_DEPRECATED')` Check entfernt (PHP 5.3 Compat)
- `cast.cls.php`: `isset($value->__toString)` → `method_exists($value, '__toString')`
- Neuer Hash-Algorithmus: `contributions/usermanagement/behaviour/commands/users/hashes/bcryp.hash.php`
- Default Hash-Type: `'pas3p'` → `'bcryp'` in `start.inc.php` und `users.model.php`
- Auto-Upgrade: Bestehender Login-Code migriert alte Hashes automatisch beim nächsten Login
- Security Headers in `pageviewbase.cls.php` mit `override=false` (Apps können überschreiben)

### Phase 2: Infrastruktur ✅ ERLEDIGT
- [x] `composer.json` erstellen mit PHPUnit 10.5 als Dev-Dependency
- [x] PHPUnit Setup: `phpunit.xml.dist`, `tests/bootstrap.php`, Test-Verzeichnisse
- [x] SimpleTest → PHPUnit Migration gestartet (3 Test-Klassen portiert: Array, String, Validation)
- [x] Prepared Statements im MySQL-Driver (`execute_prepared()`, `query_prepared()`)
- [x] `.gitignore` um `/vendor/` erweitert

#### Phase 2 Details
- `composer.json`: PHPUnit 10.5, PHP >=8.0
- `tests/bootstrap.php`: Leichtgewichtiger Bootstrap der nur Core-Helpers lädt (kein DB, kein Session)
- Portierte Tests: `ArrayTest` (10 Tests), `StringTest` (13 Tests), `ValidationTest` (6 Tests) = 29 Tests, 149 Assertions
- `ß → SS` Verhalten in `test_to_upper` für PHP 8.x korrigiert (mb_strtoupper konvertiert jetzt korrekt)
- `IDBDriver` Interface um `execute_prepared()` und `query_prepared()` erweitert
- MySQL-Driver: Prepared Statements mit auto-detect Typisierung (`detect_param_types()`)
- Bestehende `execute()`/`query()` bleiben unverändert (keine Breaking Changes)
- Nutzung: `$driver->execute_prepared('INSERT INTO t (col) VALUES (?)', ['value'])`

### Phase 3: Sicherheit (Vertiefung) ✅ ERLEDIGT
- [x] Session-Security: `secure` Flag bei HTTPS, PHP < 7.3 Branch entfernt, `httponly=true` hardcoded
- [x] CSRF-Token-System: Bereits robust (random_bytes, Session-gebunden, DB-gestützt, Einmal-Tokens)
- [x] CSRF: `==` → `===` in FormHandler::validate() für strikten Vergleich
- [x] Input-Validation: Core sauber (PageData/TracedArray), nur 3rd-Party hat rohe $_REQUEST Zugriffe

#### Phase 3 Details
- `session.cls.php`: `ini_set('session.cookie_secure', 1)` bei HTTPS automatisch gesetzt
- `session.cls.php`: PHP < 7.3 `setcookie()` Branch entfernt (braucht PHP >= 8.0)
- `formhandler.cls.php`: Strikter Vergleich `===` statt `==` bei Token-Validation
- CSRF-Tokens: `Common::create_token()` nutzt `random_bytes(20)` → kryptographisch sicher
- Input-Zugriff: Kein direkter `$_POST/$_GET` im Core (nur `$_GET['cookietest']` in Session)
- 3rd-Party `$_REQUEST` Zugriffe in csstidy/wymeditor → nicht im Scope

### Phase 4: Modernisierung ✅ ERLEDIGT
- [x] Type Declarations in Interfaces + Implementierungen eingeführt
- [ ] Namespaces einführen (PSR-4) – **zurückgestellt** (zu großer Breaking Change)
- [x] Structured Logging (PSR-3 kompatibel)

#### Phase 4 Details: Type Declarations
- **IDBResultSet** + 3 Implementierungen (DBResultSet, DBResultSetMysql, DBResultSetSphinx)
- **ISessionHandler** + 4 Implementierungen (DBSession, ACPuSession, MemcacheSession, XCacheSession)
- **IHashAlgorithm** + 6 Implementierungen (bcryp, bcrypt, md5, sha1, pas2p, pas3p)
- **IConverter** + 12 Implementierungen (callback, chain, html, mimeheader, none, json, htmltidy, punycode, htmlpurifier, textplaceholders, unidecode, twitter)
- **ICachePersister** + 5 Implementierungen (CacheDBImpl, CacheFileImpl, CacheXCacheImpl, CacheACPuImpl, CacheMemcacheImpl)
- Union Types: `array|false`, `string|false`, `int|false`, `ICacheItem|false`, `mixed`
- **IDBDriver** zurückgestellt (Sphinx-Driver hat fehlende Methoden)

#### Phase 4 Details: Structured Logging
- `Logger` erweitert um PSR-3 kompatible Methoden: `Logger::error()`, `Logger::info()`, etc.
- Context-Interpolation: `Logger::error('User {user} failed login', ['user' => $name])`
- JSON-Output pro Level-Datei (z.B. `error-2026-03-05.log`)
- Exception-Support: `Logger::error('Fehler', ['exception' => $ex])` → inkl. Trace
- Konfigurierbar: `Logger::set_min_level(Logger::WARNING)` filtert Debug/Info/Notice
- Legacy `Logger::log()` bleibt voll rückwärtskompatibel (CSV-Format)

### Phase 5: Qualität & Cleanup
- [ ] Veraltete Module entfernen (xcache, acpu, abandoned JS-Libs)
- [ ] PHPDoc für alle public APIs
- [x] Testabdeckung auf >50% bringen ✅ (Phase 7)

### Phase 6: Modernisierung II ✅ ERLEDIGT
- [x] Typed Properties in allen Interface-Implementierungen (12 Klassen, 16 Properties)
- [x] `DB::execute_prepared()` und `DB::query_prepared()` statische Wrapper
- [x] Composer classmap Autoload → **entfernt** (Phase 7: Konflikt mit `Load::directories()` und `include_once` Pfad-Auflösung)
- [x] PHPStan Level 1 eingerichtet → **Level 2 mit Baseline** (Phase 7)

#### Phase 6 Details: Typed Properties
- **DBResultSet**: `?PDOStatement $pdo_statement`
- **DBResultSetMysql**: `?mysqli_result $result_set`, `?Status $status`
- **DBResultSetSphinx**: `?array $result`, `Status $status`
- **DBResultSetCountSphinx**: `bool $done`
- **CacheDBImpl**: `mixed $cache_item`
- **CacheFileImpl**: `string $cache_dir`, `string $ext`, `string $divider`
- **FileCacheItem**: `array $item_data`
- **ACPuCacheItem**: `array $item_data`
- **MemcacheCacheItem**: `array $item_data`
- **ConverterChain**: `array $converters`, `array $params`
- **ConverterHtmlTidy**: `array $predefined_params`
- **ConverterUnidecode**: `static array $groups`

#### Phase 6 Details: Composer & PHPStan
- `composer.json`: classmap entfernt (Phase 7 — Pfadkonflikte mit `Load::directories()`)
- `phpstan.neon.dist`: Level 2 mit Baseline (Phase 7), analysiert Core + Contributions
- PHPStan als `require-dev` Dependency hinzugefügt

### Phase 7: Testabdeckung & Infrastruktur ✅ ERLEDIGT
- [x] SimpleTest → PHPUnit Migration: 43 von 45 Tests portiert (2 brauchen echte DB)
- [x] Neue Tests für alle DB-Feldtypen (Bool, Enum, Float, Serialized, Set)
- [x] Neue Tests für Converter (Callback, Chain, None, Html, HtmlEx, MimeHeader)
- [x] Neue Tests für Query Builder (Select, Count, Delete, Insert, Update, Joined, Secondary)
- [x] Neue Tests für Where/Filter/Sort (DBWhere, DBWhereGroup, DBFilter, DBFilterColumn, DBSortColumn, DBCondition)
- [x] Neue Tests für Routing (ExactMatchRoute, ParameterizedRoute, RouteBase)
- [x] Neue Tests für Helpers (Cast, Timer, HtmlString, PathStack, Header, RuntimeCache, Locale)
- [x] Neue Tests für Model (DAO, DataObject, DBExpression, DBNull, DBFieldRelation, DBJoinCondition)
- [x] Neue Tests für weitere Klassen (TracedArray, RequestInfo, GyroCookieConfig, Referer, WidgetInput)
- [x] `.env` Environment-Konfiguration (eigener Loader, keine externe Dependency)
- [x] PHPStan Level 1 → Level 2 mit Baseline (1262 bekannte Fehler)
- [x] Composer classmap entfernt (Pfadkonflikt mit `include_once`)
- [x] `ConverterHtmlEx` PHP 8.x Type-Kompatibilität gefixt
- [x] EnvTest (11 Tests)
- **Ergebnis:** 254 Tests, 985 Assertions (alle grün)

#### Phase 7 Details: .env Support
- **Datei:** `gyro/core/lib/helpers/env.cls.php` (Env-Klasse)
- **Integration:** `start.php` lädt `.env` vor `constants.inc.php`
- **Mechanismus:** `.env` Werte werden als `APP_*` Konstanten definiert (wenn nicht bereits definiert)
- Bestehende `set_value_from_constant()` / `set_feature_from_constant()` Aufrufe greifen automatisch
- `.env.example` dokumentiert alle verfügbaren `APP_*` Variablen
- `.env` in `.gitignore` aufgenommen
- Nutzung: `Env::get('DB_HOST', 'localhost')` oder über `APP_DB_HOST` Konstante

#### Phase 7 Details: PHPStan Level 2
- `phpstan.neon.dist`: Level 2, Baseline (`phpstan-baseline.neon`) mit 1262 bekannten Fehlern
- Neue Fehler werden sofort gemeldet, bestehende sind getracked
- 10 Contribution-Dateien excludiert (fehlende externe Klassen/Interfaces)
- Sphinx-Driver: `execute_prepared()`/`query_prepared()` fehlen weiterhin (bekannt)

#### Phase 7 Details: Testinfrastruktur
- `tests/bootstrap.php`: Lädt kompletten Framework-Core für Tests
  - Model-Subdirectories (`fields/`, `queries/`, `sqlbuilder/`, `constraints/`)
  - Controller/Routing, Behaviour, View/Widgets
  - Converter-Klassen (`lib/helpers/converters/`)
  - Mock-DB-Driver via Reflection als Default-Connection registriert
- `phpunit.xml.dist`: Core + Contributions Test-Suites
- Mock-Klassen: `DBDriverMySqlMock` (kein DB-Connect), `MockIDBTable` (SQL-Generation testen)

#### Phase 7 Details: Bekannte Test-Limitierungen
- 2 SimpleTest-Dateien nicht portierbar ohne echte DB (Cache, UpdateCommand)
- ~~3 PHP 8.4 Deprecation Warnings~~ → alle gefixt (dynamische Properties deklariert)
- Mock-Driver nutzt `GyroString::escape()` (HTML-Entities) statt `mysqli_real_escape_string`

### Phase 8: CLI-Tool ✅ ERLEDIGT
- [x] CLI Entry Point (`bin/gyro`) mit Bootstrap ohne HTTP-Kontext
- [x] CLI-Kernel mit Command-Routing, Argument-Parsing, farbiger Ausgabe
- [x] `model:list` — Alle DAO-Modelle auflisten (mit Model-Discovery)
- [x] `model:show <table>` — Detailliertes Schema, CREATE TABLE SQL
- [x] `db:sync` — Schema-Diff mit ALTER TABLE Generation (Dry-Run + Execute)
- [x] CLITable ASCII-Tabellenrenderer
- [x] 33 neue Tests (CLITable, CLIKernel, ModelShowCommand)
- **Ergebnis:** 287 Tests, 1066 Assertions (alle grün)

#### Phase 8 Details: CLI-Architektur
- **Entry Point:** `bin/gyro` (executable PHP-Script)
- **Bootstrap:** `gyro/core/cli/bootstrap.cli.php` — lädt Framework-Core ohne Sessions/Routing/Output
- **Kernel:** `gyro/core/cli/clikernel.cls.php` — registriert Commands, parsed Args, delegiert
- **Commands:** `gyro/core/cli/commands/` — je ein Kommando pro Datei
- **Erweiterbar:** Eigene Commands durch Ableitung von `CLICommand`

#### Phase 8 Details: Model-Discovery
- Scannt `GYRO_CORE_DIR/model/classes/` und alle geladenen Module-Verzeichnisse
- Instanziiert DAOs und liest Schema via `get_table_fields()`, `get_table_keys()`, `get_table_relations()`
- Fallback: Wenn Klassennamen-Ableitung nicht passt, erkennt neue `DAO*` Klassen via `get_declared_classes()`
- Generiert CREATE TABLE SQL aus DBField-Introspection

#### Phase 8 Details: db:sync
- Vergleicht Model-Schema mit INFORMATION_SCHEMA (SHOW COLUMNS)
- Erkennt: fehlende Tabellen (CREATE), fehlende Spalten (ADD COLUMN), geänderte Typen (MODIFY COLUMN)
- Warnt bei DB-Spalten, die nicht im Model existieren (kein Auto-DROP — zu gefährlich)
- `--dry-run` (Default) zeigt SQL, `--execute` führt aus

## Scorecard

| Aspekt | Bewertung | Notizen |
|--------|-----------|---------|
| Testabdeckung | 7/10 | ~55%+, 287 Tests / 1066 Assertions (PHPUnit 10.5) |
| Test-Framework | 7/10 | PHPUnit 10.5 primär, Mock-Infrastruktur, SimpleTest Legacy |
| Dokumentation | 4/10 | PHPDoc sparse |
| Dead Code | 8/10 | Minimal, sauber |
| Konfiguration | 7/10 | ✅ `.env` Support, zentralisiert, noch Magic Numbers |
| Error Logging | 7/10 | ✅ PSR-3 Levels, JSON-Output, Context, Exception-Support |
| Moderne PHP-Features | 5/10 | ✅ Type Declarations, ✅ Typed Properties, ✅ Union Types |
| Sicherheit | 7/10 | ✅ bcrypt, ✅ Headers, ✅ Prepared Stmt, ✅ Session, ✅ CSRF |
| CLI-Tooling | 6/10 | ✅ `bin/gyro` mit model:list, model:show, db:sync |
| Statische Analyse | 5/10 | ✅ PHPStan Level 2 mit Baseline, 1262 Fehler getracked |

## Moderne PHP-Features Analyse

### Bestandsaufnahme (Stand 2026-03-05)

| Feature | Vorhanden? | Details |
|---------|-----------|---------|
| Namespaces | NEIN | 0 Deklarationen im Framework (nur 3rd-Party FPDI nutzt sie) |
| Typed Properties | TEILWEISE | ✅ In 12 Interface-Implementierungen (Phase 6), Rest noch untypisiert |
| Enums | NEIN | Kein PHP 8.1+ `enum` |
| Named Arguments | NEIN | Nicht genutzt |
| Match Expressions | NEIN | Nur in 3rd-Party (SimpleTest, Sphinx) |
| Readonly Properties | NEIN | Nicht genutzt |
| Fibers/Async | NEIN | Nicht genutzt |
| Attributes | NEIN | Kein PHP 8.0+ `#[...]` |
| PSR-Interfaces | MINIMAL | Eigene Event-Interfaces (IEventSink/IEventSource), kein PSR-7/11/14/15/17/18 |
| Composer Autoload | NEIN | classmap entfernt (Pfadkonflikt), eigene `Load`-Klasse |
| Environment Vars (.env) | ✅ JA | Eigener `.env` Loader (`Env`-Klasse), `APP_*` auto-define (Phase 7) |
| Return Type Declarations | TEILWEISE | In 5 Core-Interfaces (Phase 4) |
| Union Types | TEILWEISE | `string\|false`, `array\|false`, `int\|false`, `ICacheItem\|false`, `mixed` |

### Interfaces mit Type Declarations (Phase 4)

| Interface | Datei | Implementierungen |
|-----------|-------|-------------------|
| IDBResultSet | `gyro/core/lib/interfaces/idbresultset.cls.php` | DBResultSet, DBResultSetMysql, DBResultSetSphinx |
| ISessionHandler | `gyro/core/lib/interfaces/isessionhandler.cls.php` | DBSession, ACPuSession, MemcacheSession, XCacheSession |
| ICachePersister | `gyro/core/lib/interfaces/icachepersister.cls.php` | CacheDBImpl, CacheFileImpl, CacheXCacheImpl, CacheACPuImpl, CacheMemcacheImpl |
| IConverter | `gyro/core/lib/interfaces/iconverter.cls.php` | 12+ Implementierungen (callback, chain, html, json, punycode, etc.) |
| IHashAlgorithm | `contributions/usermanagement/lib/interfaces/ihash.cls.php` | bcryp, bcrypt, md5, sha1, pas2p, pas3p |

### Autoloading

- **Eigene Klasse:** `gyro/core/load.cls.php` (`Load::add_module_base_dir()`)
- Kein PSR-4, kein Composer-Autoload
- Modul-Discovery über Framework-eigenes System

### Fazit

Framework ist **selektiv modernisiert**: Return Types + Union Types in Core-Interfaces, Typed Properties in Implementierungen, `.env` Support, PHPStan Level 2. Keine Nutzung von Namespaces, Enums, Attributes, Match, Readonly. Code-Stil bleibt PHP 5.x Ära mit PHP 8.x Kompatibilität und moderner Tooling-Infrastruktur.

### Nächste Schritte (Empfehlung)
- PHPStan Baseline schrittweise abbauen (1262 → 0 Fehler)
- PHPDoc für public APIs ergänzen
- Middleware-Pattern einführen
- Einfacher DI-Container für bessere Testbarkeit
- ~~CLI-Tool für Code-Generierung (ähnlich Artisan)~~ ✅ Phase 8: `bin/gyro`
- Auto-REST-API aus DAO-Modellen generieren
- Auto-Admin-Interface aus ISelfDescribing + IActionSource

## Wichtige Dateien für schnellen Einstieg

| Zweck | Pfad |
|-------|------|
| Bootstrap | `gyro/core/start.php` |
| Config | `gyro/core/config.cls.php` |
| Env-Loader | `gyro/core/lib/helpers/env.cls.php` |
| .env Beispiel | `.env.example` |
| CLI Entry Point | `bin/gyro` |
| CLI Kernel | `gyro/core/cli/clikernel.cls.php` |
| CLI Bootstrap | `gyro/core/cli/bootstrap.cli.php` |
| CLI Commands | `gyro/core/cli/commands/` |
| DB-Driver | `gyro/core/model/drivers/mysql/dbdriver.mysql.php` |
| Logger | `gyro/core/lib/components/logger.cls.php` |
| User-Model | `contributions/usermanagement/model/classes/users.model.php` |
| String-Helpers | `gyro/core/lib/helpers/string.cls.php` |
| PHPUnit-Tests | `tests/core/` (56 Dateien) |
| Test-Bootstrap | `tests/bootstrap.php` |
| SimpleTest (Legacy) | `gyro/modules/simpletest/simpletests/` |
| Routing | `gyro/core/controller/base/routes/` |
| PHPStan Config | `phpstan.neon.dist` + `phpstan-baseline.neon` |
| Changelog | `CHANGELOG.md` |
| Upgrade-Leitfaden | `UPGRADING.md` |

## Pflichtregeln für Änderungen

Bei **jeder Code-Änderung** müssen folgende Dateien mit-aktualisiert werden:

1. **`CHANGELOG.md`** — Neue Einträge oben einfügen (gleiche Phase oder neue Phase)
2. **`UPGRADING.md`** — Wenn die Änderung bestehende Nutzer betrifft (Breaking Changes, neue Features, neue Konfiguration)
3. **`CLAUDE.md`** — Statistiken, Scorecard, Phase-Details und Feature-Tabelle aktuell halten

**Reihenfolge:** Zuerst Code ändern → Tests grün → Dokumentation updaten → Committen

## Git-Branch

- Entwicklung auf: `claude/analyze-repository-7ADOV`
