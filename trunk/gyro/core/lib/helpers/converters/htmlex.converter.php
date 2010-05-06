<?php
require_once dirname(__FILE__) . '/html.converter.php';

/**
 * Converts plain text to HTML (encode) or HTML to plain text (decode) 
 * 
 * Treats short paragraphs as headings
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class ConverterHtmlEx extends ConverterHtml {
	/**
	 * Process a single paragraph
	 *
	 * @param string $text
	 * @return string
	 */
	protected function process_paragraph($text, $params) {
		if (String::length($text) <= 70 && String::right($text, 1) != '.') {
			$level = intval(Arr::get_item($params, 'h', 2));
			return html::tag('h' . $level, $text);  
		}
		else {
			return parent::process_paragraph($text, $params);
		}
	}
	
	public function decode($value, $params = false) {
		$value = str_replace("\n", ' ', $value);
		$value = str_replace("\r", ' ', $value);
		$value = str_replace('</p>', "</p>\n", $value);
		$value = preg_replace('@<br.*?>@', "\n", $value);
		$value = String::unescape($value);
		$value = strip_tags($value);
		$value = String::preg_replace('| +|', ' ', $value);
		return $value;		
	} 		
} 
