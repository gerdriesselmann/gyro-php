<?php
/**
 * Component to include WYMEditor on your page
 * 
 * @author Gerd Riesselmann
 * @ingroup WYMEditor
 */
class WYMEditor {
	const CONFIG_DEFAULT = 'default';
	
	private static $configs = array();
	
	/**
	 * Enable WYMEditor, and use given javascript file, to invoke it 
	 */
	public static function enable(PageData $page_data, $config = self::CONFIG_DEFAULT) {
		$config = self::get_config($config);
		
		$page_data->head->add_js_file('js/wymeditor/jquery.wymeditor.js');
		$page_data->head->add_css_file('js/wymeditor/jquery.wymeditor.css');
		
		$init_plugins = array();
		foreach($config->plugins as $js => $init) {
			$page_data->head->add_js_file($js);
			$init_plugins[] = $init;
		}
		
		$page_data->head->add_js_file($config->init_file);
		
		$init_plugins = implode(";\n", $init_plugins);
		$init_plugin_func = "function _wym_init_plugins(wym) {\n$init_plugins\n}";
		$page_data->head->add_js_snippet($init_plugin_func, true);
	}

	/**
	 * Create a new config
	 * 
	 * @param sring $name
	 * @param string $template Name of config to use as template
	 * @return WYMEditorConfig
	 */
	public static function create_config($name, $template = self::CONFIG_DEFAULT) {
		$template = self::get_config($template);
		self::$configs[$name] = $template;
		HtmlText::register_editor($name, $template);		
		return $template; 
	}
	
	/**
	 * Returns config with given name
	 * 
	 * @param string $name
	 * @return WYMEditorConfig
	 */
	private static function get_config($name) {
		$ret = Arr::get_item(self::$configs, $name, false);
		if ($ret === false) {
			$ret = new WYMEditorConfig();
		}
		return $ret;
	}
}

/**
 * WYMEditor config
 * 
 * @author Gerd Riesselmann
 * @ingroup WYMEditor
 */
class WYMEditorConfig implements IRichtTextEditor {
	/**
	 * The javascript file that fires up the WYM editor
	 * 
	 * @var string
	 */
	public $init_file = 'js/wymeditor/default.js';
	
	/**
	 * Assoziative array of enabled plugins, with plugin file as key and
	 * plugin js initialization code as value
	 * 
	 * @var array
	 */
	public $plugins = array(
		'js/wymeditor/plugins/fullscreen/jquery.wymeditor.fullscreen.js' => 
			'wym.fullscreen()'
	);
	
	
	// -----------------------
	// IRichTextEditor
	// -----------------------
	
	/**
	 * Apply it
	 * 
	 * @param PageData $page_data
	 * @param string $name Name of editor, can be found as class "rte_$name" on HTML textareas  
	 */
	public function apply(PageData $page_data, $name) {
		WYMEditor::enable($page_data, $this);
	}		
}
