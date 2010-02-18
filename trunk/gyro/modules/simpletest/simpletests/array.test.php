<?php
/**
 * Created on 10.11.2006
 *
 * @author Gerd Riesselmann
 */

class ArrayTest extends GyroUnitTestCase {
	public function test_implode() {
		$arr = array(
			'one' => '1',
			'two' => '2'
		);		
		
		$this->assertEqual('one=1&two=2', Arr::implode('&', $arr));
		$this->assertEqual('one-1=two-2', Arr::implode('=', $arr, '-'));
		
		$this->assertEqual('', Arr::implode('&', array()));
		
		$this->assertEqual('one=1', Arr::implode('&', array('one' => 1)));
		
		$arr = array('one', 'two');
		$this->assertEqual('0=one&1=two', Arr::implode('&', $arr));
	}	
	
	public function test_implode_tail() {
		$arr = array();
		$this->assertEqual('', Arr::implode_tail('&', '+', $arr));

		$arr = array('one');
		$this->assertEqual('one', Arr::implode_tail('&', '+', $arr));
		
		$arr = array('one', 'two');
		$this->assertEqual('one+two', Arr::implode_tail('&', '+', $arr));
		
		$arr = array('one', 'two', 'three');
		$this->assertEqual('one&two+three', Arr::implode_tail('&', '+', $arr));

		// Possible empty check failures
		$arr = array(0);
		$this->assertEqual('0', Arr::implode_tail('&', '+', $arr));

		$arr = array(0, 0);
		$this->assertEqual('0+0', Arr::implode_tail('&', '+', $arr));

		$arr = array(0, 0, 0);
		$this->assertEqual('0&0+0', Arr::implode_tail('&', '+', $arr));
	} 
	
	public function test_get_item() {
		$arr = array(
			'one' => 1
		);
		
		$this->assertEqual(1, Arr::get_item($arr, 'one', 2));
		$this->assertEqual('no way', Arr::get_item($arr, 'two', 'no way'));
		
		$this->assertEqual('no way', Arr::get_item(array(), 'two', 'no way'));

		$arr = array(
			'item'
		);
		
		$this->assertEqual('item', Arr::get_item($arr, 0, 'no way'));
		$this->assertEqual('no way', Arr::get_item($arr, 1, 'no way'));
	}

	public function test_clean() {
		$arr = array(
			'one' => 1,
			'two' => 2
		);
		
		$clean = array(
			'one' => 'none',
			'three' => 3
		);
		
		Arr::clean($clean, $arr);
		
		$this->assertEqual(2, count($clean));
		$this->assertEqual(1, Arr::get_item($clean, 'one', ''));
		$this->assertEqual(3, Arr::get_item($clean, 'three', ''));
		$this->assertFalse(isset($clean['two']));
	}
	
	public function test_force() {
		$this->assertEqual(array(1, 2), Arr::force(array(1, 2)));
		$this->assertEqual(array(1), Arr::force(1));
		$this->assertEqual(array(''), Arr::force(''));
		$this->assertEqual(array(), Arr::force('', false));
	}
	
	public function test_get_recursive() {
		$arr = array(
			'one' => 1,
			'two' => array(
				'three' => 3,
				'four' => array(
					5
				)
			)
		);
		$this->assertEqual(5, Arr::get_item_recursive($arr, 'two[four][0]', false));
		$this->assertEqual(1, Arr::get_item_recursive($arr, 'one', false));
		$this->assertEqual(false, Arr::get_item_recursive($arr, '', false));
		$this->assertEqual(false, Arr::get_item_recursive($arr, 'one[two]', false));
	}
	
	public function test_set_recursive() {
		$arr = array();
		
		$this->assertEqual(true, Arr::set_item_recursive($arr, 'key', 1));
		$test = array('key' => 1);
		$this->assertEqual($test, $arr);
		
		$arr = array();
		$this->assertEqual(true, Arr::set_item_recursive($arr, 'one[two]', 1));
		$test = array('one' => array('two' => 1));
		$this->assertEqual($test, $arr);
		
		$this->assertEqual(false, Arr::set_item_recursive($arr, 'one[two][three]', 1));
		$test = array('one' => array('two' => 1));
		$this->assertEqual($test, $arr);

		$this->assertEqual(false, Arr::set_item_recursive($arr, '', 1));
		$test = array('one' => array('two' => 1));
		$this->assertEqual($test, $arr);

		$this->assertEqual(true, Arr::set_item_recursive($arr, 'three', 4));
		$test = array('one' => array('two' => 1), 'three' => 4);
		$this->assertEqual($test, $arr);
	}
	
	public function test_unset_recursive() {
		$arr = array(
			'one' => 1,
			'two' => array(
				'three' => 3,
				'four' => array(
					5
				)
			)
		);
		
		Arr::unset_item_recursive($arr, 'one');
		$test = array(
			'two' => array(
				'three' => 3,
				'four' => array(
					5
				)
			)
		);
		$this->assertEqual($test, $arr);

		Arr::unset_item_recursive($arr, 'two[four][0]');
		$test = array(
			'two' => array(
				'three' => 3,
				'four' => array()
			)
		);
		$this->assertEqual($test, $arr);		
		
		Arr::unset_item_recursive($arr, 'does[not][exist]');
		$this->assertEqual($test, $arr);
	}	
}
