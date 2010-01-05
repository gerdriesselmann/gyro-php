<?php
// Disable DB based sessions
Config::set_feature(Config::SESSION_USE_DB, false);

require_once dirname(__FILE__) . '/session.xcache.impl.php';
$sessionhandler = new XCacheSession();