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
	 * @var IStreamer
	 */
	private $delegate;

	/**
	 * Streamer constructor.
	 * @param string $file
	 */
	public function __construct($file) {
		$this->file = $file;

		$range_header_value = strtolower(RequestInfo::current()->header_value('range'));
		if (!empty($range_header_value)) {
			require_once __DIR__ . '/streamer.range.cls.php';
			$this->delegate = new StreamerRange($file);
		} else {
			require_once __DIR__ . '/streamer.full.cls.php';
			$this->delegate = new StreamerFull($file);
		}
	}

	public function prepare() {
		$this->delegate->prepare();
	}

	public function stream() {
		$this->delegate->stream();
	}
}
