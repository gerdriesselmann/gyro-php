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
	public function get_creationdate();	
	
	/**
	 * Return expiration date 
	 * 
	 * @return datetime
	 */
	public function get_expirationdate();
	
	/**
	 * Return data associated with this item
	 * 
	 * @return mixed
	 */
	public function get_data();
	
	/**
	 * Return the content in plain form
	 * 
	 * @return string
	 */
	public function get_content_plain();
	
	/**
	 * Return the content gzip compressed
	 * 
	 * @return string
	 */
	public function get_content_compressed();
}