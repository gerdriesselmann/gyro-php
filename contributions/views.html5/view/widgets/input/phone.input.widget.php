<?php
require_once GYRO_CORE_DIR . 'view/widgets/input/base/phone.input.widget.php';

/**
 * A phone number widget for international numbers obeying E.123 standard
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class InputWidgetPhone extends InputWidgetPhoneBase {
	/**
	 * Render the actual widget
	 */
	protected function render_input($attrs, $params, $name, $title, $value, $policy) {
		$attrs['pattern'] = Validation::e123_phone_regex();
		return html::input('tel', $name, $attrs);
	}
}