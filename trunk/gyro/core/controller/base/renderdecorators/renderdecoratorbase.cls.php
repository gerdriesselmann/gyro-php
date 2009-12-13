<?php
/**
 * Default implementation if IRenderDecorator
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class RenderDecoratorBase implements IRenderDecorator {
	/**
	 * Next RenderDecorator
	 *
	 * @var IRenderDecorator
	 */
	private $next = null;
	
	/**
	 * Add a new decorator to end of decorator chain
	 *
	 * @param IRenderDecorator $decorator
	 * @return void
	 */
	public function append($decorator) {
		$next = $this->get_next();
		if ($next) {
			$next->append($decorator);
		}
		else {
			$this->next = $decorator;
		}
	}
	
	/**
	 * Return next renderer in chain
	 *
	 * @return IRenderDecorator
	 */
	public function get_next() {
		return $this->next;
	}
	
	/**
	 * Initialize this decorator and the data passed
	 *
	 * @param PageData $page_data
	 * @return void
	 */
	public function initialize($page_data) {
		$this->initialize_next($page_data);
	}

	/**
	 * Initialize next decorator in chain
	 *
	 * @param PageData $page_data
	 * @return void
	 */
	protected function initialize_next($page_data) {
		$next = $this->get_next();
		if ($next) {
			$next->initialize($page_data);
		}
	}

	/**
	 * Render content
	 *
	 * @param PageData $page_data
	 * @return void
	 */
	public function render_content($page_data) {
		$this->render_content_next($page_data);
	}
	
	/**
	 * Render content
	 *
	 * @param PageData $page_data
	 * @return void
	 */
	protected function render_content_next($page_data) {
		$next = $this->get_next();
		if ($next) {
			$next->render_content($page_data);
		}
	}
		
	/**
	 * Render page
	 *
	 * @param PageData $page_data
	 * @param IRenderDecorator Decorator to invoke render_content upon
	 * @param int $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 * @return void
	 */
	public function render_page($page_data, $content_render_decorator, $policy = IView::NONE) {
		return $this->render_page_next($page_data, $content_render_decorator, $policy);
	}
	
	/**
	 * Call render_page() on next decorator
	 *
	 * @param PageData $page_data
	 * @param IRenderDecorator Decorator to invoke render_content upon
	 * @param int $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 * @return string The rendered content
	 */
	protected function render_page_next($page_data, $content_render_decorator, $policy = IView::NONE) {
		$next = $this->get_next();
		if ($next) {
			return $next->render_page($page_data, $content_render_decorator, $policy);
		}
	}
}