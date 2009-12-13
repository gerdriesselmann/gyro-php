<?php
// You must enable status module with code like this:
// Load::enable_module('status');

if (!defined('USER_ROLE_USER')) define('USER_ROLE_USER', 'user');
if (!defined('USER_ROLE_EDITOR')) define('USER_ROLE_EDITOR', 'editor');
if (!defined('USER_ROLE_ADMIN')) define('USER_ROLE_ADMIN', 'admin');
if (!defined('USER_ROLE_SYSTEM')) define('USER_ROLE_SYSTEM', 'system');

if (!defined('USER_DEFAULT_ROLE')) define('USER_DEFAULT_ROLE', USER_ROLE_USER);

/**
 * The default URL for users logged in
 *
 * @var String
 */
if (!defined('APP_DEFAULT_PAGE_USER')) define('APP_DEFAULT_PAGE_USER', Config::get_url(Config::URL_BASEURL_SAFE) . 'user');

/**
 * The default URL for admins logged in
 *
 * @var String
 */
if (!defined('APP_DEFAULT_PAGE_ADMIN')) define('APP_DEFAULT_PAGE_ADMIN', APP_DEFAULT_PAGE_USER);


/**
 * Defines how routing should act if an anonmous user hits a page that requires login
 * 
 * Allowed are:
 * 
 * DENY - (default) Just show a 403 page and message
 * REDIRECT_LOGIN - Redirect to login page 
 */
if (!defined('APP_USER_403_BEHAVIOUR')) define('APP_USER_403_BEHAVIOUR', 'DENY');


// We add new variables to each view...
require_once (dirname(__FILE__)) . '/view/users.vieweventsink.cls.php';
EventSource::Instance()->register(new UsersViewEventSink());

Load::models('users');
Users::initialize();
