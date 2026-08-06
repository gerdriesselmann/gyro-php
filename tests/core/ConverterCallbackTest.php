<?php
use PHPUnit\Framework\TestCase;

class ConverterCallbackTest extends TestCase {
	public function test_encode() {
		$converter = ConverterFactory::create(ConverterFactory::CALLBACK);
		$result = $converter->encode('hello', 'strtoupper');
		$this->assertEquals('HELLO', $result);
	}

	public function test_decode() {
		$converter = ConverterFactory::create(ConverterFactory::CALLBACK);
		$result = $converter->decode('HELLO', 'strtolower');
		$this->assertEquals('hello', $result);
	}

	public function test_encode_with_closure() {
		$converter = ConverterFactory::create(ConverterFactory::CALLBACK);
		$result = $converter->encode('test', function ($v) { return str_repeat($v, 2); });
		$this->assertEquals('testtest', $result);
	}
}
