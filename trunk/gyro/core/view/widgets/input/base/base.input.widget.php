<?php
/**
 * Basic input widget
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class InputWidgetBaseBase implements IWidget {
	protected $name;
	protected $label;
	protected $value;
	protected $params;
	
	/**
	 * Constructor
	 */
	public function __construct($name, $label, $value, $params) {
		$this->name = $name;
		$this->label = $label;
		$this->value = $value;
		$this->params = Arr::force($params, false);		
	}
	
	/**
	 * Returns type of this input widget, like 'textarea' or 'select', or 'multiselect'
	 *  
	 * @return string Always lower case
	 */
	protected function get_input_type() {
		$cls = get_class($this);
		$cls = str_replace('InputWidget', '', $cls);
		$cls = strtolower($cls);
		return $cls; 
	}

	/**
	 * Render the widget 
	 */
	public function render($policy = self::NONE) {
		$ret = '';
		$item = Arr::get_item($this->params, 'item', false);
		$name = array_pop(Arr::extract_array_keys($this->name));
		$can_edit = ($item) ? AccessControl::is_allowed('edit', $item, $name) : true;
		if ($can_edit) {
			$ret = $this->input_build_widget($this->params, $this->name, $this->label, $this->value, $policy);
		}
		else {
			$ret = $this->input_build_noedit($this->params, $this->name, $this->label, $this->value, $policy);
		}
		return $ret;		
	}
	
	
	/**
	 * Build an edit widget
	 *
	 * @param Array of params $params
	 * @param string $name
	 * @return string
	 */
	protected function input_build_widget($params, $name, $title, $value, $policy) { 
		$ret = '';
		
		$html_attrs = $this->create_default_attributes($params, $name, $policy);
		$this->extend_attributes($html_attrs, $params, $name, $title, $value, $policy);
		
		$widget = $this->render_input($html_attrs, $params, $name, $title, $value, $policy);
		$widget = $this->render_label($widget, $html_attrs, $params, $name, $title, $value, $policy);
		$ret = $this->render_postprocess($widget, $policy);
		
		return $ret;
	}
	
	/**
 	 * Create default attribute array
	 */
	protected function create_default_attributes($params, $name, $policy) {
		$id = strtr(arr::get_item($params, 'id', $name), '[]', '__');
		$attrs = array(
			'id' => $id
		);
		
		$attrs = array_merge($attrs, $params);

		unset($attrs['label:class']);
		unset($attrs['notes']);		
		unset($attrs['item']);
		return $attrs;
	}
	
	/**
	 * Add new attributes or process old ones
	 */
	protected function extend_attributes(&$attrs, $params, $name, $title, $value, $policy) {
	}
	
	/**
	 * Render the actual widget
	 */
	protected function render_input($attrs, $params, $name, $title, $value, $policy) {
	}
	
	/**
	 * Render a label around widget
	 */
	protected function render_label($widget, $html_attrs, $params, $name, $title, $value, $policy) {
		$ret = '';
		$id = Arr::get_item($html_attrs, 'id', '');
		$lbl_class = Arr::get_item($params, 'label:class', '');
		$label = '';
		if ($title) {
			$notes = Cast::int(arr::get_item($params, 'notes', 0));
			if ($notes > 0) {
				$title .= ' '. html::span(str_repeat('*', $notes), 'notes');
			}
			if (Common::flag_is_set($policy, WidgetInput::NO_LABEL)) {
				// Div before input
				$ret = html::div($title,  'label ' . $lbl_class) . $widget;
			}
			else if (Common::flag_is_set($policy, WidgetInput::WRAP_LABEL)) {
				// Wrap label around input: <label><input /> title</label>
				$ret = html::label($widget. ' ' . $title, $id, 'spanning ' . $lbl_class);
			}
			else {	
				// label before input <label>title</label><input />
				$ret = html::label($title, $id, 'outside ' . $lbl_class) . $widget;
			}
		}
		else {
			$ret = $widget;
		}
		return $ret;
	}

	/**
	 * Last steps
	 */
	protected function render_postprocess($output, $policy) {
		$ret = $output;
		if (!Common::flag_is_set($policy, WidgetInput::NO_BREAK)) {
			$ret .= html::br(); 
		}
		return $ret;
	}
	
	/**
	 * Builds a widget that cannot be editetd
	 *
	 * @param array $params
	 * @param string $name
	 * @return string
	 */
	protected function input_build_noedit($params, $name, $title, $value, $policy) {
		$ret = '';
		$ret .= html::span($title, 'label') . ' ';
		$ret .= html::span($value, 'value');
		$ret = html::p($ret, 'noedit');
		return $ret;
	}	
}