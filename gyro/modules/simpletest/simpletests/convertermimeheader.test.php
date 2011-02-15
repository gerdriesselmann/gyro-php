<?php
class ConverterMimeHeaderTest extends GyroUnitTestCase {
	public function test_encode() {
		$c = ConverterFactory::create(ConverterFactory::MIMEHEADER);
		$e = 'UTF-8';
		
		$test = 'Some plain ASCII text 09azAZ';
		$expect = $test;
		$this->assertEqual($c->encode($test, $e), $expect);
		
		$test = 'ASCII, with some special chars: !=? <> and &';
		$expect = $test;
		$this->assertEqual($c->encode($test, $e), $expect);
		
		$test = 'Ümlauts! 09azAZ';
		$expect = '=?UTF-8?Q?=C3=9Cmlauts=21_09azAZ?=';
		$this->assertEqual($c->encode($test, $e), $expect);
	}
}
