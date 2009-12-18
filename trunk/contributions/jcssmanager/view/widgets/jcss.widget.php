<?php
/**
 * Print CSS and JS includes
 */
class WidgetJCSS implements IWidget {
	const META = 256;
	const JS = 512;
	const CSS = 1024;
	const ALL = 1792;
		
	/**
	 * Pagedata to hold Head Information
	 *
	 * @var PageData
	 */
	public $page_data;

	/**
	 * Output Mete INformatins an JS/CSS incldues
	 *
	 * @param PageData $page_data
	 * @param int $policy 
	 * @return string
	 */
	public static function output($page_data, $policy = self::ALL) {
		$w = new WidgetJCSS($page_data);
		return $w->render($policy);
	}
	
	public function __construct($page_data) {
		$this->page_data = $page_data;
	}
	
	/**
	 * Renders
	 *
	 * @param int $policy
	 * @return string
	 */
	public function render($policy = self::ALL) {
		$this->collect($this->page_data, $policy);
		Load::models('jcsscompressedfiles');
		if (Common::flag_is_set($policy, self::CSS)) {
			$this->preprocess_css($this->page_data);
		}
		if (Common::flag_is_set($policy, self::JS)) {
			$this->preprocess_js($this->page_data);
		}
		return $this->page_data->head->render($policy);
	}

	/**
 	 * Replace CSS with compressed, if necessary
 	 * 
 	 * @param PageData $page_data  
 	 */
	protected function preprocess_css($page_data) {
		$page_data->head->css_files = $this->replace_by_compressed(JCSSManager::TYPE_CSS, $page_data->head->css_files);
		foreach($page_data->head->conditional_css_files as $browser => $files) {
			switch ($browser) {
				case HeadData::IE50:
					$type = JCSSManager::TYPE_CSS_IE50;
					break;
				case HeadData::IE55:
					$type = JCSSManager::TYPE_CSS_IE55;
					break;
				case HeadData::IE6:
					$type = JCSSManager::TYPE_CSS_IE6;
					break;
				case HeadData::IE7:
					$type = JCSSManager::TYPE_CSS_IE7;
					break;
				default:
					continue;
					break;
			}
			$page_data->head->conditional_css_files[$browser] = $this->replace_by_compressed($type, $files);
		}
	}

	/**
 	 * Replace CSS with compressed, if necessary
 	 * 
 	 * @param PageData $page_data  
 	 */
	protected function preprocess_js($page_data) {
		$page_data->head->js_files = $this->replace_by_compressed(JCSSManager::TYPE_JS, $page_data->head->js_files);
	}
	
	/**
	 * Replace a bunch of files with their compressed version 
	 */
	protected function replace_by_compressed($type, $arr_files) {
		if (count($arr_files) == 0 || !Config::get_value(ConfigJCSSManager::USE_COMPRESSED)) {
			return $arr_files;
		}
		
		$dao = JCSSCompressedFiles::get($type);
		if ($dao === false) {
			return $arr_files;
		}
		
		$ret = array();
		$ret[] = $dao->get_versioned_filename();
		
		foreach($arr_files as $file) {
			if (!in_array($file, $dao->sources)) {
				$ret[] = $file;
			}
		}
		
		return $ret;
	}
	
	/**
	 * Issue an event to collect JS and CSS files
	 * 
	 * @param int $policy Defines what to collect 
	 */
	protected function collect(PageData $page_data, $policy) {
		if (Common::flag_is_set($policy, self::JS)) {
			$this->collect_js($page_data);
		}	
		if (Common::flag_is_set($policy, self::CSS)) {
			$this->collect_css($page_data);
		}
	}
	
	/** 
	 * Collect Javascript
	 */
	protected function collect_js(PageData $page_data) {
		$js_files = $this->invoke_collect_event(JCSSManager::TYPE_JS);
		foreach(array_reverse($js_files) as $f) {
			$page_data->head->add_js_file($f, true);
		}
	}
	
	/** 
	 * Collect CSS
	 */
	protected function collect_css(PageData $page_data) {
		foreach(JCSSManager::get_css_types() as $type) {
			$css_files = $this->invoke_collect_event($type);
			$css_files = array_reverse($css_files);
			switch($type) {
				case JCSSManager::TYPE_CSS:
					foreach($css_files as $f) {
						$page_data->head->add_css_file($f, true);
					}
					break;
				default:
					foreach($css_files as $f) {
						$page_data->head->add_conditional_css_file(
							$this->translate_conditional_css_type($type), 
							$f, 
							true
						);
					}
					break;					
			}
		}
	}
	
	/**
	 * Translate JCSSManager conditional CSS types to HeadData conditional CSS types
	 * 
	 * @param $type A string type
	 * @return string
	 */
	protected function translate_conditional_css_type($type) {
		return substr($type, 4);
	}
	
	/**
	 * Run event to collect files of given type
	 * 
	 * @return array
	 */
	protected function invoke_collect_event($type) {
		$files = array();
		EventSource::Instance()->invoke_event('jcssmanager_collect', $type, $files);
		return $files;
	}
}