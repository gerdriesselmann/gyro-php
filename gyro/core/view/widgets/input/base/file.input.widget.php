<?php
/**
 * A file input
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class InputWidgetFileBase extends InputWidgetBase {
	/**
	 * Render the actual widget
	 */
	protected function render_input($attrs, $params, $name, $title, $value, $policy) {
		return html::input('file', $name, $attrs);
	}	
}