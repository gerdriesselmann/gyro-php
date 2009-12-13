<?php
/*
 * Created on 21.09.2006
 */

class HtmlStringTest extends GyroUnitTestCase {
	function test_build() {
		$test = '<p>Hello<br /><strong>World</strong></p>';
		$html = new HtmlString($test);
		$this->assertEqual($test, $html->build());

		$test = 'Hello World';
		$html = new HtmlString($test);
		$this->assertEqual($test, $html->build());
	} 
	
	function test_preg_replace() {
		$in = '<p>Hello World, <br /><a href="wewewew"><strong>World</strong></a></p>';

		$test = '<p>Hello <strong>World</strong>, <br /><a href="wewewew"><strong><strong>World</strong></strong></a></p>';
		$html = new HtmlString($in);
		$num_match = $html->preg_replace('|\b(world)\b|i', '<strong>${1}</strong>');
		$this->assertEqual(2, $num_match);
		$this->assertEqual($test, $html->build());

		$test = '<p>Hello <strong>World</strong>, <br /><a href="wewewew"><strong>World</strong></a></p>';
		$html = new HtmlString($in);
		$num_match = $html->preg_replace('|\b{<}(world)\b{>}|i', '<strong>${1}</strong>', 1);
		$this->assertEqual(1, $num_match);
		$this->assertEqual($test, $html->build());

		$html = new HtmlString($in);
		$num_match = $html->preg_replace('|\b{<}(world)\b{>}|i', '<strong>${1}</strong>', -1, 'strong');
		$this->assertEqual(1, $num_match);
		$this->assertEqual($test, $html->build());

		$html = new HtmlString($in);
		$num_match = $html->preg_replace('|\b{<}(world)\b{>}|i', '<strong>${1}</strong>', -1, 'a');
		$this->assertEqual(1, $num_match);
		$this->assertEqual($test, $html->build());

		$html = new HtmlString($in);
		$num_match = $html->preg_replace('|\b{<}(world)\b{>}|i', '<strong>${1}</strong>', -1, 'strong');
		$this->assertEqual(1, $num_match);
		$this->assertEqual($test, $html->build());

		$html = new HtmlString($in);
		$num_match = $html->preg_replace('|\b{<}(world)\b{>}|i', '<strong>${1}</strong>', -1, 'strong p');
		$this->assertEqual(0, $num_match);
		$this->assertEqual($in, $html->build());

		$html = new HtmlString($in);
		$num_match = $html->preg_replace('|\b{<}(world)\b{>}|i', '<strong>${1}</strong>', 0);
		$this->assertEqual(0, $num_match);
		$this->assertEqual($in, $html->build());
	}
	
	// Special case found on live system
	function test_preg_replace_live_bug() {
		// Simplified example, reproduced bug though
		$in = 'Viele Menschen spüren in ihrer Region die Wetterveränderungen.';
		$html = new HtmlString($in);
		$num_match = $html->preg_replace('|\b{<}(Wetterver)\b{>}|i', '<strong>${1}</strong>');
		$this->assertEqual(0, $num_match);
		$this->assertEqual($in, $html->build());
	}
	
	function test_insert() {
		$in = 'Dont put it within the <strong>very strong</strong> tags!';
		$insert = '<em>groovy</em>'; 
		$expect =  'Dont put it within the <strong><em>groovy</em>very strong</strong> tags!';
		$html = new HtmlString($in);
		$html->insert($insert, 26);
		$this->assertEqual($expect, $html->build());

		$expect =  'Dont put it within the <strong>very<em>groovy</em> strong</strong> tags!';
		$html = new HtmlString($in);
		$html->insert($insert, 30);
		$this->assertEqual($expect, $html->build());

		$expect =  'Dont put<em>groovy</em> it within the <strong>very strong</strong> tags!';
		$html = new HtmlString($in);
		$html->insert($insert, 11, 'strong');
		$this->assertEqual($expect, $html->build());

		$expect =  'Dont put it within the <em>groovy</em><strong>very strong</strong> tags!';
		$html = new HtmlString($in);
		$html->insert($insert, 26, 'strong');
		$this->assertEqual($expect, $html->build());
	}
}
?>
