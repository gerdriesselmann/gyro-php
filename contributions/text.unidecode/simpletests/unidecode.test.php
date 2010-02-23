<?php
class TestUnidecode extends GyroUnitTestCase {
	public function test_ascii() {
		for ($c = 0; $c <= 0x1f; $c++) {
			$t = chr($c);
			$this->assertEqual($t, ConverterFactory::encode($t, CONVERTER_UNIDECODE, 'UTF-8'));
		}
	}

	public function test_specific() {
		$tests = array(
			'Hello, World!' => 'Hello, World!',
			'ČŽŠčžš' => 'CZSczs',
			'ア' => 'a',
			'α' => 'a',
			'château' => 'chateau',
			'viñedos' => 'vinedos',
			'Jürgen' => 'Jurgen'
		);
		
		foreach ($tests as $u => $t) {
			$this->assertEqual($t, ConverterFactory::encode($u, CONVERTER_UNIDECODE, 'UTF-8'));
		}
		
			
		$this->assertEqual('Bei Jing ', ConverterFactory::encode("\x53\x17\x4E\xB0", CONVERTER_UNIDECODE, 'UTF-16'));
	}	
}