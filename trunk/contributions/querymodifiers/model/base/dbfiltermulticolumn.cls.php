<?php
/**
 * One Item for a multicolum Filter
 */
class DBFilterMultiColumnItem {
	/**
	 * Coluimn to filter values on
	 *
	 * @var string
	 */
	public $column;
	/**
	 * Value to filter for
	 *
	 * @var mixed
	 */
	public $value;
	/**
	 * Operator. Use DBWhere Constants or usual operators like =, !=, > etc
	 *
	 * @var string
	 */
	public $operator;
	
	/**
	 * Constructor
	 *
	 * @param string $column Name of column to filter by
	 * @param mixed $value
	 * @param string $operator Operator like for DBWheres
	 */
	public function __construct($column, $value, $operator = '=') {
		$this->column = $column;
		$this->value = $value;
		$this->operator = $operator;
	}
}

/**
 * A filter containing several columns to apply to a search result
 * 
 * Construct with an array of DBFilterMultiColumnItems
 */ 
class DBFilterMultiColumn extends DBFilter {
	/**
	 * Items
	 */
	private $items;
	
	/**
	 * contructor
	 * 
	 * @param array items
	 * @param string title
	 */
	public function __construct($items, $title) {
		$this->items = $items;
		parent::__construct($title);	
	}

	public function apply($query) {
		foreach($this->items as $item) {
			/* @var $item DBFilterMultiColumnItem */
			$column = trim($item->column);
			if (empty($column)) {
				return;
			}
			$query->add_where($column, $item->operator, $item->value);
		}
	}
}
