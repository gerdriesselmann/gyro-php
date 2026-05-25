<?php
use PHPUnit\Framework\TestCase;

class RuntimeCacheTest extends TestCase {
	public function test_get_set() {
		RuntimeCache::set('key', 12345);
		$this->assertEquals(12345, RuntimeCache::get('key', false));

		RuntimeCache::set('key', 'abcde');
		$this->assertEquals('abcde', RuntimeCache::get('key', false));

		RuntimeCache::set(array('key2'), 12345);
		$this->assertEquals(12345, RuntimeCache::get(array('key2'), false));

		RuntimeCache::set(array('key2'), 'abcde');
		$this->assertEquals('abcde', RuntimeCache::get(array('key2'), false));
	}
}
