<?php
/**
 * @defgroup GeoCalc
 * @ingroup Libs
 * 
 * Helper for geografic calculations 
 * 
 * @section Usage Usage
 * 
 * Enable as "lib.geocalc" 
 * 
 * The class itself is a component:
 * 
 * @code
 * Load::components('geocalculator');
 * $distance = GeoCalculator::distance($lat1, $lon1, $lat2, $lon2);
 * @endcode
 * 
 * Additionally a class GeoCoordinate can be used that encapsulates latitude and longitude, 
 * and offers the same calculations then GeoCalculator does.  
 */
