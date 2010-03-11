<?php
/**
 * Calculates an MD5 hash
 * 
 * @since 0.5.1
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class Md5Hash implements IHashAlgorithm {
	public function hash($source) {
		return md5($source);
	}
	
	public function check($source, $hash) {
		return $hash == $this->hash($source);
	}
}