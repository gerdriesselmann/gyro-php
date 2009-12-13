<?php
/**
 * A simple timer
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class Timer {
	protected $start_time;
	
	public function __construct() {
		$this->start_time = microtime(true);
	}
	
	public function seconds_elapsed() {
		return microtime(true) - $this->start_time;
	}
	
	public function milliseconds_elapsed() {
		return 1000 * $this->seconds_elapsed();
	}	
}