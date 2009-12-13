<?php
/**
 * Simple condition class
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBCondition {
	public $column;
	public $operator;
	public $value;
	
	public function __construct($column, $operator, $value) {
		$this->column = $column;
		$this->operator = $operator;
		$this->value = $value;
	}
}
