<?php
/**
 * Interface for Hash algorithm implementations
 * 
 * @since 0.5.1
 *  
 * Hash algorithzm implementations must be placed beneath /behaviour/comands/users/hashes/ and
 * named [algo].hash.php, where [algo] is the name of the algorithm. The class itself is
 * named [Algo]Hash.
 * 
 * For example the md5 algorithm's file is named /behaviour/commands/users/hashes/md5.hash.php and
 * contains the class Md5Hash. 
 * 
 * @attention Algorithm names are limited to 5 characters!
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
interface IHashAlgorithm {
	/**
	 * Create hash
	 * 
	 * @param string $source
	 * @return string
	 */
	public function hash($source);
	
	/**
	 * Validate if given hash matches source 
	 * 
	 * @param string $source
	 * @param string $hash
	 * @return bool
	 */
	public function check($source, $hash);
}