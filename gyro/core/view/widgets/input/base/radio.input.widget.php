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
			$radio_html = '';
			$radio_html .= html::input('radio', $name, $attrs_copy);
			$radio_html .= ' ' . String::escape($opt_value);
			$radio_html = html::label($radio_html, '', 'spanning');
			$ret .= $radio_html;	
		}
		return $ret;	
	}	

	/**
	 * Render a label around widget
	 */
	protected function render_label($widget, $html_attrs, $params, $name, $title, $value, $policy) {
		return parent::render_label($widget, $html_attrs, $params, $name, $title, $value, $policy | WidgetInput::NO_LABEL);
	}	
}