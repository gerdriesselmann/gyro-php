<?php
use PHPUnit\Framework\TestCase;

class DBConditionTest extends TestCase {
	public function test_constructor() {
		$cond = new DBCondition('status', '=', 'active');
		$this->assertEquals('status', $cond->column);
		$this->assertEquals('=', $cond->operator);
		$this->assertEquals('active', $cond->value);
	}

	public function test_various_operators() {
		$cond = new DBCondition('age', '>=', 18);
		$this->assertEquals('age', $cond->column);
		$this->assertEquals('>=', $cond->operator);
		$this->assertEquals(18, $cond->value);

		$cond = new DBCondition('name', 'LIKE', '%test%');
		$this->assertEquals('LIKE', $cond->operator);
		$this->assertEquals('%test%', $cond->value);
	}
}
