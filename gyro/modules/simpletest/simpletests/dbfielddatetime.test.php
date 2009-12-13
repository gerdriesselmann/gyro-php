<?php
class DBFieldDateTimeTest extends GyroUnitTestCase {
	public function test_format() {
		$field = new DBFieldDateTime('name');
		$date = GyroDate::datetime('2008-01-22 22:10:33');
		$this->assertEqual("'2008-01-22 22:10:33'", $field->format($date));
		$time = time();
		$this->assertEqual("'" . date('Y-m-d H:i:s', $time) . "'", $field->format($time));
		
		$this->assertEqual("CURRENT_TIMESTAMP", $field->format(DBFieldDateTime::NOW));
		
		$date = '2008-01-22 22:10:33';
		$this->assertEqual("'2008-01-22 22:10:33'", $field->format($date));		
		$this->assertEqual("NULL", $field->format(null));
		
		$field = new DBFieldDateTime('name', null, DBFieldDateTime::TIMESTAMP);
		$this->assertEqual("DEFAULT", $field->format(DBFieldDateTime::NOW));
		$date = GyroDate::datetime('2008-01-22 22:10:33');
		$this->assertEqual("DEFAULT", $field->format($date));

		$this->assertEqual("CURRENT_TIMESTAMP", $field->format_where(DBFieldDateTime::NOW));
		$date = GyroDate::datetime('2008-01-22 22:10:33');
		$this->assertEqual("'2008-01-22 22:10:33'", $field->format_where($date));		
		
	}
}
