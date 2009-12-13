<?php
class CommonTest extends GyroUnitTestCase {
	public function test_flag_is_set() {
		$this->assertTrue(Common::flag_is_set(1, 1));
		$this->assertTrue(Common::flag_is_set(0, 0));
		$this->assertTrue(Common::flag_is_set(3, 1));
		$this->assertTrue(Common::flag_is_set(3, 3));
		$this->assertFalse(Common::flag_is_set(0, 1));			
		$this->assertFalse(Common::flag_is_set(2, 1));
		$this->assertFalse(Common::flag_is_set(3, 5));
	}
}
