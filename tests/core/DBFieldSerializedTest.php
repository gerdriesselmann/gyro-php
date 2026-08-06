<?php
use PHPUnit\Framework\TestCase;

class DBFieldSerializedTest extends TestCase {
	public function test_convert_result() {
		$field = new DBFieldSerialized('data');
		$this->assertEquals(array(1, 2, 3), $field->convert_result(serialize(array(1, 2, 3))));
		$this->assertEquals('hello', $field->convert_result(serialize('hello')));
		$this->assertEquals(42, $field->convert_result(serialize(42)));
	}

	public function test_default_value() {
		$field = new DBFieldSerialized('data');
		$this->assertNull($field->get_field_default());
	}

	public function test_format() {
		$field = new DBFieldSerialized('data');
		$formatted = $field->format(array('key' => 'value'));
		$this->assertStringContainsString('key', $formatted);
		$this->assertEquals('NULL', $field->format(null));
	}
}
