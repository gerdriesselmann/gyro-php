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

	/**
	 * @param string|ISelfDescribing $text The content of the anchor to create. Gets escaped if instance of ISelfDescribing, else it is printed as given
	 * @param string $action The action to retrieve URL for
	 * @param array|null $params The parameters for aboev action
	 * @param array $html_attrs Attributes to pass to HTML anchor. If a gyro_query is passed, this is added as query to the generated action URL
	 * @return string
	 */
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
		$path = ActionMapper::get_path($this->action, $this->params);
		$query_params = Arr::get_item($this->attrs, 'gyro_query', null);
		if (is_array($query_params) && count($query_params) > 0) {
			$url = Url::create('');
			$url->replace_query_parameters($query_params);
			$path .= '?' . $url->get_query();
		}
		unset($this->attrs['gyro_query']);
		return html::a(
			$text, 
			$path,
			'',
			$this->attrs
		);
	}
}