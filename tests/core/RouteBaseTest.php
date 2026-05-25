<?php
use PHPUnit\Framework\TestCase;

class RouteTestControllerPHPUnit {
	public $invoked = false;

	public function action_invoke_me($data) {
		$this->invoked = true;
	}
}

class RouteBaseTest extends TestCase {
	private function normalizePath(string $path): string {
		$parsed = parse_url($path);
		return isset($parsed['path']) ? $parsed['path'] : '/' . ltrim($path, '/');
	}

	public function test_invoke() {
		$controler = new RouteTestControllerPHPUnit();
		$token = new RouteBase('some/url', $controler, 'invoke_me');

		$data = new PageData(null, $_GET, $_POST);
		$token->invoke($data);

		$this->assertTrue($controler->invoked);
	}

	public function test_initialize() {
		$controler = new RouteTestControllerPHPUnit();
		$token = new RouteBase('some/url', $controler, 'invoke_me');

		$data = new PageData(null, $_GET, $_POST);
		$data->set_path('some/url/to/process');
		$token->initialize($data);

		$this->assertNotNull($data->get_cache_manager());
		$this->assertEquals('to', $data->get_pathstack()->current());
	}

	public function test_weight() {
		$token1 = new RouteBase('some/url', null, '', '');

		$weight = $token1->weight_against_path('some/url');
		$this->assertEquals(RouteBase::WEIGHT_FULL_MATCH, $weight);

		$weight = $token1->weight_against_path('some/url/string');
		$this->assertEquals(1, $weight);

		$weight2 = $token1->weight_against_path('some/url/stringdingsbums');
		$this->assertEquals($weight, $weight2);

		$weight3 = $token1->weight_against_path('some/url/string/dings');
		$this->assertTrue($weight < $weight3);
		$this->assertTrue($weight2 < $weight3);

		$noweight = $token1->weight_against_path('totally/different');
		$this->assertEquals(RouteBase::WEIGHT_NO_MATCH, $noweight);

		$token2 = new RouteBase('.', null, '', '');

		$weight = $token2->weight_against_path('some/url');
		$this->assertEquals(RouteBase::WEIGHT_NO_MATCH, $weight);

		$weight2 = $token2->weight_against_path('.');
		$this->assertEquals(0, $weight2);
	}

	public function test_identify() {
		$route = new RouteBase('path', new RouteTestControllerPHPUnit(), 'action');
		$this->assertEquals('RouteTestControllerPHPUnit::action', $route->identify());
	}

	public function test_build_url() {
		$route = new RouteBase('path', new RouteTestControllerPHPUnit(), 'action');
		$this->assertEquals(
			$this->normalizePath(Config::get_url(Config::URL_BASEDIR) . 'path'),
			$this->normalizePath($route->build_url(RouteBase::RELATIVE))
		);
		$this->assertEquals(Config::get_url(Config::URL_BASEURL) . 'path', $route->build_url(RouteBase::ABSOLUTE));

		$this->assertEquals(
			$this->normalizePath(Config::get_value(Config::URL_BASEDIR) . 'path/some/params'),
			$this->normalizePath($route->build_url(RouteBase::RELATIVE, array('some', 'params')))
		);
		$this->assertEquals(
			$this->normalizePath(Config::get_value(Config::URL_BASEDIR) . 'path/a_param'),
			$this->normalizePath($route->build_url(RouteBase::RELATIVE, 'a_param'))
		);

		$this->assertEquals(Config::get_url(Config::URL_BASEURL) . 'path/some/params', $route->build_url(RouteBase::ABSOLUTE, array('some', 'params')));
		$this->assertEquals(Config::get_url(Config::URL_BASEURL) . 'path/a_param', $route->build_url(RouteBase::ABSOLUTE, 'a_param'));

		// Test HTTPS stuff
		$route = new RouteBase('https://path', new RouteTestControllerPHPUnit(), 'action');
		if (Config::has_feature(Config::ENABLE_HTTPS)) {
			$this->assertEquals(
				$this->normalizePath(Config::get_url(Config::URL_BASEURL_SAFE) . 'path'),
				$this->normalizePath($route->build_url(RouteBase::RELATIVE))
			);
			$this->assertEquals(Config::get_url(Config::URL_BASEURL_SAFE) . 'path', $route->build_url(RouteBase::ABSOLUTE));
		} else {
			$this->assertEquals(
				$this->normalizePath(Config::get_value(Config::URL_BASEDIR) . 'path'),
				$this->normalizePath($route->build_url(RouteBase::RELATIVE))
			);
			$this->assertEquals(Config::get_url(Config::URL_BASEURL) . 'path', $route->build_url(RouteBase::ABSOLUTE));
		}

		$route = new RouteBase('http://path', new RouteTestControllerPHPUnit(), 'action');
		$this->assertEquals(
			$this->normalizePath(Config::get_value(Config::URL_BASEDIR) . 'path'),
			$this->normalizePath($route->build_url(RouteBase::RELATIVE))
		);
		$this->assertEquals(Config::get_url(Config::URL_BASEURL) . 'path', $route->build_url(RouteBase::ABSOLUTE));
	}
}
