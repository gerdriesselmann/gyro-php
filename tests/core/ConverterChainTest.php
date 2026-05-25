<?php
use PHPUnit\Framework\TestCase;

class ConverterChainTest extends TestCase {
	public function test_encode_chain() {
		$chain = new ConverterChain();
		$chain->append(ConverterFactory::create(ConverterFactory::HTML));
		$chain->append(ConverterFactory::create(ConverterFactory::NONE));

		$result = $chain->encode("hello\nworld");
		$this->assertStringContainsString('<p>', $result);
	}

	public function test_decode_chain() {
		$chain = new ConverterChain();
		$chain->append(ConverterFactory::create(ConverterFactory::HTML));

		$result = $chain->decode('<p>hello</p><p>world</p>');
		$this->assertStringContainsString('hello', $result);
		$this->assertStringContainsString('world', $result);
		$this->assertStringNotContainsString('<p>', $result);
	}

	public function test_empty_chain() {
		$chain = new ConverterChain();
		$this->assertEquals('test', $chain->encode('test'));
		$this->assertEquals('test', $chain->decode('test'));
	}
}
