<?php
require_once GYRO_CORE_DIR . 'view/widgets/input/base/checkbox.input.widget.php';
/**
 * A checkbox
 * 
 * @author Gerd Riesselmann
 * @ingroup Bootstrap3
 */
class InputWidgetCheckbox extends InputWidgetCheckboxBase {
	/**
	 * Render the actual widget
	 */
	protected function render_input($attrs, $params, $name, $title, $value, $policy) {
		$lbl_class = Arr::get_item($params, 'label:class', '');
		$id = Arr::get_item($params, 'id', $name);
		$lbl = html::label($title, $id, $lbl_class);
		return html::div(
			html::input('hidden', $name, array('value' => 0)) .
			html::input('checkbox', $name, $attrs) . ' ' .
			$lbl,
			'checkbox'
		);
	}

	/**
	 * Render a label around widget
	 */
	protected function render_label($widget, $html_attrs, $params, $name, $title, $value, $policy) {
		return $widget;
	}
}