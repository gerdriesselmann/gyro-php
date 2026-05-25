<?php
use PHPUnit\Framework\TestCase;

class TracedArrayTest extends TestCase {
	public function test_get_item() {
		$ta = new TracedArray(array('key1' => 'val1', 'key2' => 'val2'));
		$this->assertEquals('val1', $ta->get_item('key1'));
		$this->assertEquals('val2', $ta->get_item('key2'));
		$this->assertEquals('default', $ta->get_item('missing', 'default'));
	}

	public function test_count() {
		$ta = new TracedArray(array('a' => 1, 'b' => 2, 'c' => 3));
		$this->assertEquals(3, $ta->count());

		$ta = new TracedArray(array());
		$this->assertEquals(0, $ta->count());
	}

	public function test_contains() {
		$ta = new TracedArray(array('key1' => 'val1', 'key2' => null));
		$this->assertTrue($ta->contains('key1'));
		$this->assertTrue($ta->contains('key2')); // exists even if null
		$this->assertFalse($ta->contains('key3'));
	}

	public function test_has_unused() {
		$ta = new TracedArray(array('key1' => 'val1', 'key2' => 'val2'));
		$this->assertTrue($ta->has_unused());

		$ta->get_item('key1');
		$this->assertTrue($ta->has_unused()); // key2 still unused

		$ta->get_item('key2');
		$this->assertFalse($ta->has_unused()); // all accessed
	}

	public function test_get_used() {
		$ta = new TracedArray(array('key1' => 'val1', 'key2' => 'val2', 'key3' => 'val3'));
		$this->assertEquals(array(), $ta->get_used());

		$ta->get_item('key2');
		$this->assertEquals(array('key2'), $ta->get_used());

		$ta->get_item('key1');
		$this->assertEquals(array('key2', 'key1'), $ta->get_used());
	}

	public function test_mark_all_as_used() {
		$ta = new TracedArray(array('key1' => 'val1', 'key2' => 'val2'));
		$this->assertTrue($ta->has_unused());

		$ta->mark_all_as_used();
		$this->assertFalse($ta->has_unused());
	}

	public function test_get_array() {
		$arr = array('key1' => 'val1', 'key2' => 'val2');
		$ta = new TracedArray($arr);
		$this->assertEquals($arr, $ta->get_array());
	}

	public function test_empty_array() {
		$ta = new TracedArray(array());
		$this->assertEquals(0, $ta->count());
		$this->assertFalse($ta->has_unused());
		$this->assertEquals(array(), $ta->get_used());
	}
}
