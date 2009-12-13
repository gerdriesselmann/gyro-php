<?php
require_once dirname(__FILE__) . '/routebase.cls.php';

/**
 * Allows only exact matches in URLs
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class ExactMatchRoute extends RouteBase {
	/**
	 * Weights first path against the second.
 	 * 
 	 * Only allows exact matches 
	 * The return value is smaller the better the match is. 0 indicates a perfect match 
 	 */
 	public function weight_against_path($path) {
 		$path = trim($path, '/');
 		$path_to_weight = trim($this->path, '/');

 	 	//print 'WEIGHT: ' . $path_to_weight . ' against ' . $path . ':';
		if ($path != $path_to_weight) {
 			//print 'NO MATCH<br />';
 			return self::WEIGHT_NO_MATCH;
 		}
 		else {
 			return self::WEIGHT_FULL_MATCH;
 		}  
 	} 		

	/**
	 * Build the URL (except base part)
	 * 
	 * @param mixed $params Further parameters to use to build URL
	 * @return string
	 */
	protected function build_url_path($params) {
		return ltrim($this->path, '/');
	}	 
}
