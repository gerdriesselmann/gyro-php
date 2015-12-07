<?php
/**
 * A class that introduces string functions to html string (that are strings containing tags)
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class HtmlString {
	const USE_STRING_FUNCTIONS = 0;
	const USE_PHP_FUNCTIONS = 1;
	
	private $html_texts = array();
	private $html_tags = array();
	private $policy = 0; 
	
	public function __construct($text = '', $policy = self::USE_STRING_FUNCTIONS) {
		$this->set_text($text);
		$this->policy = $policy;
	}
	
	public function set_text($text) {
		$tag = '#<[^>]*>#';
		$text = ' ' . $text; // We need a leading non-tag, see below 

		// Split on tags
		$this->html_texts = GyroString::preg_split($tag, $text,  -1);

		// Find all tags
		$html_tags = '';
		GyroString::preg_match_all($tag, $text, $html_tags);
		$this->html_tags = $html_tags[0];

		// we now have two arrays, one for text between tags ($html_texts),
		// and one for the tags itself ($html_tags). When rebuilding the text, 
		// the first item always is a html text, because we prependend ' '.
	}
	
	/**
	 * Create the string
	 */	
	public function build() {
		$count_texts = count($this->html_texts);
		$count_tags = count($this->html_tags);

		$ret = '';		
		// We want to replace text within tags, but not within <a> anchors!
		for($index = 0; $index < $count_texts; $index ++) {
			$ret .= $this->html_texts[$index];
			// Now add tag, if any
			if ($index < $count_tags) {
				$ret .= $this->html_tags[$index];
			}
		}
		
		return trim($ret);				
	}
	
	/**
	 * Rebuld the text and tags arrays  
	 */
	protected function rebuild() {
		$this->set_text($this->build());
	}
	
	/**
	 * Do a preg_replace on text
	 * 
	 * @return int Number of replacements done 
	 */
	public function preg_replace($regex, $replace, $max = -1, $tags_to_skip = '') {
		if ($max == 0) {
			return 0;
		}
		
		$count_texts = count($this->html_texts);
		if (!is_array($tags_to_skip)) {
			$tags_to_skip = empty($tags_to_skip) ? array() : explode(' ', $tags_to_skip);
		}
		
		$num_matches = 0;
		// SAtep though all text blocks...
		for($index = 0; $index < $count_texts; $index ++) {
			if (!$this->is_within_tags($tags_to_skip, $index)) {
				$count_matches = 0;
				if ($this->policy == self::USE_STRING_FUNCTIONS) {
					$this->html_texts[$index] = GyroString::preg_replace($regex, $replace, $this->html_texts[$index], $max, $count_matches);
				}
				else {
					$this->html_texts[$index] = preg_replace($regex, $replace, $this->html_texts[$index], $max, $count_matches);
				}
				$num_matches += $count_matches;				
			}
			
			if ($max > 0 && $num_matches >= $max ) {
				break; // We did all replacements
			}
		}
		
		if ($num_matches > 0) {
			// We changed the text, so text elements now may contain html. Joind ad resplit text
			$this->rebuild();
		}
		return $num_matches;
	}
	
	/**
	 * Check if a given text block is within given tags
	 */
	protected function is_within_tags($arr_tags, $text_index) {
		if (count($arr_tags) == 0) {
			return false;
		}
		$ret = false;
		$stack = array();
		// Loop through all tags before this text block
		for($i = $text_index - 1; $i >= 0; $i--) {
			$tag = $this->html_tags[$i];
			// Validate tags
			if (substr($tag, -2, 2) == '/>') {
				// Self closing tag: skip
				continue;
			}
			else if (substr($tag, 0, 2) == '</') {
				// Closing tag, push on stack
				$tag = $this->get_plain_tagname($tag);
				array_unshift($stack, $tag);
			}
			else {
				// Opening tag
				$tag = $this->get_plain_tagname($tag);
				if (count($stack) > 0 && $stack[0] == $tag) {
					// We found opening tag for prior closing tag
					array_shift($stack);
					continue;
				} 
				else {
					// We have an opening tag, without closing tag...
					if (in_array($tag, $arr_tags)) {
						// Found!
						$ret = true;
						break;
					}
				}
			}
		}
		return $ret;
	}
	
	/**
	 * Insert some html or text, but take care of tags
	 */
	public function insert($text, $pos, $tags_to_skip = '') {
		$count_texts = count($this->html_texts);
		if (!is_array($tags_to_skip)) {
			$tags_to_skip = empty($tags_to_skip) ? array() : explode(' ', $tags_to_skip);
		}
		
		$arr_startpos = array();
		$arr_endpos = array();
		$pos_total = 0;
		// Step though all text blocks...
		for($index = 0; $index < $count_texts; $index ++) {
			$arr_startpos[] = $pos_total;
			$pos_total += GyroString::length($this->html_texts[$index]);
			$arr_endpos[] = $pos_total;

			if ($pos_total >= $pos) {
				$index++; // increment so no match and match can be treated the same
				break;
			}
		}
		// We now have index of block where replacement may happen
		$index--;
		// Step back to see if within forbidden tags 
		$matching_index = 0;
		for (; $index >= 0; $index--) {			
			if (!$this->is_within_tags($tags_to_skip, $index)) {
				$matching_index = $index;
				break;
			}
		}
		// We now have index of block where replacement should happen
		$block_text = $this->html_texts[$matching_index];
		if ($arr_endpos[$matching_index] < $pos) {
			// We have block before wanted pos. Put at end;
			$block_text  .= $text;
		}
		else {
			// Need to insert it in between.
			$tmp = GyroString::substr_word($block_text, 0, $pos - $arr_startpos[$matching_index]);
			$block_text = $tmp . $text . GyroString::substr($block_text, GyroString::length($tmp));
		}
		$this->html_texts[$matching_index] = $block_text;
		$this->rebuild();
	}
	
	/**
	 * Returns the plain text name, e.g. a for <a href="dfdfsdfsf">
	 */
	protected function get_plain_tagname($tag) {
		$ret = str_replace(array('<', '>', '/'), '', $tag);
		$ret = trim($ret);
		$ret = GyroString::extract_before($ret, ' ');
		return $ret;
	}
}
