<?php
/**
 * Interface for elements having geo coordinates
 * 
 * @ingroup GeoCalc
 * @author Gerd Riesselmann 
 */
interface IGeoLocated {
	/**
	 * Returns geo coordinate of element
	 * 
	 * @return GeoCoordinate
	 */
	public function get_coordinate();
}