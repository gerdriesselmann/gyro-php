<?php
/**
 * Helper for using JQueryUI, defines widgets and dependencies 
 * 
 * @author Gerd Riesselmann
 * @ingroup JQueryUI
 */
class JQueryUI {
	/* Effect constants */
	const EFFECTS_CORE = 'effects.core';
	const EFFECTS_BLIND = 'effects.blind';
	const EFFECTS_BOUNCE = 'effects.bounce';
	const EFFECTS_CLIP = 'effects.clip';
	const EFFECTS_DROP = 'effects.drop';
	const EFFECTS_EXPLODE = 'effects.explode';
	const EFFECTS_FADE = 'effects.fade';
	const EFFECTS_FOLD = 'effects.fold';
	const EFFECTS_HIGHLIGHT = 'effects.highlight';
	const EFFECTS_PULSATE = 'effects.pulsate';
	const EFFECTS_SCALE = 'effects.scale';
	const EFFECTS_SHAKE = 'effects.shake';
	const EFFECTS_SLIDE = 'effects.slide';
	const EFFECTS_TRANSFER = 'effects.transfer';
	
	/* Widgets constants */
	const WIDGET_ACCORDION = 'ui.accordion';
	const WIDGET_BUTTON = 'ui.button';
	const WIDGET_DATEPICKER = 'ui.datepicker';
	const WIDGET_DIALOG = 'ui.dialog';
	const WIDGET_PROGRESSBAR = 'ui.progressbar';
	const WIDGET_SLIDER = 'ui.slider';
	const WIDGET_TABS = 'ui.tabs';
	const WIDGET_AUTOCOMPLETE = 'ui.autocomplete';
	
	/* Feature constants */
	const FEATURE_DRAGGABLE = 'ui.draggable';
	const FEATURE_DROPPABLE = 'ui.droppable';
	const FEATURE_RESIZABLE = 'ui.resizable';
	const FEATURE_SELECTABLE = 'ui.selectable';
	const FEATURE_SORTABLE = 'ui.sortable';
	// 1.8 stuff
	const FEATURE_MOUSE = 'ui.mouse';
	const FEATURE_POSITION = 'ui.position';
	
	const CORE = 'ui.core';
	const CORE_WIDGET = 'ui.widget';
	
	/**
	 * Componenents selected for current page
	 * 
	 * @var array
	 */
	private static $enabled_components = array();

	/**
	 * Enable some components
	 * 
	 * @param array|string $components
	 */
	public static function enable_components($components) {
		self::$enabled_components = array_merge(self::$enabled_components, Arr::force($components, false));
		self::$enabled_components = array_unique(self::$enabled_components);
	}
	
	/**
	 * Returns array of enabled components
	 * 
	 * @return array
	 */
	public static function get_enabled_components() {
		return self::$enabled_components;
	}
	
	/**
	 * Returns paths for selected components
	 * 
	 * @param array $component
	 * @return array
	 */
	public static function get_js_paths($components) {
		$resolved = array();
		$deps = self::get_dependencies();
		foreach($components as $c) {
			self::collect_dependencies($c, $resolved, $deps);
		}
		
		$ret = array();
		$prefix = self::is_version_1_8() ? 'jquery.' : ''; 
		foreach($resolved as $c) {
			$path = 'js/jqueryui/' . $prefix . $c . '.js';
			if (!in_array($path, $ret)) {
				$ret[] = $path;
			}	
		}
		return $ret;
	}
	
	/**
	 * Collect dependencies for given component
	 */
	private static function collect_dependencies($component, &$resolved, &$dependencies) {
		$deps = Arr::get_item($dependencies, $component, array());
		foreach($deps as $d) {
			self::collect_dependencies($d, $resolved, $dependencies);
		}
		$resolved[$component] = $component;
	}

	/**
	 * Returns css paths for selected components
	 * 
	 * @param array $component
	 * @return array
	 */
	public static function get_css_paths($components) {
		$css_components = array(
			self::CORE
		);
		$css_having = self::get_components_having_css();
		
		foreach($components as $c) {
			if (in_array($c, $css_having)) {
				$css_components[] = $c;
			}
		}
		$css_components[] = 'ui.theme';
		
		$ret = array();
		foreach($css_components as $c) {
			$path = 'css/jqueryui/' . $c . '.css';
			if (!in_array($path, $ret)) {
				$ret[] = $path;
			}	
		}
		return $ret;
	} 	
	
	/**
	 * Returns an array with all widgets
	 * 
	 * @return array
	 */
	public static function get_all_widgets() {
		$ret = array(
			self::WIDGET_ACCORDION,
			self::WIDGET_DATEPICKER,
			self::WIDGET_DIALOG,
			self::WIDGET_PROGRESSBAR,
			self::WIDGET_SLIDER,
			self::WIDGET_TABS,
			self::WIDGET_AUTOCOMPLETE
		);
		if (self::is_version_1_8()) {
			$ret[] = self::WIDGET_BUTTON; 
		}
		return $ret;
	}
		
	/**
	 * Returns true, if version is 1.8
	 * 
	 * @return bool
	 */
	private static function is_version_1_8() {
		return Config::get_value(ConfigJQueryUI::JQUERYUI_VERSION) == '1.8';
	}
	
