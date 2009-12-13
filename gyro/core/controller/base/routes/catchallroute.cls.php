<?php
require_once dirname(__FILE__) . '/routebase.cls.php'; 
 
/**
 * Process all URLs that no one seems responsible for
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class CatchAllRoute extends RouteBase {
	/**
	 * Weights first path against the second.
 	 * 
 	 * Always returns a high number. 
 	 * The return value is smaller the better the match is. 0 indicates a perfect match 
 	 */
 	public function weight_against_path($path) {
		$tmp = trim($path, '/');
		if (!empty($tmp)) {
			$this->path_further = explode('/', $tmp);
		}
 		
 		return self::WEIGHT_NO_MATCH - 1;
 	} 		
}
?>