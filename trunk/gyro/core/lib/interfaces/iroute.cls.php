<?php
/**
 * A route checks if a given url can be processed and invokes the according functions
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */ 
interface IRoute {
	const WEIGHT_NO_MATCH = 10000;
	const WEIGHT_FULL_MATCH = 0;
	
	/**
	 * Returns a suitable renderer 
	 *
	 * @param PageDate $page_data The page data
	 * @return IRenderer
	 */
	public function get_renderer($page_data);
	
	/**
	 * Initialize the data passed
	 */
	public function initialize($data);
	
	/**
	 * Weight this token against path
	 */
	public function weight_against_path($path);
	
	/**
	 * Return a string that identifies this Route - e.g for debug purposes
	 */
	public function identify();
	
	/**
	 * Returns true, if this route is a directory (that is: ends with '/')
	 */
	public function is_directory();
}
