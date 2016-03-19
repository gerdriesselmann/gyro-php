<?php
/**
 * Converts plain texyt to HTML (encode) or HTML to plain text (decode) 
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class ConverterHtml implements IConverter {
	public function encode($value, $params = false) {
		$value = $this->decode($value);
		$value = str_replace("\r", "\n", $value);
		//var_dump(str_replace("\n", '\n', $value));
		$arr_paragraphs = explode("\n", $value);
		$c = count($arr_paragraphs);
		$arr_result = array();
		for($i = 0; $i < $c; $i++) {
			$tmp = trim($arr_paragraphs[$i]);
		 	if (empty($tmp)) {
		 		continue;
		 	}
		 	
			$arr_result[] = $this->process_paragraph($tmp, $params);
		}
		$ret = implode("\n", $arr_result);
		$ret = GyroString::preg_replace('| +|', ' ', $ret);
		return $ret;
	}
	
	/**
	 * Process a single paragraph
	 *
	 * @param string $text
	 * @return string
	 */
	protected function process_paragraph($text, $params) {
		return html::tag('p', GyroString::escape($text));
	}
	
	public function decode($value, $params = false) {
		//$value = str_replace("\n", ' ', $value);
		//$value = str_replace("\r", ' ', $value);
		$value = str_replace('</p>', "</p>\n", $value);
		$value = preg_replace('@<br.*?>@', "\n", $value);
		$value = GyroString::unescape($value);
		return strip_tags($value);		
	} 	
} 
