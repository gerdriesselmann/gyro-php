<?php
/**
 * Component to include CLEditor on your page
 * 
 * @author Gerd Riesselmann
 * @ingroup CLEditor
 */
class CLEditor {
	const CONFIG_DEFAULT = 'default';
	
	private static $configs = array();
	
	/**
	 * Enable CLEditor, and use given javascript file, to invoke it 
	 */
	public static function enable(PageData $page_data, $config = self::CONFIG_DEFAULT) {
		if (!$config instanceof CLEditorConfig) {
			$config = self::get_config($config);
		}

		if ($config->lang) {
			$page_data->head->add_js_file('js/cleditor/lang/jquery.cleditor.' . strtolower($config->lang) . '.js');
		}
		$page_data->head->add_js_file('js/cleditor/jquery.cleditor.js');
		$page_data->head->add_css_file('js/cleditor/jquery.cleditor.css');
		foreach($config->plugins as $p) {
			$page_data->head->add_js_file($p);
		}
		$page_data->head->add_js_file($config->init_file);
	}

	/**
	 * Create a new config
	 * 
	 * @param string $name
	 * @param string $template Name of config to use as template
	 * @return CLEditorConfig
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
	 * @return CLEditorConfig
	 */
	private static function get_config($name) {
		$ret = Arr::get_item(self::$configs, $name, false);
		if ($ret === false) {
			$ret = new CLEditorConfig();
		}
		return $ret;
	}
	
	/**
	 * Returns all configs
	 * 
	 * @return array Array with config names as keys and CLEditorConfig instances as values
	 */
	public static function get_all_configs() {
		return self::$configs;
	}
}

/**
 * CLEditor config
 * 
 * @author Gerd Riesselmann
 * @ingroup CLEditor
 */
class CLEditorConfig implements IRichtTextEditor {
	/**
	 * The javascript file that fires up the editor
	 * 
	 * @var string
	 */
	public $init_file = 'js/cleditor/default.js';

	public $plugins = array();
	public $lang = '';
	
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
		CLEditor::enable($page_data, $this);
	}
}
