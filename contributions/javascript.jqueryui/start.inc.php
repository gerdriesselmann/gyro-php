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
 * To define the version of JQueryUI to use, you must specify a version to use by defining
 * the constant APP_JQUERYUI_VERSION. By now, since only version 1.7 is supported, this value 
 * has to be 1.7:
 * 
 * @code
 * define('APP_JQUERYUI_VERSION', '1.7');
 * @endcode
 * 
 * To enable components, you may call JQueryUI::enable_components() and pass either an array 
 * or a single component:
 * 
 * @code
 * JQueryUI::enable_components(JQueryUI::WIDGET_DATEPICKER);
 * JQueryUI::enable_components(array(JQueryUI::WIDGET_DATEPICKER, JQueryUI::WIDGET_PROGRESSBAR));
 * @endcode
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
 * The 1.7 release includes the autocomplete plugin by Jörn Zaefferer. For details see
 * http://bassistance.de/jquery-plugins/jquery-plugin-autocomplete/
 * 
 * The JS and CSS file has been renamed to ui.autocomplete for consistency.
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


