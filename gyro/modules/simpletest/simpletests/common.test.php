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
	
	public function test_header_related() {
		$h = 'X-Test-Common';
		$this->assertFalse(Common::is_header_sent($h));
		
		Common::header($h, 1, false);
		$this->assertTrue(Common::is_header_sent($h));
		$this->assertTrue(Common::is_header_sent(strtolower($h)));
		//$this->assertTrue(in_array($h . ': 1', headers_list()));
		
		Common::header($h, 2, false);
		$this->assertTrue(in_array($h . ': 1', Common::get_headers()));
				
		Common::header(strtoupper($h), 2, false);
		$this->assertTrue(in_array($h . ': 1', Common::get_headers()));
		
		Common::header(strtolower($h), 2, false);
		$this->assertTrue(in_array($h . ': 1', Common::get_headers()));
		
		Common::header($h, 2, true);
		$this->assertTrue(in_array($h . ': 2', Common::get_headers()));
		
		// TEst with date (containes ":")
		$h = 'X-Test-Date-Common';
		$d1 = GyroDate::http_date(time());
		$d2 = GyroDate::http_date(time() + GyroDate::ONE_HOUR);
		$this->assertFalse(Common::is_header_sent($h));
		
		Common::header($h, $d1, false);
		$this->assertTrue(Common::is_header_sent($h));		

		Common::header($h, $d2, false);
		$this->assertTrue(in_array($h . ': ' . $d1, Common::get_headers()));
				
		Common::header($h, $d2, true);
		$this->assertTrue(in_array($h . ': ' . $d2, Common::get_headers()));
	}
}
