<?php
/**
 * @defgroup HtmlPurifier
 * @ingroup Text
 *
 * Cleans HTML and removes malicious code.
 * 
 * @section Installation Installation
 * 
 * On install, this module created directories 
 * 
 * - htmlpurifier/CSS
 * - htmlpurifier/HTML
 * - htmlpurifier/URI
 * 
 * in the application's temp directory with permissions set to 777. These directories must
 * be writable by the web server.
 * 
 * @section Usage Usage
 * 
 * @code
 * $clean = ConverterFactory::encode($dirty, CONVERTER_HTMLPURIFIER);
 * @endcode
 * 
 * You may pass HtmlPurifier specific parameters like this:
 * 
 * @code
 * $clean = ConverterFactory::encode($dirty, CONVERTER_HTMLPURIFIER, array('HTML.TidyLevel' => 'heavy'));
 * @endcode
 * 
 * For a list of possible values see http://htmlpurifier.org/live/configdoc/plain.html
 * 
 * The module comes with a DBField that purifies its content before storing it in the database. Most 
 * likely, you will overload it to fit your needs, though. 
 *
 * @section Notes Additional notes
 * 
 * HTML Purifier is released under GNU Lesser General Public License.
 * 
 * The version contained within this module is 4.1.1.
 * 
 * @see http://htmlpurifier.org/
 *   
 */
define ('CONVERTER_HTMLPURIFIER', 'htmlpurifier');

require_once dirname(__FILE__) . '/lib/helpers/converters/htmlpurifier.converter.php';
ConverterFactory::register_converter(CONVERTER_HTMLPURIFIER, new ConverterHtmlPurifier());
