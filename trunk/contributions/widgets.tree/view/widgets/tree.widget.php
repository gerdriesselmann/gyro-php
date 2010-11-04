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
		$tree = $this->build_tree_array($this->params, $policy, $branch);
		
		$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/tree');
		$view->assign('tree', $tree);
		return $view->render();
	}
	
	/**
	 * Build branch of leaf, that is chain of leaf and leaf's parent
	 */
	protected function build_branch_array($params, $policy) {
		$ret = array();
		$leaf = Arr::get_item($params, self::P_LEAF, false);
		if ($leaf) {
			$item = $leaf;
			while($item) {
				array_unshift($ret, $item);
				$item = $item->get_parent();
			}			
		}
		return $ret;
	}
	
	/**
	 * Build the tree
	 */
	protected function build_tree_array($params, $policy, $branch) {
		$max_level = Common::flag_is_set($policy, self::OPEN_UNTIL_LEVEL) ? Arr::get_item($params, self::P_LEVEL, 0) : 0;
		$roots = Arr::force(Arr::get_item($params, self::P_ROOTS, array()), false);
		return $this->create_level(0, $max_level, $roots, $policy, $branch);
	}
	
	/**
	 * Creates a level that is a set of child nodes
	 * 
	 * @param int $level Deepness of level
	 * @param int $max_level Maximum levels to be expanded by default
	 * @param array $items Nodes of parent level
	 * @param int $policy Render policy
	 * @param array $branch Elements that for a branch to a given leaf. Branches always get expanded  
	 */
	protected function create_level($level, $max_level, $items, $policy, $branch) {
		$ret = array();
		// If has next level, create a next level 
		$has_next_level = ($level < $max_level);
		// Extract current branch item
		$branch_item = array_shift($branch);
		foreach($items as $item) {
			// $is_branch indicates if a branch is processed
			$is_branch = ($branch_item) ? $item->is_same_as($branch_item) : false;
			// True if last element on branch is reached
			$is_leaf = $is_branch && (count($branch) == 0);
			if ($is_branch) {
				// A branch is always expanded up to leaf
				// But expand leaf itself only if render policy states so
				$expand_branch = (!$is_leaf || Common::flag_is_set($policy, self::OPEN_LEAF));
				if ($expand_branch) {
					$childs = $this->create_level($level + 1, $max_level, $item->get_childs(), $policy, $branch);
				}
			}
			else if ($has_next_level) {
				// Expand next level that is not a branch 
				$childs = $this->create_level($level + 1, $max_level, $item->get_childs(), $policy, array());
			}
			else {	
				$childs = array();
			}
			$ret[] = $this->create_node($item, $childs, $is_branch, $is_leaf);
		}
		return $ret;
	}
	
	protected function create_node($item, $childs, $is_branch, $is_leaf) {
		return array('item' => $item, 'childs' => $childs, 'is_branch' => $is_branch, 'is_leaf' => $is_leaf);
	}
}