<?php
/**
 * Represent an item in cache, e.g. a row in cache table 
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface ICacheItem {
	/**
	 * Return creation date 
	 * 
	 * @return datetime
	 */
	public function get_creationdate(): mixed;

	/**
	 * Return expiration date
	 *
	 * @return mixed Unix timestamp or datetime string
	 */
	public function get_expirationdate(): mixed;

	/**
	 * Return data associated with this item
	 *
	 * @return mixed
	 */
	public function get_data(): mixed;

	/**
	 * Return the content in plain form
	 *
	 * @return string
	 */
	public function get_content_plain(): string;

	/**
	 * Return the content gzip compressed
	 *
	 * @return string
	 */
	public function get_content_compressed(): string;
}