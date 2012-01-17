<?php
/**
 * Prints letter bar
 */
class WidgetAlphaPager implements IWidget {
	const LINK_TO_SUBPAGES = 256;
	const INCLUDE_NUMERIC = 4096;

	public $selected = '';
	public $base_url = '';
	
	public static function output($policy = self::LINK_TO_SUBPAGES, $base_url = '', $selected = '') {
		$w = new WidgetAlphaPager($base_url, $selected);
		return $w->render($policy);
	}

	public function __construct($base_url, $selected) {
		$this->selected = $selected;
		$this->base_url = $base_url ? $base_url : Url::current()->build(Url::RELATIVE);
		$this->base_url = rtrim($this->base_url, '/') . '/';
	}
	
	public function render($policy = self::NONE) {
		$letters = range('a', 'z');
		if (Common::flag_is_set($policy, self::INCLUDE_NUMERIC)) {
			array_unshift($letters, '0-9');
		}
		$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/alphapager');
		$view->assign('letters', $letters);
		$view->assign('base_url', $this->base_url);
		$view->assign('selected', $this->selected);
		$view->assign('policy', $policy);
		return $view->render();
	}	
}