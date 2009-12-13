<?php
/**
 * Created on 06.08.2007
 *
 * @author Gerd Riesselmann
 */
 
require_once  dirname(__FILE__) . '/../../../3rdparty/idna_convert/idna_convert.class.php';

class ConverterPunycode implements IConverter {
	public function encode($value, $params = false) {
		$inst = $this->create_converter();
		$ret = false;
		if ($inst) {
			$ret = $inst->encode($value);
		}
		return $ret;
	}
	
	public function decode($value, $params = false) {
		$inst = $this->create_converter();
		$ret = false;
		if ($inst) {
			$ret = $inst->decode($value);
		}
		return $ret;		
	} 	
	
	private function create_converter() {
		return new idna_convert();
	}
} 
?>