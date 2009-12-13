<?php
class DBFieldIntTest extends GyroUnitTestCase {
	public function test_validate() {
		$field = new DBFieldInt('int', 0, DBField::NONE);
		$this->assertStatusSuccess($field->validate(1));
		$this->assertStatusSuccess($field->validate(-1));
		$this->assertStatusSuccess($field->validate('1'));	
		$this->assertStatusSuccess($field->validate(0));
		$this->assertStatusSuccess($field->validate(null));
	
		$field = new DBFieldInt('int', 0, DBFieldInt::NOT_NULL);
		$this->assertStatusSuccess($field->validate(1));
		$this->assertStatusSuccess($field->validate(0));
		$this->assertStatusSuccess($field->validate(null));

		$field = new DBFieldInt('int', null, DBFieldInt::NOT_NULL);
		$this->assertStatusSuccess($field->validate(1));
		$this->assertStatusSuccess($field->validate(0));
		$this->assertStatusError($field->validate(null));
		
		$field = new DBFieldInt('int', 0, DBFieldInt::UNSIGNED);
		$this->assertStatusSuccess($field->validate(1));
		$this->assertStatusError($field->validate(-1));
		$this->assertStatusSuccess($field->validate(0));
	}
	
	public function test_format() {
		$field = new DBFieldInt('int', 0, DBField::NONE);
		$this->assertEqual('1', $field->format(1));		
		$this->assertEqual('0', $field->format('a'));
		$this->assertEqual("NULL", $field->format(null));
	}
}
