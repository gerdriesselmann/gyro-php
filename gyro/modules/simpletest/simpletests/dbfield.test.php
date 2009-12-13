<?php
class DBFieldTest extends GyroUnitTestCase  {
	public function test_get_set() {
		$field = new DBField('name');
		$this->assertEqual('name', $field->get_field_name());
		$this->assertNull($field->get_field_default());
		$this->assertTrue($field->get_null_allowed());

		$field = new DBField('name', 'value', DBField::NOT_NULL);
		$this->assertEqual('name', $field->get_field_name());
		$this->assertEqual('value', $field->get_field_default());
		$this->assertFalse($field->get_null_allowed());
	}
	
	public function test_validate() {
		$field = new DBField('name');
		$this->assertStatusSuccess($field->validate(false));
		$this->assertStatusSuccess($field->validate(null));
		$this->assertStatusSuccess($field->validate('value'));

		$field = new DBField('name', 'value', DBField::NOT_NULL);
		$this->assertStatusSuccess($field->validate(false));
		$this->assertStatusSuccess($field->validate(null));
		$this->assertStatusSuccess($field->validate('value'));

		$field = new DBField('name', null, DBField::NOT_NULL);
		$this->assertStatusSuccess($field->validate(false));
		$this->assertStatusError($field->validate(null));
		$this->assertStatusSuccess($field->validate('value'));
		
	}
	
	public function test_format() {
		$field = new DBField('name');
		$this->assertEqual("'value'", $field->format("value"));
		$this->assertEqual("'\'value\''", $field->format("'value'"));
		$this->assertEqual('NULL', $field->format(null));
	}
}
