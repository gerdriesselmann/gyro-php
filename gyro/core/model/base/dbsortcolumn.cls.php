<?php
/**
 * A sortable column
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBSortColumn implements IDBQueryModifier {
	const TYPE_TEXT = 'text';
	const TYPE_CURRENCY = 'currency';
	const TYPE_NUMERIC = 'numeric';
	const TYPE_DATE = 'date';
	const TYPE_MATCH = 'match';

	const ORDER_FORWARD = 'forward';
	const ORDER_BACKWARD = 'backward';
	
	/**
	 * Colkum name
	 */
	private $column;
	/**
	 * Title of column
	 */
	private $title;
	/**
	 * Type of column
	 */
	private $type;
	/**
	 * True if this allows a single direction only
	 *
	 * @var bool
	 */
	private $single_direction = false;
	/**
	 * Direction to sort
	 */
	private $direction = self::ORDER_FORWARD;
	
	/**
	 * contructor
	 * 
	 * @param string column
	 * @param string title
	 * @param enum type of column
	 */
	public function __construct($column, $title, $type, $direction = self::ORDER_FORWARD, $single_direction = false) {
		$this->column = $column;
		$this->title = $title;
		$this->type = $type;	
		$this->direction = $direction;
		$this->single_direction = $single_direction;
	}
	
	public function apply($query) {
		$query->sort($this->get_db_column(), $this->get_sort_order($this->direction));
	}
	
	/**
	 * Returns name of column in DB
	 *
	 * @return unknown
	 */
	protected function get_db_column() {
		return $this->get_column();
	}
	
	public function get_column() {
		return $this->column;
	}
	
	public function get_title() {
		return $this->title;
	}
	
	/**
	 * Returns true if this column should be sorted in a single direction only
	 *
	 * @return true
	 */
	public function get_is_single_direction() {
		return $this->single_direction;
	}
	
	public function set_direction($direction) {
		if (!$this->get_is_single_direction()) {
			$this->direction = $direction;
		}
	}

	public function get_direction() {
		return $this->direction;
	}	
	
	/**
	 * Return title for sort direction
	 */
	public function get_order_title($direction) {
		if ($direction == self::ORDER_FORWARD) {
			switch ($this->type) {
				case self::TYPE_CURRENCY:
					return tr('Ascending (cheaper first)', 'core');
					break;
				case self::TYPE_DATE:
					return tr('Ascending (newer first)', 'core');
					break;
				case self::TYPE_TEXT:
					return tr('Ascending (A-Z)', 'core');
					break;
				case self::TYPE_MATCH:
					return tr('Ascending (Most important first)', 'core');
					break;					
				case self::TYPE_NUMERIC:
				default:
					return tr('Ascending (smaller first)', 'core');
					break;
			}
		}
		else {
			switch ($this->type) {
				case self::TYPE_CURRENCY:
					return tr('Descending (expensive first)', 'core');
					break;
				case self::TYPE_DATE:
					return tr('Descending (older first)', 'core');
					break;
				case self::TYPE_TEXT:
					return tr('Descending (Z-A)', 'core');
					break;
				case self::TYPE_MATCH:
					return tr('Descending (Less important first)', 'core');
					break;					
				case self::TYPE_NUMERIC:
				default:
					return tr('Descending (greater first)', 'core');
					break;
			}
		}
	}	
	
	/**
	 * Return sort direction key for sorting 
	 * 
	 * This class supports forward and backward sorting. Translate this into ascending/descending   
	 */
	public function get_sort_order($direction) {
		switch ($this->type) {
			case self::TYPE_DATE:
			case self::TYPE_MATCH:
				return ($direction == self::ORDER_BACKWARD) ? ISearchAdapter::ASC : ISearchAdapter::DESC;
				break;
			default:
				return ($direction == self::ORDER_BACKWARD) ? ISearchAdapter::DESC : ISearchAdapter::ASC;
				break;
		}
	}
	 
	/**
	 * Return reverse direction
	 */
	public function get_opposite_order($direction) {
		return ($direction == self::ORDER_BACKWARD) ? self::ORDER_FORWARD : self::ORDER_BACKWARD;
	}
}
