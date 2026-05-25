<?php
use PHPUnit\Framework\TestCase;

class ParameterizedRouteTestMockObjectPHPUnit {
	function test() { return 'test_'; }
}

class ParameterizedRouteTest extends TestCase {
	private function normalizePath(string $path): string {
		$parsed = parse_url($path);
		return isset($parsed['path']) ? $parsed['path'] : '/' . ltrim($path, '/');
	}

	public function test_fallback_exact_match() {
		$token1 = new ParameterizedRoute('some/url', null, '');

		$weight = $token1->weight_against_path('some/url');
		$this->assertEquals(RouteBase::WEIGHT_FULL_MATCH, $weight);

		$weight = $token1->weight_against_path('some/url/string');
		$this->assertEquals(RouteBase::WEIGHT_NO_MATCH, $weight);

		$noweight = $token1->weight_against_path('totally/different');
		$this->assertEquals(RouteBase::WEIGHT_NO_MATCH, $noweight);
	}

	public function test_int() {
		$token1 = new ParameterizedRoute('some/url/{test:i}', null, '');

		$this->assertEquals(RouteBase::WEIGHT_FULL_MATCH, $token1->weight_against_path('some/url/123'));
		$this->assertEquals(RouteBase::WEIGHT_FULL_MATCH, $token1->weight_against_path('some/url/-123'));
		$this->assertEquals(RouteBase::WEIGHT_FULL_MATCH, $token1->weight_against_path('some/url/0'));
		$this->assertEquals(RouteBase::WEIGHT_NO_MATCH, $token1->weight_against_path('some/url/string'));
		$this->assertEquals(RouteBase::WEIGHT_NO_MATCH, $token1->weight_against_path('some/url/123string'));
	}

	public function test_unsigned_int() {
		$token1 = new ParameterizedRoute('some/url/{test:ui}', null, '');

		$this->assertEquals(RouteBase::WEIGHT_FULL_MATCH, $token1->weight_against_path('some/url/123'));
		$this->assertEquals(RouteBase::WEIGHT_NO_MATCH, $token1->weight_against_path('some/url/-123'));
		$this->assertEquals(RouteBase::WEIGHT_FULL_MATCH, $token1->weight_against_path('some/url/0'));
		$this->assertEquals(RouteBase::WEIGHT_NO_MATCH, $token1->weight_against_path('some/url/string'));
		$this->assertEquals(RouteBase::WEIGHT_NO_MATCH, $token1->weight_against_path('some/url/123string'));
	}

	public function test_unsigned_positive_int() {
		$token1 = new ParameterizedRoute('some/url/{test:ui>}', null, '');

		$this->assertEquals(RouteBase::WEIGHT_FULL_MATCH, $token1->weight_against_path('some/url/123'));
		$this->assertEquals(RouteBase::WEIGHT_NO_MATCH, $token1->weight_against_path('some/url/-123'));
		$this->assertEquals(RouteBase::WEIGHT_NO_MATCH, $token1->weight_against_path('some/url/0'));
		$this->assertEquals(RouteBase::WEIGHT_NO_MATCH, $token1->weight_against_path('some/url/string'));
		$this->assertEquals(RouteBase::WEIGHT_NO_MATCH, $token1->weight_against_path('some/url/123string'));
	}

	public function test_string() {
		$token1 = new ParameterizedRoute('some/url/{test:s}', null, '');

		$this->assertEquals(RouteBase::WEIGHT_FULL_MATCH, $token1->weight_against_path('some/url/123'));
		$this->assertEquals(RouteBase::WEIGHT_FULL_MATCH, $token1->weight_against_path('some/url/-123'));
		$this->assertEquals(RouteBase::WEIGHT_FULL_MATCH, $token1->weight_against_path('some/url/0'));
		$this->assertEquals(RouteBase::WEIGHT_FULL_MATCH, $token1->weight_against_path('some/url/string'));
		$this->assertEquals(RouteBase::WEIGHT_NO_MATCH, $token1->weight_against_path('some/url/123/string'));

		$token2 = new ParameterizedRoute('some/url/{test:s}.html', null, '');
		$this->assertEquals(RouteBase::WEIGHT_FULL_MATCH, $token2->weight_against_path('some/url/123.html'));
		$this->assertEquals(RouteBase::WEIGHT_NO_MATCH, $token2->weight_against_path('some/url/123.htm'));

		$token3 = new ParameterizedRoute('some/url/{test:s:2}', null, '');
		$this->assertEquals(RouteBase::WEIGHT_NO_MATCH, $token3->weight_against_path('some/url/abc'));
		$this->assertEquals(RouteBase::WEIGHT_FULL_MATCH, $token3->weight_against_path('some/url/ab'));
		$this->assertEquals(RouteBase::WEIGHT_NO_MATCH, $token3->weight_against_path('some/url/a'));
	}

