<?php
/**
 * Created on 09.11.2006
 *
 * @author Gerd Riesselmann
 */
class ParameterizedRouteTest extends GyroUnitTestCase {
	function test_fallback_exact_match() {
		$token1 = new ParameterizedRoute('some/url', null, '');

		$weight = $token1->weight_against_path('some/url');
		$this->assertEqual(RouteBase::WEIGHT_FULL_MATCH, $weight);

		$weight = $token1->weight_against_path('some/url/string');		
		$this->assertTrue($weight == RouteBase::WEIGHT_NO_MATCH);
				
		$noweight = $token1->weight_against_path('totally/different');
		$this->assertEqual(RouteBase::WEIGHT_NO_MATCH, $noweight);
	}
	
	function test_int() {
		$token1 = new ParameterizedRoute('some/url/{test:i}', null, '');

		$weight = $token1->weight_against_path('some/url/123');
		$this->assertEqual(RouteBase::WEIGHT_FULL_MATCH, $weight);
		
		$weight = $token1->weight_against_path('some/url/-123');
		$this->assertEqual(RouteBase::WEIGHT_FULL_MATCH, $weight);
		
		$weight = $token1->weight_against_path('some/url/0');
		$this->assertEqual(RouteBase::WEIGHT_FULL_MATCH, $weight);
		
		$weight = $token1->weight_against_path('some/url/string');		
		$this->assertTrue($weight == RouteBase::WEIGHT_NO_MATCH);

		$weight = $token1->weight_against_path('some/url/123string');		
		$this->assertTrue($weight == RouteBase::WEIGHT_NO_MATCH);
	}
	
	function test_unsigned_int() {
		$token1 = new ParameterizedRoute('some/url/{test:ui}', null, '');

		$weight = $token1->weight_against_path('some/url/123');
		$this->assertEqual(RouteBase::WEIGHT_FULL_MATCH, $weight);
		
		$weight = $token1->weight_against_path('some/url/-123');
		$this->assertEqual(RouteBase::WEIGHT_NO_MATCH, $weight);
		
		$weight = $token1->weight_against_path('some/url/0');
		$this->assertEqual(RouteBase::WEIGHT_FULL_MATCH, $weight);
		
		$weight = $token1->weight_against_path('some/url/string');		
		$this->assertEqual(RouteBase::WEIGHT_NO_MATCH, $weight);
		
		$weight = $token1->weight_against_path('some/url/123string');		
		$this->assertEqual(RouteBase::WEIGHT_NO_MATCH, $weight);		
	}

	function test_unsigned_positive_int() {
		$token1 = new ParameterizedRoute('some/url/{test:ui>}', null, '');

		$weight = $token1->weight_against_path('some/url/123');
		$this->assertEqual(RouteBase::WEIGHT_FULL_MATCH, $weight);
		
		$weight = $token1->weight_against_path('some/url/-123');
		$this->assertEqual(RouteBase::WEIGHT_NO_MATCH, $weight);
		
		$weight = $token1->weight_against_path('some/url/0');
		$this->assertEqual(RouteBase::WEIGHT_NO_MATCH, $weight);
		
		$weight = $token1->weight_against_path('some/url/string');		
		$this->assertEqual(RouteBase::WEIGHT_NO_MATCH, $weight);
		
		$weight = $token1->weight_against_path('some/url/123string');		
		$this->assertEqual(RouteBase::WEIGHT_NO_MATCH, $weight);		
	}
	
