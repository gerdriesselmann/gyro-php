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
	 * 
	 * The following parameters are supported:
	 * 
	 * @param array $params 
	 *   Associative array supporting the following featurs:
	 *   @li p: Text after paragraph. Default is "\n"
	 *   @li br: Text after a <br> tag. Default is "\n"
	 *   @li a: Format to decode <a> tags. Supports $title$ and $url$ placeholders. Default is "$title$: $url$"
	 */
	public function decode($value, $params = false) {
		// If there is no HTML, decoding would do more harm than good
		if (!preg_match('@<\w+.*?>@', $value)) {
			return $value;
		}
		
		// Turn <a href="xxx">Text</a> into e.g. Text: xxx
		$value = $this->decode_anchors($value, Arr::get_item($params, 'a', '$title$: $url$'));
		
		// Remove paragraphs
		$value = str_replace("\r", "\n", $value);
		$value = String::preg_replace('|\n+|', " ", $value);
		
		// Replace <p> and <br>
		$value = preg_replace('@\s*<p>\s*@', "<p>", $value);
		$after_p = Arr::get_item($params, 'p', "\n");
		$value = preg_replace('@\s*</p>\s*@', "</p>$after_p", $value);
		$after_br = Arr::get_item($params, 'br', "\n");
		$value = preg_replace('@<br.*?>\s*@', $after_br, $value);
		
		// Strip HTML
		$value = strip_tags($value);
		$value = String::unescape($value);
		$value = String::preg_replace('| +|', ' ', $value);
		
		return $value;		
	}
	
	/**
	 * Decode a HTML <a> tag into plain text
	 * 
	 * @param string $value
	 * @param string $link_format Supports placeholders $title$ and $url$
	 */
	protected function decode_anchors($value, $link_format) {
		$value = $this->relative_to_absolute($value, Config::get_url(Config::URL_BASEURL));
		$link_format = str_replace('$title$', '$2', $link_format);
		$link_format = str_replace('$url$', '$1', $link_format);
		return String::preg_replace('|<a.*?href="(.*?)".*?>(.*?)</a>|', $link_format, $value);
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
