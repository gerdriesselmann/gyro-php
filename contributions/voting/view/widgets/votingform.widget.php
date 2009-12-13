<?php
/**
 * A widget to print a voting result
 */
class WidgetVotingForm implements IWidget {
	public $instance;
	
	public static function output($instance, $policy = self::NONE) {
		$w = new WidgetVotingForm($instance);
		return $w->render($policy);
	}

	public function __construct($instance) {
		$this->instance = $instance;
	}
	
	public function render($policy = self::NONE) {
  		Load::models('votes');
  		$inst = $this->instance;
		$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/votingform');
		$view->assign('instance', $inst);
		$view->assign('action', ActionMapper::get_path('voting_vote', $inst));
		$view->assign('policy', $policy);
		return $view->render();
	}
}