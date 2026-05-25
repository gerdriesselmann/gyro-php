<?php
use PHPUnit\Framework\TestCase;

class DBExpressionTest extends TestCase {
	public function test_format() {
		$expr = new DBExpression('NOW()');
		$this->assertEquals('NOW()', $expr->format());

		$expr = new DBExpression('col1 + col2');
		$this->assertEquals('col1 + col2', $expr->format());

		$expr = new DBExpression('');
		$this->assertEquals('', $expr->format());
	}
}
