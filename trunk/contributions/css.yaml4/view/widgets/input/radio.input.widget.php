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
		$radio_html .= html::label($label_text, $name);
		return html::div($radio_html);
	}
}