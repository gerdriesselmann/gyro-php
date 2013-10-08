<?php
/**
 * A checkbox
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class InputWidgetCheckboxBase extends InputWidgetBase {
	/**
	 * Add new attributes or process old ones
	 */
	protected function extend_attributes(&$attrs, $params, $name, $title, $value, $policy) {
		parent::extend_attributes($attrs, $params, $name, $title, $value, $policy);
		$attrs['checked'] = $value ? 'checked' : false;		
	}
	
	/**
	 * Render the actual widget
	 */
	protected function render_input($attrs, $params, $name, $title, $value, $policy) {
		return 
			html::input('hidden', $name, array('value' => 0)) .
			html::input('checkbox', $name, $attrs);
	}	

	/**
	 * Render a label around widget
	 */
	protected function render_label($widget, $html_attrs, $params, $name, $title, $value, $policy) {
		return parent::render_label($widget, $html_attrs, $params, $name, $title, $value, $policy | WidgetInput::WRAP_LABEL);
	}
}