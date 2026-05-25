<?php
use PHPUnit\Framework\TestCase;

class RequestInfoTest extends TestCase {
	public function test_is_ssl_with_https_on() {
		$ri = RequestInfo::create(array('HTTPS' => 'on'));
		$this->assertTrue($ri->is_ssl());
	}

	public function test_is_ssl_with_https_off() {
		$ri = RequestInfo::create(array('HTTPS' => 'off'));
		$this->assertFalse($ri->is_ssl());
	}

	public function test_is_ssl_with_port_443() {
		$ri = RequestInfo::create(array('SERVER_PORT' => '443'));
		$this->assertTrue($ri->is_ssl());
	}

	public function test_is_ssl_with_port_80() {
		$ri = RequestInfo::create(array('SERVER_PORT' => '80'));
		$this->assertFalse($ri->is_ssl());
	}

	public function test_is_ssl_no_https() {
		$ri = RequestInfo::create(array());
		$this->assertFalse($ri->is_ssl());
	}

	public function test_method() {
		$ri = RequestInfo::create(array('REQUEST_METHOD' => 'POST'));
		$this->assertEquals('POST', $ri->method());

		$ri = RequestInfo::create(array('REQUEST_METHOD' => 'get'));
		$this->assertEquals('GET', $ri->method());

		// Default is GET
		$ri = RequestInfo::create(array());
		$this->assertEquals('GET', $ri->method());
	}

	public function test_is_forwarded() {
		$ri = RequestInfo::create(array('HTTP_X_FORWARDED_FOR' => '1.2.3.4'));
		$this->assertTrue($ri->is_forwarded());

		$ri = RequestInfo::create(array());
		$this->assertFalse($ri->is_forwarded());
	}

	public function test_remote_address() {
		$ri = RequestInfo::create(array('REMOTE_ADDR' => '192.168.1.1'));
		$this->assertEquals('192.168.1.1', $ri->remote_address());

		// Forwarded takes precedence
		$ri = RequestInfo::create(array(
			'REMOTE_ADDR' => '192.168.1.1',
			'HTTP_X_FORWARDED_FOR' => '10.0.0.1'
		));
		$this->assertEquals('10.0.0.1', $ri->remote_address(true));
		$this->assertEquals('192.168.1.1', $ri->remote_address(false));
	}

	public function test_remote_address_comma_separated() {
		$ri = RequestInfo::create(array(
			'HTTP_X_FORWARDED_FOR' => '10.0.0.1, 10.0.0.2'
		));
		$this->assertEquals('10.0.0.1', $ri->remote_address());
	}

	public function test_user_agent() {
		$ri = RequestInfo::create(array('HTTP_USER_AGENT' => 'TestBot/1.0'));
		$this->assertEquals('TestBot/1.0', $ri->user_agent());

		$ri = RequestInfo::create(array());
		$this->assertEquals('', $ri->user_agent());
	}

	public function test_header_value() {
		$ri = RequestInfo::create(array(
			'HTTP_ACCEPT' => 'text/html',
			'HTTP_ACCEPT_LANGUAGE' => 'en-US',
			'HTTP_X_CUSTOM_HEADER' => 'custom-value'
		));
		$this->assertEquals('text/html', $ri->header_value('Accept'));
		$this->assertEquals('en-US', $ri->header_value('Accept-Language'));
		$this->assertEquals('custom-value', $ri->header_value('X-Custom-Header'));
		$this->assertEquals('', $ri->header_value('Nonexistent'));
	}

	public function test_referer() {
		$ri = RequestInfo::create(array('HTTP_REFERER' => 'http://example.com'));
		$this->assertEquals('http://example.com', $ri->referer());

		$ri = RequestInfo::create(array());
		$this->assertEquals('', $ri->referer());
	}
}
