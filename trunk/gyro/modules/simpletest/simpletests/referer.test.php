<?php
/*
 * Created on 21.09.2006
 */
 
Load::components('referer');
 
class RefererTest extends GyroUnitTestCase {
	function test_is_empty() {
		$refer = Referer::create('');
		$this->assertTrue($refer->is_empty());

		$refer = Referer::create(' ');
		$this->assertTrue($refer->is_empty());

		$refer = Referer::create(null);
		$this->assertTrue($refer->is_empty());

		$refer = Referer::create('-');
		$this->assertFalse($refer->is_empty());
	}	
	
	function test_internal() {
		$referer = new Referer();
		$this->assertFalse($referer->is_internal());
		
		$referer = new Referer(Url::current()->build());
		$this->assertTrue($referer->is_internal());

		$referer = new Referer('http://www.google.de?q=34343434');
		$this->assertFalse($referer->is_internal());		
	}

	function test_external() {
		$referer = new Referer();
		$this->assertFalse($referer->is_external());
		
		$referer = new Referer(Url::current()->build());
		$this->assertFalse($referer->is_external());

		$referer = new Referer('http://www.google.de?q=34343434');
		$this->assertTrue($referer->is_external());		
	}
	
	function test_searchengine() {
		$referer = new Referer();
		$this->assertFalse($referer->search_engine_info());
		
		$referer = new Referer(Url::current()->build());
		$this->assertFalse($referer->search_engine_info());

		$referer = new Referer('http://www.google.de?q=searchme');
		$sei = $referer->search_engine_info();
		$this->assertTrue(is_array($sei));
		$this->assertEqual('google', $sei['searchengine']);
		$this->assertEqual('google.de', $sei['domain']);
		$this->assertEqual('www.google.de', $sei['host']);
		$this->assertEqual('searchme', $sei['keywords']);		

		$test = 'http://www.google.de/search?hl=de&ie=ISO-8859-1&q=Betriebsformen+des+Gro%DF-+und+Au%DFenhandels&meta=';
		$referer = new Referer($test);
		$this->assertEqual($test, $referer->get_original_referer_url());
		// We have ANSI encoding in $test,m this should have become UTF-8
		$this->assertNotEqual($test, $referer->build());
		$sei = $referer->search_engine_info();
		$this->assertEqual('Betriebsformen des Groß- und Außenhandels', $sei['keywords']);		

		$test = 'http://www.google.de/search?hl=de&q=www.weihnachtspl%C3%A4tzchen.de&meta=';
		$referer = new Referer($test);
		// We have UTF-8 encoding in $test, this should have stayed UTF-8
		$this->assertEqual($test, $referer->build());
		$sei = $referer->search_engine_info();
		$this->assertEqual('www.weihnachtsplätzchen.de', $sei['keywords']);		
	}
	
	function test_fragment() {
		$in = 'http://www.example.org/in/a/dir/#p403187';
		$test = Referer::create($in);
		$this->assertEqual($in, $test->build());		
	}
	
}
