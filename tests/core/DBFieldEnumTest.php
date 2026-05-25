<?php
use PHPUnit\Framework\TestCase;

class DBFieldEnumTest extends TestCase {
	public function test_validate_allowed() {
		$field = new DBFieldEnum('status', array('active', 'inactive', 'pending'));
		$this->assertTrue($field->validate('active')->is_ok());
		$this->assertTrue($field->validate('inactive')->is_ok());
		$this->assertTrue($field->validate('pending')->is_ok());
	}

	public function test_validate_not_allowed() {
		$field = new DBFieldEnum('status', array('active', 'inactive'));
		$this->assertTrue($field->validate('deleted')->is_error());
	}

	public function test_validate_null_allowed() {
		$field = new DBFieldEnum('status', array('active', 'inactive'));
		$this->assertTrue($field->validate(null)->is_ok());
	}

	public function test_validate_not_null() {
		$field = new DBFieldEnum('status', array('active', 'inactive'), null, DBField::NOT_NULL);
		$this->assertTrue($field->validate(null)->is_error());
	}

	public function test_format() {
		$field = new DBFieldEnum('status', array('active', 'inactive'));
		$this->assertEquals("'active'", $field->format('active'));
		$this->assertEquals('NULL', $field->format(null));
	}
}
