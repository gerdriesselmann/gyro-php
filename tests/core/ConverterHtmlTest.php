<?php
use PHPUnit\Framework\TestCase;

class ConverterHtmlTest extends TestCase {
	public function test_decode() {
		$test = "<p><a href=\"/test.html\">abc def <br />ghi</p>abc";
		$expected = "abc def \nghi\nabc";
		$result = ConverterFactory::decode($test, ConverterFactory::HTML);
		$this->assertEquals($expected, $result);
	}

	public function test_encode() {
		$test = "abc  def \nghi\nabc";
		$expected = "<p>abc def</p>\n<p>ghi</p>\n<p>abc</p>";
		$result = ConverterFactory::encode($test, ConverterFactory::HTML);
		$this->assertEquals($expected, $result);
	}
}
