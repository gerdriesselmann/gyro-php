<?php
/**
 * A widget printing a message in style defined by policy.
 *
 * @attention Content passed is not escaped, but printed as is.
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class WidgetAlert implements IWidget {
	const ERROR = 256;
	const WARNING = 512;
	const INFO = 1024;
	const SUCCESS = 2048;
	const NOTE = 4096;

	public $content;

	public static function output($content, $policy = self::NONE) {
		$w = new WidgetAlert($content);
		return $w->render($policy);
	}
	
	public function __construct($content) {
		$this->content = $content;
	}
	
	public function render($policy = self::NONE) {
		$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/alert', false);
		$view->assign('content', $this->content);
		$view->assign('policy', $policy);
		return $view->render();
	}
}
