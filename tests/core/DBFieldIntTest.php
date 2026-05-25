<?php
use PHPUnit\Framework\TestCase;

class DBFieldIntTest extends TestCase {
	public function test_validate() {
		$field = new DBFieldInt('int', 0, DBField::NONE);
		$this->assertTrue($field->validate(1)->is_ok());
		$this->assertTrue($field->validate(-1)->is_ok());
		$this->assertTrue($field->validate('1')->is_ok());
		$this->assertTrue($field->validate(0)->is_ok());
		$this->assertTrue($field->validate(null)->is_ok());

		$field = new DBFieldInt('int', 0, DBFieldInt::NOT_NULL);
		$this->assertTrue($field->validate(1)->is_ok());
		$this->assertTrue($field->validate(0)->is_ok());
		$this->assertTrue($field->validate(null)->is_ok());

		$field = new DBFieldInt('int', null, DBFieldInt::NOT_NULL);
		$this->assertTrue($field->validate(1)->is_ok());
		$this->assertTrue($field->validate(0)->is_ok());
		$this->assertTrue($field->validate(null)->is_error());

		$field = new DBFieldInt('int', 0, DBFieldInt::UNSIGNED);
		$this->assertTrue($field->validate(1)->is_ok());
		$this->assertTrue($field->validate(-1)->is_error());
		$this->assertTrue($field->validate(0)->is_ok());
	}

	public function test_format() {
		$field = new DBFieldInt('int', 0, DBField::NONE);
		$this->assertEquals('1', $field->format(1));
		$this->assertEquals('0', $field->format('a'));
		$this->assertEquals("NULL", $field->format(null));
	}
}
