<?php
/*
 * Created on 21.09.2006
 */
 
define ('TEST_URL', 'https://www.host.org/dir/file.ext?arg=value#anchor'); 
define ('TEST_URL2', 'http://at.www.host.co.jp/dir/file.ext?arg=value&barg=othervalue&carg=&carg[]=1&carg[]=2#anchor');
define ('TEST_URL3', 'www.host.org:8080/dir/file.ext');
 
class UrlTest extends GyroUnitTestCase {
	private $url;
	private $url1;
	private $url2;

	function setUp() {
  		$this->url = new Url(TEST_URL);
		$this->url2 = new Url(TEST_URL2);
		$this->url3 = new Url(TEST_URL3);
	}
    
	function test_build() {
		$this->assertEqual(TEST_URL, $this->url->build());
		$this->assertEqual(TEST_URL2, $this->url2->build());
		$this->assertEqual('http://' .TEST_URL3, $this->url3->build());
		
		// Test if Latin gets converted to UTF-8
		$url = Url::create('http://www.google.de/search?hl=de&ie=ISO-8859-1&q=Betriebsformen+des+Gro%DF-+und+Au%DFenhandels&meta=');
		$this->assertEqual('http://www.google.de/search?hl=de&ie=ISO-8859-1&q=Betriebsformen+des+Gro%C3%9F-+und+Au%C3%9Fenhandels&meta=', $url->build());	
	} 
	
	function test_replace_param() {
		// replacing non-existent should do nothing
		$this->assertEqual(TEST_URL, $this->url->replace_query_parameter('key', '')->build());

		$expect = str_replace('?arg=value', '?arg=value&key=other', TEST_URL);
		$this->assertEqual($expect, $this->url->replace_query_parameter('key', 'other')->build());

		$this->assertEqual(TEST_URL, $this->url->replace_query_parameter('key', '')->build());

		$expect = str_replace('value', 'other', TEST_URL);
		$this->assertEqual($expect, $this->url->replace_query_parameter('arg', 'other')->build());		

		$expect = str_replace('value', 'other%26me', TEST_URL); // %26 = &!
		$this->assertEqual($expect, $this->url->replace_query_parameter('arg', 'other&me')->build());		
	}
	
	function test_set_path() {
		$expect = str_replace('dir/file.ext', 'ext/file.dir', TEST_URL);
		$this->assertEqual($expect, $this->url->set_path('ext/file.dir')->build());
		
		$expect = str_replace('/dir/file.ext', '', TEST_URL);
		$this->assertEqual($expect, $this->url->set_path('')->build());
	}
	
	function test_set_port() {
		$expect = str_replace(':8080', ':128', TEST_URL3);
		$this->assertEqual('http://' . $expect, $this->url3->set_port(128)->build());
		
		$expect = str_replace(':8080', '', TEST_URL3);
		$this->assertEqual('http://' . $expect, $this->url3->set_port(0)->build());
		$this->assertEqual('http://' . $expect, $this->url3->set_port(false)->build());
		$this->assertEqual('http://' . $expect, $this->url3->set_port('')->build());
	}
	
	function test_getters() {
		$expect = 'www.host.org';
		$this->assertEqual($expect, $this->url->get_host());
		
		$expect = 'https';
		$this->assertEqual($expect, $this->url->get_scheme());
		
		$expect = 'dir/file.ext';
		$this->assertEqual($expect, $this->url->get_path());
		
		$expect = 'arg=value';
		$this->assertEqual($expect, $this->url->get_query());
		
		$expect = 'anchor';
		$this->assertEqual($expect, $this->url->get_fragment());
	}
	
	function test_parse_host() {
		$arr_host = $this->url->parse_host();
		$this->assertEqual('org', $arr_host['tld']);
		$this->assertEqual('host', $arr_host['sld']);
		$this->assertEqual('host.org', $arr_host['domain']);
		$this->assertEqual('www', $arr_host['subdomain']);

		$arr_host = $this->url2->parse_host();
		$this->assertEqual('co.jp', $arr_host['tld']);
		$this->assertEqual('host', $arr_host['sld']);
		$this->assertEqual('host.co.jp', $arr_host['domain']);
		$this->assertEqual('at.www', $arr_host['subdomain']);
		
		$url = Url::create('http://a.de/path/file.txt');
		$arr_host = $url->parse_host();
		$this->assertEqual('de', $arr_host['tld']);
		$this->assertEqual('a', $arr_host['sld']);
		$this->assertEqual('a.de', $arr_host['domain']);
		$this->assertEqual('', $arr_host['subdomain']);
	}
	
	function test_host_to_lower() {
		$url = Url::create('http://www.domain.INFO/Some/Path');
		$this->assertEqual('www.domain.info', $url->get_host());
		$this->assertEqual('http://www.domain.info/Some/Path', $url->build());
	}
	
	public function test_is_valid() {
		$this->assertTrue($this->url->is_valid());
		$this->assertTrue($this->url2->is_valid());
		
		$url = new Url('ftp://de/dir/file.ext');
		$this->assertFalse($url->is_valid());
		
		$this->assertFalse(Url::create('http://lenuw.comConnection: close')->is_valid());
		$this->assertFalse(Url::create('http://www.,utzumleben.info')->is_valid());
		$this->assertFalse(Url::create('http://www. hobbyplace.de')->is_valid());
		$this->assertFalse(Url::create('http://www.aula-institut..de')->is_valid());
		$this->assertFalse(Url::create('http://www..bam-bini-shop.de')->is_valid());
		$this->assertFalse(Url::create('http://www,pkv-preiswaerter.de')->is_valid());
		$this->assertFalse(Url::create('http://www.deltus-mDeltus Media, Bücher über erfolgreiche Menschenedia.de')->is_valid());		
	}
	
	public function test_current() {
		$cur1 = Url::current();
		$cur2 = Url::current();
		$this->assertClone($cur1, $cur2);
		$this->assertEqual($cur1->build(), $cur2->build());
	}

	public function test_serialization() {
		$test = $this->url->build();
		$serialize = serialize($this->url);
		$this->assertTrue(strpos($serialize, $test) !== false);
		
		$url_unserialized = unserialize($serialize);
		$this->assertEqual($test, $url_unserialized->build());
	}
	
	public function test_affilinet_tracking_urls() {
		$in = "http://partners.webmasterplan.com/click.asp?ref=485184&site=5635&type=text&tnb=19&diurl=http://www.livingtools.de/product_info.php?info=p1234_-Amex---Das-Kultboot---.html";
		$url = Url::create($in);
		
		$this->assertEqual($in, $url->build(Url::ABSOLUTE, URl::NO_ENCODE_PARAMS));
	}
}

