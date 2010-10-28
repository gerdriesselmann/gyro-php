<?php
/**
 * A widgets that prints a tree, sonsiting of nested <li>
 */
class WidgetTree implements IWidget {
	const OPEN_UNTIL_LEVEL = 512;
	const OPEN_LEAF = 1024;

	/**
	 * "level" parameter name
	 */
	const P_LEVEL = 'level';
	/**
	 * "leaf" parameter name
	 */
	const P_LEAF = 'leaf';
	/**
	 * "roots" parameter name
	 */
	const P_ROOTS = 'roots';
	
	public $params = array();
	
	public static function output($params, $policy = self::OPEN_LEAF) {
		$w = new WidgetTree($params);
		return $w->render($policy);
	}
	
	public function __construct($params) {
		$this->params = $params;
	}
	
	public function render($policy = self::NONE) {
		// First build an array with type/id as key and title, url and childs as members
		$branch = $this->build_branch_array($this->params, $policy); 
		$tree = $this->build_roots_array($this->params, $policy, $branch);
		
		$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/tree');
		$view->assign('tree', $tree);
		return $view->render();
	}
	
	protected function build_branch_array($params, $policy) {
		$ret = array();
		$leaf = Arr::get_item($params, self::P_LEAF, false);
		if ($leaf) {
			$open_leaf = Common::flag_is_set($policy, self::OPEN_LEAF);
			$item = ($open_leaf) ? $leaf : $leaf->get_parent();
			while($item) {
				array_unshift($ret, $item);
				$item = $item->get_parent();
			}			
		}
		return $ret;
	}
	
	protected function build_roots_array($params, $policy, $branch) {
		$max_level = Common::flag_is_set($policy, self::OPEN_UNTIL_LEVEL) ? Arr::get_item($params, self::P_LEVEL, 0) : 0;
		$roots = Arr::force(Arr::get_item($params, self::P_ROOTS, array()), false);
		return $this->create_level(0, $max_level, $roots, $policy, $branch);
	}
	
	protected function create_level($level, $max_level, $items, $policy, $branch) {
		$ret = array();
		$has_next_level = ($level < $max_level);
		$branch_item = array_shift($branch);
		foreach($items as $item) {
			if ($branch_item && $item->is_same_as($branch_item)) {
				$childs = $this->create_level($level + 1, $max_level, $item->get_childs(), $policy, $branch);		
			}
			else if ($has_next_level) {
				$childs = $this->create_level($level + 1, $max_level, $item->get_childs(), $policy, array());
			}
			else {	
				$childs = array();
			}
			$ret[] = $this->create_node($item, $childs);
		}
		return $ret;
	}
	
	protected function create_node($item, $childs) {
		return array('item' => $item, 'childs' => $childs);
	}
}