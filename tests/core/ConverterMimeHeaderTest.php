<?php
use PHPUnit\Framework\TestCase;

class ConverterMimeHeaderTest extends TestCase {
	public function test_encode() {
		$c = ConverterFactory::create(ConverterFactory::MIMEHEADER);
		$e = 'UTF-8';

		$test = 'Some plain ASCII text 09azAZ';
		$this->assertEquals($test, $c->encode($test, $e));

		$test = 'ASCII, with some special chars: !=? <> and &';
		$this->assertEquals($test, $c->encode($test, $e));

		$test = 'Ümlauts! 09azAZ =?';
		$expect = '=?UTF-8?Q?=C3=9Cmlauts=21_09azAZ_=3D=3F?=';
		$this->assertEquals($expect, $c->encode($test, $e));
	}
}
