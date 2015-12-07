<?php
/**
 * A radion option button
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class InputWidgetRadioBase extends InputWidgetBase {
	/**
	 * Render the actual widget
	 */
	protected function render_input($attrs, $params, $name, $title, $value, $policy) {
		$options = Arr::get_item($attrs, 'options', array());
		unset($attrs['options']);
		
		$ret = '';
		$id = Arr::get_item($attrs, 'id', '');
		foreach(Arr::force($options, false) as $opt_key => $opt_value) {
			$attrs_copy = $attrs;
			$attrs_copy['value'] = $opt_key;
			if ($id) {
				$attrs_copy['id'] = $id . '_' . $opt_key;
				if ($opt_key == $value) {
					$attrs_copy['checked'] = 'checked';
				}
			}
			$ret .= $this->render_radio_button_and_label($name, $attrs_copy, $opt_value);
		}
		return $ret;	
	}

	protected function render_radio_button_and_label($name, $attrs, $label_text) {
		$radio_html = '';
		$radio_html .= html::input('radio', $name, $attrs);
		$radio_html .= ' ' . GyroString::escape($label_text);
		$radio_html = html::label($radio_html, '', 'spanning');
		return $radio_html;
	}

	/**
	 * Render a label around widget
	 */
	protected function render_label($widget, $html_attrs, $params, $name, $title, $value, $policy) {
		return parent::render_label($widget, $html_attrs, $params, $name, $title, $value, $policy | WidgetInput::NO_LABEL);
	}	
}