<?php
/**
 * Print link for given action
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class WidgetActionLink implements IWidget {
	public $action;
	public $params;
	public $attrs;
	public $text;
	
	public static function output($text, $action, $params = null, $html_attrs = array()) {
		$w = new WidgetActionLink($text, $action, $params, $html_attrs);
		return $w->render();
	}
	
	public function __construct($text, $action, $params = null, $html_attrs = array()) {
		$this->text = $text;
		$this->action = $action;
		$this->attrs = $html_attrs;
		$this->params = $params;
	}
	
	public function render($policy = self::NONE) {
		$text = ($this->text instanceof ISelfDescribing) ? GyroString::escape($this->text->get_title()) : $this->text;
		return html::a(
			$text, 
			ActionMapper::get_path($this->action, $this->params),
			'',
			$this->attrs
		);
	}
}