<?php
require_once dirname(__FILE__) . '/renderdecoratorbase.cls.php';

/**
 * Caching implementation 
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class CacheRenderDecorator extends RenderDecoratorBase {
	/**
	 * Desired Cache Manager
	 *
	 * @var ICacheManager
	 */
	private $chache_manager = null;

	/**
	 * Constructor
	 *
	 * @param ICacheManager $cache_manager Desired Cache Manager
	 * @return void
	 */
	public function __construct($cache_manager) {
		$this->cache_manager = $cache_manager;
	}

	/**
	 * Initialize this decorator and the data passed
	 *
	 * @param PageData $page_data
	 * @return void
	 */
	public function initialize($page_data) {
		if ($this->cache_manager) {
			$page_data->set_cache_manager($this->cache_manager);
		}
		parent::initialize($page_data);
	}
}