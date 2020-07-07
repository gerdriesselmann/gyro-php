<?php

require_once __DIR__ . '/istreamer.cls.php';

/**
 * Stream a file without any further features
 */
class Streamer implements IStreamer {
	/**
	 * @var string Path to existing file
	 */
	private $file;

	/**
	 * Streamer constructor.
	 * @param string $file
	 */
	public function __construct($file) {
		$this->file = $file;
	}

	public function prepare() {
		GyroHeaders::append("Content-Length: " . filesize($this->file));
		GyroHeaders::append('Content-Transfer-Encoding: binary');

	}

	public function stream() {
		$handle = fopen($this->file, 'rb');
		@fpassthru($handle);
	}
}
