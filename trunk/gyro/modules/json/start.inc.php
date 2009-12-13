<?php
/**
 * @defgroup JSON
 * @ingroup Modules
 *  
 * JSON encoding and decoding 
 */

define('CONVERTER_JSON', 'json');
// Register Converter
require_once dirname(__FILE__) . '/lib/helpers/converters/json.cls.php';
ConverterFactory::register_converter(CONVERTER_JSON, new GyroJSON());
