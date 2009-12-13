<?php
/**
 * Class to create a filter widget and to filter search results by free text
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */ 
class FilterText implements IDBQueryModifier {
	/**
	 * 
	 * @var DBFilterColumn
	 */
	protected $filter_object;
	/**
	 * @var IFilterTextAdapter
	 */
	protected $adapter;
	protected $page_data;
	
	/**
	 * Contructor
	 * 
	 * @param PathStack 
	 * @param Array Array with filter names as keys and descriptions to display as values 
	 */
	public function __construct($page_data, $filtercolumn, $filtername, $operator, $adapter = false) {
		$this->adapter = ($adapter instanceof IFilterTextAdapter) ? $adapter : new FilterTextDefaultAdapter($page_data, String::plain_ascii($filtername));
		$this->page_data = $page_data;
		$this->filter_object = new DBFilterColumn(
			$filtercolumn, 
			$this->adapter->get_value(), 
			tr($filtername, 'app'), 
			$operator
		);
	}
	
	/**
	 * Modify given query
	 */
	public function apply($query) {
		if ($this->get_filter_value()) {
			$this->filter_object->apply($query);
		}
	}
	
	/**
	 * Return title of filter
	 * 
	 * @return unknown_type
	 */
	public function get_filter_title() {
		return $this->filter_object->get_title();
	}
	
	/**
	 * Return current value to be filter after
	 * 
	 * @return string
	 */
	public function get_filter_value() {
		return $this->filter_object->get_value();
	}

	/**
	 * Set all data on the view 
	 *
	 * @param IView $view
	 */
	public function prepare_view($view) {		
		$var_name = $this->get_view_var_name();
		$textfilter_data = Arr::force($view->retrieve($var_name), false);
		$textfilter_data[] = array(
			'title' => $this->get_filter_title(),
			'value' => $this->get_filter_value(),
			'filter_object' => $this->filter_object,
			'adapter' => $this->adapter,
			'page_data' => $this->page_data
		);
		$view->assign($var_name, $textfilter_data);
	}
	
	/**
	 * Returns the name of the variable that gets set on the view
	 *
	 * @return string
	 */
	protected function get_view_var_name() {
		return 'filtertext_data';
	}
}


/**
 * Default Implementation of Filter Text adapter 
 * 
 * Uses GET-Parameters as filter parameters
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class FilterTextDefaultAdapter implements IFilterTextAdapter {
	protected $param;
	protected $page_data;
	
	public function __construct($page_data, $param, $prefix = 'filter_') {
		$this->page_data = $page_data;
		$this->param = $prefix . $param;
	}

	/**
	 * Return current value to be filtered after
	 * 
	 * @return string
	 */
	public function get_value() {
		$reset = $this->page_data->get_get()->get_item($this->get_reset_param(), false);
		return ($reset) ? '' : $this->page_data->get_get()->get_item($this->get_param(), '');
	}

	public function get_param() {
		return $this->param;
	}
	
	public function get_reset_param() {
		return $this->param . '_reset_filter';
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
	public static function apply_to_url($url, $value, $parameter) {
		$url->replace_query_parameter($parameter, $value);
	}	
}

