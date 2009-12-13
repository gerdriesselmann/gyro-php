= JQueryUI Module =

Author: Gerd Riesselmann
Depends: javascript.jquery, jcssmanager
Requires: Write access to web root

== Purpose ==

Include JQueryUI components on all or some pages. See http://jqueryui.com/ for details.

== Usage ==

On install, the module copies JQuery UI Javascript and CSS files folders below web root.

To define the version of JQueryUI to use, you must specify a version to use by defining
the constant APP_JQUERYUI_VERSION. By now, since only version 1.7 is supported, this value 
has to be 1.7:

define('APP_JQUERYUI_VERSION', '1.7');

To enable components, you may call JQueryUI::enable_components() and pass either an array 
or a single component:

JQueryUI::enable_components(JQueryUI::WIDGET_DATEPICKER);
JQueryUI::enable_components(array(JQueryUI::WIDGET_DATEPICKER, WIDGET_PROGRESSBAR));

To include components on each page and to have them compressed by JCSSManager, its best to 
include according code in the file start.inc.php in your ap directory.
   
You must use the JCSSManager Widget instead of rendering head data directly for this
module to work.

== Theming ==

Themes can be downloaded on http://jqueryui.com/download. Just drop the css files 
(or just ui.theme.css) in app/www/css/jqueryui

== Additional notes ==

The 1.7 release includes the autocomplete plugin by Jörn Zaefferer. For details see
http://bassistance.de/jquery-plugins/jquery-plugin-autocomplete/

The JS and CSS file has been renamed to ui.autocomplete for consistency.

The autocomplete plugin for 1.7 is not themed, since it is not part of the original 
JQuery UI package. 

JQueryUI uses the MIT license. The JQueryUI licence is included as license.jqueryui.txt.

The autocomplete plugin uses the MIT license. See the source file for license details.