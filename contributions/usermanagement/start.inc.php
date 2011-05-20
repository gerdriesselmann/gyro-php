<?php
/**
 * @defgroup Usermanagement
 *
 * A complete role based user management implementation
 * 
 * @section Usage Usage
 * 
 * After enabling user management, check if the UserController is correctly installed at
 * /app/controllers/user.controller.php. If not, create your own, and derive it from 
 * UserBaseController. You can disable or enable features like user registration, login, 
 * mail on password loss etc. by overloading the function get_features_policy().
 * 
 * The user management is roled based, whereas roles can be defined in table userroles. By default
 * three roles are set: "Admin", "Editor", and "User". There also is a System user role, which however 
 * should not be assignable to users, so it is not stored in userroles table.
 * 
 * Users can have more than one role. They will always have at least one, though. This role is by default
 * "User", you may change it by redefining APP_USER_DEFAULT_ROLE.
 * 
 * @section Routing Routing and Caching
 * 
 * Use AccessRenderDecorator to allow access to a route only for logged in users or users with given roles.
 * See UserBaseController for some examples.
 * 
 * The AnonymousCacheManager should be used as default cache. It will disable cache for logged in users.
 * Change /app/ww/index.php and replace creating the PageData instance with this code:
 * 
 * @code
 * $cache_manager = new AnonymousCacheManager();
 * $page_data = new PageData($cache_manager, $_GET, $_POST);
 * @endcode 
 * 
 * @attention 
 *   It is a common pitfall to forget this! Your users' data may become public if you do!  
 * 
 * @section Views Views
 * 
 * If user management is enabled, all views are extended by two variables:
 * 
 * - $current_user: The current user (DAOUsers) or false, if no user is logged in
 * - $is_logged_in: True if user is logged in, false otherwise
 * 
 * @section Hashing Pasword Hashing
 * 
 * The usermanagement offers several different password hashing methods. See ConfigUsermanagement
 * for details. Default is "md5". This choice has been made for compatability reasons. Most likely,
 * though, salted md5 (portable phpass) will become the default in the near future, since this is
 * usually regarded more safe.
 * 
 * You may however want to enable salted md5 right away. Do so by defining APP_USER_HASH_TYPE:
 * 
 * @code
 * define('APP_USER_HASH_TYPE', 'pas2p');
 * @endcode
 * 
 * The system will automatically update the password hash on login, if a user's hash type differs from 
 * the default one. This makes it safe to change the hash type at any time.
 * 
 * @section Update Update from 0.5 to 0.5.1 or later
 * 
 * With 0.5.1 release a "hash_type" field has been added to users table, along with some changes
 * regarding DB consistency. If you do not use Systemupdate module, please run the SQL in 
 * [module]/install/updates/0001_hash_type.sql manually.
 * 
 * Additionally, some changes have been made to the config options. Namely 
 * 
 *  - Usermanagement config moved to Config class
 *  - Names of configuration defines have been unified:
 *       - USER_DEFAULT_ROLE => APP_USER_DEFAULT_ROLE
 *       - APP_DEFAULT_PAGE_USER => APP_USER_APP_DEFAULT_PAGE
 *       - APP_DEFAULT_PAGE_ADMIN => has been removed
 *  
 * If you are not sure, if this has consequences for your code, set APP_VERSION_MAX to 0.5:
 * 
 * @code
 * define('APP_VERSION_MAX', 0.5);
 * @endcode
 * 
 * This will enable a compatibility layer. In most cases, however, transition should be smooth, since 
 * default values usually don't get modified.
 */

