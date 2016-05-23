<?php
/**
 * A number widget
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class InputWidgetNumberBase extends InputWidgetBase {
	/**
	 * Add new attributes or process old ones
	 */
	protected function extend_attributes(&$attrs, $params, $name, $title, $value, $policy) {
		parent::extend_attributes($attrs, $params, $name, $title, $value, $policy);
		$attrs['value'] = Cast::int($value);
		$attrs['class'] = trim(Arr::get_item($attrs, 'class', '') . ' number');
	}
	
	/**
	* Render the actual widget
	 */
	protected function render_input($attrs, $params, $name, $title, $value, $policy) {
		return html::input('text', $name, $attrs);
	}	
}