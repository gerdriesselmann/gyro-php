<?php
/**
 * ASCII transliterations of unicode text
 * 
 * @author Gerd Riesselmann
 * @ingroup Unidecode
 */
class ConverterUnidecode implements IConverter {
	private static $groups = array();
	
	/**
	 * Convert Unicode chars to ASCII transliterals
	 * 
	 * @param string $value
	 * @param string Encoding of $value, if different from current GyroLocale
	 */
	public function encode($value, $params = false) {
		// We need 
		if (empty($params)) {
			$params = GyroLocale::get_charset();
		}
		$value = String::convert($value, $params, 'UTF-16');
		$value = $this->unidecode($value);
			
		return $value;
	}
	
	/**
	 * Using "ConverterUnidecode::encode() may be confusing, so let decode8) just do the same
	 */
	public function decode($value, $params = false) {
		return $this->encode($value, $params);		
	} 	
	
	/**
	 * Unidecode given string (already Unicode)
	 */
	protected function unidecode($value) {
		$ret = '';
		foreach(unpack('n*', $value) as $uchar) {
			$ret .= $this->unidecode_uchar($uchar);
		}
		return $ret;	
	}
	
	/**
	 * Decode a given unicde chat (two byte long)
	 */
	protected function unidecode_uchar($uchar) {
		if ($uchar <= 0x007f) {
			return chr($uchar);
		}
		
		$high = $uchar >> 8;
		$low = $uchar & 0x00ff;
		
		// Did we resolve the group already?
		$group = Arr::get_item(self::$groups, $high, false);
		if ($group === false) {
			// No, try to load it...
			$hex = substr('00' . dechex($high), -2); 
			$file = dirname(__FILE__) . '/data/x' . $hex . '.php';
			if (file_exists($file)) {
				include($file);
				$group = $data;
			} 
			else {
				// No such group, fill with empty
				$group = array_fill(0, 0x100, '');
			}
			self::$groups[$high] = $group;
		}
		
		return Arr::get_item($group, $low, '');		
	}
	
} 

