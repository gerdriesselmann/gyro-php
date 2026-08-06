<?php
use PHPUnit\Framework\TestCase;

class TimerTest extends TestCase {
	public function test_seconds_elapsed() {
		$timer = new Timer();
		$elapsed = $timer->seconds_elapsed();
		$this->assertIsFloat($elapsed);
		$this->assertGreaterThanOrEqual(0, $elapsed);
		$this->assertLessThan(1, $elapsed);
	}

	public function test_milliseconds_elapsed() {
		$timer = new Timer();
		$ms = $timer->milliseconds_elapsed();
		$this->assertIsFloat($ms);
		$this->assertGreaterThanOrEqual(0, $ms);
		$this->assertLessThan(1000, $ms);
	}

	public function test_milliseconds_is_1000x_seconds() {
		$timer = new Timer();
		$sec = $timer->seconds_elapsed();
		$ms = $timer->milliseconds_elapsed();
		// ms should be roughly 1000x seconds (allow some time passing between calls)
		$this->assertEqualsWithDelta($sec * 1000, $ms, 50);
	}
}
