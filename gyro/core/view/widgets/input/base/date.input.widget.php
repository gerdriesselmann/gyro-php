<?php
/**
 * A date widget
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class InputWidgetDateBase extends InputWidgetBase {
	/**
	 * Add new attributes or process old ones
	 */
	protected function extend_attributes(&$attrs, $params, $name, $title, $value, $policy) {
		parent::extend_attributes($attrs, $params, $name, $title, $value, $policy);
		if ($value) {
			$value = GyroDate::local_date(GyroDate::datetime($value), false);
		}
		$attrs['value'] = $value;
		$attrs['class'] = trim(Arr::get_item($attrs, 'class', '') . ' date');
	}
	
	/**
	* Render the actual widget
	 */
	protected function render_input($attrs, $params, $name, $title, $value, $policy) {
		return html::input('text', $name, $attrs);
	}	
}