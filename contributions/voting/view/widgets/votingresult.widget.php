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
  		Load::models('votesaggregates');
		$inst = $this->instance;
		$dao_avg = ($inst instanceof DAOVotesaggregates) ? $inst : VotesAggregates::get_for_instance($inst);
		$avg = ($dao_avg) ? $dao_avg->get_average() : 0;
		$count = ($dao_avg) ? $dao_avg->numtotal : 0;
			
		$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/votingresult');
		$view->assign('average', $avg);
		$view->assign('count', $count);
		$view->assign('instance', $inst);
		foreach(Arr::force($this->params, false) as $var => $value) {
			$view->assign($var, $value);
		}
		return $view->render();
	}
}