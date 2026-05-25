<?php
use PHPUnit\Framework\TestCase;

class ArrayTest extends TestCase {
	public function test_implode() {
		$arr = array('one' => '1', 'two' => '2');
		$this->assertEquals('one=1&two=2', Arr::implode('&', $arr));
		$this->assertEquals('one-1=two-2', Arr::implode('=', $arr, '-'));
		$this->assertEquals('', Arr::implode('&', array()));
		$this->assertEquals('one=1', Arr::implode('&', array('one' => 1)));

		$arr = array('one', 'two');
		$this->assertEquals('0=one&1=two', Arr::implode('&', $arr));
	}

	public function test_implode_tail() {
		$this->assertEquals('', Arr::implode_tail('&', '+', array()));
		$this->assertEquals('one', Arr::implode_tail('&', '+', array('one')));
		$this->assertEquals('one+two', Arr::implode_tail('&', '+', array('one', 'two')));
		$this->assertEquals('one&two+three', Arr::implode_tail('&', '+', array('one', 'two', 'three')));

		// Possible empty check failures
		$this->assertEquals('0', Arr::implode_tail('&', '+', array(0)));
		$this->assertEquals('0+0', Arr::implode_tail('&', '+', array(0, 0)));
		$this->assertEquals('0&0+0', Arr::implode_tail('&', '+', array(0, 0, 0)));
	}

	public function test_get_item() {
		$arr = array('one' => 1);
		$this->assertEquals(1, Arr::get_item($arr, 'one', 2));
		$this->assertEquals('no way', Arr::get_item($arr, 'two', 'no way'));
		$this->assertEquals('no way', Arr::get_item(array(), 'two', 'no way'));

		$arr = array('item');
		$this->assertEquals('item', Arr::get_item($arr, 0, 'no way'));
		$this->assertEquals('no way', Arr::get_item($arr, 1, 'no way'));
	}

	public function test_clean() {
		$arr = array('one' => 1, 'two' => 2);
		$clean = array('one' => 'none', 'three' => 3);
		Arr::clean($clean, $arr);

		$this->assertCount(2, $clean);
		$this->assertEquals(1, Arr::get_item($clean, 'one', ''));
		$this->assertEquals(3, Arr::get_item($clean, 'three', ''));
		$this->assertArrayNotHasKey('two', $clean);
	}

	public function test_force() {
		$this->assertEquals(array(1, 2), Arr::force(array(1, 2)));
		$this->assertEquals(array(1), Arr::force(1));
		$this->assertEquals(array(''), Arr::force(''));
		$this->assertEquals(array(), Arr::force('', false));
	}

	public function test_get_recursive() {
		$arr = array(
			'one' => 1,
			'two' => array(
				'three' => 3,
				'four' => array(5)
			)
		);
		$this->assertEquals(5, Arr::get_item_recursive($arr, 'two[four][0]', false));
		$this->assertEquals(1, Arr::get_item_recursive($arr, 'one', false));
		$this->assertFalse(Arr::get_item_recursive($arr, '', false));
		$this->assertFalse(Arr::get_item_recursive($arr, 'one[two]', false));
	}

	public function test_set_recursive() {
		$arr = array();
		$this->assertTrue(Arr::set_item_recursive($arr, 'key', 1));
		$this->assertEquals(array('key' => 1), $arr);

		$arr = array();
		$this->assertTrue(Arr::set_item_recursive($arr, 'one[two]', 1));
		$this->assertEquals(array('one' => array('two' => 1)), $arr);

		$this->assertFalse(Arr::set_item_recursive($arr, 'one[two][three]', 1));
		$this->assertEquals(array('one' => array('two' => 1)), $arr);

		$this->assertFalse(Arr::set_item_recursive($arr, '', 1));

		$this->assertTrue(Arr::set_item_recursive($arr, 'three', 4));
		$this->assertEquals(array('one' => array('two' => 1), 'three' => 4), $arr);
	}

	public function test_unset_recursive() {
		$arr = array(
			'one' => 1,
			'two' => array('three' => 3, 'four' => array(5))
		);

		Arr::unset_item_recursive($arr, 'one');
		$this->assertEquals(array('two' => array('three' => 3, 'four' => array(5))), $arr);

		Arr::unset_item_recursive($arr, 'two[four][0]');
		$this->assertEquals(array('two' => array('three' => 3, 'four' => array())), $arr);

		$expected = $arr;
		Arr::unset_item_recursive($arr, 'does[not][exist]');
		$this->assertEquals($expected, $arr);
	}

	public function test_remove() {
		$arr = array('a', 2, 3, 'b', 'a', 'k' => 'b');

		Arr::remove($arr, 'a');
		$this->assertEquals(array(1 => 2, 2 => 3, 3 => 'b', 'k' => 'b'), $arr);

		Arr::remove($arr, '2');
		$this->assertEquals(array(2 => 3, 3 => 'b', 'k' => 'b'), $arr);

		Arr::remove($arr, 'b');
		$this->assertEquals(array(2 => 3), $arr);
	}

	public function test_remove_recursive() {
		$arr = array('a', 2, 3, 'b', 'a', 'k' => array('b'), 'l' => array(1, 2, array('x', 'a')));

		Arr::remove_recursive($arr, 'a');
		$this->assertEquals(array(1 => 2, 2 => 3, 3 => 'b', 'k' => array('b'), 'l' => array(1, 2, array('x'))), $arr);

		Arr::remove_recursive($arr, '2');
		$this->assertEquals(array(2 => 3, 3 => 'b', 'k' => array('b'), 'l' => array(1, 2 => array('x'))), $arr);

		Arr::remove_recursive($arr, 'b');
		$this->assertEquals(array(2 => 3, 'k' => array(), 'l' => array(1, 2 => array('x'))), $arr);
	}
}
