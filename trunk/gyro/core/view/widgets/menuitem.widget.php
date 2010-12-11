<?php
/**
 * A widget printing one menu item.
 * 
 * This widget resuses the templates widgets/menu/action and widgets/menu/command
 * depending on the type of the given action
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class WidgetItemMenuItem implements IWidget {
	public $action;

	/**
	 * Convenience wrapper
	 * 
	 * @param IAction $action
	 */
	public static function output($action, $policy = self::NONE) {
		$w = new WidgetItemMenuItem($action);
		return $w->render($policy);
	}
	
	public function __construct($action) {
		$this->action = $action;
	}
	
	public function render($policy = self::NONE) {
		$ret = '';
		$is_command = ($this->action instanceof ICommand);  
		$template =  $is_command ? 'widgets/menu/command' : 'widgets/menu/action';
		$view = ViewFactory::create_view(IViewFactory::MESSAGE, $template);
		$view->assign('action', $this->action);
		if ($is_command) {
			Load::tools('formhandler');
			$formhandler = new FormHandler('process_commands', 'process_commands', FormHandler::TOKEN_POLICY_REUSE);
			$formhandler->prepare_view($view);
		}
		
		$ret = $view->render();
		return $ret;
	}
}
