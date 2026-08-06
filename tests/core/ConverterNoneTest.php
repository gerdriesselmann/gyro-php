<?php
use PHPUnit\Framework\TestCase;

class ConverterNoneTest extends TestCase {
	public function test_encode() {
		$converter = ConverterFactory::create(ConverterFactory::NONE);
		$this->assertEquals('hello', $converter->encode('hello'));
		$this->assertEquals(42, $converter->encode(42));
		$this->assertEquals('', $converter->encode(''));
		$this->assertNull($converter->encode(null));
	}

	public function test_decode() {
		$converter = ConverterFactory::create(ConverterFactory::NONE);
		$this->assertEquals('hello', $converter->decode('hello'));
		$this->assertEquals(42, $converter->decode(42));
	}
}
