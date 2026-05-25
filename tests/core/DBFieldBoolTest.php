<?php
use PHPUnit\Framework\TestCase;

class DBFieldBoolTest extends TestCase {
	public function test_format() {
		$field = new DBFieldBool('active');
		$this->assertEquals("'TRUE'", $field->format(true));
		$this->assertEquals("'TRUE'", $field->format(1));
		$this->assertEquals("'FALSE'", $field->format(false));
		$this->assertEquals("'FALSE'", $field->format(0));
		// null goes through parent format() which returns 'NULL' (SQL NULL)
		$this->assertEquals('NULL', $field->format(null));
	}

	public function test_convert_result() {
		$field = new DBFieldBool('active');
		$this->assertTrue($field->convert_result('TRUE'));
		$this->assertTrue($field->convert_result('1'));
		$this->assertFalse($field->convert_result('FALSE'));
		$this->assertFalse($field->convert_result('0'));
		$this->assertFalse($field->convert_result(''));
		$this->assertFalse($field->convert_result(null));
	}

	public function test_default_value() {
		$field = new DBFieldBool('active');
		$this->assertFalse($field->get_field_default());

		$field = new DBFieldBool('active', true);
		$this->assertTrue($field->get_field_default());
	}
}
