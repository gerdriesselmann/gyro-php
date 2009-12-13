<?php
require_once dirname(__FILE__) . '/viewbase.cls.php';

/**
 * Base class for Views that produce content
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class ContentViewBase extends ViewBase {
	/**
	 * Page Data
	 *
	 * @var PageData
	 */
	protected $page_data = null;
	
	/**
	 * Contructor takes a name and the page data
	 */	
	public function __construct($name, $page_data) {
		parent::__construct($name, '');
		if (empty($page_data)) {
			throw new Exception('ContentView called with empty PageData');
		}
		$this->page_data = $page_data;
	} 
	
	public function render($policy = self::NONE) {
		$ret = parent::render($policy);
		$this->page_data->content = $ret;
		return $ret;
	}	

	/**
	 * Assign all default vars
	 *
	 * @param int $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 */
	protected function assign_default_vars($policy) {
		parent::assign_default_vars($policy);
		$this->assign('page_data', $this->page_data);
		if ($this->page_data->router) {
			$this->assign('route_id', $this->page_data->router->get_route_id());
		}
	}
	
	/**
	 * Returns PageData
	 * 
	 * @return PageData
	 */
	public function get_page_data() {
		return $this->page_data;		
	}
}
