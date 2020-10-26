<?php
/**
 * @defgroup Confirmations
 * 
 * Offers temporary URLs to perform a given action, used for double opt-in, email-verfication, and such. 
 */

class ConfigConfirmations {
	/**
	 * Defines how confirmations are handled
	 *
	 * DIRECT: Confirmation are triggered by invoking a URL
	 *      Default for backwards compatibility
	 *      This is error prone, since poorly programmed third party tools (corporate firewall,
	 *      email preview) may invoke the URL ignoring e.g. robots.txt
	 *  SUBMIT: Confirmations are triggered by invoking URL and clicking on a submit button
	 *      Recommended
	 */
	const ACTION_INVOCATION = 'CONFIRMATIONS_ACTION_INVOCATION';


	/**
	 * Confirmation are triggered by invoking a URL
	 *
	 * Default for backwards compatibility
	 *
	 * This is error prone, since poorly programmed third party tools (corporate firewall,
	 * email preview) may invoke the URL ignoring e.g. robots.txt
	 */
	const ACTION_DIRECT = 'DIRECT';


	/**
	 * Confirmations are triggered by invoking URL and clicking on a submit button
	 *
	 * Recommended
	 */
	const ACTION_SUBMIT = 'SUBMIT';
}

Config::set_value_from_constant(
	ConfigConfirmations::ACTION_INVOCATION,
	'APP_CONFIRMATIONS_ACTION_INVOCATION',
	ConfigConfirmations::ACTION_DIRECT
);


