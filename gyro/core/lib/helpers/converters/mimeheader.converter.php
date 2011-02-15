<?php
/**
 * Converts text into mime encoded text
 * 
 * @attention supports encoding only!
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class ConverterMimeHeader implements IConverter {
	/**
	 * ENcode. Takes optional charset as parameter 
	 */
	public function encode($value, $params = false) {
		if (!$params) {
			$params = GyroLocale::get_charset();
		}
		
		$ret = '';
		$requires_escaping = false;
		$l = strlen($value);
		// First check if there are characters that must be converted
		for ($i = 0; $i < $l; $i++) {
			$c = ord(substr($value, $i, 1));
			if ($c < 0x20 || $c > 0x7E) {
				$requires_escaping = true;
				break;
			}
		}
		if ($requires_escaping) {
			// Convert everything other then 0-9 and A-Z and a-z
			for ($i = 0; $i < $l; $i++) {
				$c = ord(substr($value, $i, 1));
				if ($c == 0x20) {
					$c = '_'; // Space to _
				}
				elseif ( 
					($c >= 0x30 && $c <= 0x39) || 
					($c >= 0x41 && $c <= 0x5A) ||
					($c >= 0x61 && $c <= 0x7A)
				) {
					$c = chr($c); // 0-9, a-z and A-Z stay the same
				}
				else {
					$c = '=' . strtoupper(dechex($c)); // Encode the rest
				}
				$ret .= $c;
			}
			$ret = '=?' . $params . '?Q?' . $ret . '?=';
		}
		else {
			$ret = $value;
		}
		return $ret;			
	}
	
	public function decode($value, $params = false) {
		return $value;		
	} 	
} 
