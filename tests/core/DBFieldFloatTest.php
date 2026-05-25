<?php
use PHPUnit\Framework\TestCase;

class DBFieldFloatTest extends TestCase {
	public function test_validate() {
		$field = new DBFieldFloat('price', 0, DBField::NONE);
		$this->assertTrue($field->validate(1.5)->is_ok());
		$this->assertTrue($field->validate(-1.5)->is_ok());
		$this->assertTrue($field->validate('3.14')->is_ok());
		$this->assertTrue($field->validate(0)->is_ok());
		$this->assertTrue($field->validate(null)->is_ok());
	}

	public function test_validate_unsigned() {
		$field = new DBFieldFloat('price', 0, DBFieldFloat::UNSIGNED);
		$this->assertTrue($field->validate(1.5)->is_ok());
		$this->assertTrue($field->validate(0)->is_ok());
		$this->assertTrue($field->validate(-1.5)->is_error());
	}

	public function test_format() {
		$field = new DBFieldFloat('price', 0, DBField::NONE);
		$formatted = $field->format(3.14);
		$this->assertStringContainsString('3.14', $formatted);
		$this->assertEquals('NULL', $field->format(null));
	}
}