/**
 * Usermanagement config options
 * 
 * @since 0.5.1
 * 
 * Every option can be set through the according APP_ constant, e.g. 
 * to define default role, use constant APP_USER_DEFAULT_ROLE.
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class ConfigUsermanagement {
	/**
	 * Default role of newly registerd user. Default is "user"
	 */
	const DEFAULT_ROLE = 'USER_DEFAULT_ROLE';
	const USER_DEFAULT_ROLE = 'USER_DEFAULT_ROLE';
	
	/**
	 * The default URL for users logged in
	 */
	const DEFAULT_PAGE = 'USER_DEFAULT_PAGE';
	const USER_DEFAULT_PAGE = 'USER_DEFAULT_PAGE';
	
	/**
	 * Defines how routing should act if an anonymous user hits a page that requires login
	 * 
	 * Allowed are:
	 * 
	 * - DENY: (default) Just show a 403 page and message
	 * - REDIRECT_LOGIN: Redirect to login page 
	 * 
	 * @deprecated Use AccessDeniedRedirectRenderDecorator instead
	 */
	const BEHAVIOUR_403 = 'USER_403_BEHAVIOUR';
	const USER_403_BEHAVIOUR = 'USER_403_BEHAVIOUR';
	
	/**
	 * Defines the hash algorithm to encyrpt the user's password. 
	 * 
	 * @since 0.5.1
	 *
	 * Possible values are:
	 * 
	 * - md5: (default) The MD5 hash
	 * - sha1: The SHA1 hash
	 * - pas2f: phpass 0.2 in full mode 
	 * - pas2p: phpass 0.2 in portable mode. This is a kind of salted md5.
	 * 
	 * @see http://www.openwall.com/phpass/
	 * 
	 * Modules or applications may add more algorithms
	 * 
	 * Regarding the two phppass modes, the full mode may lead to different results based on your system's 
	 * configuration. It should only be used if either PHP 5.3 or the Suhosin Patch is installed. Moving from 
	 * a PHP 5.2/Non Suhosin to a PHP 5.3 or PHP 5.2/Suhosin system (or vice versa) may turn your user's 
	 * passwords unverifyable.
	 * 
	 * Full mode is generally safer, though.
	 */
	const HASH_TYPE = 'USER_HASH_TYPE';
	const USER_HASH_TYPE = 'USER_HASH_TYPE';	
	
	/**
	 * Time in days a permantent login is valid. Default is 14.
	 * 
	 * @since 0.5.1
	 */
	const PERMANENT_LOGIN_DURATION = 'USER_PERMANENT_LOGIN_DURATION';
	
	/**
	 * CacheHeaderManager policy for logged in users. 
	 * 
	 * Class name without CacheHeaderManager, e.g. NoCache for NoCacheCacheHeaderManager
	 */
	const CACHEHEADER_CLASS_LOGGEDIN = 'USER_CACHEHEADER_CLASS_LOGGEDIN';
	
	
	/**
	 * Current version of TOS. Only integer values allowed.
	 * 
	 * 0 means there are no TOS, and this is the default 
	 */
	const TOS_VERSION = 'USER_TOS_VERSION';
	
	/**
	 * User receives Mail when user status changes
	 * Standard is true 
	 */
	const MAIL_STATUSCHANGE = 'USER_MAIL_STATUSCHANGE';
	
	/**
	 * Enable Passwordcheck when changing e-mail. Defaults to true
	 */
	const ENABLE_PWD_ON_EMAILCHANGE = 'USER_ENABLE_PWD_ON_EMAILCHANGE';
}


if (!defined('USER_ROLE_USER')) define('USER_ROLE_USER', 'user');
if (!defined('USER_ROLE_EDITOR')) define('USER_ROLE_EDITOR', 'editor');
if (!defined('USER_ROLE_ADMIN')) define('USER_ROLE_ADMIN', 'admin');
if (!defined('USER_ROLE_SYSTEM')) define('USER_ROLE_SYSTEM', 'system');


if (Config::get_value(Config::VERSION_MAX) < 0.6) {
	// Allow old constants.
	if (!defined('APP_USER_DEFAULT_ROLE')) {		
		define('APP_USER_DEFAULT_ROLE', Common::constant('USER_DEFAULT_ROLE', USER_ROLE_USER));
	}
	if (!defined('APP_USER_DEFAULT_PAGE')) {		
		define('APP_USER_DEFAULT_PAGE', Common::constant('APP_DEFAULT_PAGE_USER', Config::get_url(Config::URL_BASEURL_SAFE) . 'user'));
	}
	if (!defined('APP_USER_403_BEHAVIOUR')) define('APP_USER_403_BEHAVIOUR', 'DENY');
	if (!defined('USER_DEFAULT_ROLE')) define('USER_DEFAULT_ROLE', APP_USER_DEFAULT_ROLE);
	if (!defined('APP_DEFAULT_PAGE_USER')) define('APP_DEFAULT_PAGE_USER', APP_USER_DEFAULT_PAGE);
	if (!defined('APP_DEFAULT_PAGE_ADMIN')) define('APP_DEFAULT_PAGE_ADMIN', APP_USER_DEFAULT_PAGE);	
}

Config::set_value_from_constant(ConfigUsermanagement::DEFAULT_PAGE, 'APP_USER_DEFAULT_PAGE', Config::get_url(Config::URL_BASEURL_SAFE) . 'user');
Config::set_value_from_constant(ConfigUsermanagement::DEFAULT_ROLE, 'APP_USER_DEFAULT_ROLE', USER_ROLE_USER);
Config::set_value_from_constant(ConfigUsermanagement::BEHAVIOUR_403, 'APP_USER_403_BEHAVIOUR', 'DENY');
Config::set_value_from_constant(ConfigUsermanagement::HASH_TYPE, 'APP_USER_HASH_TYPE', 'md5');
Config::set_value_from_constant(ConfigUsermanagement::PERMANENT_LOGIN_DURATION, 'APP_USER_PERMANENT_LOGIN_DURATION', 14);
Config::set_value_from_constant(ConfigUsermanagement::TOS_VERSION, 'APP_USER_TOS_VERSION', 0);
Config::set_value_from_constant(ConfigUsermanagement::CACHEHEADER_CLASS_LOGGEDIN, 'APP_USER_CACHEHEADER_CLASS_LOGGEDIN', 'PrivateRigidEtagOnly');
Config::set_feature_from_constant(ConfigUsermanagement::MAIL_STATUSCHANGE, 'APP_USER_MAIL_STATUSCHANGE', true);
Config::set_feature_from_constant(ConfigUsermanagement::ENABLE_PWD_ON_EMAILCHANGE, 'APP_USER_ENABLE_PWD_ON_EMAILCHANGE', true);

// We add new variables to each view...
require_once (dirname(__FILE__)) . '/view/users.vieweventsink.cls.php';
EventSource::Instance()->register(new UsersViewEventSink());

Load::models('users');
Users::initialize();
