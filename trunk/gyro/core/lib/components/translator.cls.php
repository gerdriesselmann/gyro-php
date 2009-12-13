<?php
/**
 * Translates strings
 * 
 * Singleton
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class Translator {
	/**
	 * Singleton Instance
	 */
	private static $inst = null;
	
	/**
	 * Language to translate form
	 */
	private $lang;
	
	/**
	 * Array of translation groups
	 */
	private $groups = array();
		
	/**
	 * Translations as array 
	 * 
	 * Array is of form 
	 */
	
	/**
	 * Returns singleton instance
	 */
	public static function Instance() {
		if (empty(self::$inst)) {
			self::$inst = new Translator();
		}
		return self::$inst;
	}
	
	/**
	 * Constructor
	 */
	public function __construct($lang = false) {
		$this->lang = ($lang) ? $lang : GyroLocale::get_language();
	}
	
	/**
	 * Translate string
	 */
	public function translate($text, $groupname = 'default', $params = NULL) {
		$groupname = Arr::force($groupname, false);
		$arr_groupnames = array();
		foreach($groupname as $g) {
			if ($g !== 'app' && $g !== 'default') {
				$arr_groupnames[] = $g. '.overloads';
			}
			$arr_groupnames[] = $g;
		}
		
		
		if (count($arr_groupnames) == 0) {
			$arr_groupnames[] = 'default';
		}
	
		$ret = false;
		$arr_groupnames[] = 'global';
		foreach($arr_groupnames as $current_groupname) {
			$group = $this->get_group($current_groupname);	
			$ret = $this->get_translation($group, $text, $this->lang);

			if ($ret !== false) {
				break;
			}
		}
		
		if ($ret === false) {
			if (Config::has_feature(Config::TESTMODE)) {
				$data = array(
					'groupname' => implode('|', $groupname),
					'groups' => implode('|', $arr_groupnames),
					'lang' => $this->lang,
					'translation' => $text
				);
				Load::components('logger');
				Logger::log('translations', $data);
			}
			$ret = $text;
		}
		
		$ret = $this->apply_params($ret, $params);
		return $ret; 
	}
	
	/**
	 * Apply params
	 */
	private function apply_params($text, $params) {
		if (is_array($params)) {
			$text = str_replace(array_keys($params), array_values($params), $text);
		}
		return $text;
	}
	
	/**
	 * Get translation group, loads if not recently loaded 
	 */	
	private function get_group($group) {
		if (!isset($this->groups[$group])) {
			$ret = $this->load_group($group);
			$this->groups[$group] = $ret;
		}
		else {
			$ret = $this->groups[$group];
		}
		return $ret;	
	} 	
	
	/**
	 * Load translation group
	 * 
	 * @return Array Returns array even if group not found
	 */
	protected function load_group($group) {
		$ret = array();
		$file = 'view/translations/' . $group . '.translations.php';
		if (Load::first_file($file)) {
			$func = String::plain_ascii($group, '_') . '_load_translations';
			if (function_exists($func)) {
				$params = array($this->lang);
				$ret = $func($params);
			}
		}
		
		return $ret;
	} 
	
	/**
	 * Return translation from group for given text and language
	 */
	protected function get_translation($group, $key, $lang) {
		if (isset($group[$key])) {
			return Arr::get_item($group[$key], $lang, false);
		}
		return false;
	}
}

/**
 * Wrapper to Translation instance
 */
function tr($text, $group = '', $params = NULL) {
	return Translator::Instance()->translate($text, $group, $params);
}
?>