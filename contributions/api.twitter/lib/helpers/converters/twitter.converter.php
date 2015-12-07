<?php
require_once GYRO_CORE_DIR . 'lib/helpers/converters/html.converter.php';

/**
 * Converte a twitter message into HTML
 * 
 * Replaces hashes with <b>..</b>, user (@..) by <i>..</i>
 * and links by an anchor tag
 * 
 * Optionally, if ConverterTwitter::EXPAND_LINKS is passed as parameter,
 * The component will try to resolve redirect services like bit.ly et al,
 * replacing the url in the message with the resolving one.
 * 
 * @ingroup Twitter
 * @author Gerd Riesselmann
 */
class ConverterTwitter implements IConverter {
	const EXPAND_LINKS = 1024;
	
	public function encode($value, $params = false) {
		$value = GyroString::escape($this->decode($value));
		// Try to find hash tags and make them bold
		$search = '@(\s#[\S]*)@';
		$replace = ' <b>$1</b>';
		$value = GyroString::preg_replace($search, $replace, $value);

		// Try to find users and make them italic
		$search = '|(@[\S]*)|';
		$replace = '<i>$1</i>';
		$value = GyroString::preg_replace($search, $replace, $value);
			
		// Replace URLS
		$search = "@(http[s]?://[\S]*)@";
		
		if (Common::flag_is_set($params, self::EXPAND_LINKS)) {
			// FInd them to check for bit.ly et al
			$matches = array();
			GyroString::preg_match_all($search, $value, $matches);
			
			Load::components('httprequest');
			$err = new Status();
			foreach (Arr::get_item($matches, 0, array()) as $url) {
				$head = HttpRequest::get_head($url, $err);
				// Look for a location header element
				$search_loc = '@location: ([\S]*)@i';
				$matches_loc = array();
				if (GyroString::preg_match_all($search_loc, $head, $matches_loc)) {
					// Get last redirect
					$new_url = GyroString::escape(array_pop($matches_loc[1]));
					$value = str_replace($url, $new_url, $value); 	
				}			
			}
		}
		
		$replace =  '<a href="$1">$1</a>';
		$value = GyroString::preg_replace($search, $replace, $value);
		
		return $value;
	}


	public function decode($value, $params = false) {
		$value = str_replace('&gt;', ">", $value);
		$value = str_replace('&lt;', "<", $value);
		return trim($value);		
	} 		
}