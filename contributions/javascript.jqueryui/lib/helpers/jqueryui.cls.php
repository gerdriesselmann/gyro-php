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
	 * Locales selected for current page
	 * 
	 * @var array
	 */
	private static $enabled_locales = array();
	
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
	 * Enable some locales
	 * 
	 * @param array|string $locales
	 */
	public static function enable_locales($locales) {
		self::$enabled_locales = array_merge(self::$enabled_locales, Arr::force($locales, false));
		self::$enabled_locales = array_unique(self::$enabled_locales);
	}
	
	/**
	 * Returns array of enabled locales
	 * 
	 * @return array
	 */
	public static function get_enabled_locales() {
		return self::$enabled_locales;
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
		$locs = self::get_supported_locales();
		foreach($components as $c) {
			self::collect_dependencies($c, $resolved, $deps, $locs);
		}
		
		$ret = array();
		$prefix = self::is_version_1_8() ? 'jquery.' : ''; 
		foreach($resolved as $c) {
			$c = explode('/', $c);
			$file = array_pop($c);
			$file = $prefix . $file;
			$c[] = $file;
			$path = 'js/jqueryui/' . implode('/', $c) . '.js';
			if (!in_array($path, $ret)) {
				$ret[] = $path;
			}	
		}
		return $ret;
	}
	
	/**
	 * Collect dependencies for given component
	 */
	private static function collect_dependencies($component, &$resolved, $dependencies, $localizations) {
		$deps = Arr::get_item($dependencies, $component, array());
		foreach($deps as $d) {
			self::collect_dependencies($d, $resolved, $dependencies, $localizations);
		}
		$resolved[$component] = $component;
		self::collect_localizations($component, $resolved, $localizations);
	}
	
	/**
	 * Collect localization scripts for given component 
	 */
	private static function collect_localizations($component, &$resolves, $localizations) {
		$localizations_for_comp = Arr::get_item($localizations, $component, array());
		foreach(self::get_enabled_locales() as $l) {
			if (in_array($l, $localizations_for_comp)) {
				$file = 'i18n/' . $component . '-' . $l;
				$resolves[$file] = $file;
			}
		}
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
		$prefix = self::is_version_1_8() ? 'jquery.' : ''; 
		foreach($css_components as $c) {
			$path = 'css/jqueryui/' . $prefix . $c . '.css';
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
				self::WIDGET_AUTOCOMPLETE => array(self::CORE, self::CORE_WIDGET, self::FEATURE_POSITION),
				
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
	 * Returns array of dependencies for every component
	 * 
	 * @return array
	 */
	private static function get_supported_locales() {
		if (self::is_version_1_8()) {
			return array(
				self::WIDGET_DATEPICKER => array(
					'af', 'ar', 'az',
					'bg', 'bs',
					'ca', 'cs',
					'da', 'de', 
					'el', 'en-GB', 'eo', 'es', 'et', 'eu', 
					'fa', 'fi', 'fo', 'fr-CH', 'fr',
					'he', 'hr', 'hu', 'hy',
					'id', 'is', 'it', 
					'ja', 
					'ko', 
					'lt', 'lv',
					'ms',
					'nl', 'no', 
					'pl', 'pt-BR',
					'ro', 'ru',
					'sk', 'sl', 'sq', 'sr', 'sr-SR', 'sv',
					'ta', 'th', 'tr', 
					'uk', 
					'vi',
					'zh-CN', 'zh-HK', 'zh-TW',					
				),
			);
		}	
		else {
			// Version 1.7
			return array(
				self::WIDGET_DATEPICKER => array(
					'ar',
					'bg',
					'ca', 'cs',
					'da', 'de', 
					'el', 'eo', 'es', 
					'fa', 'fi', 'fr',
					'he', 'hr', 'hu', 'hy',
					'id', 'is', 'it', 
					'ja', 
					'ko', 
					'lt', 'lv',
					'ms',
					'nl', 'no', 
					'pl', 'pt-BR',
					'ro', 'ru',
					'sk', 'sl', 'sq', 'sr', 'sr-SR', 'sv',
					'th', 'tr', 
					'uk', 
					'vi',
					'zh-CN', 'zh-TW',
				),	
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
