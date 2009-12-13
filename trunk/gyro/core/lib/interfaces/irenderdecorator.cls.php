<?php
require_once dirname(__FILE__) . '/irenderer.cls.php';

/**
 * Decorates the rendering process
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface IRenderDecorator {
	/**
	 * Add a new decorator to end of decorator chain
	 *
	 * @param IRenderDecorator $decorator
	 * @return void
	 */
	public function append($decorator);
	
	/**
	 * Return next renderer in chain
	 *
	 * @return IRenderDecorator
	 */
	public function get_next();

	/**
	 * Initialize this decorator and the data passed
	 *
	 * @param PageData $page_data
	 * @return void
	 */
	public function initialize($page_data);

	/**
	 * Render content
	 *
	 * @param PageData $page_data
	 * @return void
	 */
	public function render_content($page_data);
	
	/**
	 * Render page
	 *
	 * @param PageData $page_data
	 * @param IRenderDecorator Decorator to invoke render_content upon
	 * @param int $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 * @return mixed Success information
	 */
	public function render_page($page_data, $content_render_decorator, $policy = 0);
}

