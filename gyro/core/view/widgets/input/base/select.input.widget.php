<?php
/**
 * A select box
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class InputWidgetSelectBase extends InputWidgetBase {
	/**
	 * Render the actual widget
	 */
	protected function render_input($attrs, $params, $name, $title, $value, $policy) {
		$options = Arr::get_item($attrs, 'options', array());
		$nodefault = Arr::get_item($attrs, 'nodefault', false);
		unset($attrs['options']);
		unset($attrs['nodefault']);
		if (empty($value) && $nodefault) {
			// This preservers numeric keys, array_merge destroys them
			$new_options = array('' => tr('Please choose...', 'core'));
			foreach($options as $key => $v) {
				$new_options[$key] = $v;
			}
			$options = $new_options;
		}
		$ret = html::select($name, $options, $value, $attrs);
		return $ret;
	}	
}