<?php
/**
 * Created on 16.11.2006
 *
 * @author Gerd Riesselmann
 */
 
class PathStackTest extends GyroUnitTestCase {
	public function test_current() {
		$stack = new PathStack('/some/path/');
		$this->assertEqual('some', $stack->current());
	}
	
	public function test_next() {
		$stack = new PathStack('/some/path/');
		$this->assertEqual('path', $stack->next());
	}

	public function test_adjust() {
		$stack = new PathStack('/some/path/to/somewhere');
		$this->assertTrue($stack->adjust('some/path/'));
		$this->assertEqual('to', $stack->current());
	}	
}
?>