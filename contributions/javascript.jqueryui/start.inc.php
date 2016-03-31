<?php
/**
 * @defgroup JQueryUI
 * @ingroup JavaScript
 * 
 * Include JQueryUI components on all or some pages. 
 * 
 * @section Usage
 *
 * On install, the module copies JQuery UI Javascript and CSS files folders below web root.
 * 
 * To define the version of JQueryUI to use, use the constant APP_JQUERYUI_VERSION. 
 * Valid values are  "1.7" for JQueryUI 1.7.2, and "1.8" for JQueryUI 1.8.11. 
 * Default is "1.8":
 * 
 * @code
 * define('APP_JQUERYUI_VERSION', '1.8');
 * @endcode
 * 
 * @attention The default version policy changed to "newest", so if you do not define a JQuery UI version,
 *            you will automaticalyy get the latest release
 * 
 * @attention 
 *   Note that javascript and css files get prefixed by "jquery." since version 1.8. For example 
 *   "ui.accordion" became "jquery.ui.accordion". When updating from 1.7 to a higher version,
 *   make sure to delete the old files. 
 *
 * To enable components, you may call JQueryUI::enable_components() and pass either an array 
 * or a single component:
 * 
 * @code
 * JQueryUI::enable_components(JQueryUI::WIDGET_DATEPICKER);
 * JQueryUI::enable_components(array(JQueryUI::WIDGET_DATEPICKER, JQueryUI::WIDGET_PROGRESSBAR));
 * @endcode
 * 
 * This module resolves any dependencies for the components selected, so no need to worry about 
 * that.
 * 
 * To enable localization for a given language use
 * 
 * @code
 * JQueryUI::enable_locales('de');
 * @endcode
 * 
 * This module will automatically include localization files, if available. Note it's still up to you
 * to correctly initialize the accordings components, e.g. within the document.ready() event.
 * 
 * To include components on each page and to have them compressed by JCSSManager, its best to 
 * include according code in the file start.inc.php in your app directory.
 *    
 * You must use WidgetJCSS of JCSSManager module to render head data for this module to work.
 * 
 * @section Theming
 * 
 * Themes can be downloaded on http://jqueryui.com/download. Just drop the css files 
 * (or just ui.theme.css) and image folder in app/www/css/jqueryui/
 * 
 * @section Notes Additional notes
 * 
 * The 1.7 release includes the autocomplete plugin by Jörn Zaefferer, which became a 
 * part of JQueryUI in 1.8 release. For details see http://bassistance.de/jquery-plugins/jquery-plugin-autocomplete/
 * 
 * The JS and CSS file have been renamed to ui.autocomplete for consistency.
 * 
 * The autocomplete plugin for 1.7 is not themed, since it is not part of the original 
 * JQuery UI package.
 * 
 * JQueryUI and the autocomplete plugin are released under MIT license.

 * @see http://jqueryui.com/ for details about JQueryUI
 */

/**
 * JQueryUI Config options 
 * 
 * @author Gerd Riesselmann
 * @ingroup JQueryUI
 */
class ConfigJQueryUI {
	/** @deprecated Use VERSION instead */
	const JQUERYUI_VERSION = 'JQUERYUI_VERSION';
	const VERSION = 'JQUERYUI_VERSION';

	/**
	 * JQueryUI CDN URL. Either an URL with %version% as placeholder for version OR
	 *
	 * - "google" for the Google CDN
	 * - "ms" for the Microsoft CDN
	 *
	 * @attention %version% will be always 3 digits (e.g. 1.6.0). Use %version_min% to force this to 1.6
	 *
	 * Using a CDN will always load the full jQueryUI but serve local styles and localizations
	 *
	 * Files will be loaded using https with both Google and Microsoft CDN
	 */
	const CDN = 'JQUERYUI_CDN';
	
	const ON_EVERY_PAGE = 'JQUERYUI_ON_EVERY_PAGE';
}

// To be changed on new releases
define('JQUERYUI_VERSION_1_7', '1.7.3');
define('JQUERYUI_VERSION_1_8', '1.8.16');
define('JQUERYUI_VERSION_1_10', '1.10.3');

Config::set_value_from_constant(ConfigJQueryUI::CDN, 'APP_JQUERYUI_CDN', '');
Config::set_value_from_constant(ConfigJQueryUI::VERSION, 'APP_JQUERYUI_VERSION', '1.10');
Config::set_feature_from_constant(ConfigJQueryUI::ON_EVERY_PAGE, 'APP_JQUERYUI_ON_EVERY_PAGE', true);

JQueryUI::enable_locales(GyroLocale::get_language());

if (Config::has_feature(ConfigJQueryUI::ON_EVERY_PAGE)) {
	EventSource::Instance()->register(new JavascriptJQueryUIEventSink());
}
