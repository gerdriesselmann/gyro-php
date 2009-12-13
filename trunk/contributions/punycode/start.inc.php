<?php
/**
 * This module requires a set of constant to be set
 * 
 * OPENX_SERVER: URL of ad server
 * OPENX_AFFILIATE_ID: The id for this site 
 */
define ('CONVERTER_PUNYCODE', 'punycode');

require_once dirname(__FILE__) . '/lib/helpers/converters/punycode.converter.php';
ConverterFactory::register_converter(CONVERTER_PUNYCODE, new ConverterPunycode());