	public function test_string_plain() {
		$token1 = new ParameterizedRoute('some/url/{test:sp}', null, '');

		$this->assertEquals(RouteBase::WEIGHT_FULL_MATCH, $token1->weight_against_path('some/url/123'));
		$this->assertEquals(RouteBase::WEIGHT_FULL_MATCH, $token1->weight_against_path('some/url/-123'));
		$this->assertEquals(RouteBase::WEIGHT_FULL_MATCH, $token1->weight_against_path('some/url/_123'));
		$this->assertEquals(RouteBase::WEIGHT_FULL_MATCH, $token1->weight_against_path('some/url/0'));
		$this->assertEquals(RouteBase::WEIGHT_FULL_MATCH, $token1->weight_against_path('some/url/string'));

		// No matches
		$this->assertEquals(RouteBase::WEIGHT_NO_MATCH, $token1->weight_against_path('some/url/123/string'));
		$this->assertEquals(RouteBase::WEIGHT_NO_MATCH, $token1->weight_against_path('some/url/!123'));
		$this->assertEquals(RouteBase::WEIGHT_NO_MATCH, $token1->weight_against_path('some/url/#123'));

		$token2 = new ParameterizedRoute('some/url/{test:sp}.html', null, '');
		$this->assertEquals(RouteBase::WEIGHT_FULL_MATCH, $token2->weight_against_path('some/url/123.html'));
		$this->assertEquals(RouteBase::WEIGHT_NO_MATCH, $token2->weight_against_path('some/url/123.htm'));

		$token3 = new ParameterizedRoute('some/url/{test:sp:2}', null, '');
		$this->assertEquals(RouteBase::WEIGHT_NO_MATCH, $token3->weight_against_path('some/url/abc'));
		$this->assertEquals(RouteBase::WEIGHT_FULL_MATCH, $token3->weight_against_path('some/url/ab'));
		$this->assertEquals(RouteBase::WEIGHT_NO_MATCH, $token3->weight_against_path('some/url/a'));
	}

	public function test_enum() {
		$token1 = new ParameterizedRoute('some/url/{test:e:one,two,three}', null, '');

		$this->assertEquals(RouteBase::WEIGHT_FULL_MATCH, $token1->weight_against_path('some/url/one'));
		$this->assertEquals(RouteBase::WEIGHT_FULL_MATCH, $token1->weight_against_path('some/url/two'));
		$this->assertEquals(RouteBase::WEIGHT_FULL_MATCH, $token1->weight_against_path('some/url/three'));
		$this->assertEquals(RouteBase::WEIGHT_NO_MATCH, $token1->weight_against_path('some/url/four'));
		$this->assertEquals(RouteBase::WEIGHT_NO_MATCH, $token1->weight_against_path('some/url/123/one_string'));
	}

	public function test_complex() {
		$token1 = new ParameterizedRoute('some/{url:e:url,test}/{a:i}-{b:ui}.text.{c:s}{i:ui>}.html', null, '');

		$weight = $token1->weight_against_path('some/url/-1-2.text.abc2.html');
		$this->assertEquals(RouteBase::WEIGHT_FULL_MATCH, $weight);
	}

