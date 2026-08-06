<?php
use PHPUnit\Framework\TestCase;

class GyroCookieConfigTest extends TestCase {
	public function test_defaults() {
		$config = new GyroCookieConfig();
		$this->assertNull($config->valid_seconds);
		$this->assertEquals('/', $config->path);
		$this->assertTrue($config->http_only);
		$this->assertEquals('', $config->domain);
		$this->assertFalse($config->ssl_only);
		$this->assertNull($config->same_site);
	}

	public function test_create() {
		$config = GyroCookieConfig::create(3600);
		$this->assertEquals(3600, $config->valid_seconds);
		$this->assertEquals('/', $config->path);
		$this->assertTrue($config->http_only);
	}

	public function test_expires_with_seconds() {
		$before = time();
		$config = GyroCookieConfig::create(3600);
		$expires = $config->expires();
		$after = time();

		$this->assertGreaterThanOrEqual($before + 3600, $expires);
		$this->assertLessThanOrEqual($after + 3600, $expires);
	}

	public function test_expires_without_seconds() {
		$config = new GyroCookieConfig();
		$this->assertNull($config->expires());
	}

	public function test_to_array() {
		$config = GyroCookieConfig::create(3600);
		$config->path = '/admin';
		$config->domain = '.example.com';
		$config->ssl_only = true;
		$config->http_only = true;
		$config->same_site = GyroCookieConfig::SAME_SITE_STRICT;

		$arr = $config->to_array();

		$this->assertArrayHasKey('expires', $arr);
		$this->assertEquals('/admin', $arr['path']);
		$this->assertEquals('.example.com', $arr['domain']);
		$this->assertTrue($arr['secure']);
		$this->assertTrue($arr['httponly']);
		$this->assertEquals('Strict', $arr['samesite']);
	}

	public function test_to_array_without_samesite() {
		$config = GyroCookieConfig::create(3600);
		$arr = $config->to_array();

		$this->assertArrayNotHasKey('samesite', $arr);
	}

	public function test_samesite_constants() {
		$this->assertEquals('None', GyroCookieConfig::SAME_SITE_NONE);
		$this->assertEquals('Lax', GyroCookieConfig::SAME_SITE_LAX);
		$this->assertEquals('Strict', GyroCookieConfig::SAME_SITE_STRICT);
	}
}
