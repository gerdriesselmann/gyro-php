<?php
use PHPUnit\Framework\TestCase;

class DBFieldTest extends TestCase {
	public function test_get_set() {
		$field = new DBField('name');
		$this->assertEquals('name', $field->get_field_name());
		$this->assertNull($field->get_field_default());
		$this->assertTrue($field->get_null_allowed());

		$field = new DBField('name', 'value', DBField::NOT_NULL);
		$this->assertEquals('name', $field->get_field_name());
		$this->assertEquals('value', $field->get_field_default());
		$this->assertFalse($field->get_null_allowed());
	}

	public function test_validate() {
		$field = new DBField('name');
		$this->assertTrue($field->validate(false)->is_ok());
		$this->assertTrue($field->validate(null)->is_ok());
		$this->assertTrue($field->validate('value')->is_ok());

		$field = new DBField('name', 'value', DBField::NOT_NULL);
		$this->assertTrue($field->validate(false)->is_ok());
		$this->assertTrue($field->validate(null)->is_ok());
		$this->assertTrue($field->validate('value')->is_ok());

		$field = new DBField('name', null, DBField::NOT_NULL);
		$this->assertTrue($field->validate(false)->is_ok());
		$this->assertTrue($field->validate(null)->is_error());
		$this->assertTrue($field->validate('value')->is_ok());
	}

	public function test_format() {
		$field = new DBField('name');
		$this->assertEquals("'value'", $field->format("value"));
		// Mock driver uses GyroString::escape() (HTML entities) instead of mysqli_real_escape_string
		$this->assertEquals("'&#039;value&#039;'", $field->format("'value'"));
		$this->assertEquals('NULL', $field->format(null));
	}
}
