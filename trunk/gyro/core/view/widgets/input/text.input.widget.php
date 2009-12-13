<?php
/**
 * A text widget
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class InputWidgetText extends InputWidgetBase {
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
		return html::input('text', $name, $attrs);
	}	
}