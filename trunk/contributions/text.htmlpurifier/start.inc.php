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
 * @section Notes Additional notes
 * 
 * HTML Purifier is released under GNU Lesser General Public License
 * 
 * @see http://htmlpurifier.org/
 *   
 */
define ('CONVERTER_HTMLPURIFIER', 'htmlpurifier');

require_once dirname(__FILE__) . '/lib/helpers/converters/htmlpurifier.converter.php';
ConverterFactory::register_converter(CONVERTER_HTMLPURIFIER, new ConverterHtmlPurifier());
