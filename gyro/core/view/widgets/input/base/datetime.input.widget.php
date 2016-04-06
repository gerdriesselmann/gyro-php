<?php
/**
 * A date widget
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class InputWidgetDateTimeBase extends InputWidgetBase {
	/**
	 * Add new attributes or process old ones
	 */
	protected function extend_attributes(&$attrs, $params, $name, $title, $value, $policy) {
		parent::extend_attributes($attrs, $params, $name, $title, $value, $policy);
		if ($value) {
			$value = GyroDate::local_date($value, false);
		}
		$attrs['value'] = $value;
		$attrs['class'] = trim(Arr::get_item($attrs, 'class', '') . ' datetime');
	}
	
	/**
	 * Render the actual widget
	 */
	protected function render_input($attrs, $params, $name, $title, $value, $policy) {
		return
			$this->render_datetime_widget($attrs, $params, $name, $title, $value, $policy) .
			$this->render_timezone_offset($name);

	}

	protected function render_timezone_offset($name) {
		$offset_name = $name . '_timezone_offset';

		$ret ='';
		$ret .= html::input('hidden', $offset_name, array('id' => $offset_name));
		$ret .= html::script_js(
			"document.getElementById('$offset_name').value = new Date().getTimezoneOffset();"
		);

		return $ret;
	}

	protected function render_datetime_widget($attrs, $params, $name, $title, $value, $policy) {
		return html::input('text', $name, $attrs);
	}
}