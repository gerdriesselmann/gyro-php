<?php
use PHPUnit\Framework\TestCase;

class RefererTest extends TestCase {
	public function test_is_empty() {
		$refer = Referer::create('');
		$this->assertTrue($refer->is_empty());

		$refer = Referer::create(' ');
		$this->assertTrue($refer->is_empty());

		$refer = Referer::create(null);
		$this->assertTrue($refer->is_empty());

		$refer = Referer::create('-');
		$this->assertFalse($refer->is_empty());
	}

	public function test_internal() {
		$referer = new Referer();
		$this->assertFalse($referer->is_internal());

		$referer = new Referer(Url::current()->build());
		$this->assertTrue($referer->is_internal());

		$referer = new Referer('http://www.google.de?q=34343434');
		$this->assertFalse($referer->is_internal());
	}

	public function test_external() {
		$referer = new Referer();
		$this->assertFalse($referer->is_external());

		$referer = new Referer(Url::current()->build());
		$this->assertFalse($referer->is_external());

		$referer = new Referer('http://www.google.de?q=34343434');
		$this->assertTrue($referer->is_external());
	}

	public function test_searchengine() {
		$referer = new Referer();
		$this->assertFalse($referer->search_engine_info());

		$referer = new Referer(Url::current()->build());
		$this->assertFalse($referer->search_engine_info());

		$referer = new Referer('http://www.google.de?q=searchme');
		$sei = $referer->search_engine_info();
		$this->assertIsArray($sei);
		$this->assertEquals('google', $sei['searchengine']);
		$this->assertEquals('google.de', $sei['domain']);
		$this->assertEquals('www.google.de', $sei['host']);
		$this->assertEquals('searchme', $sei['keywords']);

		$test = 'http://www.google.de/search?hl=de&q=www.weihnachtspl%C3%A4tzchen.de&meta=';
		$referer = new Referer($test);
		$this->assertEquals($test, $referer->build());
		$sei = $referer->search_engine_info();
		$this->assertEquals('www.weihnachtsplätzchen.de', $sei['keywords']);
	}

	public function test_fragment() {
		$in = 'http://www.example.org/in/a/dir/#p403187';
		$test = Referer::create($in);
		$this->assertEquals($in, $test->build());
	}
}
