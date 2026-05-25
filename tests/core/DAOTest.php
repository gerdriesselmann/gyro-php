<?php
use PHPUnit\Framework\TestCase;

class DAOTest extends TestCase {
	public function test_format() {
		$table = new MockIDBTable();
		$this->assertEquals("'value'", DB::format('value', $table, 'column'));
	}
}
