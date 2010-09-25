<?php
/**
 * @defgroup Palceholders
 * @ingroup Text
 *
 * Allows use of placehodlers in user entered text. Placeholders get replaced by arbitrary complex dynamic content. 
 * 
 * @section Usage Usage
 * 
 * A placeholder can look like this:
 * 
 * @code
 * <a href="posts:5:view">my recent musings on this topic</a>
 * @endcode
 * 
 * The placeholder "posts:5:view" would get expanded to the path of action "view" of post with id 5. 
 */
define ('CONVERTER_TEXTPLACEHOLDERS', 'textplaceholders');

require_once dirname(__FILE__) . '/lib/helpers/converters/textplaceholders.converter.php';
ConverterFactory::register_converter(CONVERTER_TEXTPLACEHOLDERS, new ConverterTextPlaceholders());

if (Load::is_module_loaded('text.html')) {
	HtmlText::add_conversion(HtmlText::OUTPUT, CONVERTER_TEXTPLACEHOLDERS);
}

Load::directories('behaviour/textplaceholders');


