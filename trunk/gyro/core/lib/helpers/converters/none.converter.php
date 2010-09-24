<?php
/**
 * A converter that does nothing
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class ConverterNone implements IConverter {
	public function encode($value, $params = false) {
		return $value;
	}
	
	public function decode($value, $params = false) {
		return $value;		
	}
} 
