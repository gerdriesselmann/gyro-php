<?php
use PHPUnit\Framework\TestCase;

class CastTest extends TestCase {
	public function test_int() {
		$this->assertSame(0, Cast::int(0));
		$this->assertSame(1, Cast::int(1));
		$this->assertSame(-1, Cast::int(-1));
		$this->assertSame(42, Cast::int('42'));
		$this->assertSame(0, Cast::int('abc'));
		$this->assertSame(0, Cast::int(''));
		$this->assertSame(0, Cast::int(null));
		$this->assertSame(0, Cast::int(false));
		$this->assertSame(0, Cast::int(true)); // is_numeric(true) is false
		$this->assertSame(3, Cast::int(3.7));
		$this->assertSame(0, Cast::int('12abc'));
	}

	public function test_float() {
		$this->assertSame(0.0, Cast::float(0));
		$this->assertSame(1.0, Cast::float(1));
		$this->assertSame(3.14, Cast::float('3.14'));
		$this->assertSame(0.0, Cast::float('abc'));
		$this->assertSame(0.0, Cast::float(''));
		$this->assertSame(0.0, Cast::float(null));
		$this->assertSame(-2.5, Cast::float('-2.5'));
	}

	public function test_string() {
		$this->assertSame('hello', Cast::string('hello'));
		$this->assertSame('42', Cast::string(42));
		$this->assertSame('3.14', Cast::string(3.14));
		$this->assertSame('', Cast::string(array()));
		$this->assertSame('', Cast::string(array(1, 2)));
		$this->assertSame('0', Cast::string(0));
		$this->assertSame('', Cast::string(false));
		$this->assertSame('1', Cast::string(true));
		$this->assertSame('', Cast::string(null));
	}

	public function test_string_with_object() {
		$obj = new class {
			public function __toString() {
				return 'stringified';
			}
		};
		$this->assertSame('stringified', Cast::string($obj));
	}
}
