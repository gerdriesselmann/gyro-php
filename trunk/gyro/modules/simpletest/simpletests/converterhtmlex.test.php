<?php
class ConverterHtmlExTest extends GyroUnitTestCase {
	public function test_decode() {
		$test = "<p>\n<a href=\"/test.html\">abc def <br />ghi\n</p>\nabc";
		$expected = "abc def \nghi\nabc";
		$result = ConverterFactory::decode($test, ConverterFactory::HTML_EX);
		$this->assertEqual($result, $expected);

		$test = "<p><a href=\"/test.html\">abc def</a> <br />ghi</p>abc";
		$expected = 'abc def: ' . Config::get_url(Config::URL_BASEURL) . "test.html \nghi\nabc";
		$result = ConverterFactory::decode($test, ConverterFactory::HTML_EX);
		$this->assertEqual($result, $expected);
		
		// Params
		// Other anchor format 
		$result = ConverterFactory::decode($test, ConverterFactory::HTML_EX, array('a' => '$title$ ($url$)'));
		$expected = 'abc def (' . Config::get_url(Config::URL_BASEURL) . "test.html) \nghi\nabc";
		$this->assertEqual($result, $expected);
		// p with two \n
		$result = ConverterFactory::decode($test, ConverterFactory::HTML_EX, array('p' => "\n\n"));
		$expected = 'abc def: ' . Config::get_url(Config::URL_BASEURL) . "test.html \nghi\n\nabc";
		$this->assertEqual($result, $expected);
		// br
		$result = ConverterFactory::decode($test, ConverterFactory::HTML_EX, array('br' => "\n\n"));
		$expected = 'abc def: ' . Config::get_url(Config::URL_BASEURL) . "test.html \n\nghi\nabc";
		$this->assertEqual($result, $expected);
	}
	
	public function test_encode() {
		$test = "abc  def \n\nWhen Wellignton  came to the crossroads, the devil already was waiting for him\rabc.";
		$expected = "<h2>abc def</h2>\n<p>When Wellignton came to the crossroads, the devil already was waiting for him</p>\n<p>abc.</p>";
		$result = ConverterFactory::encode($test, ConverterFactory::HTML_EX);
		
		$this->assertEqual($result, $expected);
	}
}
