<?php

/**
 * Unified interface for different approaches to streaming a file
 */
interface IStreamer {
	/**
	 * Do what needs to be done before headers are sent
	 *
	 * @return void
	 */
	public function prepare();

	/**
	 * Stream the content. Header are sent at this point
	 *
	 * @return void
	 */
	public function stream();
}
