<?php
/**
 * Created on 07.05.2007
 *
 * @author Gerd Riesselmann
 */
 
class DateTest extends GyroUnitTestCase {
	public function setUp() {
		GyroDate::$non_workdays = array(0, 6);
		GyroDate::$holidays = array();
	}
	
	public function test_datetime() {
		$time = mktime(22, 10, 33, 1, 22, 2008);
		$this->assertEqual($time, GyroDate::datetime('2008-01-22T22:10:33'));
		$this->assertEqual($time, GyroDate::datetime('2008-01-22 22:10:33'));
		$this->assertEqual($time, GyroDate::datetime('2008/01/22, 22:10:33'));

		$this->assertEqual($time, GyroDate::datetime($time));
		
	}
	
	public function test_add_months() {
		$dt = GyroDate::datetime('2007/01/01');
		$dt_expect = GyroDate::datetime('2007/04/01');
		$this->assertEqual($dt_expect, GyroDate::add_months($dt, 3));
		
		$dt_expect = GyroDate::datetime('2008/04/01');
		$this->assertEqual($dt_expect, GyroDate::add_months($dt, 15));

		$dt = GyroDate::datetime('2007/01/31');
		$dt_expect = GyroDate::datetime('2007/02/28');
		$this->assertEqual($dt_expect, GyroDate::add_months($dt, 1));

		$dt = GyroDate::datetime('2007/01/28');
		$dt_expect = GyroDate::datetime('2007/02/28');
		$this->assertEqual($dt_expect, GyroDate::add_months($dt, 1));

		$dt = GyroDate::datetime('2007/01/27');
		$dt_expect = GyroDate::datetime('2007/02/27');
		$this->assertEqual($dt_expect, GyroDate::add_months($dt, 1));

		$dt = GyroDate::datetime('2014/04/03');
		$dt_expect = GyroDate::datetime('2014/05/03');
		$this->assertEqual($dt_expect, GyroDate::add_months($dt, 1));
	}
	
	public function test_substract_months() {		
		$dt = GyroDate::datetime('2007/01/01');
		$dt_expect = GyroDate::datetime('2006/07/01');		
		$this->assertEqual($dt_expect, GyroDate::substract_months($dt, 6));

		$dt_expect = GyroDate::datetime('2005/07/01');		
		$this->assertEqual($dt_expect, GyroDate::substract_months($dt, 18));

		$dt = GyroDate::datetime('2007/03/31');
		$dt_expect = GyroDate::datetime('2007/02/28');
		$this->assertEqual($dt_expect, GyroDate::substract_months($dt, 1));

		$dt = GyroDate::datetime('2007/03/28');
		$dt_expect = GyroDate::datetime('2007/02/28');
		$this->assertEqual($dt_expect, GyroDate::substract_months($dt, 1));

		$dt = GyroDate::datetime('2007/03/27');
		$dt_expect = GyroDate::datetime('2007/02/27');
		$this->assertEqual($dt_expect, GyroDate::substract_months($dt, 1));

		$dt = GyroDate::datetime('2007/03/28');
		$dt_expect = GyroDate::datetime('2005/02/28');
		$this->assertEqual($dt_expect, GyroDate::substract_months($dt, 25));
	}
	
	public function test_is_workday() {
		$date = GyroDate::datetime('2008/02/27'); // A wednesday
		$this->assertTrue(GyroDate::is_workday($date));
		$date += 24 * 60 * 60; // Thursday
		$this->assertTrue(GyroDate::is_workday($date));
		$date += 24 * 60 * 60; // Friday
		$this->assertTrue(GyroDate::is_workday($date));
		$date += 24 * 60 * 60; // Saturday
		$this->assertFalse(GyroDate::is_workday($date));
		$date += 24 * 60 * 60; // Saturday
		$this->assertFalse(GyroDate::is_workday($date));
		$date += 24 * 60 * 60; // Monday
		$this->assertTrue(GyroDate::is_workday($date));
		$date += 24 * 60 * 60; // Tuesday
		$this->assertTrue(GyroDate::is_workday($date));
		$date += 24 * 60 * 60; // Wednesday again
		$this->assertTrue(GyroDate::is_workday($date));

		GyroDate::$non_workdays = array(0);
		$this->assertTrue(GyroDate::is_workday('2008-03-15')); // A Saturday
		$this->assertFalse(GyroDate::is_workday('2008-03-16')); // A Sunday
		
		GyroDate::$holidays = array('2008/03/15', GyroDate::datetime('2008-03-13' ));
		$this->assertFalse(GyroDate::is_workday('2008-03-15')); 
		$this->assertTrue(GyroDate::is_workday('2008-03-14'));
		$this->assertFalse(GyroDate::is_workday('2008-03-13'));
	}
	
