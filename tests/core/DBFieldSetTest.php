<?php
use PHPUnit\Framework\TestCase;

class DBFieldSetTest extends TestCase {
	public function test_validate_allowed() {
		$field = new DBFieldSet('perms', array('read', 'write', 'execute'));
		$this->assertTrue($field->validate(array('read'))->is_ok());
		$this->assertTrue($field->validate(array('read', 'write'))->is_ok());
		$this->assertTrue($field->validate(array())->is_ok());
	}

	public function test_validate_not_allowed() {
		$field = new DBFieldSet('perms', array('read', 'write', 'execute'));
		$this->assertTrue($field->validate(array('delete'))->is_error());
	}

	public function test_validate_null() {
		$field = new DBFieldSet('perms', array('read', 'write'));
		$this->assertTrue($field->validate(null)->is_ok());
	}

	public function test_format_and_convert() {
		$allowed = array('read', 'write', 'execute');
		$field = new DBFieldSet('perms', $allowed);

		// Format encodes to bitfield
		$formatted = $field->format(array('read'));
		$this->assertEquals(1, intval($formatted)); // pow(2, 0) = 1

		$formatted = $field->format(array('write'));
		$this->assertEquals(2, intval($formatted)); // pow(2, 1) = 2

		$formatted = $field->format(array('read', 'write'));
		$this->assertEquals(3, intval($formatted)); // 1 + 2 = 3

		$formatted = $field->format(array('read', 'execute'));
		$this->assertEquals(5, intval($formatted)); // 1 + 4 = 5
	}

	public function test_convert_result() {
		$allowed = array('read', 'write', 'execute');
		$field = new DBFieldSet('perms', $allowed);

		$this->assertEquals(array('read'), $field->convert_result(1));
		$this->assertEquals(array('write'), $field->convert_result(2));
		$this->assertEquals(array('read', 'write'), $field->convert_result(3));
		$this->assertEquals(array('execute'), $field->convert_result(4));
		$this->assertEquals(array('read', 'execute'), $field->convert_result(5));
		$this->assertEquals(array('read', 'write', 'execute'), $field->convert_result(7));
		$this->assertEquals(array(), $field->convert_result(0));
	}

	public function test_static_set_helpers() {
		$set = array('a', 'b');

		DBFieldSet::set_set_value($set, 'c');
		$this->assertEquals(array('a', 'b', 'c'), $set);

		// No duplicate
		DBFieldSet::set_set_value($set, 'b');
		$this->assertEquals(array('a', 'b', 'c'), $set);

		$this->assertTrue(DBFieldSet::set_has_value($set, 'a'));
		$this->assertTrue(DBFieldSet::set_has_value($set, 'c'));
		$this->assertFalse(DBFieldSet::set_has_value($set, 'd'));

		DBFieldSet::set_clear_value($set, 'b');
		$this->assertFalse(DBFieldSet::set_has_value($set, 'b'));
		$this->assertEquals(array('a', 'c'), array_values($set));
	}
}
