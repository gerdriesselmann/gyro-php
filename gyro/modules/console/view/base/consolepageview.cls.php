<?php
/**
 * Render page for console display
 * 
 * @author Gerd Riesselmann
 * @ingroup Console
 */
class ConsolePageView extends PageViewBase {
	public function __construct($page_data, $file = false) {
		parent::__construct($page_data, 'console/page');
	}
		
	
	/**
	 * Process view and returnd the created content
	 *
	 * @param int $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 * @return string The rendered content
	 */
	public function render($policy = self::NONE) {
		$policy |= self::CONTENT_ONLY | self::NO_CACHE;
		return parent::render($policy);
	}
	
	/**
	 * Sets content
	 * 
	 * @param mixed $rendered_content The content rendered
	 * @param int $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 * @return void
	 */
	protected function after_render(&$rendered_content, $policy) {
	}	


	/**
	 * Called before content is rendered, always
	 * 
	 * @param int $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 * @return void
	 */
	protected function render_preprocess($policy) {
	}	
	
	/**
	 * Called after content is rendered, always
	 * 
	 * @param mixed $rendered_content The content rendered
	 * @paramint $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 * @return void
	 */
	protected function render_postprocess(&$rendered_content, $policy) {
		$this->send_status();
	}

	/**
	 * Returns true, if cache should be used
	 *
	 * @return bool
	 */
	protected function should_cache() {
		return false;
	}	
	

	/**
	 * Assign variables
	 * 
	 * @param int $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 */
	protected function assign_default_vars($policy) {
		$this->assign('page_data', $this->page_data);
		$this->assign('status', $this->page_data->status);
		$this->assign('status_code', $this->page_data->status);
		$this->assign('content', $this->page_data->content);
	}
}