	function test_string() {
		$token1 = new ParameterizedRoute('some/url/{test:s}', null, '');

		$weight = $token1->weight_against_path('some/url/123');
		$this->assertEqual(RouteBase::WEIGHT_FULL_MATCH, $weight);
		
		$weight = $token1->weight_against_path('some/url/-123');
		$this->assertEqual(RouteBase::WEIGHT_FULL_MATCH, $weight);
		
		$weight = $token1->weight_against_path('some/url/0');
		$this->assertEqual(RouteBase::WEIGHT_FULL_MATCH, $weight);
		
		$weight = $token1->weight_against_path('some/url/string');		
		$this->assertEqual(RouteBase::WEIGHT_FULL_MATCH, $weight);
		
		$weight = $token1->weight_against_path('some/url/123/string');		
		$this->assertEqual(RouteBase::WEIGHT_NO_MATCH, $weight);		

		$token2 = new ParameterizedRoute('some/url/{test:s}.html', null, '');

		$weight = $token2->weight_against_path('some/url/123.html');
		$this->assertEqual(RouteBase::WEIGHT_FULL_MATCH, $weight);

		$weight = $token2->weight_against_path('some/url/123.htm');
		$this->assertEqual(RouteBase::WEIGHT_NO_MATCH, $weight);
		
		$token3 = new ParameterizedRoute('some/url/{test:s:2}', null, '');

		$weight = $token3->weight_against_path('some/url/abc');
		$this->assertEqual(RouteBase::WEIGHT_NO_MATCH, $weight);
		
		$weight = $token3->weight_against_path('some/url/ab');
		$this->assertEqual(RouteBase::WEIGHT_FULL_MATCH, $weight);
		
		$weight = $token3->weight_against_path('some/url/a');
		$this->assertEqual(RouteBase::WEIGHT_NO_MATCH, $weight);
	}
	
	function test_string_plain() {
		$token1 = new ParameterizedRoute('some/url/{test:sp}', null, '');

		$weight = $token1->weight_against_path('some/url/123');
		$this->assertEqual(RouteBase::WEIGHT_FULL_MATCH, $weight);
		
		$weight = $token1->weight_against_path('some/url/-123');
		$this->assertEqual(RouteBase::WEIGHT_FULL_MATCH, $weight);
		
		$weight = $token1->weight_against_path('some/url/_123');
		$this->assertEqual(RouteBase::WEIGHT_FULL_MATCH, $weight);
		
		$weight = $token1->weight_against_path('some/url/0');
		$this->assertEqual(RouteBase::WEIGHT_FULL_MATCH, $weight);
		
		$weight = $token1->weight_against_path('some/url/string');		
		$this->assertEqual(RouteBase::WEIGHT_FULL_MATCH, $weight);
		
		// No matches
		$weight = $token1->weight_against_path('some/url/123/string');		
		$this->assertEqual(RouteBase::WEIGHT_NO_MATCH, $weight);		

		$weight = $token1->weight_against_path('some/url/!123');
		$this->assertEqual(RouteBase::WEIGHT_NO_MATCH, $weight);
		
		$weight = $token1->weight_against_path('some/url/#123');
		$this->assertEqual(RouteBase::WEIGHT_NO_MATCH, $weight);
		
		$token2 = new ParameterizedRoute('some/url/{test:sp}.html', null, '');

		$weight = $token2->weight_against_path('some/url/123.html');
		$this->assertEqual(RouteBase::WEIGHT_FULL_MATCH, $weight);

		$weight = $token2->weight_against_path('some/url/123.htm');
		$this->assertEqual(RouteBase::WEIGHT_NO_MATCH, $weight);
		
		$token3 = new ParameterizedRoute('some/url/{test:sp:2}', null, '');

		$weight = $token3->weight_against_path('some/url/abc');
		$this->assertEqual(RouteBase::WEIGHT_NO_MATCH, $weight);
		
		$weight = $token3->weight_against_path('some/url/ab');
		$this->assertEqual(RouteBase::WEIGHT_FULL_MATCH, $weight);
		
		$weight = $token3->weight_against_path('some/url/a');
		$this->assertEqual(RouteBase::WEIGHT_NO_MATCH, $weight);
	}	
	
	function test_enum() {
		$token1 = new ParameterizedRoute('some/url/{test:e:one,two,three}', null, '');

		$weight = $token1->weight_against_path('some/url/one');
		$this->assertEqual(RouteBase::WEIGHT_FULL_MATCH, $weight);
		
		$weight = $token1->weight_against_path('some/url/two');
		$this->assertEqual(RouteBase::WEIGHT_FULL_MATCH, $weight);
		
		$weight = $token1->weight_against_path('some/url/three');
		$this->assertEqual(RouteBase::WEIGHT_FULL_MATCH, $weight);
		
		$weight = $token1->weight_against_path('some/url/four');		
		$this->assertEqual(RouteBase::WEIGHT_NO_MATCH, $weight);
		
		$weight = $token1->weight_against_path('some/url/123/one_string');		
		$this->assertEqual(RouteBase::WEIGHT_NO_MATCH, $weight);		
	}

