<?php
/**
 * A submit button
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */ 
class InputWidgetSubmit extends InputWidgetBase {
	/**
	 * Add new attributes or process old ones
	 */
	protected function extend_attributes(&$attrs, $params, $name, $title, $value, $policy) {
		$attrs['value'] = $value;
	}
	
	/**
	* Render the actual widget
	 */
	protected function render_input($attrs, $params, $name, $title, $value, $policy) {
		return html::input('submit', $name, $attrs);
	}	
}