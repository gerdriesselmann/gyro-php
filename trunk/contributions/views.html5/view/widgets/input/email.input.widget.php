<?php
require_once GYRO_CORE_DIR . 'view/widgets/input/base/email.input.widget.php';

/**
 * A date widget
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class InputWidgetEMail extends InputWidgetEMailBase {
	/**
	 * Render the actual widget
	 */
	protected function render_input($attrs, $params, $name, $title, $value, $policy) {
		return html::input('email', $name, $attrs);
	}
}