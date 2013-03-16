<?php
require_once GYRO_CORE_DIR . 'view/widgets/input/base/checkbox.input.widget.php';
/**
 * A checkbox
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class InputWidgetCheckbox extends InputWidgetCheckboxBase {
	/**
	 * Render the actual widget
	 */
	protected function render_input($attrs, $params, $name, $title, $value, $policy) {
		return
			html::input('hidden', $name, array('value' => 0)) .
			html::input('checkbox', $name, $attrs) . ' ' .
			html::label($title, $name);
	}

	/**
	 * Render a label around widget
	 */
	protected function render_label($widget, $html_attrs, $params, $name, $title, $value, $policy) {
		return $widget;
	}
}