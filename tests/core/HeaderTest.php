<?php
use PHPUnit\Framework\TestCase;

class HeaderTest extends TestCase {
	public function test_header_related() {
		$h = 'X-Test-Header';
		$this->assertFalse(GyroHeaders::is_set($h));

		GyroHeaders::set($h, 1, false);
		$this->assertTrue(GyroHeaders::is_set($h));
		$this->assertTrue(GyroHeaders::is_set(strtolower($h)));

		GyroHeaders::set($h, 2, false);
		$this->assertTrue(in_array($h . ': 1', GyroHeaders::headers()));

		GyroHeaders::set(strtoupper($h), 2, false);
		$this->assertTrue(in_array($h . ': 1', GyroHeaders::headers()));

		GyroHeaders::set(strtolower($h), 2, false);
		$this->assertTrue(in_array($h . ': 1', GyroHeaders::headers()));

		GyroHeaders::set($h, 2, true);
		$this->assertTrue(in_array($h . ': 2', GyroHeaders::headers()));

		// Test with date (contains ":")
		$h = 'X-Test-Date-Header';
		$d1 = GyroDate::http_date(time());
		$d2 = GyroDate::http_date(time() + GyroDate::ONE_HOUR);
		$this->assertFalse(GyroHeaders::is_set($h));

		GyroHeaders::set($h, $d1, false);
		$this->assertTrue(GyroHeaders::is_set($h));

		GyroHeaders::set($h, $d2, false);
		$this->assertTrue(in_array($h . ': ' . $d1, GyroHeaders::headers()));

		GyroHeaders::set($h, $d2, true);
		$this->assertTrue(in_array($h . ': ' . $d2, GyroHeaders::headers()));
	}
}
