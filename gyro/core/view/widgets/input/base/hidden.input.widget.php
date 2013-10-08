<?php
/**
 * A hidden input widget
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class InputWidgetHiddenBase extends InputWidgetBase {
	/**
	 * Add new attributes or process old ones
	 */
	protected function extend_attributes(&$attrs, $params, $name, $title, $value, $policy) {
		parent::extend_attributes($attrs, $params, $name, $title, $value, $policy);
		$attrs['value'] = $value;
	}
	
	/**
	* Render the actual widget
	 */
	protected function render_input($attrs, $params, $name, $title, $value, $policy) {
		return html::input('hidden', $name, $attrs);
	}	
	
	/**
	 * Last steps
	 */
	protected function render_postprocess($output, $policy) {
		return $output;
	}	
}