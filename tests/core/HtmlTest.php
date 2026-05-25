<?php
use PHPUnit\Framework\TestCase;

class HtmlTest extends TestCase {
	public function test_br(): void {
		$this->assertEquals('<br />', html::br());
		$this->assertEquals('<br class="cls" />', html::br('cls'));
	}

	public function test_attr(): void {
		$this->assertEquals(' name="value"', html::attr('name', 'value'));
		$this->assertEquals(' name="&quot;&gt;&lt;script&gt;"', html::attr('name', '"><script>')); // XSS
		$this->assertEquals(' scriptalertHelloscript="value"', html::attr('><script>alert("Hello")</script><', 'value')); // XSS
		$this->assertEquals('', html::attr('>', 'value')); // XSS
		$this->assertEquals('', html::attr('name', ''));
	}
}
