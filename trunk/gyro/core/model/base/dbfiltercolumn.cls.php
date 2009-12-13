<?php
define ('FILTER_OPERATOR_EQUAL', '=');
define ('FILTER_OPERATOR_LIKE', 'LIKE');
define ('FILTER_OPERATOR_GREATER', '>');
define ('FILTER_OPERATOR_GREATER_OR_EQUAL', '>=');
define ('FILTER_OPERATOR_LESS', '<');
define ('FILTER_OPERATOR_LESS_OR_EQUAL', '<=');
define ('FILTER_OPERATOR_NOT', '<>');
define ('FILTER_OPERATOR_NOT_NULL', 'NOT NULL');

define('FILTER_COLUMN_TYPE_TEXT', 'text');
define('FILTER_COLUMN_TYPE_CURRENCY', 'currency');
define('FILTER_COLUMN_TYPE_NUMERIC', 'numeric');
define('FILTER_COLUMN_TYPE_DATE', 'date');

require_once dirname(__FILE__) . '/dbfilter.cls.php';

/**
 * A filter to apply to a search result
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */ 
class DBFilterColumn extends DBFilter {
	/**
	 * Column name
	 */
	private $column;
	
	/**
	 * Value to filter for
	 */
	private $value;
	
	/**
	 * Operator for filter comparison
	 */	
	private $operator;
	
	/**
	 * contructor
	 * 
	 * @param string column
	 * @param string title
	 * @param enum type of column
	 */
	public function __construct($column, $value, $title, $operator = FILTER_OPERATOR_EQUAL) {
		$this->column = $column;
		$this->value = $value;
		$this->operator = $operator;
		
		parent::__construct($title);	
	}
	
	public function get_column() {
		return $this->column;
	}
	
	public function get_value() {
		return $this->value;
	}

	public function apply($query) {
		$column = trim($this->column);
		if (empty($column)) {
			return;
		}

		switch ($this->operator) {
			case FILTER_OPERATOR_LIKE:
				if ($this->value !== '') {
					$query->add_where($column, DBWhere::OP_LIKE, '%' . $this->value . '%');
				}
				break;
			case FILTER_OPERATOR_NOT_NULL:
				$query->add_where($column, DBWhere::OP_NOT_NULL);
				break;
			default:
				$query->add_where($column, $this->operator, $this->value);
				break;
		}
	}
}