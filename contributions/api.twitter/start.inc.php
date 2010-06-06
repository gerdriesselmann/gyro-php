<?php
define ('CONVERTER_TWITTER', 'twitter');

require_once dirname(__FILE__) . '/lib/helpers/converters/twitter.converter.php';
ConverterFactory::register_converter(CONVERTER_TWITTER, new ConverterTwitter());
