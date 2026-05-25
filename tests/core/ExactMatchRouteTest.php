<?php
use PHPUnit\Framework\TestCase;

class ExactMatchRouteTest extends TestCase {
	public function test_weight() {
		$token1 = new ExactMatchRoute('some/url', null, '');

		$weight = $token1->weight_against_path('some/url');
		$this->assertEquals(ExactMatchRoute::WEIGHT_FULL_MATCH, $weight);

		$weight = $token1->weight_against_path('some/url/string');
		$this->assertEquals(ExactMatchRoute::WEIGHT_NO_MATCH, $weight);

		$noweight = $token1->weight_against_path('totally/different');
		$this->assertEquals(ExactMatchRoute::WEIGHT_NO_MATCH, $noweight);

		$token2 = new ExactMatchRoute('.', null, '');

		$weight = $token2->weight_against_path('some/url');
		$this->assertEquals(ExactMatchRoute::WEIGHT_NO_MATCH, $weight);

		$weight2 = $token2->weight_against_path('.');
		$this->assertEquals(ExactMatchRoute::WEIGHT_FULL_MATCH, $weight2);
	}
}
