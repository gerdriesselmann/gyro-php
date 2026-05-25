<?php
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase {
	protected function setUp(): void {
		GyroDate::$non_workdays = array(0, 6);
		GyroDate::$holidays = array();
	}

	public function test_datetime(): void {
		$time = mktime(22, 10, 33, 1, 22, 2008);
		$this->assertEquals($time, GyroDate::datetime('2008-01-22T22:10:33'));
		$this->assertEquals($time, GyroDate::datetime('2008-01-22 22:10:33'));
		$this->assertEquals($time, GyroDate::datetime('2008/01/22, 22:10:33'));
		$this->assertEquals($time, GyroDate::datetime($time));
	}

	public function test_add_months(): void {
		$dt = GyroDate::datetime('2007/01/01');
		$this->assertEquals(GyroDate::datetime('2007/04/01'), GyroDate::add_months($dt, 3));
		$this->assertEquals(GyroDate::datetime('2008/04/01'), GyroDate::add_months($dt, 15));

		$dt = GyroDate::datetime('2007/01/31');
		$this->assertEquals(GyroDate::datetime('2007/02/28'), GyroDate::add_months($dt, 1));

		$dt = GyroDate::datetime('2007/01/28');
		$this->assertEquals(GyroDate::datetime('2007/02/28'), GyroDate::add_months($dt, 1));

		$dt = GyroDate::datetime('2007/01/27');
		$this->assertEquals(GyroDate::datetime('2007/02/27'), GyroDate::add_months($dt, 1));

		$dt = GyroDate::datetime('2014/04/03');
		$this->assertEquals(GyroDate::datetime('2014/05/03'), GyroDate::add_months($dt, 1));
	}

	public function test_substract_months(): void {
		$dt = GyroDate::datetime('2007/01/01');
		$this->assertEquals(GyroDate::datetime('2006/07/01'), GyroDate::substract_months($dt, 6));
		$this->assertEquals(GyroDate::datetime('2005/07/01'), GyroDate::substract_months($dt, 18));

		$dt = GyroDate::datetime('2007/03/31');
		$this->assertEquals(GyroDate::datetime('2007/02/28'), GyroDate::substract_months($dt, 1));

		$dt = GyroDate::datetime('2007/03/28');
		$this->assertEquals(GyroDate::datetime('2007/02/28'), GyroDate::substract_months($dt, 1));

		$dt = GyroDate::datetime('2007/03/27');
		$this->assertEquals(GyroDate::datetime('2007/02/27'), GyroDate::substract_months($dt, 1));

		$dt = GyroDate::datetime('2007/03/28');
		$this->assertEquals(GyroDate::datetime('2005/02/28'), GyroDate::substract_months($dt, 25));
	}

	public function test_is_workday(): void {
		$date = GyroDate::datetime('2008/02/27'); // A Wednesday
		$this->assertTrue(GyroDate::is_workday($date));
		$date += 24 * 60 * 60; // Thursday
		$this->assertTrue(GyroDate::is_workday($date));
		$date += 24 * 60 * 60; // Friday
		$this->assertTrue(GyroDate::is_workday($date));
		$date += 24 * 60 * 60; // Saturday
		$this->assertFalse(GyroDate::is_workday($date));
		$date += 24 * 60 * 60; // Sunday
		$this->assertFalse(GyroDate::is_workday($date));
		$date += 24 * 60 * 60; // Monday
		$this->assertTrue(GyroDate::is_workday($date));

		GyroDate::$non_workdays = array(0);
		$this->assertTrue(GyroDate::is_workday('2008-03-15')); // A Saturday
		$this->assertFalse(GyroDate::is_workday('2008-03-16')); // A Sunday

		GyroDate::$holidays = array('2008/03/15', GyroDate::datetime('2008-03-13'));
		$this->assertFalse(GyroDate::is_workday('2008-03-15'));
		$this->assertTrue(GyroDate::is_workday('2008-03-14'));
		$this->assertFalse(GyroDate::is_workday('2008-03-13'));
	}

	public function test_set_time(): void {
		$date1 = GyroDate::datetime('2008/02/27 18:20:21');
		$date2 = GyroDate::datetime('2008/02/27 22:04:05');
		$this->assertNotEquals($date1, $date2);
		$date1 = GyroDate::set_time($date1, 22, 4, 5);
		$this->assertEquals($date1, $date2);
	}

	public function test_add_workdays(): void {
		$date = GyroDate::datetime('2008/02/27'); // A Wednesday
		$this->assertEquals('2008/02/28', date('Y/m/d', GyroDate::add_workdays($date, 1)));
		$this->assertEquals('2008/02/29', date('Y/m/d', GyroDate::add_workdays($date, 2)));
		$this->assertEquals('2008/03/03', date('Y/m/d', GyroDate::add_workdays($date, 3))); // Monday
		$this->assertEquals('2008/03/04', date('Y/m/d', GyroDate::add_workdays($date, 4)));
		$this->assertEquals('2008/03/11', date('Y/m/d', GyroDate::add_workdays($date, 9)));

		// Subtract
		$this->assertEquals('2008/02/26', date('Y/m/d', GyroDate::add_workdays($date, -1)));
		$this->assertEquals('2008/02/25', date('Y/m/d', GyroDate::add_workdays($date, -2)));
		$this->assertEquals('2008/02/22', date('Y/m/d', GyroDate::add_workdays($date, -3))); // Friday
		$this->assertEquals('2008/02/21', date('Y/m/d', GyroDate::add_workdays($date, -4)));
		$this->assertEquals('2008/02/14', date('Y/m/d', GyroDate::add_workdays($date, -9)));

		// Zero
		$this->assertEquals($date, GyroDate::add_workdays($date, 0));
		$saturday = GyroDate::datetime('2008/03/01');
		$this->assertEquals('2008/03/03', date('Y/m/d', GyroDate::add_workdays($saturday, 0))); // Monday
		$sunday = GyroDate::datetime('2008/03/02');
		$this->assertEquals('2008/03/03', date('Y/m/d', GyroDate::add_workdays($sunday, 0))); // Monday
	}

	public function test_add_days(): void {
		$date = GyroDate::datetime('2008/02/27');
		$this->assertEquals('2008/02/28', date('Y/m/d', GyroDate::add_days($date, 1)));
		$this->assertEquals('2008/02/26', date('Y/m/d', GyroDate::substract_days($date, 1)));
		$this->assertEquals('2008/02/25', date('Y/m/d', GyroDate::substract_days($date, 2)));
	}
}
