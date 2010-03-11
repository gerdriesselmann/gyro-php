<?php
/**
 * Calculates an SHA1 hash
 * 
 * @since 0.5.1
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class Sha1Hash implements IHashAlgorithm {
	public function hash($source) {
		return sha1($source);
	}
	
	public function check($source, $hash) {
		return $hash == $this->hash($source);
	}
}
