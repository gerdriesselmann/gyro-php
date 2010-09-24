<?php
/**
 * A converter that calls function given as parameter
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class ConverterCallback implements IConverter {
	public function encode($value, $params = false) {
		if (is_callable($params)) {
			return $params($value);
		}
		throw new Exception('Callback in ConverterCallback::encode not callable');
	}
	
	public function decode($value, $params = false) {
		if (is_callable($params)) {
			return $params($value);
		}
		throw new Exception('Callback in ConverterCallback::decode not callable');
	}
} 
