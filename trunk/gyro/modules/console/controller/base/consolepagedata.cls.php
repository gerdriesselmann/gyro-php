<?php
/**
 * A console implementation of page data
 * 
 * Sets ConsoleRenderDecorator as default render decorator and
 * switches page template to console's.
 * 
 * @author Gerd Riesselmann
 * @ingroup Console
 */
class ConsolePageData extends PageData {
	/**
	 * Constructor
	 *
	 * @param ICacheManager $cache_manager
	 * @param array $get Usually $_GET
	 * @param array $post Usually $_POST
	 */
	public function __construct($cache_manager, $get, $post) {
		parent::__construct($cache_manager, $get, $post);
		$this->page_template = 'console/page';
		$this->add_render_decorator_class('ConsoleRenderDecorator');
	}
}
