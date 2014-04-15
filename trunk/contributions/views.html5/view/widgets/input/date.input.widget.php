<?php
require_once GYRO_CORE_DIR . 'view/widgets/input/base/date.input.widget.php';

/**
 * A date widget
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class InputWidgetDate extends InputWidgetDateBase {
	/**
	 * Add new attributes or process old ones
	 */
	protected function extend_attributes(&$attrs, $params, $name, $title, $value, $policy) {
		parent::extend_attributes($attrs, $params, $name, $title, $value, $policy);
		if ($value) {
			$value = date('Y-m-d', GyroDate::datetime($value));
		}
		$attrs['value'] = $value;
		$attrs['class'] = trim(Arr::get_item($attrs, 'class', '') . ' date');
	}

	/**
	 * Render the actual widget
	 */
	protected function render_input($attrs, $params, $name, $title, $value, $policy) {
		return html::input('date', $name, $attrs);
	}
}