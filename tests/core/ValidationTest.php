<?php
use PHPUnit\Framework\TestCase;

class ValidationTest extends TestCase {
	public function test_is_domain() {
		$this->assertTrue(Validation::is_domain('a.de'));
		$this->assertTrue(Validation::is_domain('1.a.de'));
		$this->assertFalse(Validation::is_domain('de'));
		$this->assertFalse(Validation::is_domain('.de'));
		$this->assertFalse(Validation::is_domain('a.12'));
		$this->assertFalse(Validation::is_domain('@a.de'));
		$this->assertFalse(Validation::is_domain('a .de'));
	}

	public function test_is_email() {
		$this->assertTrue(Validation::is_email('a.b@c.de'));
		$this->assertTrue(Validation::is_email('a..b@c.de'));
		$this->assertFalse(Validation::is_email('.ab@c.de'));
		$this->assertFalse(Validation::is_email('ab.@c.de'));
	}

	public function test_is_string_of_length() {
		$this->assertTrue(Validation::is_string_of_length(''));
		$this->assertTrue(Validation::is_string_of_length('', 0));
		$this->assertTrue(Validation::is_string_of_length('', 0, 1));
		$this->assertFalse(Validation::is_string_of_length('', 1));
		$this->assertTrue(Validation::is_string_of_length('abc', 3));
		$this->assertTrue(Validation::is_string_of_length('abc', 3, 3));
		$this->assertFalse(Validation::is_string_of_length('abc', 4));
		$this->assertFalse(Validation::is_string_of_length('abc', 1, 2));
		$this->assertTrue(Validation::is_string_of_length(' '));
		$this->assertFalse(Validation::is_string_of_length(false));
		$this->assertFalse(Validation::is_string_of_length(0));
	}

	public function test_is_int() {
		$this->assertTrue(Validation::is_int(1));
		$this->assertTrue(Validation::is_int('1'));
	}

	public function test_is_ip4() {
		$valid = array('1.1.1.1', '255.255.255.255', '1.0.0.0', '0.0.0.1');
		$invalid = array('::0:0.0.0.0', '0.0.0.0', '::', '256.1.2.3');

		foreach ($valid as $ip) {
			$this->assertTrue(Validation::is_ip4($ip), "$ip is valid IPv4");
		}
		foreach ($invalid as $ip) {
			$this->assertFalse(Validation::is_ip4($ip), "$ip is invalid IPv4");
		}
	}

	public function test_is_ip6() {
		$valid = array(
			'A::A:1.1.1.1',
			'12AB:0000:0000:CD30:0000:0000:0000:0000',
			'12AB::CD30:0:0:0:0',
			'12AB:0:0:CD30::',
			'::13.1.68.3',
			'::FFFF:129.144.52.38',
		);
		$invalid = array(
			'::0:0.0.0.0', '::0.0.0.0', '::', '::0',
			'ABCD:1080:0:0:0:8:800:200C:417A',
			'12AB::0:0:CD30::',
		);

		foreach ($valid as $ip) {
			$this->assertTrue(Validation::is_ip6($ip), "$ip is valid IPv6");
		}
		foreach ($invalid as $ip) {
			$this->assertFalse(Validation::is_ip6($ip), "$ip is invalid IPv6");
		}
	}
}
