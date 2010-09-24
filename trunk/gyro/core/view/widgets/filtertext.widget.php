<?php
/**
 * A widget printing text filter
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class WidgetFilterText implements IWidget {
	const DONT_INDEX_FILTERED = 2048;
	const DONT_CHANGE_TITLE = 4096;		
	
	public $data;	
	
	public static function output($data, $policy = self::NONE) {
		$w = new WidgetFilterText($data);
		return $w->render($policy);
	}
	
	public function __construct($data) {
		$this->data = $data;
	}
	
	public function render($policy = self::NONE) {
		$out = '';
		foreach (Arr::force($this->data, false) as $item) {
			$adapter = Arr::get_item($item, 'adapter', false);
			if (empty($adapter)) {
				continue;
			}
			
			$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/filtertext.meta');
			$view->assign_array($item);
			$view->assign('policy', $policy);			
			$view->render(); // No output!
			
			$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/filtertext');
			$view->assign_array($item);
			$view->assign('policy', $policy);			
			$out .= $view->render();
		}
		return $out;
	}
}
