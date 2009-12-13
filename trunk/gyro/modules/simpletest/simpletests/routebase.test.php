<?php
/**
 * Created on 09.11.2006
 *
 * @author Gerd Riesselmann
 */
class RouteTestController {
	public $invoked = false;
	
	public function action_invoke_me($data) {
		$this->invoked = true;
	}
}

class RouteBaseTest extends GyroUnitTestCase {
	public function test_invoke() {
		$controler = new RouteTestController();
		$title = 'The Title';
		$token = new RouteBase('some/url', $controler, 'invoke_me');
		
		$data = new PageData(null, $_GET, $_POST);
		$token->invoke($data);
		
		$this->assertTrue($controler->invoked);
	}
	
	public function test_initialize() {
		$controler = new RouteTestController();
		$token = new RouteBase('some/url', $controler, 'invoke_me');
		
		$data = new PageData(null, $_GET, $_POST);
		$data->set_path('some/url/to/process');
		$token->initialize($data);
		
		$this->assertNull($data->get_cache_manager());
		$this->assertEqual('to', $data->get_pathstack()->current());
	}

	public function test_weight() {
		$token1 = new RouteBase('some/url', null, '', '');

		$weight = $token1->weight_against_path('some/url');
		$this->assertEqual(RouteBase::WEIGHT_FULL_MATCH, $weight);

		$weight = $token1->weight_against_path('some/url/string');		
		$this->assertEqual(1, $weight);
		
		$weight2 = $token1->weight_against_path('some/url/stringdingsbums');		
		$this->assertTrue($weight == $weight2);

		$weight3 = $token1->weight_against_path('some/url/string/dings');		
		$this->assertTrue($weight < $weight3);
		$this->assertTrue($weight2 < $weight3);

		$noweight = $token1->weight_against_path('totally/different');
		$this->assertEqual(RouteBase::WEIGHT_NO_MATCH, $noweight);
		
		$token2 = new RouteBase('.', null, '', '');

		$weight = $token2->weight_against_path('some/url');
		$this->assertEqual(RouteBase::WEIGHT_NO_MATCH, $weight);

		$weight2 = $token2->weight_against_path('.');		
		$this->assertEqual(0, $weight2);
	}

	public function test_identify() {
		$route = new RouteBase('path', new RouteTestController(), 'action');
		$this->assertEqual('RouteTestController::action', $route->identify());
	}
	
	public function test_build_url() {
		$route = new RouteBase('path', new RouteTestController(), 'action');
		$this->assertEqual(Config::get_url(Config::URL_BASEDIR) . 'path', $route->build_url(RouteBase::RELATIVE));
		$this->assertEqual(Config::get_url(Config::URL_BASEURL) . 'path', $route->build_url(RouteBase::ABSOLUTE));

		$this->assertEqual(Config::get_value(Config::URL_BASEDIR) . 'path/some/params', $route->build_url(RouteBase::RELATIVE, array('some', 'params')));
		$this->assertEqual(Config::get_value(Config::URL_BASEDIR) . 'path/a_param', $route->build_url(RouteBase::RELATIVE, 'a_param'));

		$this->assertEqual(Config::get_url(Config::URL_BASEURL) . 'path/some/params', $route->build_url(RouteBase::ABSOLUTE, array('some', 'params')));
		$this->assertEqual(Config::get_url(Config::URL_BASEURL) . 'path/a_param', $route->build_url(RouteBase::ABSOLUTE, 'a_param'));
		
		// Test HTTPS stuff
		$route = new RouteBase('https://path', new RouteTestController(), 'action');
		if (Config::has_feature(Config::ENABLE_HTTPS)) {
			$this->assertEqual(Config::get_url(Config::URL_BASEURL_SAFE) . 'path', $route->build_url(RouteBase::RELATIVE));
			$this->assertEqual(Config::get_url(Config::URL_BASEURL_SAFE) . 'path', $route->build_url(RouteBase::ABSOLUTE));
		}
		else {
			$this->assertEqual(Config::get_value(Config::URL_BASEDIR) . 'path', $route->build_url(RouteBase::RELATIVE));
			$this->assertEqual(Config::get_url(Config::URL_BASEURL) . 'path', $route->build_url(RouteBase::ABSOLUTE));			
		}

		$route = new RouteBase('http://path', new RouteTestController(), 'action');
		$this->assertEqual(Config::get_value(Config::URL_BASEDIR) . 'path', $route->build_url(RouteBase::RELATIVE));
		$this->assertEqual(Config::get_url(Config::URL_BASEURL) . 'path', $route->build_url(RouteBase::ABSOLUTE));
	}
} 
