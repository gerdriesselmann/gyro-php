<?php
/** @deprecated Use DBWhere-Constants instead */
define ('FILTER_OPERATOR_EQUAL', '=');
/** @deprecated Use DBWhere-Constants instead */
define ('FILTER_OPERATOR_LIKE', 'LIKE');
/** @deprecated Use DBWhere-Constants instead */
define ('FILTER_OPERATOR_GREATER', '>');
/** @deprecated Use DBWhere-Constants instead */
define ('FILTER_OPERATOR_GREATER_OR_EQUAL', '>=');
/** @deprecated Use DBWhere-Constants instead */
define ('FILTER_OPERATOR_LESS', '<');
/** @deprecated Use DBWhere-Constants instead */
define ('FILTER_OPERATOR_LESS_OR_EQUAL', '<=');
/** @deprecated Use DBWhere-Constants instead */
define ('FILTER_OPERATOR_NOT', '<>');
/** @deprecated Use DBWhere-Constants instead */
define ('FILTER_OPERATOR_NOT_NULL', 'IS NOT NULL');

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
	protected $column;
	
	/**
	 * Value to filter for
	 */
	protected $value;
	
	/**
	 * Operator for filter comparison
	 */	
	protected $operator;
	
	/**
	 * contructor
	 * 
	 * @param string column
	 * @param string title
	 * @param enum type of column
	 */
	public function __construct($column, $value, $title, $operator = '=') {
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
		
		$value = $this->preprocess_value($this->value, $this->operator);
		$query->add_where($column, $this->operator, $value);
	}
}