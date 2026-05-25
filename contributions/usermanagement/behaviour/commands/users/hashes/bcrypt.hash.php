<?php
/**
 * Calculates a bcrypt hash using PHP's password_hash/password_verify
 *
 * @author Security Fix
 * @ingroup Usermanagement
 */
class BcryptHash implements IHashAlgorithm {
	public function hash(string $source): string {
		return password_hash($source, PASSWORD_BCRYPT, ['cost' => 12]);
	}

	public function check(string $source, string $hash): bool {
		return password_verify($source, $hash);
	}
}
