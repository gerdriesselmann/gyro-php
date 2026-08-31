<?php
/**
 * An email input  widget
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class InputWidgetUrlBase extends InputWidgetBase {
	/**
	 * Add new attributes or process old ones
	 */
	protected function extend_attributes(&$attrs, $params, $name, $title, $value, $policy) {
		parent::extend_attributes($attrs, $params, $name, $title, $value, $policy);
		$attrs['value'] = $value;
		$attrs['class'] = trim(Arr::get_item($attrs, 'class', '') . ' url-input');
	}
	
	/**
	* Render the actual widget
	 */
	protected function render_input($attrs, $params, $name, $title, $value, $policy) {
		return html::input('text', $name, $attrs);
	}	
}