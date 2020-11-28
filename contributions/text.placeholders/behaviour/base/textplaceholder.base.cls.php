<?php
/**
 * Basic implementation of a text placeholder
 * 
 * Looks for placeholders of form {command:a[:b...]}, that is 
 * a command and a list of parameters, separated by double colon 
 * and wrapped in curly brackets. 
 * 
 * @attention There must be at least one parameter!
 * 
 * This base class must be instantiated with the command.
 */
class TextPlaceholderBase implements ITextPlaceholder {
	protected $cmd = '';
	
	public function __constructor($cmd) {
		$this->cmd = $cmd;
	}
	
	/**
	 * Apply on given text
	 * 
	 * @param string $text
	 * @return string
	 */
	public function apply($text) {
		$ret = $text;
		$matches = array();
		GyroString::preg_match_all($this->build_regex(), $ret, $matches, PREG_SET_ORDER);
		foreach($matches as $match) {
			$params = explode(':', $match[1]);
			$replace = $this->do_apply($params);
			if ($replace !== false) {
				$ret = str_replace($match[0], $replace, $ret);
			}
		}	
		return $ret;
	}	
	
	/**
	 * Build the tegex used to find placeholders
	 * 
	 * @return string
	 */
	protected function build_regex() {
		$cmd = preg_quote($this->cmd, '@');
		return "@{{$cmd}:(.*?)}@i";
	}

	/**
	 * Appyl params
	 * 
	 * @param array $params
	 * @return string Replacement or FALSE if $params are invalid
	 */
	protected function do_apply($params) {
		return false;
	}
}