	public function test_placeholders() {
		// *
		$token1 = new ParameterizedRoute('some/url/{path:s}*', null, '');
		$this->assertEquals(RouteBase::WEIGHT_FULL_MATCH, $token1->weight_against_path('some/url/one'));
		$this->assertEquals(RouteBase::WEIGHT_FULL_MATCH, $token1->weight_against_path('some/url/one/two'));
		$this->assertEquals(RouteBase::WEIGHT_FULL_MATCH, $token1->weight_against_path('some/url/'));

		// %
		$token1 = new ParameterizedRoute('some/url/{path:s}%', null, '');
		$this->assertEquals(RouteBase::WEIGHT_FULL_MATCH, $token1->weight_against_path('some/url/one'));
		$this->assertEquals(RouteBase::WEIGHT_FULL_MATCH, $token1->weight_against_path('some/url/one/two'));
		$this->assertEquals(RouteBase::WEIGHT_NO_MATCH, $token1->weight_against_path('some/url/'));

		// !
		$token1 = new ParameterizedRoute('some/url/{path:s}!', null, '');
		$this->assertEquals(RouteBase::WEIGHT_FULL_MATCH, $token1->weight_against_path('some/url/one'));
		$this->assertEquals(RouteBase::WEIGHT_NO_MATCH, $token1->weight_against_path('some/url/one/two'));
		$this->assertEquals(RouteBase::WEIGHT_FULL_MATCH, $token1->weight_against_path('some/url/'));
	}

	public function test_build_url() {
		$token1 = new ParameterizedRoute('some/{url}/{path:s}*', null, '');
		$url = $token1->build_url(false, array('url' => 'url', 'path' => 'path'));
		$this->assertEquals($this->normalizePath('/some/url/path'), $this->normalizePath($url));

		$url = $token1->build_url(false, array('url' => 'url'));
		$this->assertEquals($this->normalizePath('/some/url/'), $this->normalizePath($url));

		$token1 = new ParameterizedRoute('some/{url}/{path:s}%', null, '');
		$url = $token1->build_url(false, array('url' => 'url', 'path' => 'path'));
		$this->assertEquals($this->normalizePath('/some/url/path'), $this->normalizePath($url));

		$url = $token1->build_url(false, array('url' => 'url'));
		$this->assertEquals($this->normalizePath('/some/url/{path:s}%'), $this->normalizePath($url));

		$token1 = new ParameterizedRoute('some/{url}/{path:s}*', null, '');
		$url = $token1->build_url(false, array('url' => 'url', 'path' => 'some/path%'));
		$this->assertEquals($this->normalizePath('/some/url/some/path%25'), $this->normalizePath($url));

		$token1 = new ParameterizedRoute('some/{url}/{path:s}-*', null, '');
		$url = $token1->build_url(false, array('url' => 'url', 'path' => 'path'));
		$this->assertEquals($this->normalizePath('/some/url/path-*'), $this->normalizePath($url));

		$token1 = new ParameterizedRoute('some/{url}/{path:s}!', null, '');
		$url = $token1->build_url(false, array('url' => 'url', 'path' => 'path'));
		$this->assertEquals($this->normalizePath('/some/url/path'), $this->normalizePath($url));

		$url = $token1->build_url(false, array('url' => 'url'));
		$this->assertEquals($this->normalizePath('/some/url/'), $this->normalizePath($url));

		$token1 = new ParameterizedRoute('some/{url}/{url_path:s}!', null, '');
		$url = $token1->build_url(false, array('url' => 'url', 'url_path' => 'path'));
		$this->assertEquals($this->normalizePath('/some/url/path'), $this->normalizePath($url));
	}

	public function test_build_url_sp() {
		$token1 = new ParameterizedRoute('some/{test:sp}', null, '');

		$url = $token1->build_url(false, array('test' => '_test'));
		$this->assertEquals($this->normalizePath('/some/_test'), $this->normalizePath($url));

		$url = $token1->build_url(false, array('test' => '!test-ö!'));
		$this->assertEquals($this->normalizePath('/some/test-oe'), $this->normalizePath($url));
	}

	public function test_function_parameter() {
		$token1 = new ParameterizedRoute('some/{test():sp}', null, '');

		$url = $token1->build_url(false, array('test()' => '_test'));
		$this->assertEquals($this->normalizePath('/some/_test'), $this->normalizePath($url));

		$url = $token1->build_url(false, new ParameterizedRouteTestMockObjectPHPUnit());
		$this->assertEquals($this->normalizePath('/some/test_'), $this->normalizePath($url));
	}
}