	public function test_set_time() {
		$date1 = GyroDate::datetime('2008/02/27 18:20:21');
		$date2 = GyroDate::datetime('2008/02/27 22:04:05');
		$this->assertNotEqual($date1, $date2);
		$date1 = GyroDate::set_time($date1, 22, 4, 5);
		$this->assertEqual($date1, $date2); 	
	}
	
	public function test_add_workdays() {
		$date = GyroDate::datetime('2008/02/27'); // A Wednesday
		$date_test = GyroDate::add_workdays($date, 1); // Set to Thursday
		$this->assertEqual('2008/02/28', date('Y/m/d', $date_test));
		$date_test = GyroDate::add_workdays($date, 2); // Set to Friday
		$this->assertEqual('2008/02/29', date('Y/m/d', $date_test));
		$date_test = GyroDate::add_workdays($date, 3); // Set to Monday!
		$this->assertEqual('2008/03/03', date('Y/m/d', $date_test));
		$date_test = GyroDate::add_workdays($date, 4); // Set to Tuesday!
		$this->assertEqual('2008/03/04', date('Y/m/d', $date_test));
		$date_test = GyroDate::add_workdays($date, 9); // Set to Tuesday next week!
		$this->assertEqual('2008/03/11', date('Y/m/d', $date_test));

		// Now substract
		$date_test = GyroDate::add_workdays($date, -1); // Set to Tuesday
		$this->assertEqual('2008/02/26', date('Y/m/d', $date_test));
		$date_test = GyroDate::add_workdays($date, -2); // Set to Monday
		$this->assertEqual('2008/02/25', date('Y/m/d', $date_test));
		$date_test = GyroDate::add_workdays($date, -3); // Set to Friday
		$this->assertEqual('2008/02/22', date('Y/m/d', $date_test));
		$date_test = GyroDate::add_workdays($date, -4); // Set to Thursday
		$this->assertEqual('2008/02/21', date('Y/m/d', $date_test));
		$date_test = GyroDate::add_workdays($date, -9); // Set to Thursday week before last week
		$this->assertEqual('2008/02/14', date('Y/m/d', $date_test));

		// Test 0
		$date = GyroDate::datetime('2008/02/27'); // A Wednesday
		$date_test = GyroDate::add_workdays($date, 0); 
		$this->assertEqual($date, $date_test);
		$date = GyroDate::datetime('2008/02/28'); // Thurday
		$date_test = GyroDate::add_workdays($date, 0); 
		$this->assertEqual($date, $date_test);
		$date = GyroDate::datetime('2008/02/29'); // Friday
		$date_test = GyroDate::add_workdays($date, 0); 
		$this->assertEqual($date, $date_test);
		$date = GyroDate::datetime('2008/03/01'); // Saturday
		$date_test = GyroDate::add_workdays($date, 0); 
		$this->assertEqual('2008/03/03', date('Y/m/d', $date_test)); // Should become Monday
		$date = GyroDate::datetime('2008/03/02'); // Sunday
		$date_test = GyroDate::add_workdays($date, 0); 
		$this->assertEqual('2008/03/03', date('Y/m/d', $date_test)); // Should become Monday
		$date = GyroDate::datetime('2008/03/04'); // Monday
		$date_test = GyroDate::add_workdays($date, 0); 
		$this->assertEqual($date, $date_test);
	}
	
	public function test_add_days() {
		$date = GyroDate::datetime('2008/02/27');
		$date_test = GyroDate::add_days($date, 1); 
		$this->assertEqual('2008/02/28', date('Y/m/d', $date_test));
		$date_test = GyroDate::add_workdays($date, 2); // Set to Friday
		$this->assertEqual('2008/02/29', date('Y/m/d', $date_test));
		
		// Now substract
		$date_test = GyroDate::substract_days($date, 1); 
		$this->assertEqual('2008/02/26', date('Y/m/d', $date_test));
		$date_test = GyroDate::substract_days($date, 2); // Set to Monday
		$this->assertEqual('2008/02/25', date('Y/m/d', $date_test));
	}
} 
?>