<?php
/**
 * One Item for a multicolum Filter
 * 
 * @ingroup QueryModifiers
 * @author Gerd Riesselmann
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
	 * Logical operator, AND or OR
	 * 
	 * @since 0.5.1
	 * 
	 * @var string
	 */
	public $logical;
	
	/**
	 * Constructor
	 *
	 * @param string $column Name of column to filter by
	 * @param mixed $value
	 * @param string $operator Operator like for DBWhere
	 * @param string $logical Logical Operator 
	 */
	public function __construct($column, $value, $operator = '=', $logical = DBWhere::LOGIC_AND) {
		$this->column = $column;
		$this->value = $value;
		$this->operator = $operator;
		$this->logical = $logical;		
	}
}

/**
 * A filter containing several columns to apply to a search result
 * 
 * Construct with an array of DBFilterMultiColumnItems
 * 
 * @code
 * public function get_filters() {
 *   return array(
 *     new DBFilterGroup(
 *       '2col',
 *       tr('Two Column Test'),
 *       array(
 *         'example' => new DBFilterMultiColumn(array(
 *           new DBFilterMultiColumnItem('field_a', 'value_a'),
 *           new DBFilterMultiColumnItem('field_b', 'value_b', '<>')
 *         ), tr('Example')),
 *         'other' => new DBFilterMultiColumn(array(
 *           new DBFilterMultiColumnItem('field_a', 'other_value'),
 *           new DBFilterMultiColumnItem('field_b', 'other_value_b', '<>')
 *         ), tr('Other stuff'),
 *         // Filters can be mixed!
 *         'classic' => new DBFilterColumn('field_c', 'old_stuff', tr('Classic'))
 *       )
 *     );
 *   )
 * }
 * @endcode
 * 
 * @ingroup QueryModifiers
 * @author Gerd Riesselmann
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

	/**
	 * Apply 
	 * 
	 * @param ISearchAdapter $query
	 */
	public function apply($query) {
		$where = new DBWhereGroup($query);
		foreach($this->items as $item) {
			/* @var $item DBFilterMultiColumnItem */
			$column = trim($item->column);
			if (empty($column)) {
				return;
			}
			$where->add_where($column, $item->operator, $this->preprocess_value($item->value, $item->operator), $item->logical);
		}
		$query->add_where_object($where);
	}
	
	/**
	 * Return colum items 
	 * 
	 * @return array Array of DBFilterMultiColumnItem
	 */
	public function get_items() {
		return $this->items;
	}
}
