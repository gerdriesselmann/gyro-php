<?php
Load::components('georectangle');
/**
 *
 */
class GeoRectangleTest extends GyroUnitTestCase {
	public function test_center() {
		$rect = new GeoRectangle(1, -1, -1, 1);
		$center = $rect->center();
		$this->assertEqual(0.0, $center->lat);
		$this->assertEqual(0.0, $center->lon);

		$rect = new GeoRectangle(1, -179, -1, 179);
		$center = $rect->center();
		$this->assertEqual(0.0, $center->lat);
		$this->assertEqual(0.0, $center->lon);

		$rect = new GeoRectangle(1, 179, -1, -179);
		$center = $rect->center();
		$this->assertEqual(0.0, $center->lat);
		$this->assertEqual(180.0, $center->lon);
	}

	public function test_contains() {
		$rect = new GeoRectangle(1, -1, -1, 1);
		$this->assertTrue($rect->contains(new GeoCoordinate(0.0, 0.0)));

		$rect = new GeoRectangle(1, -179, -1, 179);
		$this->assertTrue($rect->contains(new GeoCoordinate(0.0, 0.0)));

		$rect = new GeoRectangle(1, 179, -1, -179);
		$this->assertTrue($rect->contains(new GeoCoordinate(0.0, 180.0)));
	}
}