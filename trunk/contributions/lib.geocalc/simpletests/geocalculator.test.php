<?php
Load::components('geocalculator');
/**
 *
 */
class GeoCalculatorTest extends GyroUnitTestCase {
	public function test_normalize_lat() {
		$this->assertEqual(27.4, GeoCalculator::normalize_lat(27.4));
		$this->assertEqual(80.0, GeoCalculator::normalize_lat(100.0));
		$this->assertEqual(-27.4, GeoCalculator::normalize_lat(-27.4));
		$this->assertEqual(-80.0, GeoCalculator::normalize_lat(-100.0));
		$this->assertEqual(0.0, GeoCalculator::normalize_lat(0.0));
	}

	public function test_normalize_lon() {
		$this->assertEqual(127.5, GeoCalculator::normalize_lon(127.5));
		$this->assertEqual(-127.5, GeoCalculator::normalize_lon(-127.5));
		$this->assertEqual(-178.0, GeoCalculator::normalize_lon(182.0));
		$this->assertEqual(178.0, GeoCalculator::normalize_lon(-182.0));
		$this->assertEqual(180.0, GeoCalculator::normalize_lon(180.0));
		$this->assertEqual(180.0, GeoCalculator::normalize_lon(-180.0));
		$this->assertEqual(0.0, GeoCalculator::normalize_lon(0.0));
	}

	public function test_bounding_box() {
		$box = GeoCalculator::bounding_box(0.0, 0.0, 1, 1);
		$this->assertTrue($box['lat']['min'] < 0.0);
		$this->assertTrue($box['lat']['max'] > 0.0);
		$this->assertTrue($box['lon']['min'] < 0.0);
		$this->assertTrue($box['lon']['max'] > 0.0);

		$box = GeoCalculator::bounding_box(0.0, 180.0, 1, 1);
		$this->assertTrue($box['lat']['min'] < 0.0);
		$this->assertTrue($box['lat']['max'] > 0.0);
		$this->assertTrue($box['lon']['min'] < 180.0);
		$this->assertTrue($box['lon']['min'] > 179.0);
		$this->assertTrue($box['lon']['max'] > -180.0);
		$this->assertTrue($box['lon']['max'] < -179.0);
	}

	public function test_distance() {
		$box = GeoCalculator::bounding_box(0.0, 0.0, 1, 1);
		$dist_we = GeoCalculator::distance(0, $box['lon']['min'], 0, $box['lon']['max']);
		$dist_ns = GeoCalculator::distance($box['lat']['min'], 0, $box['lat']['max'], 0);
		$this->assertEqual(2.0, round($dist_we, 5));
		$this->assertEqual(2.0, round($dist_ns, 5));

		// Should be the same the other way around
		$dist_we = GeoCalculator::distance(0, $box['lon']['max'], 0, $box['lon']['min']);
		$dist_ns = GeoCalculator::distance($box['lat']['max'], 0, $box['lat']['min'], 0);
		$this->assertEqual(2.0, round($dist_we, 5));
		$this->assertEqual(2.0, round($dist_ns, 5));

		$box = GeoCalculator::bounding_box(0.0, 180.0, 1, 1);
		$dist_we = GeoCalculator::distance(0, $box['lon']['min'], 0, $box['lon']['max']);
		$this->assertEqual(2.0, round($dist_we, 5));

		// Should be the same the other way around
		$dist_we = GeoCalculator::distance(0, $box['lon']['max'], 0, $box['lon']['min']);
		$this->assertEqual(2.0, round($dist_we, 5));
	}

	public function test_bounding_box_of() {
		$box = GeoCalculator::bounding_box_of(array(
			array('lat' => -10, 'lon' => -10),
			array('lat' =>  -9, 'lon' =>  -9),
			array('lat' =>   8, 'lon' =>   8),
			array('lat' =>  10, 'lon' =>  10),
		));
		$this->assertEqual(-10, $box['lat']['min']);
		$this->assertEqual( 10, $box['lat']['max']);
		$this->assertEqual(-10, $box['lon']['min']);
		$this->assertEqual( 10, $box['lon']['max']);

		$box = GeoCalculator::bounding_box_of(array(
			array('lat' => -10, 'lon' => -170),
			array('lat' =>  -9, 'lon' => -171),
			array('lat' =>   8, 'lon' =>  172),
			array('lat' =>  10, 'lon' =>  170),
		));
		$this->assertEqual( -10, $box['lat']['min']);
		$this->assertEqual(  10, $box['lat']['max']);
		$this->assertEqual( 170, $box['lon']['min']);
		$this->assertEqual(-170, $box['lon']['max']);

		$box = GeoCalculator::bounding_box_of(array(
			array('lat' => -10, 'lon' => -175),
			array('lat' =>   8, 'lon' =>  105),
		));
		$this->assertEqual( 105, $box['lon']['min']);
		$this->assertEqual(-175, $box['lon']['max']);

	}
}