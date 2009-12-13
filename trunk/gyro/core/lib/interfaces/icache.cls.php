<?php
/**
 * Interface for cache access
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface ICache {
	public function get_cache_id();
	public function set_cache_id($cacheid);
	public function is_cached();
} 
?>