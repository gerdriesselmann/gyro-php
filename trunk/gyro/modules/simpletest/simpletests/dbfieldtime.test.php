<?php
class DBFieldTimeTest extends GyroUnitTestCase {
	public function test_format() {
		$field = new DBFieldTime('name');
		
		$date = GyroDate::datetime('2008-01-22 22:10:33');
		$this->assertEqual("'22:10:33'", $field->format($date));
		
		$time = time();
		$this->assertEqual("'" . date('H:i:s', $time) . "'", $field->format($time));
		
		$this->assertEqual("CURRENT_TIME", $field->format(DBFieldDateTime::NOW));
		
		$date = '22:10:33';
		$this->assertEqual("'22:10:33'", $field->format($date));		

		$date = '9:5';
		$this->assertEqual("'09:05:00'", $field->format($date));		
		
		$this->assertEqual("NULL", $field->format(null));
		
		$field = new DBFieldTime('name', null, DBFieldDateTime::TIMESTAMP);
		$this->assertEqual("DEFAULT", $field->format(DBFieldDateTime::NOW));
		$date = GyroDate::datetime('22:10:33');
		$this->assertEqual('DEFAULT', $field->format($date));		
	}
}
