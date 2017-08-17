<?php
/**
 * Class to create a filter widget and to filter search results
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */ 
class Filter implements IDBQueryModifier {
    const FILTER_POLICY_NONE    = 0;
    const FILTER_POLICY_SESSION = 1;
    
	protected $filter_data = array();
	/**
	 * Adapter to process Urls
	 * 
	 * @var IFilterAdapter
	 */
	protected $adapter;
    /**
     * Policy
     *
     * @var int $policy
     */
    protected $policy;

	/**
	 * Contructor
	 * 
	 * @param PageData $page_data 
	 * @param Mixed 
	 *   Either a single DBFilterGroup instance or an array of it or an instance of ISearchAdapter.
	 *   In later case, get_filters() is invoked in the search adapter. 
	 * @param IFilterAdapter $adapter 
	 */
	public function __construct($page_data, $filtergroups, $adapter = false, $policy = self::FILTER_POLICY_NONE) {
		$this->adapter = ($adapter instanceof IFilterAdapter) ? $adapter : new FilterDefaultAdapter($page_data);
		$this->policy  = $policy;
        
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
        
        $default_value = $filtergroup->get_default_key();
        $group_id = $filtergroup->get_group_id();
        $session_group_id = 'flt'.$group_id;
        if ($this->policy == self::FILTER_POLICY_SESSION) {
            if (isset($_SESSION[$session_group_id])) {
                $filter_key = $_SESSION[$session_group_id];
                if ($filtergroup->get_filter($filter_key) !== false) {
                    $default_value = $filter_key;
                }
            }
        }
		$current_key = $this->adapter->get_current_key($group_id, $default_value);
        
        if ($this->policy == self::FILTER_POLICY_SESSION) {
            $_SESSION[$session_group_id] = $current_key;
        }        
		$filtergroup->set_current_key($current_key);		
	}
	
	/**
	 * Apply this filter to a query
	 */
	public function apply($query) {
		// Simply delegate to current filter
		foreach($this->filter_data['filter_groups'] as $filtergroup) {
			$query->apply_modifier($filtergroup);
		}
	}
		
	public function prepare_view($view) {
		$view->assign('filter_data', $this->filter_data);
	}
	
	/**
	 * Prepare URL so filter gets applied
	 * 
	 * @param Url Instance of URL class. This instance is changed.
	 * @param string Filter to append
	 * 
	 * @return void
	 * 
	 * @deprecated Used function on FilterAdapter instead
	 */
	public static function apply_to_url($url, $filter, $group_id = '') {
		return FilterDefaultAdapter::apply_to_url($url, $filter, $group_id);
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
	protected $page_data;
	
	public function __construct($page_data, $param = 'fl') {
		$this->page_data = $page_data;
		$this->param = $param;
	}
	
	public function get_current_key($group_id, $default = '') {
		$query_key = 'fl' . GyroString::plain_ascii($group_id);
		return $this->page_data->get_get()->get_item($query_key, $default);		
	}

	public function get_filter_link($filter, $group_id) {
		$key = $filter->is_default() ? '' : $filter->get_key();
		
		$url = Url::current();
		self::apply_to_url($url, $key, $group_id, $this->param);
		return $url->build(Url::RELATIVE);	
	}
	
	/**
	 * Prepare URL so filter gets applied
	 * 
	 * @param Url Instance of URL class. This instance is changed.
	 * @param string Filter to append
	 * 
	 * @return void
	 * 
	 * @deprecated Used function on FilterAdapter instead
	 */
	public static function apply_to_url($url, $filter, $group_id = '', $parameter = 'fl') {
		$url->replace_query_parameter($parameter . GyroString::plain_ascii($group_id), $filter);
	}	
}
