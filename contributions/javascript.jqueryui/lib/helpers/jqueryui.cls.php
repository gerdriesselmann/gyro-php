<?php
/**
 * Helper for using JQueryUI, defines widgets and dependencies 
 * 
 * @author Gerd Riesselmann
 * @ingroup JQueryUI
 */
class JQueryUI {
	/* Effect constants */
	const EFFECTS_CORE = 'effects.blind';
	const EFFECTS_BLIND = 'effects.blind';
	const EFFECTS_BOUNCE = 'effects.bounce';
	const EFFECTS_CLIP = 'effects.clip';
	const EFFECTS_DROP = 'effects.drop';
	const EFFECTS_EXPLODE = 'effects.explode';
	const EFFECTS_FOLD = 'effects.fold';
	const EFFECTS_HIGHLIGHT = 'effects.highlight';
	const EFFECTS_PULSATE = 'effects.pulsate';
	const EFFECTS_SCALE = 'effects.scale';
	const EFFECTS_SHAKE = 'effects.shake';
	const EFFECTS_SLIDE = 'effects.slide';
	const EFFECTS_TRANSGER = 'effects.transfer';
	
	/* Widgets constants */
	const WIDGET_ACCORDION = 'ui.accordion';
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
	
	const CORE = 'ui.core';
	
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
		$cores = array();
		foreach($components as $c) {
			if (self::is_effect($c)) {
				$cores[self::EFFECTS_CORE] = self::EFFECTS_CORE;
			}
			else {
				$cores[self::CORE] = self::CORE;
			}
		}
		
		$components = array_merge(array_values($cores), $components);
		
		$ret = array();
		foreach($components as $c) {
			$path = 'js/jqueryui/' . $c . '.js';
			if (!in_array($path, $ret)) {
				$ret[] = $path;
			}	
		}
		return $ret;
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
		foreach($components as $c) {
			if (!self::is_effect($c)) {
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
		return array(
			WIDGET_ACCORDION,
			WIDGET_DATEPICKER,
			WIDGET_DIALOG,
			WIDGET_PROGRESSBAR,
			WIDGET_SLIDER,
			WIDGET_TABS,
		);
	}
	
	/**
	 * Returns true, if the given component is an effect
	 */
	private static function is_effect($component) {
		return (substr($component, 0, 7) == 'effects');		
	}
}
