<?php
require_once GYRO_CORE_DIR . 'view/widgets/input/base/datetime.input.widget.php';

/**
 * A date widget
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class InputWidgetDateTime extends InputWidgetDateTimeBase {
	/**
	 * Add new attributes or process old ones
	 */
	protected function extend_attributes(&$attrs, $params, $name, $title, $value, $policy) {
		parent::extend_attributes($attrs, $params, $name, $title, $value, $policy);
		if ($value) {
			$value = date('Y-m-d\TH:i', GyroDate::datetime($value));
		}
		$attrs['value'] = $value;
		$attrs['class'] = trim(Arr::get_item($attrs, 'class', '') . ' datetime');
	}

	/**
	 * Render the actual widget
	 */
	protected function render_datetime_widget($attrs, $params, $name, $title, $value, $policy) {
		return html::input('datetime-local', $name, $attrs);
	}
}