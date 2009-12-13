<?php
class DAOTest extends GyroUnitTestCase {
	public function test_format() {
		$table = new MockIDBTable();
		$this->assertEqual("'value'", DB::format('value', $table, 'column'));
	}
}