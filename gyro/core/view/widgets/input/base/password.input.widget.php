<?php
/**
 * A password widget
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class InputWidgetPasswordBase extends InputWidgetBase {
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
		return html::input('password', $name, $attrs);
	}	
}
