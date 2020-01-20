<?php
/**
 * Define number of items up to which checkboxes are used instead of multiselect box 
 */
if (!defined('APP_MULTISELECT_THRESHOLD')) define ('APP_MULTISELECT_THRESHOLD', 5);

/**
 * Create an input widget (not necessarily an input element)
 * 
 * Requires.
 * - name: input name (and id, if id is not set)
 * - type: hidden, text, select, checkbox (defaults to text) 
 *
 * Optional param array has different members according to type
 * 
 * Accepts: 
 * - id (optional): id of element, defaults to name
 * - value (optional): Default value for input. If this is an array, [$name] is extracted
 * 
 * If type is not hidden, the following is supported
 * - label (optional): Label text
 * - item (optional): If provided item is checked if field can be edited
 * - notes (optional): If larger than 0, n stars (*) are added. E.g. notes:2 adds 2 stars. Needs label to be set  
 * 
 * If type is text, the following is supported:
 * - size (optional): For text fields
 * - Additional, if a type attribute is set, this is used rather than 'text'
 *
 * If type is select, multi select or radio buttons, the following is supported:
 * - options: Array of options. Options are key => value pair. If value itself is an array, all its values will 
 *   be put into an <optgroup>
 * - value (optional): The select option (key)
 * - nodefault (optional): If true the first option is NOT preselected, instead "Please Choose" is show
 * 
 * If type is file:
 * - size (optional): For text fields
 * - accepts: Mime types to accept
 * 
 * Note that for file type input boxes to work in PHP, the form must have the attribute enctype="multipart/form-data"!
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */ 
class WidgetInput implements IWidget {
	public $name;
	public $label;
	public $type;
	public $value;
	public $params;
	
	/**
	 * Do not add breaks after input element
	 */
	const NO_BREAK = 128;
	/**
	 * Wrap label around inputs, instead putting it before them
	 */
	const WRAP_LABEL = 256;
	/**
	 * For multiselect: Use checkboxes, even if number of items > 5
	 */
	const FORCE_CHECKBOXES = 512;
	/**
	 * For multiselect: Use select box, even if number of items <= 5
	 */
	const FORCE_SELECT_BOX = 1024;
	/**
	 * Do not use a label at all
	 */
	const NO_LABEL = 2048;	
	
	// INPUT Widget types
	const TEXT = 'text';
	const PASSWORD = 'password';
	const SELECT = 'select';
	const MULTISELECT = 'multiselect';
	const HIDDEN = 'hidden';
	const CHECKBOX = 'checkbox';
	const TEXTAREA = 'textarea';
	const SUBMIT = 'submit';
	const RADIO = 'radio';
	const FILE = 'file';
	const DATE = 'date';
	const DATETIME = 'datetime';
	const EMAIL = 'email';
	const NUMBER = 'number'; // Actually an integer...
	const PHONE = 'phone';
	const FLOAT = 'float';

	public static function output($name, $label, $value = '', $type = self::TEXT, $params = array(), $policy = self::NONE) {
		$widget = new WidgetInput($name, $label, $value, $type, $params);
		return $widget->render($policy);
	}
	
	/**
	 * Cosntructor
	 *
	 * @param string $name
	 * @param string $type
	 * @param array $params
	 */
	public function __construct($name, $label, $value, $type, $params) {
		$this->name = $name;
		$this->label = $label;
		$this->type = $type;
		$this->value = is_array($value) ? Arr::get_item_recursive($value, $name, '') : $value;
		$this->params = Arr::force($params, false);
	}
	
	/**
	 * Render content
	 *
	 * @param int $policy
	 * @return string
	 */
	public function render($policy = self::NONE) {
		Load::classes_in_directory('view/widgets/input', array('base', $this->type), 'input.widget', true);
		$cls = 'InputWidget' . Load::filename_to_classname($this->type);
		if (class_exists($cls)) {
			$delegate = new $cls($this->name, $this->label, $this->value, $this->params);
			return $delegate->render($policy);
		}	
		else {
			throw new Exception('input: unknown type "' . $this->type . '"');
		}	
	}
}
