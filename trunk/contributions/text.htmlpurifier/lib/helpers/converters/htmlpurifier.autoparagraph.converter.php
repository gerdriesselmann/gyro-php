<?php
/**
 * Convert HTML to purified HTML, automatically inserting paragraphps
 * 
 * @author Gerd Riesselmann
 * @ingroup HtmlPurifier
 */
class ConverterHtmlPurifierAutoParagraph extends ConverterHtmlPurifier {
	/**
	 * Purify HTML
	 * 
	 * @param string $value
	 * @param array See http://htmlpurifier.org/live/configdoc/plain.html for all possible values
	 */
	public function encode($value, $params = false) {
		if (is_null($value) || $value instanceof DBNull) {
			return $value;
		}
		
		$input = trim($value);
		$input = str_replace("\r", "\n", $input);
		$input = preg_replace('|<br.*?>|', "\n", $input);
		$input = String::preg_replace('|\n\n+|m', "\n", $input);
		$input = str_replace("\n", "\n\n", $input);
		$params = array_merge(array(
 			'AutoFormat.RemoveEmpty' => true,
 			'AutoFormat.RemoveEmpty.RemoveNbsp' => true,
 			'AutoFormat.AutoParagraph' => true
 			), Arr::force($params, false)
 		);
 		$input = parent::encode($input, $params);
		$input = String::preg_replace('|\n\n+|m', "\n", $input); 	
 		return $input;	
	}
} 
