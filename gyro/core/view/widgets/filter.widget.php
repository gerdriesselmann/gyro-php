<?php
/**
 * A widget printing filters
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class WidgetFilter implements IWidget {
	const DONT_INDEX_FILTERED = 2048;
	const DONT_CHANGE_TITLE = 4096;	
	
	public $data;
	
	public static function output($data, $policy = self::NONE) {
		$w = new WidgetFilter($data);
		return $w->render($policy);
	}
	
	public function __construct($data) {
		$this->data = $data;
	}
	
	public function render($policy = self::NONE) {
		$builder = Arr::get_item($this->data, 'filter_url_builder', false);
		$page_data = Arr::get_item($this->data, 'page_data', false);
		if (empty($builder) || empty($page_data)) {
			return '';
		}
		
		$groups = Arr::force(Arr::get_item($this->data, 'filter_groups', array()));
		$ret = '';
		foreach($groups as $group) {
			$view = ViewFactory::create_view(IViewFactory::MESSAGE, $group->get_meta_template_name());
			$view->assign('filter_group', $group);
			$view->assign('page_data', $page_data);
			$view->assign('policy', $policy);
			$view->render(); // No output!
			
			$view = ViewFactory::create_view(IViewFactory::MESSAGE, $group->get_template_name());
			$view->assign('filter_group', $group);
			$view->assign('filter_url_builder', $builder);
			$view->assign('page_data', $page_data);
			$view->assign('policy', $policy);
			$ret .= $view->render();
		}
		if ($ret) {
			$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/filter.wrapper');
			$view->assign('content', $ret);
			$ret = $view->render();
		}
		return $ret;
	}
}
