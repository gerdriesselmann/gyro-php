<?php
class ConverterHtmlTest extends GyroUnitTestCase {
	public function test_decode() {
		$test = "<p><a href=\"/test.html\">abc def <br />ghi</p>abc";
		$expected = "abc def \nghi\nabc";
		$result = ConverterFactory::decode($test, ConverterFactory::HTML);
		$this->assertEqual($result, $expected);
	}
	
	public function test_encode() {
		$test = "abc  def \nghi\nabc";
		$expected = "<p>abc  def</p>\n<p>ghi</p>\n<p>abc</p>";
		$result = ConverterFactory::encode($test, ConverterFactory::HTML);
		$this->assertEqual($result, $expected);		
	}
}
