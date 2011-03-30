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
 * There is already a preconfigured converter solving the common problem to convert HTML without paragraphs
 * like created by most CMS into valid HTML. This not only uses the AutoFormat.AutoParagraph configuration
 * directive but tries to normalize line breaks before. This converter ias available as 
 * CONVERTER_HTMLPURIFIER_AUTOPARAGRAPH.
 * 
 * @code
 * $clean = ConverterFactory::encode($dirty, CONVERTER_HTMLPURIFIER_AUTOPARAGRAPH);
 * @endcode
 * 
 * Of course you may also pass additional parameters.
 * 
 * The module comes with a DBField that purifies its content before storing it in the database. This is deprecated
 * in favour of the more flexible DBFieldTextHtml that comes with the text.html package.
 * 
 * The module sets the edit fallback conversion of HtmlRules to purifing without tidying, and 
 * storage and output conversion to default Purifier.
 *
 * @section Notes Additional notes
 * 
 * HTML Purifier is released under GNU Lesser General Public License.
 * 
 * The version contained within this module is 4.2
 * 
 * @see http://htmlpurifier.org/
 *   
 */
define ('CONVERTER_HTMLPURIFIER', 'htmlpurifier');
define ('CONVERTER_HTMLPURIFIER_AUTOPARAGRAPH', 'htmlpurifier_autoparagraph');

require_once dirname(__FILE__) . '/lib/helpers/converters/htmlpurifier.converter.php';
require_once dirname(__FILE__) . '/lib/helpers/converters/htmlpurifier.autoparagraph.converter.php';
ConverterFactory::register_converter(CONVERTER_HTMLPURIFIER, new ConverterHtmlPurifier());
ConverterFactory::register_converter(CONVERTER_HTMLPURIFIER_AUTOPARAGRAPH, new ConverterHtmlPurifierAutoParagraph());

HtmlText::set_conversion(HtmlText::EDIT, array(CONVERTER_HTMLPURIFIER => array('HTML.TidyLevel' => 'none')));
HtmlText::set_conversion(HtmlText::OUTPUT, CONVERTER_HTMLPURIFIER);
HtmlText::set_conversion(HtmlText::STORAGE, CONVERTER_HTMLPURIFIER);
