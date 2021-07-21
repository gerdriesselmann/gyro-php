<?php
/**
 * Class to create a sorting widget and to process search results
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */ 
class Sorter implements IDBQueryModifier {
	/**
	 * @deprecated Use Widget Constants instead!
	 */
	const DONT_INDEX_SORTED = 1;
	/**
	 * @deprecated Use Widget Constants instead!
	 */
	const DO_INDEX_SORTED = 0;

	/**
	 * Adapter to work on URL
	 * 
	 * @var ISorterAdapter
	 */
	protected $adapter; 
	protected $sorter_data = array();
	
	/**
	 * Constructor
	 * 
	 * @param PageData $page_data
	 * @param array|ISearchAdapter $columns 
	 *   Associative array of DBSortColumns or instance of ISearchAdapter.
	 *   In later case get_sortable_columns() and get_sort_default_column() are called on search 
	 *   adapter. Value of $default_column_key is ignored.
	 * @param string $default_column Key for default sort column
	 * @param ISorterAdapter $adapter If integer is passed it is interpreted as policy for backward compatability
	 */
	public function __construct(PageData $page_data, $columns, $default_column_key = false, $adapter = false) {
		$this->adapter = ($adapter instanceof ISorterAdapter) ? $adapter : new SorterDefaultAdapter($page_data);
		if ($columns instanceof ISearchAdapter) {
			$default_column_key = $columns->get_sort_default_column();
			$columns = $columns->get_sortable_columns();
		}
		$this->sorter_data['policy'] = is_int($adapter) ? $adapter : 0;
		$this->sorter_data['page_data'] = $page_data;
		$this->sorter_data['column_objects'] = $columns;
		$this->sorter_data['columns_total'] = count($columns);
		$this->sorter_data['default_column_key'] = $default_column_key;
		
		// Get sort column objects
		$column_key =  $this->adapter->get_column();
		if (empty($column_key)) {
			$column_key = $default_column_key;
		}
		$column_obj = Arr::get_item($columns, $column_key, false);
		if ($column_obj == false) {
			$column_obj = Arr::get_item($columns, 0, false);
		}
		$this->sorter_data['current_column_key'] = $column_key;
		$this->sorter_data['current_column_object'] = $column_obj;

		// Set order
		$default_order = ($column_obj) ? $column_obj->get_direction() : DBSortColumn::ORDER_FORWARD;
		$order = $this->adapter->get_order();
		if (empty($order)) {
			$order = $default_order;
		}
		else {
			if ($column_obj && $column_obj->get_is_single_direction()) {
				// Oops, we have a sort on a non-sortable column. Redirect
				$this->adapter->get_url_for_sort($column_key, '')->redirect();
				exit;				
			}
		}
		$this->sorter_data['order'] = $order;
		$this->sorter_data['default_order'] = $default_order;
		$this->sorter_data['is_default'] = (($order == $default_order) && ($column_key == $default_column_key));
	}

	/**
	 * Do what should be done
	 * 
	 * @param DataObjectBase The current query to  be modified
	 */
	public function apply($query) {
		$column_obj = $this->sorter_data['current_column_object'];
		if ($column_obj instanceof DBSortColumn) {
			$column_obj->set_direction($this->sorter_data['order']);
			$column_obj->apply($query);	 
		}
	}

	/**
	 * Sets sorter_data on view, so WidgetSorter can be applied
	 */
	public function prepare_view($view) {
		if ($this->sorter_data['columns_total'] < 2) {
			return;
		}

		$order = $this->sorter_data['order']; 
		$arr_colums = array();
		foreach($this->sorter_data['column_objects'] as $column => $data) {
			$arr_colums[$column] = $this->create_column_array($data, $order, $column);
		}

		$current_column_obj = $this->sorter_data['current_column_object']; 
		if ($current_column_obj) {
			$current_column = $this->create_column_array($current_column_obj, $order, $this->sorter_data['current_column_key']);
		}

		$this->sorter_data['columns'] = $arr_colums;
		$this->sorter_data['current_column'] = $current_column ?? null;
		$view->assign('sorter_data', $this->sorter_data);
	}
	
	/**
	 * Build sorter data for given column 
	 *
	 * @param DBSortColumn $data
	 * @param string $order
	 * @return array
	 */
	private function create_column_array($data, $order, $column_key = '') {
		$column = $data->get_column();
		if ($column_key === '') {
			$column_key = $column;
		}
		$order_param_value = ($data->get_is_single_direction()) ? '' : $order;
		$ret = array(
			'column' => $column,
			'title' => $data->get_title(),
			'link' => $this->get_url_for_sort($column_key, $order_param_value),
			'sort_title' => $data->get_order_title($order),
		);
		if (!$data->get_is_single_direction()) {
			$ret['other_sort_link'] = $this->get_url_for_sort($column_key, $data->get_opposite_order($order));
			$ret['other_sort_title'] = $data->get_order_title($data->get_opposite_order($order));
		}		
		return $ret;
	}
	
	/**
	 * Return link to invoke sorting on given column with given order (relative)
	 *
	 * @param string $column
	 * @param string $order
	 * @return string
	 */
	private function get_url_for_sort($column, $order) {
		$url = $this->adapter->get_url_for_sort($column, $order);
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
	 * @deprecated Use function on Adapter instead
	 */
	public static function apply_to_url($url, $column, $order) {
		return SorterDefaultAdapter::apply_to_url($url, $column, $order);
	}	
}

/**
 * Default Implementation of Sorter adapter 
 * 
 * Uses GET-Parameters as sorter parameters
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class SorterDefaultAdapter implements ISorterAdapter {
	protected $page_data;
	protected $param_column;
	protected $param_order;
	
	public function __construct(PageData $page_data, $param_column = 'sc', $param_order = 'so') {
		$this->page_data = $page_data;
		$this->param_column = $param_column;
		$this->param_order = $param_order;
	}
	
	/**
	 * Return column key of column to sort after
	 * 
	 * @return string
	 */
	public function get_column() {
		return $this->page_data->get_get()->get_item($this->param_column, false);		
	}

	/**
	 * Returns sort order ('forward' or 'backward')
	 * 
	 * False if no order was set 
	 * 
	 * @return string
	 */
	public function get_order() {
		return $this->page_data->get_get()->get_item($this->param_order, false);
	}

	/**
	 * Return link to invoke sorting on given column with given order
	 *
	 * @param string $column
	 * @param string $order
	 * @return Url
	 */
	public function get_url_for_sort($column, $order) {
		$url = Url::current();
		self::apply_to_url($url, $column, $order, $this->param_column, $this->param_order);
		return $url;
	}

	/**
	 * Prepare URL so filter gets applied
	 * 
	 * @param Url Instance of URL class. This instance is changed.
	 * @param string Filter to append
	 * 
	 * @return void
	 */
	public static function apply_to_url($url, $column, $order, $param_column = 'sc', $param_order = 'so') {
		$url->replace_query_parameter($param_order, $order)->replace_query_parameter($param_column, $column);
	}	
}
