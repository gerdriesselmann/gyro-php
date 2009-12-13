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
	private $target_path = null;

	/**
	 * Constructor
	 *
	 * @param ICacheManager $cache_manager Desired Cache Manager
	 * @return void
	 */
	public function __construct($target_path) {
		$this->target_path = $target_path;
	}

	/**
	 * Initialize this decorator and the data passed
	 *
	 * @param PageData $page_data
	 * @return void
	 */
	public function initialize($page_data) {
		Url::current()->clear_query()->set_path($this->target_path)->redirect(Url::PERMANENT);
		exit;
	}
}