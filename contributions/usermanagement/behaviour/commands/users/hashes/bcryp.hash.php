<?php
/**
 * Calculates a hash using PHP's native password_hash() with bcrypt.
 *
 * Uses PASSWORD_BCRYPT (cost 12) via password_hash() / password_verify().
 * This is the recommended replacement for MD5, SHA1, and PHPass hashing.
 *
 * @since 0.7
 *
 * @author Claude Code
 * @ingroup Usermanagement
 */
class BcrypHash implements IHashAlgorithm {
	/**
	 * Create bcrypt hash using password_hash()
	 *
	 * @param string $source
	 * @return string
	 */
	public function hash(string $source): string {
		return password_hash($source, PASSWORD_BCRYPT, array('cost' => 12));
	}

	/**
	 * Validate if given hash matches source using password_verify()
	 *
	 * Timing-safe comparison via password_verify().
	 *
	 * @param string $source
	 * @param string $hash
	 * @return bool
	 */
	public function check(string $source, string $hash): bool {
		return password_verify($source, $hash);
	}
}
