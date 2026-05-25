<?php
use PHPUnit\Framework\TestCase;

class DBFieldDateTest extends TestCase {
	public function test_format_date() {
		$field = new DBFieldDate('name');

		$date = GyroDate::datetime('2008-01-22 22:10:33');
		$this->assertEquals("'2008-01-22'", $field->format($date));

		$time = time();
		$this->assertEquals("'" . date('Y-m-d', $time) . "'", $field->format($time));

		$this->assertEquals("CURRENT_DATE", $field->format(DBFieldDateTime::NOW));

		$date = '2008-01-22 22:10:33';
		$this->assertEquals("'2008-01-22'", $field->format($date));
		$this->assertEquals("NULL", $field->format(null));

		$field = new DBFieldDate('name', null, DBFieldDateTime::TIMESTAMP);
		$this->assertEquals("DEFAULT", $field->format(DBFieldDateTime::NOW));
		$date = GyroDate::datetime('2008-01-22 22:10:33');
		$this->assertEquals("DEFAULT", $field->format($date));
	}

	public function test_format_datetime() {
		$field = new DBFieldDateTime('name');
		$date = GyroDate::datetime('2008-01-22 22:10:33');
		$this->assertEquals("'2008-01-22 22:10:33'", $field->format($date));
		$time = time();
		$this->assertEquals("'" . date('Y-m-d H:i:s', $time) . "'", $field->format($time));

		$this->assertEquals("CURRENT_TIMESTAMP", $field->format(DBFieldDateTime::NOW));

		$date = '2008-01-22 22:10:33';
		$this->assertEquals("'2008-01-22 22:10:33'", $field->format($date));
		$this->assertEquals("NULL", $field->format(null));

		$field = new DBFieldDateTime('name', null, DBFieldDateTime::TIMESTAMP);
		$this->assertEquals("DEFAULT", $field->format(DBFieldDateTime::NOW));
		$date = GyroDate::datetime('2008-01-22 22:10:33');
		$this->assertEquals("DEFAULT", $field->format($date));

		$this->assertEquals("CURRENT_TIMESTAMP", $field->format_where(DBFieldDateTime::NOW));
		$date = GyroDate::datetime('2008-01-22 22:10:33');
		$this->assertEquals("'2008-01-22 22:10:33'", $field->format_where($date));
	}

	public function test_format_time() {
		$field = new DBFieldTime('name');

		$date = GyroDate::datetime('2008-01-22 22:10:33');
		$this->assertEquals("'22:10:33'", $field->format($date));

		$time = time();
		$this->assertEquals("'" . date('H:i:s', $time) . "'", $field->format($time));

		$this->assertEquals("CURRENT_TIME", $field->format(DBFieldDateTime::NOW));

		$date = '22:10:33';
		$this->assertEquals("'22:10:33'", $field->format($date));

		$date = '9:5';
		$this->assertEquals("'09:05:00'", $field->format($date));

		$this->assertEquals("NULL", $field->format(null));

		$field = new DBFieldTime('name', null, DBFieldDateTime::TIMESTAMP);
		$this->assertEquals("DEFAULT", $field->format(DBFieldDateTime::NOW));
		$date = GyroDate::datetime('22:10:33');
		$this->assertEquals('DEFAULT', $field->format($date));
	}
}
