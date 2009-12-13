<?php
/**
 * Created on 09.11.2006
 *
 * @author Gerd Riesselmann
 */
class ExactMatchRouteTest extends GyroUnitTestCase {
	function test_weight() {
		$token1 = new ExactMatchRoute('some/url', null, '');

		$weight = $token1->weight_against_path('some/url');
		$this->assertEqual(ExactMatchRoute::WEIGHT_FULL_MATCH, $weight);

		$weight = $token1->weight_against_path('some/url/string');		
		$this->assertTrue($weight == ExactMatchRoute::WEIGHT_NO_MATCH);
				
		$noweight = $token1->weight_against_path('totally/different');
		$this->assertEqual(ExactMatchRoute::WEIGHT_NO_MATCH, $noweight);

		$token2 = new ExactMatchRoute('.', null, '');

		$weight = $token2->weight_against_path('some/url');
		$this->assertEqual(ExactMatchRoute::WEIGHT_NO_MATCH, $weight);

		$weight2 = $token2->weight_against_path('.');		
		$this->assertEqual(ExactMatchRoute::WEIGHT_FULL_MATCH, $weight2);
	}
} 
?>