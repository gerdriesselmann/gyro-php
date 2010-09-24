<?php
/**
 * A generic list's item
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class WidgetListItem implements IWidget {
	protected $page_data;
	protected $item;
	
	public static function output(PageData $page_data, $item, $policy = self::NONE) {
		$widget = new WidgetListItem($page_data, $item);
		return $widget->render($policy);			
	} 

	public function __construct(PageData $page_data, $item) {
		$this->page_data = $page_data;
		$this->item = $item;
	} 
	
	public function render($policy = self::NONE) {
		$ret = '';
		$model = $this->item->get_table_name();
		$view = $this->create_item_view($model);
		$view->assign('item', $this->item);
		$view->assign('page_data', $page_data);
		$view->assign('policy', $policy);
		$ret = $view->render();
		return $ret;
	}
	
	protected function create_item_view($model) {
		$paths = array(
			$model . '/inc/listitem',
			'widgets/listitem'
		); 		
		return ViewFactory::create_view(IViewFactory::MESSAGE, $paths);
	}
}