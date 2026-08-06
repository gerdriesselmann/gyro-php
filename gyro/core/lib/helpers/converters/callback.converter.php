<?php
/**
 * A converter that calls function given as parameter
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class ConverterCallback implements IConverter {
	public function encode(mixed $value, mixed $params = false): mixed {
		if (is_callable($params)) {
			return $params($value);
		}
		throw new Exception('Callback in ConverterCallback::encode not callable');
	}
	
	public function decode(mixed $value, mixed $params = false): mixed {
		if (is_callable($params)) {
			return $params($value);
		}
		throw new Exception('Callback in ConverterCallback::decode not callable');
	}
} 
