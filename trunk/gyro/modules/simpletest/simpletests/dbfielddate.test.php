<?php
class DBFieldDateTest extends GyroUnitTestCase {
	public function test_format() {
		$field = new DBFieldDate('name');
		
		$date = GyroDate::datetime('2008-01-22 22:10:33');
		$this->assertEqual("'2008-01-22'", $field->format($date));
		
		$time = time();
		$this->assertEqual("'" . date('Y-m-d', $time) . "'", $field->format($time));
		
		$this->assertEqual("CURRENT_DATE", $field->format(DBFieldDateTime::NOW));
		
		$date = '2008-01-22 22:10:33';
		$this->assertEqual("'2008-01-22'", $field->format($date));		
		$this->assertEqual("NULL", $field->format(null));
		
		$field = new DBFieldDate('name', null, DBFieldDateTime::TIMESTAMP);
		$this->assertEqual("DEFAULT", $field->format(DBFieldDateTime::NOW));
		$date = GyroDate::datetime('2008-01-22 22:10:33');
		$this->assertEqual("DEFAULT", $field->format($date));		

	}
}
