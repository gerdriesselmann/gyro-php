<?php
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase {
	private const TEST_URL = 'https://www.host.org/dir/file.ext?arg=value#anchor';
	private const TEST_URL2 = 'http://at.www.host.co.jp/dir/file.ext?arg=value&barg=othervalue&carg=&carg[]=1&carg[]=2#anchor';
	private const TEST_URL3 = 'www.host.org:8080/dir/file.ext';
	private const TEST_URL4 = 'http://example.com/?a&b=';

	private Url $url;
	private Url $url2;
	private Url $url3;
	private Url $url4;

	protected function setUp(): void {
		$this->url = new Url(self::TEST_URL);
		$this->url2 = new Url(self::TEST_URL2);
		$this->url3 = new Url(self::TEST_URL3);
		$this->url4 = new Url(self::TEST_URL4);
	}

	public function test_build(): void {
		$this->assertEquals(self::TEST_URL, $this->url->build());
		$this->assertEquals(self::TEST_URL2, $this->url2->build());
		$this->assertEquals('http://' . self::TEST_URL3, $this->url3->build());
		$this->assertEquals(self::TEST_URL4, $this->url4->build());
	}

	public function test_replace_param(): void {
		$this->assertEquals(self::TEST_URL, $this->url->replace_query_parameter('key', '')->build());

		$expect = str_replace('?arg=value', '?arg=value&key=other', self::TEST_URL);
		$this->assertEquals($expect, $this->url->replace_query_parameter('key', 'other')->build());

		$expect = str_replace('?arg=value', '?arg=value&key=set&key=twice', self::TEST_URL);
		$this->assertEquals($expect, $this->url->replace_query_parameter('key', array('set', 'twice'))->build());

		$this->assertEquals(self::TEST_URL, $this->url->replace_query_parameter('key', '')->build());

		$expect = str_replace('value', 'other', self::TEST_URL);
		$this->assertEquals($expect, $this->url->replace_query_parameter('arg', 'other')->build());

		$expect = str_replace('value', 'other%26me', self::TEST_URL);
		$this->assertEquals($expect, $this->url->replace_query_parameter('arg', 'other&me')->build());
	}

	public function test_set_path(): void {
		$expect = str_replace('dir/file.ext', 'ext/file.dir', self::TEST_URL);
		$this->assertEquals($expect, $this->url->set_path('ext/file.dir')->build());

		$expect = str_replace('dir/file.ext', '', self::TEST_URL);
		$this->assertEquals($expect, $this->url->set_path('')->build());
	}

	public function test_set_port(): void {
		$expect = str_replace(':8080', ':128', self::TEST_URL3);
		$this->assertEquals('http://' . $expect, $this->url3->set_port(128)->build());

		$expect = str_replace(':8080', '', self::TEST_URL3);
		$this->assertEquals('http://' . $expect, $this->url3->set_port(0)->build());
		$this->assertEquals('http://' . $expect, $this->url3->set_port(false)->build());
		$this->assertEquals('http://' . $expect, $this->url3->set_port('')->build());
	}

	public function test_getters(): void {
		$this->assertEquals('www.host.org', $this->url->get_host());
		$this->assertEquals('https', $this->url->get_scheme());
		$this->assertEquals('dir/file.ext', $this->url->get_path());
		$this->assertEquals('arg=value', $this->url->get_query());
		$this->assertEquals('anchor', $this->url->get_fragment());
	}

	public function test_parse_host(): void {
		$arr_host = $this->url->parse_host();
		$this->assertEquals('org', $arr_host['tld']);
		$this->assertEquals('host', $arr_host['sld']);
		$this->assertEquals('host.org', $arr_host['domain']);
		$this->assertEquals('www', $arr_host['subdomain']);

		$arr_host = $this->url2->parse_host();
		$this->assertEquals('co.jp', $arr_host['tld']);
		$this->assertEquals('host', $arr_host['sld']);
		$this->assertEquals('host.co.jp', $arr_host['domain']);
		$this->assertEquals('at.www', $arr_host['subdomain']);

		$url = Url::create('http://a.de/path/file.txt');
		$arr_host = $url->parse_host();
		$this->assertEquals('de', $arr_host['tld']);
		$this->assertEquals('a', $arr_host['sld']);
		$this->assertEquals('a.de', $arr_host['domain']);
		$this->assertEquals('', $arr_host['subdomain']);
	}

	public function test_host_to_lower(): void {
		$url = Url::create('http://www.domain.INFO/Some/Path');
		$this->assertEquals('www.domain.info', $url->get_host());
		$this->assertEquals('http://www.domain.info/Some/Path', $url->build());

		$url->set_host('www.domain.INFO');
		$this->assertEquals('www.domain.info', $url->get_host());

		$url->set_host('www.ÜMLAUT.INFO');
		$this->assertEquals('www.ümlaut.info', $url->get_host());
	}

	public function test_is_valid(): void {
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
	}

	public function test_serialization(): void {
		$test = $this->url->build();
		$serialize = serialize($this->url);
		$this->assertStringContainsString($test, $serialize);

		$url_unserialized = unserialize($serialize);
		$this->assertEquals($test, $url_unserialized->build());
	}

	public function test_fragment_on_dir(): void {
		$in = 'http://www.example.org/in/a/dir/#p403187';
		$url = Url::create($in);
		$this->assertEquals('p403187', $url->get_fragment());
	}

	public function test_no_host(): void {
		$url = Url::create('http:///');
		$this->assertFalse($url->is_valid());
	}

	public function test_equals(): void {
		$a = Url::create('http://www.example.org/some/path/?a=b&c=d');
		$this->assertFalse($a->equals(''));
		$this->assertTrue($a->equals('http://www.example.org/some/path/?a=b&c=d#hash'));
		$this->assertTrue($a->equals('http://www.example.org/some/path/?a=b&c=d#otherhash'));
		$this->assertTrue($a->equals('http://www.example.org/some/path/#otherhash', Url::EQUALS_IGNORE_QUERY));

		$this->assertFalse($a->equals('http://www.example.org/some/path/?a=b&c=d&e=f#hash'));
		$this->assertFalse($a->equals('http://www.example.org/some/other/path/?a=b&c=d#hash'));
		$this->assertFalse($a->equals('http://www.example.net/some/path/?a=b&c=d#hash'));
		$this->assertFalse($a->equals('https://www.example.org/some/path/?a=b&c=d#hash'));
	}

	public function test_with_ip(): void {
		$a = Url::create('127.0.0.1/test');
		$this->assertTrue($a->is_valid());
	}

	public function test_is_ancestor_of(): void {
		$a = Url::create('http://www.example.org/some/path/?a=b&c=d#f');
		$this->assertFalse($a->is_ancestor_of('/some/path/deeper'));
		$this->assertTrue($a->is_ancestor_of('/some/path/'));
		$this->assertTrue($a->is_ancestor_of('/some/path/#g'));
		$this->assertTrue($a->is_ancestor_of('/some/path/?blah=blubb'));
		$this->assertTrue($a->is_ancestor_of('/some/path'));
		$this->assertTrue($a->is_ancestor_of('/some/'));
		$this->assertTrue($a->is_ancestor_of('/some'));
		$this->assertFalse($a->is_ancestor_of('/'));
		$this->assertFalse($a->is_ancestor_of(''));

		$a = Url::create('http://www.example.org/#f');
		$this->assertFalse($a->is_ancestor_of('/some/path/deeper'));
		$this->assertFalse($a->is_ancestor_of('/some'));
		$this->assertTrue($a->is_ancestor_of('/'));
		$this->assertTrue($a->is_ancestor_of(''));
	}

	public function test_is_same_as(): void {
		$a = Url::create('http://www.example.org/some/path/?a=b&c=d#f');
		$this->assertFalse($a->is_same_as('/some/path/deeper'));
		$this->assertTrue($a->is_same_as('/some/path/'));
		$this->assertTrue($a->is_same_as('/some/path/?blah=blubb'));
		$this->assertTrue($a->is_same_as('/some/path/#h'));
		$this->assertFalse($a->is_same_as('/some/path'));
		$this->assertFalse($a->is_same_as('/some/'));

		$a = Url::create('http://www.example.org/some/path?a=b&c=d#f');
		$this->assertTrue($a->is_same_as('/some/path'));
		$this->assertTrue($a->is_same_as('/some/path?blah=blubb'));
		$this->assertFalse($a->is_same_as('/some/path/'));
	}

	public function test_encoding(): void {
		$a = Url::create("http://www.example.org/");
		$a->replace_query_parameter('unenc', " n\n\r&%=");
		$this->assertEquals(" n\n\r&%=", $a->get_query_param('unenc', false, Url::NO_ENCODE_PARAMS));
		$this->assertEquals("+n%0A%0D%26%25%3D", $a->get_query_param('unenc', false, Url::ENCODE_PARAMS));
		$this->assertEquals("http://www.example.org/?unenc= n\n\r&%=", $a->build(Url::ABSOLUTE, Url::NO_ENCODE_PARAMS));
		$this->assertEquals("http://www.example.org/?unenc=+n%0A%0D%26%25%3D", $a->build());
	}

	public function test_with_mailto(): void {
		$a = Url::create('mailto:user@domain.com', Url::ALL_PROTOCOLS);
		$this->assertEquals('mailto', $a->get_scheme());
		$this->assertEquals('user@domain.com', $a->get_path());
		$this->assertEquals('', $a->get_host());
	}

	public function test_with_tel(): void {
		$a = Url::create('tel:+1234567890', Url::ALL_PROTOCOLS);
		$this->assertEquals('tel', $a->get_scheme());
		$this->assertEquals('+1234567890', $a->get_path());
		$this->assertEquals('', $a->get_host());
	}

	public function test_empty_query(): void {
		$url = Url::create('http://www.example.com/?');
		$this->assertEquals('http://www.example.com/?', $url->build(Url::ABSOLUTE));

		$url->set_query("");
		$this->assertEquals('http://www.example.com/', $url->build(Url::ABSOLUTE));
	}

	public function test_wrong_query(): void {
		$test = 'https://example.com/path/?=a';
		$url = Url::create($test);
		$this->assertEquals($test, $url->build(Url::ABSOLUTE));

		$query = $url->get_query_params();
		$this->assertEquals(array('' => 'a'), $query);
	}

	public function test_get_query_param(): void {
		$this->assertEquals('value', $this->url->get_query_param('arg'));

		$this->url->set_query("q=a&q=b");
		$this->assertEquals('a', $this->url->get_query_param('q'));

		$this->url->set_query("q[]=a&q[]=b");
		$this->assertEquals(array('a', 'b'), $this->url->get_query_param('q[]'));
	}
}
