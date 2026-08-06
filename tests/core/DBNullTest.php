<?php
use PHPUnit\Framework\TestCase;

class DBNullTest extends TestCase {
	public function test_to_string() {
		$null = new DBNull();
		$this->assertEquals('NULL', (string)$null);
		$this->assertEquals('NULL', $null->__toString());
	}
}
