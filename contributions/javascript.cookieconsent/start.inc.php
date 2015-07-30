<?php
/**
 * @defgroup CookieConsent
 * @ingroup JavaScript
 * 
 * Show Cookie Conset screen for anonymous users
 */
EventSource::Instance()->register(new JavascriptCookieConsentEventSink());


/**
 * Cookie Consent Config options
 *
 * @author Gerd Riesselmann
 * @ingroup CookieConsent
 */
class ConfigCookieConsent {
	const MESSAGE = 'COOKIE_CONSENT_MESSAGE';
	const DISMISS = 'COOKIE_CONSENT_DISMISS';
	const LEARN_MORE = 'COOKIE_CONSENT_LEARN_MORE';
	const LINK = 'COOKIE_CONSENT_LINK';
	const THEME = 'COOKIE_CONSENT_THEME';
}

Config::set_value_from_constant(ConfigCookieConsent::MESSAGE, 'APP_COOKIE_CONSENT_MESSAGE' ,'Cookies help us deliver our services. By using our services, you agree to our use of cookies.');
Config::set_value_from_constant(ConfigCookieConsent::DISMISS, 'APP_COOKIE_CONSENT_DISMISS', 'Got it!');
Config::set_value_from_constant(ConfigCookieConsent::LEARN_MORE, 'APP_COOKIE_CONSENT_LEARN_MORE', 'More info');
Config::set_value_from_constant(ConfigCookieConsent::LINK, 'APP_COOKIE_CONSENT_LINK', '/privacy');
Config::set_value_from_constant(ConfigCookieConsent::THEME, 'APP_COOKIE_CONSENT_THEME', 'dark-bottom');

define('COOKIE_CONSENT_THEME_LIGHT_FLOATING', 'light-floating');
define('COOKIE_CONSENT_THEME_DARK_FLOATING', 'dark-floating');
define('COOKIE_CONSENT_THEME_LIGHT_BOTTOM', 'light-bottom');
define('COOKIE_CONSENT_THEME_DARK_BOTTOM', 'dark-bottom');
define('COOKIE_CONSENT_THEME_LIGHT_TOP', 'light-top');
define('COOKIE_CONSENT_THEME_DARK_TOP', 'dark-top');
