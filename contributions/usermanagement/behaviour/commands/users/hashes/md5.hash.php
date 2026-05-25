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
	public function hash(string $source): string {
		return md5($source);
	}
	
	public function check(string $source, string $hash): bool {
		return hash_equals($hash, $this->hash($source));
	}
}