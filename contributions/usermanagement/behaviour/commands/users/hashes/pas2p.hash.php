<?php
require_once Load::get_module_dir('usermanagement') . '3rdparty/phppass-0.2/PasswordHash.php';

/**
 * Calculates a hash using PHPPass 0.2 in portable mode
 * 
 * @since 0.5.1
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class Pas2pHash implements IHashAlgorithm {
	/**
	 * Return preconfigured instance of PasswordHash
	 * 
	 * @return PasswordHash
	 */
	protected function create_pass2_instance() {
		return new PasswordHash(8, TRUE);		
	}
	
	public function hash($source) {
		$o_hash = $this->create_pass2_instance();
		return $o_hash->HashPassword($source);
	}
	
	public function check($source, $hash) {
		$o_hash = $this->create_pass2_instance();
		return $o_hash->CheckPassword($source, $hash);
	}
}