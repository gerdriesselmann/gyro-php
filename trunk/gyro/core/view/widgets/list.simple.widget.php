<?php
/**
 * A generic list, but without pager, sorting et al.
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class WidgetListSimple implements IWidget {
	protected $items;
	protected $empty_message;
	
	public static function output($items, $empty_message = '', $policy = self::NONE) {
		$widget = new WidgetListSimple($items, $empty_message);
		return $widget->render($policy);			
	} 

	public function __construct($items, $empty_message = '') {
		$this->items = $items;
		$this->empty_message = $empty_message;	
	} 
	
	public function render($policy = self::NONE) {
		$ret = '';
		$items = Arr::force($this->items, false);
		$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/list.simple');
		$view->assign('items', $this->render_items($this->page_data, $items, $policy));
		$view->assign('policy', $policy);
		$view->assign('empty_message', $this->empty_message);
		$ret = $view->render();
		return $ret;
	}
	
	protected function render_items($page_data, $items, $policy) {
		$ret = array();
		$i = 1;
		$c = count($items);
		foreach($items as $item) {
			$cls = array('listitem');
			if ($item instanceof IDataObject) {
				$cls[] = 'listitem-' . $item->get_table_name();
			}
			if ($i == 1) { $cls[] = 'first'; }
			if ($i == $c) { $cls[] = 'last'; }
			$ret[] = html::div(WidgetListItem::output($item, $policy), implode(' ', $cls));
			$i++;
		}		
		return $ret;
	}	
}