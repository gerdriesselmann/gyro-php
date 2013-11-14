<?php
require_once GYRO_CORE_DIR . 'view/widgets/input/base/radio.input.widget.php';

/**
 * A radion option button
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class InputWidgetRadio extends InputWidgetRadioBase {
	protected function render_radio_button_and_label($name, $attrs, $label_text) {
		$radio_html = '';
		$radio_html .= html::input('radio', $name, $attrs);
		$radio_html .= ' ';
		$radio_html .= html::label($label_text, $attrs['id']);
		return html::div($radio_html, 'radio');
	}

	/**
	 * Render a label around widget
	 */
	protected function render_label($widget, $html_attrs, $params, $name, $title, $value, $policy) {
		return html::div(
			html::label($title, '')  . $widget,
			'form-group'
		);
	}
}