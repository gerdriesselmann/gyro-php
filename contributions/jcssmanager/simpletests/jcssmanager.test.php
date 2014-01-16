<?php
/**
 * Test CSS concatenation and other stuff
 */
class JCSSManagerTest extends GyroUnitTestCase {
	private $basepath;

	public function setUp() {
		$this->basepath = dirname(__FILE__) . '/';
	}


	public function test_transform_make_absolute() {
		$css_file = $this->basepath . 'css/file/style.css';

		// Rel path
		$line = ".test { background: url(img.png) }";
		$this->assertEqual(".test { background: url(/css/file/img.png) }", JCSSManager::transform_css_line($line, $css_file, $this->basepath));
		// Variations
		$line = ".test { background: url('img.png') }";
		$this->assertEqual(".test { background: url('/css/file/img.png') }", JCSSManager::transform_css_line($line, $css_file, $this->basepath));
		$line = '.test { background: url("img.png") }';
		$this->assertEqual('.test { background: url("/css/file/img.png") }', JCSSManager::transform_css_line($line, $css_file, $this->basepath));

		// Abs path
		$line = ".test { background: url(/img.png) }";
		$this->assertEqual(".test { background: url(/img.png) }", JCSSManager::transform_css_line($line, $css_file, $this->basepath));
		// Variations
		$line = ".test { background: url('/img.png') }";
		$this->assertEqual(".test { background: url('/img.png') }", JCSSManager::transform_css_line($line, $css_file, $this->basepath));
		$line = '.test { background: url("/img.png") }';
		$this->assertEqual('.test { background: url("/img.png") }', JCSSManager::transform_css_line($line, $css_file, $this->basepath));

		// HTTP 
		$line = ".test { background: url(http://www.example.com/img.png.png) }";
		$this->assertEqual(".test { background: url(http://www.example.com/img.png.png) }", JCSSManager::transform_css_line($line, $css_file, $this->basepath));
		// Variations
		$line = ".test { background: url('http://www.example.com/img.png.png') }";
		$this->assertEqual(".test { background: url('http://www.example.com/img.png.png') }", JCSSManager::transform_css_line($line, $css_file, $this->basepath));
		$line = '.test { background: url("http://www.example.com/img.png.png") }';
		$this->assertEqual('.test { background: url("http://www.example.com/img.png.png") }', JCSSManager::transform_css_line($line, $css_file, $this->basepath));

	}
}