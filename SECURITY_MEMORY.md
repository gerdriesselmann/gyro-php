# Security Analysis Memory - Gyro PHP

## Summary: 30 files modified across 3 commits

## Commit 1: Core Security Fixes

### 1. CRITICAL: Insecure Token Generation (common.cls.php)
- `create_token()` used `sha1(uniqid(mt_rand(), true))` - NOT cryptographically secure
- **Fix**: Replaced with `bin2hex(random_bytes(20))` and `bin2hex(random_bytes(32))`

### 2. CRITICAL: Insecure Deserialization (6 files)
- `unserialize()` without `allowed_classes` restriction in:
  - dbfield.serialized.cls.php, cache.acpu.impl.php, cache.file.impl.php
  - cache.xcache.impl.php, dbdriver.sphinx.php
- **Fix**: Added `['allowed_classes' => false]` to all calls

### 3. CRITICAL: Password Hashing with MD5/SHA1 (md5.hash.php, sha1.hash.php)
- Timing attack via loose `==` comparison
- **Fix**: Replaced with `hash_equals()`, added bcrypt.hash.php

### 4. HIGH: SQL Injection in escape_database_entity (dbdriver.mysql.php)
- Backticks in entity names not escaped
- **Fix**: Added `str_replace('`', '``', $obj)`

### 5. HIGH: Host Header Injection (requestinfo.cls.php)
- `HTTP_X_FORWARDED_HOST` used directly without validation
- **Fix**: Validate host against configured domain

### 6. HIGH: phpinfo() without access control (phpinfo.controller.php)
- **Fix**: Added Config::TESTMODE check

### 7. MEDIUM: Session Security (session.cls.php)
- Missing SameSite, httponly, strict mode
- Deprecated session.bug_compat_42
- **Fix**: Added SameSite=Lax, strict mode, httponly defaults

### 8. MEDIUM: XSS in ConverterHtmlEx (htmlex.converter.php)
- Missing HTML escaping in heading output
- **Fix**: Added GyroString::escape()

### 9. MEDIUM: Missing Security Headers (pageviewbase.cls.php)
- **Fix**: Added X-Content-Type-Options, X-Frame-Options, Referrer-Policy

## Commit 2: Command Injection & Path Traversal Fixes

### 10. CRITICAL: Command Injection in jcssmanager (5 files)
- webpack, uglifyjs, postcss, csso, yui compressors all used exec() without escapeshellarg()
- **Fix**: Added escapeshellarg() to all file path and option arguments

### 11. HIGH: Path Traversal in deletedialog (3 template files)
- `get_table_name()` used directly in include paths
- **Fix**: Added basename() + path traversal character stripping

### 12. HIGH: eval() in punycode uctc.php
- **Fix**: Replaced with call_user_func()

### 13. MEDIUM: shell_exec('mkdir') in install (check_preconditions.php)
- **Fix**: Replaced with PHP native mkdir()

## Commit 3: XSS, Weak Randomness & Permissions

### 14. HIGH: XSS in punycode example.php
- $_SERVER['PHP_SELF'] and $_REQUEST['lang'] output without escaping
- **Fix**: Added htmlspecialchars()

### 15. MEDIUM: XSS in wymeditor tidy plugin
- $_REQUEST['html'] processed without Content-Type header
- **Fix**: Added Content-Type header, fixed deprecated magic_quotes check

### 16. MEDIUM: Weak rand() for feed tokens (notificationssettings.model.php)
- **Fix**: Replaced rand() with random_int()

### 17. LOW: Insecure chmod 0777 (check_preconditions.php)
- **Fix**: Changed to 0755

## All Modified Files
1. gyro/core/lib/helpers/common.cls.php
2. gyro/core/model/base/fields/dbfield.serialized.cls.php
3. gyro/core/model/drivers/mysql/dbdriver.mysql.php
4. gyro/core/lib/helpers/requestinfo.cls.php
5. gyro/core/lib/helpers/session.cls.php
6. gyro/core/lib/helpers/converters/htmlex.converter.php
7. gyro/core/view/base/pageviewbase.cls.php
8. gyro/modules/phpinfo/controller/phpinfo.controller.php
9. gyro/install/check_preconditions.php
10. contributions/cache.acpu/cache.acpu.impl.php
11. contributions/cache.file/cache.file.impl.php
12. contributions/cache.xcache/cache.xcache.impl.php
13. contributions/sphinx/model/drivers/sphinx/dbdriver.sphinx.php
14. contributions/usermanagement/behaviour/commands/users/hashes/md5.hash.php
15. contributions/usermanagement/behaviour/commands/users/hashes/sha1.hash.php
16. contributions/usermanagement/behaviour/commands/users/hashes/bcrypt.hash.php (NEW)
17. contributions/jcssmanager/behaviour/commands/jcssmanager/webpack/compress.base.cmd.php
18. contributions/jcssmanager/behaviour/commands/jcssmanager/uglifyjs/compress.js.cmd.php
19. contributions/jcssmanager/behaviour/commands/jcssmanager/postcss/compress.css.cmd.php
20. contributions/jcssmanager/behaviour/commands/jcssmanager/csso/compress.css.cmd.php
21. contributions/jcssmanager/behaviour/commands/jcssmanager/yui/compress.base.cmd.php
22. contributions/deletedialog/view/templates/default/deletedialog/approve_status.tpl.php
23. contributions/deletedialog/view/templates/default/deletedialog/inc/message.tpl.php
24. contributions/deletedialog/view/templates/default/deletedialog/inc/status/message.tpl.php
25. contributions/punycode/3rdparty/idna_convert/uctc.php
26. contributions/punycode/3rdparty/idna_convert/example.php
27. contributions/javascript.wymeditor/data/js/wymeditor/plugins/tidy/tidy.php
28. contributions/usermanagement.notifications/model/classes/notificationssettings.model.php

## Scanner Results Summary
- SQL Injection: No critical issues in core framework (well-protected ORM layer)
- XSS: 3 issues found (all in 3rd party/contributions), all fixed
- Command Injection: 5 critical issues in jcssmanager, all fixed
- Path Traversal: 3 issues in deletedialog templates, all fixed
- Crypto/Session: Weak hashing and token generation, all fixed
- CSRF: Properly implemented with database-backed tokens (no issues)

## Status: COMPLETE - All 3 commits pushed
