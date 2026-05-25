<?php
/**
 * A converter that does nothing
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class ConverterNone implements IConverter {
	public function encode(mixed $value, mixed $params = false): mixed {
		return $value;
	}
	
	public function decode(mixed $value, mixed $params = false): mixed {
		return $value;		
	}
} 
