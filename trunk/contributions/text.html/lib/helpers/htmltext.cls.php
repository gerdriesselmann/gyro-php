<?php
/**
 * This class manages conversions applied to HTML content 
 * before storing and display and also rich text editors
 * 
 * @author Gerd Riesselmann
 * @ingroup Html
 */
class HtmlText {
	/**
	 * Use this to create a HTML InputWidget. Model name can be passed using parameters.
	 * 
	 * @code
	 * print WidgetInput::output('content', 'Edit post content:', $form_data, HtmlText::WIDGET, array('model' => 'posts'));
	 * @code
	 */
	const WIDGET = 'html';
	
	/**
	 * Defines conversions to use, if a model has no special conversion defined   
	 */
	const CONVERSION_DEFAULT = 'default';
	
	/**
	 * Defines default EDITOR config
	 */
	const EDITOR_DEFAULT = 'default';
	
	/**
	 * Storage conversions
	 */
	const STORAGE = 'STORAGE';
	/**
	 * Output conversions
	 */
	const OUTPUT = 'OUTPUT';
	/**
	 * Editing conversions (e.g. when opening with Rich Text Editor)
	 */
	const EDIT = 'EDIT';
	
	private static $conversions = array();
	private static $richtexteditors = array();
	private static $richtexteditors_enabled = array();
	
	
	/**
	 * Set a conversion
	 * 
	 * @param string $type Type, mostly one of STORAGE, OUTPUT or BASIC_SAFETY. You may add new ones, though
	 * @param array Array of IConverter instances or converter names that can be resolved using ConverterFactory::create_chain()
	 * @param array|string $model Model name
	 */
	public static function set_conversion($type, $conversions, $model = self::CONVERSION_DEFAULT) {
		foreach(Arr::force($model, false) as $m) {
			self::$conversions[$type][$m] = Arr::force($conversions, false);
		}			
	}
	
	/**
	 * Get a conversion
	 * 
	 * @param string $type Type, mostly one of STORAGE, OUTPUT or BASIC_SAFETY. You may add new ones, though
	 * @param string|IDataObject $model Model name or CONVERSION_DEFAULT
	 * @param bool $use_fallback Use fallback rules, if model is not set
	 * @return array Array of IConverter instances or converter names
	 */
	public static function get_conversion($type, $model = self::CONVERSION_DEFAULT, $use_fallback = true) {
		if ($model instanceof IDataObject) {
			$model = $model->get_table_name();
		}
		$ret = array();
		if (isset(self::$conversions[$type][$model])) {
			$ret = self::$conversions[$type][$model];
		}
		else if ($use_fallback) {
			$ret = Arr::get_item_recursive(self::$conversions, array($type, self::CONVERSION_DEFAULT), array());
		}
		return $ret;			
	}
	
	/**
	 * Add a conversion
	 * 
	 * @param string $type Type, mostly one of STORAGE, OUTPUT or BASIC_SAFETY. You may add new ones, though
	 * @param IConverter|array ICOnverter or array of IConverter instances or converter names that can be resolved using ConverterFactory::create_chain()
	 * @param array|string $model Model name
	 * @params bool $to_front If true, placehodler is prepended to list, else it is appended
	 */
	public static function add_conversion($type, $conversions, $model = self::CONVERSION_DEFAULT, $to_front = false) {
		$conversions = Arr::force($conversions, false);
		foreach(Arr::force($model, false) as $m) {
			$recent = self::get_conversion($type, $m, true);
			if ($to_front) {
				$recent = array_merge($conversions, $recent);
			}
			else {
				$recent = array_merge($recent, $conversions);
			}
			self::set_conversion($type, $recent, $m);
		}
	} 
	
	/**
	 * Apply a conversion
	 * 
	 * @param string $type Type, mostly one of STORAGE, OUTPUT or EDIT. You may add new ones, though
	 * @param string $text To to convert
	 * @param string|IDataObject $model Model name
	 * @return string Converted text
	 */
	public static function apply_conversion($type, $text, $model = self::CONVERSION_DEFAULT) {
		$key = array($model, sha1($text));
		$ret = RuntimeCache::get($key, null);
		if (is_null($ret)) {
			$conversions = self::get_conversion($type, $model, true);
			$chain = ConverterFactory::create_chain($conversions);
			$ret = $chain->encode($text);
			RuntimeCache::set($key, $ret);
		}
		return $ret;
	}
	
	/**
	 * Register a Rich Text Editor
	 */
	public static function register_editor($key, IRichtTextEditor $editor) {
		self::$richtexteditors[$key] = $editor;
	}
	
	/**
	 * Enable given Editor
	 */
	public static function enable_editor($key) {
		self::$richtexteditors_enabled[$key] = $key;
	}
	
	/**
	 * Apply all enabled editors
	 * 
	 * @attention 
	 *   If an enabled editor is not found, it is silently ignored. This can cause
	 *   hard to find bugs due to typos. However, since at least the default editor
	 *   is always enabled, if a HTML input is used, this is necessary.  
	 */
	public static function apply_enabled_editors(PageData $page_data) {
		foreach(self::$richtexteditors_enabled as $name) {
			$editor = Arr::get_item(self::$richtexteditors, $name, false);
			if ($editor instanceof IRichtTextEditor) {
				$editor->apply($page_data, $name);
			}
		}		
	}
}