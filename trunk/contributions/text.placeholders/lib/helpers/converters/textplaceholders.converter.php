<?php
/**
 * Replace place holders 
 * 
 * @author Gerd Riesselmann
 * @ingroup Placeholders
 */
class ConverterTextPlaceholders implements IConverter {
	/**
	 * Purify HTML
	 * 
	 * @param string $value
	 * @param array See http://htmlpurifier.org/live/configdoc/plain.html for all possible values
	 */
	public function encode($value, $params = false) {
		return TextPlaceholders::apply($value);
	}
	
	/**
	 * This function does nothing! Especially it does NOT purify HTML! 
	 */
	public function decode($value, $params = false) {
		return $value;		
	} 	
} 
