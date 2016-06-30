<?php
/**
 * Prints list, and ALPHA pager
 */
class WidgetAlphaList implements IWidget {
	/**
	 * Link to fragment (#Letter) from pager
	 */
	const LINK_TO_FRAGMENT = 0;
	/**
	 * Link to subpages Url::current() + letter from pager
	 */
	const LINK_TO_SUBPAGES = 256;
	/**
	 * Do not print a pager
	 */
	const NO_PAGER = 512;
	/**
	 * Put a lin kto subpages below each section
	 */
	const LINK_TO_SUBPAGE_BELOW_SECTION = 1024;

	const INCLUDE_NUMERIC = 4096;
	
	public $items;
	public $params;
	
	/**
	 * Constructor
	 * 
	 * @param array $params associative array with members 'css_class' and 'more_title'
	 */
	public static function output($items, $params = array(), $policy = self::LINK_TO_SUBPAGES) {
		if (is_string($params)) {
			$params = array('css_class' => $params);
		}
		$w = new WidgetAlphaList($items, $params);
		return $w->render($policy);
	}
	
	public function __construct($items, $params) {
		$this->items = $items;
		$this->params = $params;
	}
	
	public function render($policy = self::NONE) {
		$ret = '';
		if (count($this->items) > 10) {
			if (!Common::flag_is_set($policy, self::NO_PAGER)) {
				$ret .= WidgetAlphaPager::output($policy);
			}

			$data = array_fill_keys(range('a', 'z'), array());
			if (Common::flag_is_set($policy, self::INCLUDE_NUMERIC)) {
				$data = array_merge(array('0-9' => array()), $data);
			}
			// TODO Does not handle numbers
			foreach($this->items as $key => $value) {
				$clean = GyroString::plain_ascii($key);
				$data[substr($clean, 0, 1)][] = $value;
			}

			$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/alphalist', false);
			$view->assign('data', $data);
			$view->assign('items', $this->items);
			$view->assign('params', $this->params);
			$view->assign('policy', $policy);
		}
		else {
			ksort($this->items);
			$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/alphalist/empty', false);
		}
		$view->assign('items', $this->items);
		$view->assign('params', $this->params);
		$view->assign('policy', $policy);
		$ret .= $view->render();
		return $ret;
	}	
}