<?php
require_once GYRO_CORE_DIR . 'view/widgets/input/base/base.input.widget.php';

/**
 * Basic input widget
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class InputWidgetBase extends InputWidgetBaseBase {
	/**
	 * Last steps
	 */
	protected function render_postprocess($output, $policy) {
		if (!Config::has_feature(ConfigYAML::YAML_USE_FORMS)) {
			return parent::render_postprocess($output, $policy);
		}
		$cls = $this->get_input_type();
		$type = $cls;
		switch ($cls) {
			case 'textarea':
			case 'password':
			case 'file':
			case 'date':
			case 'html':
				$type = 'text';
				break;
			case 'radio':
			case 'checkbox':
				$type = 'check';
				break;
			case 'submit':
				$type = 'button';
				break;	
			case 'multiselect':
				$type = 'select';
				break;		
		}
		
		$ret = html::div($output, "type-$type");
		return $ret;
	}	
}