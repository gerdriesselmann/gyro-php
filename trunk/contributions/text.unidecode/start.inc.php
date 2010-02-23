<?php
/**
 * @defgroup Unidecode
 * @ingroup Text
 * 
 * It often happens that you have non-Roman text data in Unicode, but
 * you can't display it -- usually because you're trying to show it
 * to a user via an application that doesn't support Unicode, or
 * because the fonts you need aren't accessible. You could represent
 * the Unicode characters as "???????" or "\15BA\15A0\1610...", but
 * that's nearly useless to the user who actually wants to read what
 * the text says.

 * What Unidecode provides is a function, that
 * takes Unicode data and tries to represent it in ASCII characters 
 * (i.e., the universally displayable characters between 0x00 and 0x7F). 
 * The representation is almost always an attempt at *transliteration* 
 * -- i.e., conveying, in Roman letters, the pronunciation expressed by 
 * the text in some other writing system. (See the example above)
 * 
 * @section Usage Usage
 * 
 * @code
 * print ConverterFactory::encode("\x53\17\x4E\B0", CONVERTER_UNIDECODE, 'UTF-16');
 * // prints: Bei Jing
 * @endcode
 * 
 * Ommit encoding when dealing with strings in default encoding
 * 
 * @code
 * print ConverterFactory::encode('Jürgen\'s Café offers à la carte', CONVERTER_UNIDECODE);
 * // prints: Jurgen's Cafe offers a la carte
 * @endcode
 * 
 * @section Notes Additional notes
 * 
 * This is a port of the Tomaz Solc's <tomaz@zemanta.com> Python port of the 
 * Text::Unidecode Perl module by Sean M. Burke <sburke@cpan.org>.
 * 
 * 
 * Character transliteration tables copyright 2001, Sean M. Burke <sburke@cpan.org>, 
 * all rights reserved. http://search.cpan.org/~sburke/Text-Unidecode-0.04/
 *
 * Python code copyright 2009, Tomaz Solc <tomaz@zemanta.com>, 
 * http://pypi.python.org/pypi/Unidecode
 */
define ('CONVERTER_UNIDECODE', 'unidecode');

require_once dirname(__FILE__) . '/lib/helpers/converters/unidecode.converter.php';
ConverterFactory::register_converter(CONVERTER_UNIDECODE, new ConverterUnidecode());
