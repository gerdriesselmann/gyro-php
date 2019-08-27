<?php
/**
 * @defgroup Tidy
 * @ingroup Modules
 *  
 * Encoding and decoding using Html Tidy library
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

// If tidy is not installed, do not throw exception, but do nothing
//
// A hack for systems that depend on modules that depend on the tidy module, but run on systems
// that do not support it. Namely the Synology NAS....
if (!defined('GYRO_TIDY_IGNORE_NOT_INSTALLED')) define('GYRO_TIDY_IGNORE_NOT_INSTALLED', false); 