<?php
/**
 * Component to include CKEditor on your page
 * 
 * @author Gerd Riesselmann
 * @ingroup CKEditor
 */
class CKEditor {
	const CONFIG_DEFAULT = 'default';
	
	private static $configs = array();
	
	/**
	 * Enable CKEditor, and use given javascript file, to invoke it 
	 */
	public static function enable(PageData $page_data, $config = self::CONFIG_DEFAULT) {
		$config = self::get_config($config);
		
		$page_data->head->add_js_file('js/ckeditor/ckeditor.js');
		if (Load::is_module_loaded('javascript.jquery')) {
			$page_data->head->add_js_file('js/ckeditor/adapters/jquery.js');
		}
		
		$page_data->head->add_js_file($config->init_file);
	}

	/**
	 * Create a new config
	 * 
	 * @param sring $name
	 * @param string $template Name of config to use as template
	 * @return CKEditorConfig
	 */
	public static function create_config($name, $template = self::CONFIG_DEFAULT) {
		$template = self::get_config($template);
		self::$configs[$name] = $template;
		return $template; 
	}
	
	/**
	 * Returns config with given name
	 * 
	 * @param string $name
	 * @return CKEditorConfig
	 */
	private static function get_config($name) {
		$ret = Arr::get_item(self::$configs, $name, false);
		if ($ret === false) {
			$ret = new CKEditorConfig();
		}
		return $ret;
	}
}

/**
 * CKEditor config
 * 
 * @author Gerd Riesselmann
 * @ingroup CKEditor
 */
class CKEditorConfig {
	/**
	 * The javascript file that fires up the WYM editor
	 * 
	 * @var string
	 */
	public $init_file = 'js/ckeditor/default.js';
}
