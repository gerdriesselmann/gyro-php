<?php
use PHPUnit\Framework\TestCase;

class PathStackTest extends TestCase {
	public function test_current() {
		$stack = new PathStack('/some/path/');
		$this->assertEquals('some', $stack->current());
	}

	public function test_next() {
		$stack = new PathStack('/some/path/');
		$this->assertEquals('path', $stack->next());
	}

	public function test_adjust() {
		$stack = new PathStack('/some/path/to/somewhere');
		$this->assertTrue($stack->adjust('some/path/'));
		$this->assertEquals('to', $stack->current());
	}
}
