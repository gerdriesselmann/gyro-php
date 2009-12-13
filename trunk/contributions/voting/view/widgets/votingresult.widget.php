<?php
/**
 * A widget to print a voting result
 */
class WidgetVotingResult implements IWidget {
	public $instance;
	public $params;
	
	/**
	 * Output voting result for given instance
	 * 
	 * $params get set on view
	 * 
	 * @param IDataObject $instance
	 * @param array $params
	 */
	public static function output($instance, $params = array()) {
		$w = new WidgetVotingResult($instance, $params);
		return $w->render();
	}

	public function __construct($instance, $params) {
		$this->instance = $instance;
		$this->params = $params;
	}
	
	public function render($policy = self::NONE) {
  		Load::models('votes');
		$inst = $this->instance;
		$avg = 0;
		if ($inst instanceof DAOVotesaggregates) {
			$avg = $inst->get_average();
		}
		else {
			$avg = Votes::get_average_for_instance($inst);
		}
			
		$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/votingresult');
		$view->assign('average', $avg);
		$view->assign('instance', $inst);
		foreach(Arr::force($this->params, false) as $var => $value) {
			$view->assign($var, $value);
		}
		return $view->render();
	}
}