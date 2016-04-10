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
// This is experiemental HTML5 support. Note it does not really support HTML5, but loosens validatation here and there
define('CONVERTER_TIDY_HTML5', 'tidy5');
// Register Converter
require_once dirname(__FILE__) . '/lib/helpers/converters/htmltidy.converter.php';
ConverterFactory::register_converter(CONVERTER_TIDY, new ConverterHtmlTidy());
ConverterFactory::register_converter(CONVERTER_TIDY_HTML5, new ConverterHtmlTidy(array(
	'drop-proprietary-attributes' => false,
	'output-xhtml' => false,
	'doctype' => '<!DOCTYPE HTML>'
)));
