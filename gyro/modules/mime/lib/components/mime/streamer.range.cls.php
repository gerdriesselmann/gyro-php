<?php

require_once __DIR__ . '/streamer.full.cls.php';

interface IStreamerRangeHeader extends IStreamer {
}

class StreamerRangeHeaderInvalid implements IStreamerRangeHeader {
	public function prepare() {
		Common::send_status_code(400);
	}

	public function stream() {
		// Do nothing
	}
}

class StreamerRangeHeaderNotSatisfiable implements IStreamerRangeHeader {
	public function prepare() {
		Common::send_status_code(416);
	}

	public function stream() {
		// Do nothing
	}
}

class StreamerRangeHeaderValid implements IStreamerRangeHeader {
	public $start;
	public $end;

	public $size;

	public $file;

	/**
	 * StreamerRangeHeaderValid constructor.
	 * @param $start
	 * @param $end
	 * @param $file
	 * @param $size
	 */
	public function __construct($start, $end, $file, $size) {
		$this->start = $start;
		$this->end = min($size - 1, $end);
		$this->file = $file;
		$this->size = $size;
	}

	public function prepare() {
		$length = $this->length();

		Common::send_status_code(206);
		GyroHeaders::append("Content-Length: $length");
		GyroHeaders::append("Content-Range: bytes {$this->start}-{$this->end}/{$this->size}");
	}

	public function stream() {
		$chunk_size = 1024 * 1024;

		$handle = fopen($this->file, 'rb');
		fseek($handle, $this->start);
		$current_pos = $this->start;

		$bytes_to_read = min($chunk_size, $this->end - $current_pos);
		while ($bytes_to_read > 0) {
			$bytes = fread($handle, $bytes_to_read);

			print $bytes;
			ob_flush();
			flush();

			$current_pos += $bytes_to_read;
			$bytes_to_read = min($chunk_size, $this->end - $current_pos);
		}

		fclose($handle);
	}


	private function length() {
		return $this->end - $this->start + 1;
	}
}

/**
 * Stream a file supporting byte ranges
 */
class StreamerRange extends StreamerFull {
	/**
	 * @var IStreamerRangeHeader|null
	 */
	private $range;

	protected function do_prepare($file, $size) {
		$header_value = RequestInfo::current()->header_value('range');
		$this->range = RangeHeaderParser::parse_range_header($header_value, $file, $size);
		if ($this->range) {
			$this->range->prepare();
		} else {
			parent::do_prepare($file, $size);
		}
	}

	protected function do_stream($file) {
		if ($this->range) {
			$this->range->stream();
		} else {
			parent::do_stream($file);
		}
	}
}

class RangeHeaderParser {
	/**
	 * Parse a range header. Retunrs null if no header is present
	 *
	 * @param string $range_header_value The value of HTTP Range header
	 * @param string $file The filename to server
	 * @param int $size The size of the file to serve
	 * @return IStreamerRangeHeader|null
	 */
	public static function parse_range_header($range_header_value, $file, $size) {
		$range_header_value = trim($range_header_value);
		if (empty($range_header_value)) {
			return null;
		}

		// Remove all whitespace since header value is WS agnostic
		$range_header_value = preg_replace('@\s@', '', $range_header_value);

		// Check basic syntax
		if (!GyroString::starts_with($range_header_value, 'bytes=')) {
			return new StreamerRangeHeaderInvalid();
		}

		// Remove bytes= from string
		$range_header_value = substr($range_header_value, 6);
		$ranges = explode(',', $range_header_value);
		if (count($ranges) == 0) {
			return new StreamerRangeHeaderInvalid();
		}

		$start = $size;
		$end = 0;
		// No support for multiple ranges, therefore compute the
		// largest range spanning all given partial ranges
		foreach($ranges as $range) {
			$parsed = self::parse_range($range, $size);
			if (empty($parsed)) {
				return new StreamerRangeHeaderInvalid();
			}

			$start = min($start, $parsed[0]);
			$end = max($end, $parsed[1]);
		}

		if ($end < $start) {
			return new StreamerRangeHeaderInvalid();
		}

		if ($start >= $size) {
			return new StreamerRangeHeaderNotSatisfiable();
		}

		return new StreamerRangeHeaderValid($start, $end, $file, $size);
	}

	/**
	 * Parses byte range value, and returns either an array(start, end) or null,
	 * if the range is not valid.
	 *
	 * @param string $range_string A string defining a range like "10-100", "50-" or "-80"
	 * @param int $total_size Total size of file to server
	 * @return int[]|null
	 */
	private static function parse_range($range_string, $total_size) {
		$split = explode('-', $range_string);
		if (count($split) != 2) {
			return null;
		}

		$max_end = $total_size - 1;
		$start = $split[0];
		$end = $split[1];
		if ($end === '') {
			// handle bytes=100- meaning from byte 100 to end
			$end = $max_end;
		}
		$end = self::as_int($end);
		if (!is_int($end)) {
			return null;
		}

		if ($start === '') {
			// handle bytes=-100 meaning last 100 bytes
			$start = max(0, $total_size - $end);
			$end = $max_end;
		}
		$start = self::as_int($start);
		// Note start can not b < 0 here, since -a-b return array of 2 when splitting and -a is handled above
		if (!is_int($start)) {
			return null;
		}

		if ($end < $start) {
			return null;
		}

		return array($start, $end);
	}

	private static function as_int($val) {
		if (is_numeric($val)) {
			$val += 0;
			if (is_int($val)) {
				return $val;
			}
		}

		return null;
	}
}
