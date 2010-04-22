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
 * Valid values are  "1.7" for JQueryUI 1.7.2, and "1.8" for JQueryUI 1.8. 
 * Default is "1.7":
 * 
 * @code
 * define('APP_JQUERYUI_VERSION', '1.8');
 * @endcode
 * 
 * @attention 
 *   Note that javascript and css files get prefixed by "jquery." since version 1.8. For example 
 *   "ui.accordion" became "jquery.ui.accordion". When updating from 1.7 to a higher version,
 *   make sure to delete the old files. 
 *
 * If version is set to "1.8", this modules will try to set the JQuery version to "1.4", if
 * not yet defined by the application.
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

EventSource::Instance()->register(new JavascriptJQueryUIEventSink());

/**
 * JQueryUI Config options 
 * 
 * @author Gerd Riesselmann
 * @ingroup JQueryUI
 */
class ConfigJQueryUI {
	const JQUERYUI_VERSION = 'JQUERYUI_VERSION';
}

Config::set_value_from_constant(ConfigJQueryUI::JQUERYUI_VERSION, 'APP_JQUERYUI_VERSION', '1.7');

// Force JQuery 1.4 if JQueryUI is 1.8
if (Config::get_value(ConfigJQueryUI::JQUERYUI_VERSION) == '1.8' && !defined('APP_JQUERY_VERSION')) {
	define('APP_JQUERY_VERSION', 1.4);
}
