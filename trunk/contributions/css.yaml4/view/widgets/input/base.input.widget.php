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
		if (!Config::has_feature(ConfigYAML4::USE_FORMS)) {
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
		
		$ret = html::div($output, "ym-fbox-$type");
		return $ret;
	}


	/**
	 * Render a label around widget
	 */
	protected function render_label($widget, $html_attrs, $params, $name, $title, $value, $policy) {
		if ($title && Common::flag_is_set($policy, WidgetInput::NO_LABEL)) {
			$lbl_class = Arr::get_item($params, 'label:class', '');
			return html::div($title,  'ym-label ' . $lbl_class) . $widget;
		} else {
			return parent::render_label($widget, $html_attrs, $params, $name, $title, $value, $policy);
		}
	}

}