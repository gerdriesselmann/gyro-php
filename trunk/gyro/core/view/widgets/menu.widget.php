<?php
/**
 * A widget printing a menu based upon actions retrieved from given item
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class WidgetItemMenu implements IWidget {
	const SORTED = 128;
	const SEPARATE_COMMANDS = 256;
	
	public $item;
	public $context;
	public $params;
	public $aro;
	public $args;
	
	public static function output($item, $context = 'view', $params = false, $args = array(), $policy = self::NONE) {
		$w = new WidgetItemMenu($item, $context, $params, $args);
		return $w->render($policy);
	}
	
	public function __construct($item, $context = 'view', $params = false, $args = array()) {
		$this->item = $item;
		$this->context = $context;
		$this->params = $params;
		$this->aro = AccessControl::get_current_aro();
		$this->args = $args; 
	}
	
	public function render($policy = self::NONE) {
		$ret = '';
		$sources = $this->retrieve_actions($this->item);
		$commands = array();
		$actions = array();
		
		if (Common::flag_is_set($policy, self::SEPARATE_COMMANDS)) {
			$this->separate($sources, $actions, $commands);
		}
		else {
			$actions = $sources;
		}
		
		if (Common::flag_is_set($policy, self::SORTED)) {
			$this->sort($actions);
			$this->sort($commands);
		}
		
		if (count($actions)) {
			$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'core::widgets/menu');
			$view->assign('actions', $actions);
			
			if (count($commands)) {
				Load::tools('formhandler');
				$formhandler = new FormHandler('process_commands', 'process_commands', FormHandler::TOKEN_POLICY_REUSE);
				$formhandler->prepare_view($view);
			}
			
			$view->assign('commands', $commands);
			$view->assign('policy', $policy);
			$view->assign('css_class', Arr::get_item($this->args, 'css_class', 'list_menu'));
			$ret = $view->render();
		}
		
		return $ret;
	}
	
	/**
	 * Obtain actions from item , if suitable
	 * 
	 * @param IActionSource|array $item
	 * @return array
	 */
	protected function retrieve_actions($item) {
		$actions = array();
		if ($item instanceof IActionSource) {
			$actions = $this->item->get_actions($this->aro, $this->context, $this->params);
		}
		else if (is_array($item)) {
			$actions = $this->item;
		}			
		return $actions;
	}
	
	/**
	 * Divide actions into actions and commands
	 */
	protected function separate($arr_in, &$out_actions, &$out_commands) {
		foreach($arr_in as $action) {
			// commands
			if ($action instanceof ICommand) {
				$out_commands[] = $action;
			}
			else if ($action instanceof IAction) {
				$out_actions[] = $action;
			}
		}
	} 
	
	/**
	 * Sort callback to sort by description
	 * 
	 * @param IAction $a
	 * @param IAction $b
	 * @return int
	 */
	protected function sort_compare_callback($a, $b) {
		$da = $a->get_description();
		$db = $b->get_description();
		if ($da == $db) {
        	return 0;
    	}
    	return ($da < $db) ? -1 : 1;
	}
	
	/**
	 * Sort by Description
	 */
	protected function sort(&$arr_actions) {
		usort($arr_actions, array($this, 'sort_compare_callback'));
	}
}
