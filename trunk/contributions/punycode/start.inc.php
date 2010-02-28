<?php
define ('CONVERTER_PUNYCODE', 'punycode');

require_once dirname(__FILE__) . '/lib/helpers/converters/punycode.converter.php';
ConverterFactory::register_converter(CONVERTER_PUNYCODE, new ConverterPunycode());

/**
 * @defgroup Punycode
 * @ingroup Text
 * 
 * A converter to and from punycode
 * 
 * @section Usage Usage
 * 
 * Use converter factory to convert to and from punycode:
 * 
 * @code
 * $domain = 'ümlaut-domain.de';
 * $puny = ConverterFactory::encode($domain, CONVERTER_PUNYCODE);
 * $non_puny = ConverterFactory::decode($puny, CONVERTER_PUNYCODE);
 * @endcode
 */
