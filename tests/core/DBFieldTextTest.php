<?php
use PHPUnit\Framework\TestCase;

class DBFieldTextTest extends TestCase {
	public function test_validate() {
		$field = new DBFieldText('text');
		$this->assertTrue($field->validate('')->is_ok());
		$this->assertTrue($field->validate('a')->is_ok());
		$this->assertTrue($field->validate(null)->is_ok());
		$teststring = str_pad('', 254, 'a');
		$this->assertTrue($field->validate($teststring)->is_ok());
		$teststring .= 'b';
		$this->assertTrue($field->validate($teststring)->is_ok());
		$teststring .= 'c';
		$this->assertTrue($field->validate($teststring)->is_error());

		$field = new DBFieldText('text', 5, '', DBFieldText::NOT_NULL);
		$this->assertTrue($field->validate('')->is_error());
		$this->assertTrue($field->validate('a')->is_ok());
		$this->assertTrue($field->validate(null)->is_error());
		$this->assertTrue($field->validate('12345')->is_ok());
		$this->assertTrue($field->validate('123456')->is_error());
	}

	public function test_format() {
		$field = new DBFieldText('text');
		$this->assertEquals("'a'", $field->format('a'));
		// Mock driver uses GyroString::escape() (HTML entities) instead of mysqli_real_escape_string
		$this->assertEquals("'&#039;a'", $field->format("'a"));
		$this->assertEquals("NULL", $field->format(null));
	}
}
