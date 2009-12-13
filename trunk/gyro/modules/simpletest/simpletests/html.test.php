<?php
/**
 * Created on 11.11.2006
 *
 * @author Gerd Riesselmann
 */

class HtmlTest extends GyroUnitTestCase {
	
	function test_br() {
		$this->assertEqual('<br />', html::br());
		$this->assertEqual('<br class="cls" />', html::br('cls'));
	}
	
	function test_attr() {
		$this->assertEqual(' name="value"', html::attr('name', 'value'));
		$this->assertEqual(' name="&quot;&gt;&lt;script&gt;"', html::attr('name', '"><script>')); // XSS
		
		$this->assertEqual(' scriptalertHelloscript="value"', html::attr('><script>alert("Hello")</script><', 'value')); // XSS		
		$this->assertEqual('', html::attr('>', 'value')); // XSS
		
		$this->assertEqual('', html::attr('name', '')); 
	}
}
?>