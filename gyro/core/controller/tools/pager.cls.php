<?php
/**
 * Class to handle pagination
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class Pager implements IDBQueryModifier {
	/**
	 * @deprecated Use Widget Constants instead!
	 */
	const DONT_INDEX_PAGE_2PP = 1;
	/**
	 * @deprecated Use Widget Constants instead!
	 */
	const DO_INDEX_PAGE_2PP = 0;
	
	protected $pager_data = array();
	/**
	 * Adapter to process and create urls 
	 * 
	 * @var IPagerAdapter
	 */
	protected $adapter = null;
	
	/**
	 * Constructor
	 * 
	 * @param PageData $page_data
	 * @param int Total numbers of items
	 * @param int Number of items per page 
	 * @param IPagerAdapter Adapter. For compatability reasons, this is also interpreted as a policy, if you pass an integer 
	 */
	public function __construct($page_data, $items_total, $items_per_page, $adapter = false) {
		$this->adapter = ($adapter instanceof IPagerAdapter) ? $adapter : new PagerDefaultAdapter($page_data, 'page');
		
		$this->pager_data['page_data'] = $page_data;
		$this->pager_data['policy'] = (is_int($adapter)) ? $adapter : 0;
		$this->pager_data['items_per_page'] = $items_per_page;
		$this->pager_data['items_total'] = $items_total;
		$this->pager_data['pages'] = array();
		$this->pager_data['previous_link'] = '';
		$this->pager_data['next_link'] = '';	
		
		// We asume an url like x/y/?page=2, where page=2 indicates page 2 of pagination		
		$page = $this->adapter->get_current_page();
		if ($page <= 0) {
			$page = 1;
		}

		$page_total = 0;
		if ($items_per_page > 0) {
			$page_total = ceil($items_total / (float)$items_per_page);
		}
		if ($page_total <= 0) {
			$page_total = 1;
		}		

		if ($page > $page_total) {
			$page = $page_total;
		}
		
		$this->pager_data['page'] = $page;
		$this->pager_data['pages_total'] = $page_total;
		
		$this->pager_data['start_record'] = ($page - 1) * $items_per_page;
	} 
		
	/**
	 * Returns startign record (0-based)
	 */
	public function get_start() {
		return $this->pager_data['start_record'];
	}	
	
	/**
	 * Returns number of items per page
	 */
	public function get_items_per_page() {
		return $this->pager_data['items_per_page'];
	}
	
	/**
	 * Generate pager realted data and set it on view
	 */
	public function prepare_view($view) {
		$page = $this->pager_data['page'];
		$page_total = $this->pager_data['pages_total'];		
		if ($page_total > 1) {
			if ($page > 1) {
				$this->pager_data['previous_link'] = $this->get_url_for_page($page - 1);
			}
			if ($page < $page_total) {
				$this->pager_data['next_link'] = $this->get_url_for_page($page + 1);
			}
		
			for($i = 1; $i <= $page_total; $i++) {
				$this->pager_data['pages'][] = array(
					'page' => $i,
					'url' => $this->get_url_for_page($i)
				); 
			}
		}
		$view->assign('pager_data', $this->pager_data);
	}
	
	/**
	 * Compute url for page
	 */
	protected function get_url_for_page($page) {
		return $this->adapter->get_url_for_page($page);
	}

	/**
	 * Apply this filter to a query
	 */
	public function apply($query) {
		$query->limit($this->get_start(), $this->get_items_per_page());
	}		
	
	/**
	 * Prepare URL so filter gets applied
	 * 
	 * @param Url Instance of URL class. This instance is changed.
	 * @param string Filter to append
	 *
	 * @return void
	 * 
	 * @deprecated Use PagerDefaultAdapter::apply_to_url() instead
	 */
	public static function apply_to_url($url, $page) {
		PagerDefaultAdapter::apply_to_url($url, $page);
	}		
}

/**
 * Default Implementation of Pager adapter 
 * 
 * Uses GET-Parameters as pager parameters
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class PagerDefaultAdapter implements IPagerAdapter {
	protected $parameter;
	protected $get;
	
	public function __construct(PageData $page_data, $parameter) {
		$this->parameter = $parameter;
		$this->get = $page_data->get_get();
	} 
	
	/**
	 * Return current page
	 * 
	 * @param PageData $page_data
	 * @return int
	 */
	public function get_current_page() {
		return intval($this->get->get_item($this->parameter, 1));
	}
	
	/**
	 * Compute url for page
	 * 
	 * @return Url
	 */
	public function get_url_for_page($page) {
		$url = Url::current();
		self::apply_to_url($url, $page, $this->parameter);
		return $url->build(Url::RELATIVE);
	}

	/**
	 * Prepare URL so filter gets applied
	 * 
	 * @param Url Instance of URL class. This instance is changed.
	 * @param string Filter to append
	 * 
	 * @return void
	 */
	public static function apply_to_url($url, $page, $parameter = 'page') {
		$p = Cast::int($page);
		if ($p <= 1) {
			$p = '';
		}
		$url->replace_query_parameter($parameter, $p);
	}		
}