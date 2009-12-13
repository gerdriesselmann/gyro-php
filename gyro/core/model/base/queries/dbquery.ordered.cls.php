<?php
require_once dirname(__FILE__) . '/dbquery.limited.cls.php';

/**
 * An ordered query
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
abstract class DBQueryOrdered extends DBQueryLimited {
	const ASC = 'ASC';
	const DESC = 'DESC';

	/**
	 * Array of associative arrays with members 'field', 'table' and 'direction'
	 *
	 * @var array
	 */
	protected $orders = array();

	/**
	 * Add order. Use DBQueryOrdered::CLEAR as column to clear all orders
	 *
	 * @param string $column
	 * @param string $direction Either DBQueryOrdered::ASC or DBQueryOrdered::DESC
	 */
	public function add_order($column, $direction = self::ASC) {
		if (empty($column)) {
			$this->orders = array();
		} else {
			$table = $this->table;
			$this->orders[] = array(
				'field' => $column,
				'table' => $table,
				'direction' => $direction
			);
		}		
	}
	
	/**
	 * Returns orders
	 * 
	 * @return array Array of associative arrays of form "Field => Direction"
	 */ 
	public function get_orders() {
		return $this->orders;
	}
}
