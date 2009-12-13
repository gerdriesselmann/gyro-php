<?php
class DBFieldTextTest extends GyroUnitTestCase {
	public function test_validate() {
		$field = new DBFieldText('text');
		$this->assertStatusSuccess($field->validate(''));	
		$this->assertStatusSuccess($field->validate('a'));
		$this->assertStatusSuccess($field->validate(null));
		$teststring = str_pad('', 254, 'a');
		$this->assertStatusSuccess($field->validate($teststring));
		$teststring .= 'b';
		$this->assertStatusSuccess($field->validate($teststring));
		$teststring .= 'c';
		$this->assertStatusError($field->validate($teststring));

	
		$field = new DBFieldText('text', 5, '', DBFieldText::NOT_NULL);
		$this->assertStatusError($field->validate(''));	
		$this->assertStatusSuccess($field->validate('a'));
		$this->assertStatusError($field->validate(null));
		$this->assertStatusSuccess($field->validate('12345'));
		$this->assertStatusError($field->validate('123456'));
	}
	
	public function test_format() {
		$field = new DBFieldText('text');
		$this->assertEqual("'a'", $field->format('a'));
		$this->assertEqual("'\'a'", $field->format("'a"));
		$this->assertEqual("NULL", $field->format(null));
	}
}
