<?php
/**
 * Interface for cachable elements
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface ICachable {
	/**
	 * Return an array of all possible cache ids
	 * 
	 * @return Array Array of cache ids
	 */
	public function get_all_cache_ids();
	
	/**
	 * Return an array of dependend cachable elements
	 * 
	 * @return Array Array of ICachable instances
	 */
	public function get_dependend_cachables();
}
?>