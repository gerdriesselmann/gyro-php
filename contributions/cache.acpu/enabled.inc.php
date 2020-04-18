<?php
// Switch Session Handler
require_once dirname(__FILE__) . '/session.acpu.impl.php';
Config::set_value(Config::SESSION_HANDLER, 'ACPuSession');
