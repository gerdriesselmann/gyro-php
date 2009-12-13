<?php
class ConverterHtmlExTest extends GyroUnitTestCase {
	public function test_decode() {
		$test = "<p><a href=\"/test.html\">abc def <br />ghi</p>abc";
		$expected = "abc def \nghi\nabc";
		$result = ConverterFactory::decode($test, ConverterFactory::HTML_EX);
		$this->assertEqual($result, $expected);
	}
	
	public function test_encode() {
		$test = "abc  def \n\nWhen Wellignton  came to the crossroads, the devil already was waiting for him\rabc.";
		$expected = "<h2>abc  def</h2>\n<p>When Wellignton  came to the crossroads, the devil already was waiting for him</p>\n<p>abc.</p>";
		$result = ConverterFactory::encode($test, ConverterFactory::HTML_EX);
		$this->assertEqual($result, $expected);		
	}
}
