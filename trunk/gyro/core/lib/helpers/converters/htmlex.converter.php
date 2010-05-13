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
	
	/**
	 * Smarter decoding og html into plain text than done by HTML Converter
	 * 
	 * Keeps Links
	 */
	public function decode($value, $params = false) {
		// Process Links
		$value = $this->relative_to_absolute($value, Config::get_url(Config::URL_BASEURL));
		// Turn <a href="xxx">Text</a> into Text: xxx
		$value = String::preg_replace('|<a.*?href="(.*?)".*?>(.*?)</a>|', '$2: $1', $value);
		
		$value = str_replace('</p>', "</p>\n\n", $value);
		$value = preg_replace('@<br.*?>@', "\n", $value);
		$value = strip_tags($value);
		$value = String::unescape($value);
		$value = String::preg_replace('| +|', ' ', $value);
		
		$value = str_replace("\r", "\n", $value);
		$value = String::preg_replace('|\n+|', "\n", $value);
		return $value;		
	}

	/**
	 * Helper. Turn relative URLs in text to an absolute one
	 * 
	 * @param string $text Text containing URLs
	 * @param string $base baseurl
	 */
	protected function relative_to_absolute($text, $base) {
		if (empty($base)) {
			return $text;
		}
			
		if (substr($base, -1, 1) != "/") { $base .= "/"; }
		$domain = Url::create($base)->clear_query()->set_path('')->build(Url::ABSOLUTE);
		
		// Replace href="/abc" with domain
		$pattern = 	'|<a(.*?) href="/(.*?)"|';
		$replace = '<a$1 href="' . $domain . '$2"';
		$text = preg_replace($pattern, $replace, $text);
		
		// Replace href="abc" with base
		$pattern = 	'|<a(.*?) href="(?!\w+://)(.*?)"|';
		$replace = '<a$1 href="' . $base . '$2"';
		$text = preg_replace($pattern, $replace, $text);
		
		return $text;
	}	
} 
