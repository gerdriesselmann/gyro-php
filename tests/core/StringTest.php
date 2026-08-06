<?php
use PHPUnit\Framework\TestCase;

class StringTest extends TestCase {
	private $locale;

	protected function setUp(): void {
		$this->locale = setlocale(LC_ALL, '0');
	}

	protected function tearDown(): void {
		setlocale(LC_ALL, $this->locale);
	}

	public function test_clear_html() {
		$this->assertEquals('&amp;', GyroString::clear_html('&'));
		$this->assertEquals('&gt;', GyroString::clear_html('>'));
		$this->assertEquals('&#039;', GyroString::clear_html("'"));
		$this->assertEquals('&quot;', GyroString::clear_html('"'));
		$this->assertEquals('Test', GyroString::clear_html('<a href="test">Test</a>'));
		$this->assertEquals('', GyroString::clear_html('<input type="text" name="password" value="topsecret">'));
		$this->assertEquals('topsecret', GyroString::clear_html('<textarea name="username">topsecret</textarea>'));
	}

	public function test_clear_html_xss_vectors() {
		$values = array(
			'\'\';!--"<XSS>=&{()}' => '<XSS',
			'<<p />script>alert("hallo");<</p>/script>' => 'script',
			"';alert(String.fromCharCode(88,83,83))//\';alert(String.fromCharCode(88,83,83))//\";alert(String.fromCharCode(88,83,83))//\";alert(String.fromCharCode(88,83,83))//--></SCRIPT>\">'><SCRIPT>alert(String.fromCharCode(88,83,83))</SCRIPT>" => '<script',
			'<SCRIPT SRC=http://ha.ckers.org/xss.js></SCRIPT>' => 'js',
			'<IMG SRC="javascript:alert(\'XSS\');">' => 'alert',
			'<iframe src=http://ha.ckers.org/scriptlet.html <' => 'http',
			'<XSS STYLE="xss:expression(alert(\'XSS\'))">' => 'XSS',
			'<sc<script>ript>' => 'script',
		);
		foreach ($values as $attack => $test) {
			$clean = GyroString::clear_html($attack);
			$this->assertSame(
				false,
				GyroString::strpos($clean, $test),
				'Not filtered: ' . htmlentities($attack, ENT_QUOTES, GyroLocale::get_charset())
			);
		}
	}

	public function test_escape() {
		$this->assertEquals('&lt;Test&gt;', GyroString::escape(' <Test> '));
		$this->assertEquals('&quot;Test&quot;', GyroString::escape(' "Test" '));
	}

	public function test_to_lower() {
		$this->assertEquals('this is a normal sentence', GyroString::to_lower('THiS IS A norMal SentencE'));
		$this->assertEquals('this is a normal sentence.', GyroString::to_lower('THIS IS A NORMAL SENTENCE.'));
		$this->assertEquals('1234567890', GyroString::to_lower('1234567890'));

		// UTF-8 Umlauts
		$this->assertEquals('ich hätte gerne ein äöüß oder äöü', GyroString::to_lower('ICH HÄtte Gerne EiN äöüß oder ÄÖÜ'));

		// Partial conversion
		$this->assertEquals('aBC', GyroString::to_lower('ABC', 1));
		$this->assertEquals('abC', GyroString::to_lower('ABC', 2));
		$this->assertEquals('abc', GyroString::to_lower('ABC', 3));
		$this->assertEquals('abc', GyroString::to_lower('ABC', 10));
	}

	public function test_to_upper() {
		$this->assertEquals('THIS IS A NORMAL SENTENCE', GyroString::to_upper('tHiS IS A norMal SentencE'));
		// PHP 8.x correctly converts ß to SS (German capital Eszett rule)
		$this->assertEquals('ICH HÄTTE GERNE EIN ÄÖÜSS ODER ÄÖÜ', GyroString::to_upper('ich hätte gerne ein äöüß oder ÄÖÜ'));

		// Partial conversion
		$this->assertEquals('Abc', GyroString::to_upper('abc', 1));
		$this->assertEquals('ABc', GyroString::to_upper('abc', 2));
		$this->assertEquals('ABC', GyroString::to_upper('abc', 3));
	}

	public function test_length() {
		$this->assertEquals(4, GyroString::length('this'));
		$this->assertEquals(26, GyroString::length('this is a normal sentence.'));
		$this->assertEquals(0, GyroString::length(''));
		// UTF-8
		$this->assertEquals(33, GyroString::length('ich hätte gerne ein äöüß oder ÄÖÜ'));
	}

	public function test_strpos() {
		$val = 'This is a normal sentence.';
		$this->assertEquals(0, GyroString::strpos($val, 'This'));
		$this->assertEquals(4, GyroString::strpos($val, ' is', 1));
		$this->assertEquals(25, GyroString::strpos($val, '.'));

		// UTF-8
		$val = 'ich hätte gerne ein äöüß oder ÄÖÜ';
		$this->assertEquals(5, GyroString::strpos($val, 'ä'));
	}

	public function test_substr() {
		$val = 'This is a normal sentence';
		$this->assertEquals('This', GyroString::substr($val, 0, 4));
		$this->assertEquals('s is a no', GyroString::substr($val, 3, 9));

		// UTF-8
		$val = 'ich hätte gerne ein äöüß oder ÄÖÜ';
		$this->assertEquals('ch hä', GyroString::substr($val, 1, 5));
		$this->assertEquals('äöüß ', GyroString::substr($val, 20, 5));
	}

	public function test_starts_with() {
		$val = 'This is a normal sentence';
		$this->assertTrue(GyroString::starts_with($val, 'T'));
		$this->assertTrue(GyroString::starts_with($val, 'This'));
		$this->assertFalse(GyroString::starts_with($val, 'A'));
		$this->assertFalse(GyroString::starts_with($val, ''));
	}

	public function test_ends_with() {
		$val = 'This is a normal sentence';
		$this->assertTrue(GyroString::ends_with($val, 'e'));
		$this->assertTrue(GyroString::ends_with($val, 'entence'));
		$this->assertFalse(GyroString::ends_with($val, 'A'));
		$this->assertFalse(GyroString::ends_with($val, ''));
	}

	public function test_plain_ascii() {
		$this->assertEquals('ich-haette-gerne-ein-aeoeuess-oder-aeoeue', GyroString::plain_ascii('ich hätte gerne ein äöüß oder ÄÖÜ', '-'));
		$this->assertEquals('camoes-sao-p-cachaca-en-espana-y-citroen', GyroString::plain_ascii('Camões São P. Cachaça en España y Citroën', '-'));
	}

	public function test_extract_before() {
		$val = 'This is a normal sentence';
		$this->assertEquals('This is a normal s', GyroString::extract_before($val, 'ent'));
		$this->assertEquals('', GyroString::extract_before($val, 'This'));
		$this->assertEquals($val, GyroString::extract_before($val, 'NOTFOUND'));
	}

	public function test_extract_after() {
		$val = 'This is a normal sentence';
		$this->assertEquals('tence', GyroString::extract_after($val, 'sen'));
		$this->assertEquals('', GyroString::extract_after($val, 'sentence'));
		$this->assertEquals($val, GyroString::extract_after($val, 'NOTFOUND'));
	}
}
