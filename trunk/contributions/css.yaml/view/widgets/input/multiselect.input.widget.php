<?php
require_once GYRO_CORE_DIR . 'view/widgets/input/base/multiselect.input.widget.php';
/**
 * A multiselect input
 * 
 * Renders either as a set of checkboxes or a multiselect select box
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class InputWidgetMultiselect extends InputWidgetMultiselectBase {
	/**
	 * Build a single mutiselect checkbox
	 */
	protected function build_multiselect_checkbox($name, $key, $display, $values, $attrs, $lbl_class) {
		$ret = '';
		$attrs['value'] = $key;
		if (in_array($key, $values)) {
			$attrs['checked'] = 'checked';
		}
		$attrs['id'] = strtr($name, array(']' => '', '[' => '')) . '_' . $key;
		$checkbox_html = html::input('checkbox', $name, $attrs);
		$checkbox_html .= ' ';
		$checkbox_html .= html::label($display, $attrs['id']);
		$ret .= html::div($checkbox_html, 'type-check');
		return $ret;
	}	
}