<?php
require_once GYRO_CORE_DIR . 'view/widgets/input/base/number.input.widget.php';

/**
 * A date widget
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class InputWidgetNumber extends InputWidgetNumberBase {
	/**
	 * Render the actual widget
	 */
	protected function render_input($attrs, $params, $name, $title, $value, $policy) {
		return html::input('number', $name, $attrs);
	}
}