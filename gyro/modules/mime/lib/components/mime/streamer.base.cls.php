<?php

require_once __DIR__ . '/istreamer.cls.php';

/**
 * Stream a file without any further features
 */
abstract class StreamerBase implements IStreamer {
	/**
	 * @var string Path to existing file
	 */
	protected $file;

	/**
	 * Streamer constructor.
	 * @param string $file
	 */
	public function __construct($file) {
		$this->file = $file;
	}

	final function stream() {
		$this->before_stream();
		$this->do_stream($this->file);
	}

	final function prepare() {
		$size = $this->filesize();
		$this->generic_prepare($this->file, $size);
		$this->do_prepare($this->file, $size);
	}

	/**
	 * @return false|int
	 */
	protected function filesize() {
		return filesize($this->file);
	}

	/**
	 * Close sessions, send headers, end buffer
	 * So nothing should get accidentally in the way of streaming
	 * @throws Exception
	 */
	protected function before_stream() {
		GyroHeaders::send();
		session_write_close();
		ob_end_clean();//required here or large files will not work
	}

	abstract protected function do_stream($file);

	protected function generic_prepare($file, $size) {
		GyroHeaders::append('Content-Transfer-Encoding: binary');
		GyroHeaders::append('Accept-Ranges: bytes');
	}

	abstract protected function do_prepare($file, $size);
}
