<?php
/**
 * Test cache
 */
class CacheTest extends GyroUnitTestCase {
	private $config_cache = false;
	
	public function setUp() {
		$this->config_cache = Config::has_feature(Config::DISABLE_CACHE);
		Config::set_feature(Config::DISABLE_CACHE, false);
		Cache::clear();
	}
	
	public function tearDown() {
		Config::set_feature(Config::DISABLE_CACHE, $this->config_cache);
	}
	
	public function test_set_get() {
		$keys = array(
			array(),
			array('a'),
			array('a', 'b'),
			array('a', 'b', 'c'),
		);
		foreach($keys as $val => $key) {
			Cache::store($key, $val, GyroDate::ONE_HOUR);
		}
		foreach($keys as $val => $key) {
			$this->assertEqual($val, Cache::read($key)->get_content_plain());
		}		
	}
	
	/**
	 * Array keys can be filled up with empty strings, which will get ignored. Test this!
	 */
	public function test_set_get_alias() {
		$keys = array(
			array(
				array(),
				array(''),
				array('', ''),
				array('', '' , ''),
			),
			array(
				array('a'),
				array('a', ''),
				array('a', '', ''),
			),
			array(
				array('a', 'b'),
				array('a', 'b', '')
			)
		);
		foreach($keys as $index => $arr_inner) {
			$c = count($arr_inner);
			for ($i = 0; $i < $c; $i++) {
				// Set $i on cache and check that all prior aliases now show the same value!
				$key = $arr_inner[$i];
				Cache::store($key, $i, GyroDate::ONE_HOUR);
				$this->assertEqual($i, Cache::read($key)->get_content_plain());
				for ($j = 0; $j < $i; $j++) {
					$key_test = $arr_inner[$j];
					$this->assertEqual($i, Cache::read($key_test)->get_content_plain());
				}
			}
		}
	}

	public function test_is_cached() {
		$keys = array(
			array(),
			array('a'),
			array('a', 'b'),
			array('a', 'b', 'c'),
		);
		foreach($keys as $val => $key) {
			$this->assertFalse(Cache::is_cached($key));
			Cache::store($key, $val, GyroDate::ONE_HOUR);
			$this->assertTrue(Cache::is_cached($key));
		}
	}
	
	/**
	 * Array keys can be filled up with empty strings, which will get ignored. Test this!
	 */
	public function test_is_cached_alias() {
		$keys = array(
			array(
				array(),
				array(''),
				array('', ''),
				array('', '' , ''),
			),
			array(
				array('a'),
				array('a', ''),
				array('a', '', ''),
			),
			array(
				array('a', 'b'),
				array('a', 'b', '')
			)
		);
		foreach($keys as $index => $arr_inner) {
			$c = count($arr_inner);
			for ($i = 0; $i < $c; $i++) {
				// Set $i on cache and check that all prior aliases now show the same value!
				$key = $arr_inner[$i];
				if ($i == 0) {
					$this->assertFalse(Cache::is_cached($key));
				}
				else {
					$this->assertTrue(Cache::is_cached($key));
				}
				Cache::store($key, $i, GyroDate::ONE_HOUR);
				$this->assertTrue(Cache::is_cached($key));
			}
		}
	}	
	
	/**
	 * Test clearing cache
	 */
	public function test_clear() {
		$keys = array(
			array(),
			array('a'),
			array('a', 'b'),
			array('a', 'b', 'c'),
		);
		$c = count($keys);
		for ($i = 0; $i < $c; $i++) {
			// Fill cache
			foreach($keys as $val => $key) {
				Cache::store($key, $val, GyroDate::ONE_HOUR);
			}
			// Now delete entry $i => Should delete entries above, too!
			Cache::clear($keys[$i]);
			// Test if below is still there
			for ($j = 0; $j < $i; $j++) {
				$this->assertEqual($j, Cache::read($keys[$j])->get_content_plain());	
			}
			// But abopve (inc. $i) must be deleted
			for ($j = $i; $j < $c; $j++) {
				$this->assertEqual(null, Cache::read($keys[$j]));	
			}			 				
		}
	}
	
	/**
	 * Test clearing cache (where '' is important!)
	 */
	public function test_clear_alias() {
		$keys = array(
			array(),
			array('a'),
			array('a', 'b'),
			array('a', 'b', 'c'),
		);
		$c = count($keys);
		for ($i = 0; $i < $c - 1; $i++) {
			// Fill cache
			foreach($keys as $val => $key) {
				Cache::store($key, $val, GyroDate::ONE_HOUR);
			}
			// Now delete entry $i => Should delete entries above, too!
			$alias = $keys[$i];
			$alias[] = '';
			Cache::clear($alias);
			// Test if below is still there
			for ($j = 0; $j < $i; $j++) {
				$this->assertEqual($j, Cache::read($keys[$j])->get_content_plain());	
			}
			// $i should exist, too
			$this->assertEqual($i, Cache::read($keys[$i])->get_content_plain());
			// And above should be there, too
			for ($j = $i + 1; $j < $c; $j++) {
				$this->assertEqual($j, Cache::read($keys[$j])->get_content_plain());
			}			 				
		}
	}
}