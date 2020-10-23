<?php

require_once __DIR__ . '/streamer.base.cls.php';

/**
 * Stream a complete file without any further features
 */
class StreamerFull extends StreamerBase {

	protected function do_prepare($file, $size) {
		GyroHeaders::append("Content-Length: " . $size);
	}

	protected function do_stream($file) {
		$handle = fopen($file, 'rb');
		@fpassthru($handle);
	}
}