	/**
	 * Returns array of dependencies for every component
	 * 
	 * @return array
	 */
	private static function get_dependencies() {
		if (self::is_version_1_8()) {
			return array(
				self::EFFECTS_CORE => array(),
				self::EFFECTS_BLIND => array(self::EFFECTS_CORE),
				self::EFFECTS_BOUNCE => array(self::EFFECTS_CORE),
				self::EFFECTS_CLIP => array(self::EFFECTS_CORE),
				self::EFFECTS_DROP => array(self::EFFECTS_CORE),
				self::EFFECTS_EXPLODE => array(self::EFFECTS_CORE),
				self::EFFECTS_FADE => array(self::EFFECTS_CORE),
				self::EFFECTS_FOLD => array(self::EFFECTS_CORE),
				self::EFFECTS_HIGHLIGHT => array(self::EFFECTS_CORE),
				self::EFFECTS_PULSATE => array(self::EFFECTS_CORE),
				self::EFFECTS_SCALE => array(self::EFFECTS_CORE),
				self::EFFECTS_SHAKE => array(self::EFFECTS_CORE),
				self::EFFECTS_SLIDE => array(self::EFFECTS_CORE),
				self::EFFECTS_TRANSFER => array(self::EFFECTS_CORE),
				
				/* Widgets constants */
				self::WIDGET_ACCORDION => array(self::CORE, self::CORE_WIDGET),
				self::WIDGET_BUTTON => array(self::CORE, self::CORE_WIDGET),
				self::WIDGET_DATEPICKER => array(self::CORE, self::CORE_WIDGET),
				self::WIDGET_DIALOG => array(self::WIDGET_BUTTON, self::FEATURE_MOUSE, self::FEATURE_POSITION, self::FEATURE_DRAGGABLE, self::FEATURE_RESIZABLE),
				self::WIDGET_PROGRESSBAR => array(self::CORE, self::CORE_WIDGET),
				self::WIDGET_SLIDER => array(self::CORE, self::CORE_WIDGET, self::FEATURE_MOUSE),
				self::WIDGET_TABS => array(self::CORE, self::CORE_WIDGET),
				self::WIDGET_AUTOCOMPLETE => array(self::CORE, self::CORE_WIDGET),
				
				/* Feature constants */
				self::FEATURE_DRAGGABLE => array(self::CORE, self::CORE_WIDGET, self::FEATURE_MOUSE),
				self::FEATURE_DROPPABLE => array(self::FEATURE_DRAGGABLE),
				self::FEATURE_RESIZABLE => array(self::CORE, self::CORE_WIDGET, self::FEATURE_MOUSE),
				self::FEATURE_SELECTABLE => array(self::CORE, self::CORE_WIDGET, self::FEATURE_MOUSE),
				self::FEATURE_SORTABLE => array(self::CORE, self::CORE_WIDGET, self::FEATURE_MOUSE),
				// 1.8 stuff
				self::FEATURE_MOUSE => array(self::CORE_WIDGET),
				self::FEATURE_POSITION => array(),
				
				self::CORE_WIDGET => array(),
				self::CORE => array(),			
			);
		}	
		else {
			// Version 1.7
			return array(
				self::EFFECTS_CORE => array(),
				self::EFFECTS_BLIND => array(self::EFFECTS_CORE),
				self::EFFECTS_BOUNCE => array(self::EFFECTS_CORE),
				self::EFFECTS_CLIP => array(self::EFFECTS_CORE),
				self::EFFECTS_DROP => array(self::EFFECTS_CORE),
				self::EFFECTS_EXPLODE => array(self::EFFECTS_CORE),
				self::EFFECTS_FADE => array(self::EFFECTS_CORE),
				self::EFFECTS_FOLD => array(self::EFFECTS_CORE),
				self::EFFECTS_HIGHLIGHT => array(self::EFFECTS_CORE),
				self::EFFECTS_PULSATE => array(self::EFFECTS_CORE),
				self::EFFECTS_SCALE => array(self::EFFECTS_CORE),
				self::EFFECTS_SHAKE => array(self::EFFECTS_CORE),
				self::EFFECTS_SLIDE => array(self::EFFECTS_CORE),
				self::EFFECTS_TRANSFER => array(self::EFFECTS_CORE),
				
				/* Widgets constants */
				self::WIDGET_ACCORDION => array(self::CORE),
				self::WIDGET_DATEPICKER => array(self::CORE),
				self::WIDGET_DIALOG => array(self::CORE, self::FEATURE_DRAGGABLE, self::FEATURE_RESIZABLE),
				self::WIDGET_PROGRESSBAR => array(self::CORE),
				self::WIDGET_SLIDER => array(self::CORE),
				self::WIDGET_TABS => array(self::CORE),
				self::WIDGET_AUTOCOMPLETE => array(),
				
				/* Feature constants */
				self::FEATURE_DRAGGABLE => array(self::CORE),
				self::FEATURE_DROPPABLE => array(self::FEATURE_DRAGGABLE),
				self::FEATURE_RESIZABLE => array(self::CORE),
				self::FEATURE_SELECTABLE => array(self::CORE),
				self::FEATURE_SORTABLE => array(self::CORE),
				
				self::CORE => array(),			
			);
		}
	}
	
	/**
	 * Returns all elements that have a CSS file
	 * 
	 * @return array
	 */
	private static function get_components_having_css() {
		$ret = self::get_all_widgets();
		$ret[] = self::FEATURE_RESIZABLE;
		return $ret;
	}
}
