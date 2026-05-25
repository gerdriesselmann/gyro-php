<?php
/**
 * Calculates a bcrypt hash using PHP's password_hash/password_verify
 *
 * @author Security Fix
 * @ingroup Usermanagement
 */
class BcryptHash implements IHashAlgorithm {
	public function hash($source) {
		return password_hash($source, PASSWORD_BCRYPT, ['cost' => 12]);
	}

	public function check($source, $hash) {
		return password_verify($source, $hash);
	}
}
