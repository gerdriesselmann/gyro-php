<?php
require_once GYRO_CORE_DIR . 'view/widgets/input/base/url.input.widget.php';

/**
 * A URL input widget
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class InputWidgetUrl extends InputWidgetUrlBase {
	/**
	 * Render the actual widget
	 */
	protected function render_input($attrs, $params, $name, $title, $value, $policy) {
		return html::input('url', $name, $attrs);
	}
}