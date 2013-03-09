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
	 * Render the actual widget
	 */
	protected function render_input($attrs, $params, $name, $title, $value, $policy) {
		return html::input('date', $name, $attrs);
	}
}