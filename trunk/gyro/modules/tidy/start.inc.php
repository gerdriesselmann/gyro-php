<?php
/**
 * @defgroup Tidy
 * @ingroup Modules
 *  
 * Encoding and decoding using Html Tidy library
 * 
 * \attention Requires the Tidy PECL extension: http://pecl.php.net/package/tidy
 */

define('CONVERTER_TIDY', 'tidy');
// Register Converter
require_once dirname(__FILE__) . '/lib/helpers/converters/htmltidy.converter.php';
ConverterFactory::register_converter(CONVERTER_TIDY, new ConverterHtmlTidy());
