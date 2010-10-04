<?php
require_once GYRO_CORE_DIR . 'view/widgets/input/base/textarea.input.widget.php';
/**
 * A text area for HTML input
 * 
 * To invoke it, use HtmlText::WIDGET as type for WidgetInput.
 * 
 * Example:
 * @code
 * print WidgetInput::output('content', 'Edit post content:', $form_data, HtmlText::WIDGET, array('model' => 'posts', 'editor' => 'basic'));
 * @endcode
 * 
 * This will create a textarea with classes "rte" and "rte_{EDITOR}" set. Given above example, this would be "rte rte_basic". 
 * 
 * The default value gets converted using the HtmlText::EDIT conversions. You may pass a model as parameter 'model', if you don't,
 * default conversions will be used.
 * 
 * Additionally, rich text editors are enabled, if any are available. You may pass an editor config'name as parameter 'editor'. If none
 * is given, default configuration will be used.
 * 
 * @author Gerd Riesselmann
 * @ingroup Html
 */
class InputWidgetHtmlBase extends InputWidgetTextareaBase {
	/**
	* Render the actual widget
	 */
	protected function render_input($attrs, $params, $name, $title, $value, $policy) {
		$model = Arr::get_item($params, 'model', HtmlText::CONVERSION_DEFAULT);
		$value = HtmlText::apply_conversion(HtmlText::EDIT, $value, $model);
		
		$editor = Arr::get_item($params, 'editor', HtmlText::EDITOR_DEFAULT);
		HtmlText::enable_editor($editor);
		
		$attrs['class'] = trim(Arr::get_item($attrs, 'class', '') . ' rte rte_' . strtolower($editor));
		return parent::render_input($attrs, $params, $name, $title, $value, $policy);
	}
}