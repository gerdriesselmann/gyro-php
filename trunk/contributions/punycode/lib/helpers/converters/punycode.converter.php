<?php
require_once  dirname(__FILE__) . '/../../../3rdparty/idna_convert/idna_convert.class.php';

/**
 * Converter from and to punycode
 *
 * @author Gerd Riesselmann
 * @ingroup Punycode
 */ 
class ConverterPunycode implements IConverter {
	public function encode($value, $params = false) {
		$inst = $this->create_converter();
		$ret = false;
		if ($inst) {
			$ret = $inst->encode(trim($value));
		}
		return $ret;
	}
	
	public function decode($value, $params = false) {
		$inst = $this->create_converter();
		$ret = false;
		if ($inst) {
			$ret = $inst->decode(trim($value));
		}
		return $ret;		
	} 	
	
	private function create_converter() {
		return new idna_convert();
	}
} 
