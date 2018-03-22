<?php
/**
 * A multiselect input
 * 
 * Renders either as a set of checkboxes or a multiselect select box
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class InputWidgetMultiselectBase extends InputWidgetBase {
	protected $use_checkboxes = false;
	
	/**
	 * Render the actual widget
	 */
	protected function render_input($attrs, $params, $name, $title, $value, $policy) {
		$ret = '';
		$options = Arr::get_item($attrs, 'options', array());
		$c = count($options);
		unset($attrs['options']);

		$ret .= html::input('hidden', $name, array());
		
		$name .= '[]';
		$value = Arr::force($value, false);
		
		$this->use_checkboxes = ( 
			($c <= APP_MULTISELECT_THRESHOLD && !Common::flag_is_set($policy, WidgetInput::FORCE_SELECT_BOX)) 
			|| 
			(Common::flag_is_set($policy, WidgetInput::FORCE_CHECKBOXES))
		);

		if ($this->use_checkboxes) {
			$ret .= $this->build_multiselect_checkboxes($name, $options, $value, $attrs, Arr::get_item($params, 'label:class', ''));
		}
		else {
			$ret .= $this->build_multiselect_select($name, $options, $value, $attrs);
		}
		return $ret;
	}	

	/**
	 * Render a label around widget
	 */
	protected function render_label($widget, $html_attrs, $params, $name, $title, $value, $policy) {
		if ($this->use_checkboxes) {
			$policy |= WidgetInput::NO_LABEL;
		}
		return parent::render_label($widget, $html_attrs, $params, $name, $title, $value, $policy);
	}

	/**
	 * Render a label around widget
	 */
	protected function parent_render_label($widget, $html_attrs, $params, $name, $title, $value, $policy) {
		return parent::render_label($widget, $html_attrs, $params, $name, $title, $value, $policy);
	}


	/**
	 * Last steps
	 */
	protected function render_postprocess($output, $policy) {
		if ($this->use_checkboxes) {
			$output .= '<div class="multiselect-end"></div>';
		}
		return parent::render_postprocess($output, $policy); 
	}

	/**
	 * Build a mutiselect out of a SELECT box 
	 */
	protected function build_multiselect_select($name, $options, $value, $attrs) {
		$c = count($options);
		$attrs['size'] = Arr::get_item($attrs, 'size', $c > APP_MULTISELECT_THRESHOLD ? APP_MULTISELECT_THRESHOLD : $c);
		$attrs['multiple'] = 'multiple';
		return html::select($name, $options, $value, $attrs);
	}

	/**
	 * Build a mutiselect as a set of checkboxes
	 */
	protected function build_multiselect_checkboxes($name, $options, $value, $attrs, $lbl_class) {
		$ret = '';
		foreach($options as $key => $display) {
			if (is_array($display)) {
				$ret .= $this->build_mutiselect_checkbox_group($name, $key, $display, $value, $attrs, $lbl_class);
			}
			else {
				$ret .= $this->build_multiselect_checkbox($name, $key, $display, $value, $attrs, $lbl_class);
			}
		}
		return $ret;
	}
	
	/**
	 * Build a mutiselct checbox group (heading + checkboxes)
	 */
	protected function build_mutiselect_checkbox_group($name, $title, $options, $value, $attrs, $lbl_class) {
		$ret = '';
		$ret .= '<div class="multiselect-group">';
		$ret .= html::div($title,  'label multiselect-group-title ' . $lbl_class);
		$ret .= $this->build_multiselect_checkboxes($name, $options, $value, $attrs, $lbl_class);
		$ret .= '</div>';
		return $ret;
	}
	
	/**
	 * Build a single mutiselect checkbox
	 */
	protected function build_multiselect_checkbox($name, $key, $display, $values, $attrs, $lbl_class) {
		$ret = '';
		$attrs['value'] = $key;
		if (in_array($key, $values)) {
			$attrs['checked'] = 'checked';
		}
		unset($attrs['id']);
		$checkbox_html = html::input('checkbox', $name, $attrs);
		$checkbox_html .= ' ' . $display;
		$ret .= html::label($checkbox_html, '', 'spanning ' . $lbl_class);
		return $ret;
	}
}