	function test_complex() {
		$token1 = new ParameterizedRoute('some/{url:e:url,test}/{a:i}-{b:ui}.text.{c:s}{i:ui>}.html', null, '');

		$weight = $token1->weight_against_path('some/url/-1-2.text.abc2.html');
		$this->assertEqual(RouteBase::WEIGHT_FULL_MATCH, $weight);
	}
	
	function test_placeholders() {
		// *
		$token1 = new ParameterizedRoute('some/url/{path:s}*', null, '');
		$weight = $token1->weight_against_path('some/url/one');
		$this->assertEqual(RouteBase::WEIGHT_FULL_MATCH, $weight);
		
		$weight = $token1->weight_against_path('some/url/one/two');
		$this->assertEqual(RouteBase::WEIGHT_FULL_MATCH, $weight);
		
		$weight = $token1->weight_against_path('some/url/');
		$this->assertEqual(RouteBase::WEIGHT_FULL_MATCH, $weight);
		
		// %
		$token1 = new ParameterizedRoute('some/url/{path:s}%', null, '');
		$weight = $token1->weight_against_path('some/url/one');
		$this->assertEqual(RouteBase::WEIGHT_FULL_MATCH, $weight);
		
		$weight = $token1->weight_against_path('some/url/one/two');
		$this->assertEqual(RouteBase::WEIGHT_FULL_MATCH, $weight);
		
		$weight = $token1->weight_against_path('some/url/');
		$this->assertEqual(RouteBase::WEIGHT_NO_MATCH, $weight);
		
		// !
		$token1 = new ParameterizedRoute('some/url/{path:s}!', null, '');
		$weight = $token1->weight_against_path('some/url/one');
		$this->assertEqual(RouteBase::WEIGHT_FULL_MATCH, $weight);
		
		$weight = $token1->weight_against_path('some/url/one/two');
		$this->assertEqual(RouteBase::WEIGHT_NO_MATCH, $weight);
		
		$weight = $token1->weight_against_path('some/url/');
		$this->assertEqual(RouteBase::WEIGHT_FULL_MATCH, $weight);				
	}
	
	function test_build_url() {
		$token1 = new ParameterizedRoute('some/{url}/{path:s}*', null, '');
		$url = $token1->build_url(false, array('url' => 'url', 'path' => 'path'));
		$this->assertEqual('/some/url/path', $url);

		$url = $token1->build_url(false, array('url' => 'url'));
		$this->assertEqual('/some/url/', $url);

		$token1 = new ParameterizedRoute('some/{url}/{path:s}%', null, '');
		$url = $token1->build_url(false, array('url' => 'url', 'path' => 'path'));
		$this->assertEqual('/some/url/path', $url);

		$url = $token1->build_url(false, array('url' => 'url'));
		$this->assertEqual('/some/url/{path:s}%', $url);

		$token1 = new ParameterizedRoute('some/{url}/{path:s}*', null, '');
		$url = $token1->build_url(false, array('url' => 'url', 'path' => 'some/path%'));
		$this->assertEqual('/some/url/some/path%25', $url);

		$token1 = new ParameterizedRoute('some/{url}/{path:s}-*', null, '');
		$url = $token1->build_url(false, array('url' => 'url', 'path' => 'path'));
		$this->assertEqual('/some/url/path-*', $url);		
		
		$token1 = new ParameterizedRoute('some/{url}/{path:s}!', null, '');
		$url = $token1->build_url(false, array('url' => 'url', 'path' => 'path'));
		$this->assertEqual('/some/url/path', $url);

		$url = $token1->build_url(false, array('url' => 'url'));
		$this->assertEqual('/some/url/', $url);
		
		$token1 = new ParameterizedRoute('some/{url}/{url_path:s}!', null, '');
		$url = $token1->build_url(false, array('url' => 'url', 'url_path' => 'path'));
		$this->assertEqual('/some/url/path', $url);
	}
	
	function test_build_url_sp() {
		$token1 = new ParameterizedRoute('some/{test:sp}', null, '');
		
		$url = $token1->build_url(false, array('test' => '_test'));
		$this->assertEqual('/some/_test', $url);

		$url = $token1->build_url(false, array('test' => '!test-ö!'));
		$this->assertEqual('/some/test-oe', $url);
	}
} 
