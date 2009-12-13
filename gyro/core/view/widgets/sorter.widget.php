<?php
/**
 * Prints sorting controls
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class WidgetSorter implements IWidget {
	const DONT_INDEX_SORTED = 2048;
	const DONT_CHANGE_TITLE = 4096;
	
	public $data;
	
	public static function output($data, $policy = self::NONE) {
		$w = new WidgetSorter($data);
		return $w->render($policy);
	}
	
	public function __construct($data) {
		$this->data = $data;
	}
	
	public function render($policy = self::NONE) {
		if (Arr::get_item($this->data, 'columns_total', 0) < 2) {
			return '';
		}
		
		$policy = $policy | ($this->data['policy'] * 2048); // Compatability

		$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/sorter.meta');
		$view->assign('sorter_data', $this->data);
		$view->assign('page_data', $this->data['page_data']);
		$view->assign('policy', $policy);
		$view->render(); // this view should not return anything! 
				
		$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'core::widgets/sorter');
		$view->assign('sorter_data', $this->data);
		$view->assign('page_data', $this->data['page_data']);
		$view->assign('policy', $policy);
		return $view->render();
	}
}
