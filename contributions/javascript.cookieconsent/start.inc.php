<?php
/**
 * @defgroup CookieConsent
 * @ingroup JavaScript
 * 
 * Show Cookie Conset screen for anonymous users
 *
 * Important: This module must be loaded after Usermanagement module, if user management is used
 */
if (!Load::is_module_loaded('usermanagement') || !Users::is_logged_in()) {
	// Read the cookie, and if the user has not yet accepted cookies, add JS
	$dismissed = Cookie::get_cookie_value('cookieconsent_dismissed');
	if ($dismissed !== 'yes') {
		EventSource::Instance()->register(new JavascriptCookieConsentEventSink());
	}
}

/**
 * Add the Javascript
 *
 * @param PageData $page_data
 * @throws Exception
 */
function gyro_javascript_cookie_consent_activate(PageData $page_data) {
	$msg = tr(Config::get_value(ConfigCookieConsent::MESSAGE), 'cookieconsent');
	$theme = Config::get_value(ConfigCookieConsent::THEME);
	$link = Config::get_value(ConfigCookieConsent::LINK);
	$dismiss = tr(Config::get_value(ConfigCookieConsent::DISMISS), 'cookieconsent');
	$learn_more = tr(Config::get_value(ConfigCookieConsent::LEARN_MORE), 'cookieconsent');
	/* @var PageData $page_data */
	$page_data->head->add_js_snippet(
		"window.cookieconsent_options = {'message':'$msg','dismiss':'$dismiss','learnMore':'$learn_more','link':'$link','theme':'$theme'};", true
	);
	$page_data->head->add_js_file('js/cookie-consent/cookieconsent.min.js', false, true);
}

/**
 * Cookie Consent Config options
 *
 * @author Gerd Riesselmann
 * @ingroup CookieConsent
 */
class ConfigCookieConsent {
	/* Allow disabling automatic cookie consent in favor of adding the JS manually */
	const ENABLED = 'COOKIE_CONSENT_ENABLED';

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

Config::set_feature_from_constant(ConfigCookieConsent::ENABLED, 'APP_COOKIE_CONSENT_ENABLED', true);

define('COOKIE_CONSENT_THEME_LIGHT_FLOATING', 'light-floating');
define('COOKIE_CONSENT_THEME_DARK_FLOATING', 'dark-floating');
define('COOKIE_CONSENT_THEME_LIGHT_BOTTOM', 'light-bottom');
define('COOKIE_CONSENT_THEME_DARK_BOTTOM', 'dark-bottom');
define('COOKIE_CONSENT_THEME_LIGHT_TOP', 'light-top');
define('COOKIE_CONSENT_THEME_DARK_TOP', 'dark-top');
