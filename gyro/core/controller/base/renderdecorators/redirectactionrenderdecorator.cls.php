<?php
/**
 * Redirect to given action 
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 * 
 * @attention This class is not ready yet - don't use
 */
class RedirectActionRenderDecorator extends RenderDecoratorBase {
	/**
	 * Url to redirect to
	 *
	 * @var string
	 */
	private $action = null;

	/**
	 * Constructor
	 */
	public function __construct($action) {
		$this->action = $action;
	}

	/**
	 * Initialize this decorator and the data passed
	 *
	 * @param PageData $page_data
	 * @return void
	 */
	public function initialize($page_data) {
		$url = ActionMapper::get_url($this->action);
		$redirect_type = Config::has_feature(Config::TESTMODE)
			? Url::TEMPORARY
			: Url::PERMANENT;
		Url::create($url)->redirect($redirect_type);
		exit;
	}
}