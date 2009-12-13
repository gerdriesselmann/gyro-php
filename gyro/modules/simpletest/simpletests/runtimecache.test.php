<?php
class RuntimeCacheTest extends GyroUnitTestCase {
	public function test_get_set() {
		RuntimeCache::set('key', 12345);
		$this->assertEqual(12345, RuntimeCache::get('key', false));
		
		RuntimeCache::set('key', 'abcde');
		$this->assertEqual('abcde', RuntimeCache::get('key', false));		
		
		RuntimeCache::set(array('key2'), 12345);
		$this->assertEqual(12345, RuntimeCache::get(array('key2'), false));
		
		RuntimeCache::set(array('key2'), 'abcde');
		$this->assertEqual('abcde', RuntimeCache::get(array('key2'), false));		
	}
} 