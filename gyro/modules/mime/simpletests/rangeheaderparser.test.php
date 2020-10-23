<?php
Load::components('mime/streamer.range');

class RangeHeaderParserTest extends GyroUnitTestCase {
	const FILE = '/tmp/file';
	const SIZE = 1001;

	public function test_parse_invalid() {
		// Missing correct prefix is invalid
		$this->assert_is_invalid("bytes:10-100");
		$this->assert_is_invalid("10-100");
		$this->assert_is_invalid("bytes 10-100");
	}

	public function test_parse_invalid_range() {
		// If start > end, should be invalid
		$this->assert_is_invalid("bytes=100-10");
		// If end is negative should be invaluid
		$this->assert_is_invalid("bytes=10--100");
		// start and end must be int
		$this->assert_is_invalid("bytes=10.1-100");
		$this->assert_is_invalid("bytes=10-100.4");
	}

	public function test_parse_empty() {
		// If empty, should be null
		$this->assertNull($this->parse(""));
	}

	public function test_no_satisfiable() {
		// Start mus not be larger than file size
		$outside = self::SIZE;
		$parsed = $this->parse("bytes=$outside-$outside");
		$this->assertIsA($parsed, 'StreamerRangeHeaderNotSatisfiable');
	}

	public function test_simple_range() {
		$this->assert_range('10-100', 10, 100);
		$this->assert_range('10-10', 10, 10);
		$this->assert_range('1000-1000', 1000, 1000);
		$this->assert_range('10-', 10, self::SIZE - 1);
		$this->assert_range('10-10000', 10, self::SIZE - 1);
		$this->assert_range('-100', self::SIZE - 100, self::SIZE - 1);
	}

	public function test_multiple_ranges() {
		$this->assert_range('10-100,60-200', 10, 200);
		$this->assert_range('10-200,300-10000', 10, self::SIZE - 1);
		$this->assert_range('10-20,50-', 10, self::SIZE - 1);
		$this->assert_range('10-20,-100', 10, self::SIZE - 1);
	}

	private function assert_range($value, $start, $end) {
		/* @var StreamerRangeHeaderValid */
		$parsed = $this->parse("bytes=$value");
		$this->assertIsA($parsed, 'StreamerRangeHeaderValid');
		$this->assertEqual($parsed->start, $start);
		$this->assertEqual($parsed->end, $end);
		$this->assertEqual($parsed->size, self::SIZE);
		$this->assertEqual($parsed->file, self::FILE);
	}

	private function assert_is_invalid($value) {
		$this->assertIsA(
			$this->parse($value),
			'StreamerRangeHeaderInvalid'
		);
	}

	private function parse($value) {
		return RangeHeaderParser::parse_range_header(
			$value,
			self::FILE,
			self::SIZE
		);
	}
}