<?php
Load::tools('filtertext');

/**
 * Filter name and emial for user name
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class FilterUsername extends FilterText {
	/**
	 * Contructor
	 * 
	 * @param PageData $page_data;
	 */
	public function __construct($page_data, $adapter = false) {
		$this->adapter = ($adapter instanceof IFilterTextAdapter) ? $adapter : new FilterTextDefaultAdapter($page_data, 'username');
		$this->page_data = $page_data;
		$this->filter_object = new DBFilterMultiColumn(
			array(
				new DBFilterMultiColumnItem('name', $this->adapter->get_value(), DBWhere::OP_LIKE, DBWhere::LOGIC_OR),
				new DBFilterMultiColumnItem('email', $this->adapter->get_value(), DBWhere::OP_LIKE, DBWhere::LOGIC_OR),
			),
			tr('name') 
		);
	}
	
	/**
	 * Return current value to be filter after
	 * 
	 * @return string
	 */
	public function get_filter_value() {
		$items = $this->filter_object->get_items(); 
		return $items[0]->value;
	}	
}