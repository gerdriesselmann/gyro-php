<?php
/**
 * A text widget for floating point values
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class InputWidgetFloatBase extends InputWidgetBase {
	/**
	 * Add new attributes or process old ones
	 */
	protected function extend_attributes(&$attrs, $params, $name, $title, $value, $policy) {
		parent::extend_attributes($attrs, $params, $name, $title, $value, $policy);
		if ($value) {
			$value = GyroString::localize_number($value);
		}
		$attrs['value'] = $value;
		$attrs['class'] = trim(Arr::get_item($attrs, 'class', '') . ' float');
	}
	
	/**
	* Render the actual widget
	 */
	protected function render_input($attrs, $params, $name, $title, $value, $policy) {
        $type = Arr::get_item($attrs, 'type', 'text');
		return html::input($type, $name, $attrs);
	}	
}