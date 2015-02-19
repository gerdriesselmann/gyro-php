<?php
require_once GYRO_CORE_DIR . 'view/widgets/input/base/base.input.widget.php';

/**
 * Basic input widget
 * 
 * @author Gerd Riesselmann
 * @ingroup Bootstrap3
 */
class InputWidgetBase extends InputWidgetBaseBase {
	/**
	 * Last steps
	 */
	protected function render_postprocess($output, $policy) {
		if ($this->is_generic_input()) {
			$ret = html::div($output, "form-group");
		} else {
			$ret = $output;
		}
		return $ret;
	}

	/**
	 * Add new attributes or process old ones
	 */
	protected function extend_attributes(&$attrs, $params, $name, $title, $value, $policy) {
		parent::extend_attributes($attrs, $params, $name, $title, $value, $policy);
		if ($this->is_generic_input()) {
			$attrs['class'] = trim(Arr::get_item($attrs, 'class', '') . ' form-control');
		}
	}

	private function is_generic_input() {
		$cls = $this->get_input_type();
		switch ($cls) {
			case 'text':
			case 'textarea':
			case 'password':
			case 'file':
			case 'date':
			case 'html':
			case 'email':
				return true;
			case 'radio':
			case 'checkbox':
				return false;
			case 'submit':
				return false;
			case 'select':
			case 'multiselect':
				return true;
			default:
				return true;
		}
	}
}