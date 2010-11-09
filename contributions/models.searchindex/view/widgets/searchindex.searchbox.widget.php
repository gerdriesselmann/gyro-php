<?php
/**
 * Widget that prints a search box
 * 
 * @author Gerd Riesselmann
 * @ingroup SearchIndex
 */
class WidgetSearchIndexSearchBox implements IWidget {
	const CONTEXT_CONTENT = 0;
	const CONTEXT_SIDE = 512;
	const CONTEXT_HEAD = 1024;

	public static function output($policy = self::CONTEXT_CONTENT) {
		$w = new WidgetSearchIndexSearchBox();
		return $w->render($policy);
	}
	
	public function render($policy = self::CONTEXT_CONTENT) {
		$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'searchindex/searchbox', false);
		$view->assign('policy', $policy);
		return $view->render();
	}
}