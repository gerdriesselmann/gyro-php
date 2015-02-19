<?php
require_once GYRO_CORE_DIR . 'view/widgets/input/base/submit.input.widget.php';

/**
 * A submit button
 * 
 * @author Gerd Riesselmann
 * @ingroup Bootstrap3
 */ 
class InputWidgetSubmit extends InputWidgetSubmitBase {
	/**
	 * Add new attributes or process old ones
	 */
	protected function extend_attributes(&$attrs, $params, $name, $title, $value, $policy) {
		parent::extend_attributes($attrs, $params, $name, $title, $value, $policy);
		unset($attrs['value']);
		$attrs['type'] = 'submit';
		$attrs['name'] = $name;
		$attrs['id'] = Arr::get_item($attrs, 'id', $name);
		$attrs['class'] = trim(Arr::get_item($attrs, 'class', '') . ' btn btn-default');

	}

	/**
	 * Render the actual widget
	 */
	protected function render_input($attrs, $params, $name, $title, $value, $policy) {
		return html::tag('button', String::escape($value), $attrs);
	}

}