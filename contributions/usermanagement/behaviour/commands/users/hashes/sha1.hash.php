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
	public function hash(string $source): string {
		return sha1($source);
	}
	
	public function check(string $source, string $hash): bool {
		return hash_equals($hash, $this->hash($source));
	}
}
