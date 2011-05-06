<?php
/**
 * Takes an expression that is used as is for example when updating
 */
class DBExpression {
	/**
	 * @var string
	 */
	protected $expression; 
	
	/**
	 * Constructor
	 */
	public function __construct($expression) {
		$this->expression = $expression;
	}
	
	/**
	 * Format this expression
	 *
	 * @param mixed $value
	 * @return string
	 */
	public function format() {
		return $this->expression;
	}
}