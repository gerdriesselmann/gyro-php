<?php
/**
 * Class to create a filter widget and to filter search results
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */ 
class Filter implements IDBQueryModifier {
	protected $filter_data = array();
	/**
	 * Adapter to process Urls
	 * 
	 * @var IFilterAdapter
	 */
	protected $adapter;

	/**
	 * Contructor
	 * 
	 * @param PageData $page_data 
	 * @param Mixed $filtergroups
	 *   Either a single DBFilterGroup instance or an array of it or an instance of ISearchAdapter.
	 *   In later case, get_filters() is invoked in the search adapter. 
	 *
	 * @param IFilterAdapter|false $adapter
	 */
	public function __construct($page_data, $filtergroups, $adapter = false) {
		$this->adapter = ($adapter instanceof IFilterAdapter) ? $adapter : new FilterDefaultAdapter($page_data);
		
		if ($filtergroups instanceof ISearchAdapter) {
			$filtergroups = $filtergroups->get_filters();
		}
		if (!is_array($filtergroups)) {
			$filtergroups = ($filtergroups) ? array($filtergroups) : array(new DBFilterGroup());
		}
		
		foreach($filtergroups as $filtergroup) {
			// Add "all" filter
			$this->prepare_filter_group($filtergroup);
		}
		
		$this->filter_data['filter_groups'] = $filtergroups;
		$this->filter_data['filter_url_builder'] = $this->adapter;
		$this->filter_data['page_data'] = $page_data;
	}
	
	/**
	 * Prepare a filter group
	 * 
	 * @param DBFilterGroup $filtergroup
	 */
	protected function prepare_filter_group($filtergroup) {
		if ($filtergroup->count() > 1) {
			$filtergroup->add_filter('all', new DBFilter(tr('Show All', 'core')));
			if (!$filtergroup->get_default_key()) {
				$filtergroup->set_default_key('all');
			}
		}
		$current_key = $this->adapter->get_current_key($filtergroup->get_group_id(), $filtergroup->get_default_key());
		$filtergroup->set_current_key($current_key);		
	}

	/**
	 * Apply this filter to a query
	 * @param ISearchAdapter $query
	 */
	public function apply($query) {
		// Simply delegate to current filter
		foreach($this->filter_data['filter_groups'] as $filtergroup) {
			$query->apply_modifier($filtergroup);
		}
	}

	/**
	 * @param IView $view
	 */
	public function prepare_view($view) {
		$view->assign('filter_data', $this->filter_data);
	}
	
	/**
	 * Prepare URL so filter gets applied
	 * 
	 * @param Url Instance of URL class. This instance is changed.
	 * @param string Filter to append
	 * @param string $group_id
	 *
	 * @return void
	 * 
	 * @deprecated Used function on FilterAdapter instead
	 */
	public static function apply_to_url($url, $filter, $group_id = '') {
		FilterDefaultAdapter::apply_to_url($url, $filter, $group_id);
	}
}

/**
 * Default Implementation of Filter adapter 
 * 
 * Uses GET-Parameters as filter parameters
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class FilterDefaultAdapter implements IFilterAdapter {
	protected $param;
	/**
	 * @var PageData
	 */
	protected $page_data;
	
	public function __construct($page_data, $param = 'fl') {
		$this->page_data = $page_data;
		$this->param = $param;
	}
	
	public function get_current_key($group_id, $default = '') {
		$query_key = 'fl' . GyroString::plain_ascii($group_id);
		return $this->page_data->get_get()->get_item($query_key, $default);		
	}

	/**
	 * @param DBFilter $filter
	 * @param string $group_id
	 *
	 * @return string
	 * @throws Exception
	 */
	public function get_filter_link($filter, $group_id) {
		$key = $filter->is_default() ? '' : $filter->get_key();
		
		$url = Url::current();
		self::apply_to_url($url, $key, $group_id, $this->param);
		return $url->build(Url::RELATIVE);	
	}

	/**
	 * Prepare URL so filter gets applied.
	 *
	 * @param Url $url Instance of URL class. This instance is changed.
	 * @param string $filter Filter to append	 *
	 * @param string $group_id
	 * @param string $parameter
	 *
	 * @return void
	 */
	public static function apply_to_url($url, $filter, $group_id = '', $parameter = 'fl') {
		$url->replace_query_parameter($parameter . GyroString::plain_ascii($group_id), $filter);
	}	
}
