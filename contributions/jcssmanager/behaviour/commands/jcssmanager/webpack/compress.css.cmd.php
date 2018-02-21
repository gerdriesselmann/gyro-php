<?php
Load::commands('jcssmanager/webpack/compress.base');

class JCSSManagerCompressCSSWebpackCommand extends JCSSManagerCompressBaseWebpackCommand {
	protected $type;

	/**
	 * COnstructor
	 *  
	 * @param $in_files array
	 * @param $out_file string
	 * @return void
	 */
	public function __construct($in_files, $out_file, $type) {
		$this->type = $type;
		parent::__construct($in_files, $out_file);
	}	
	
	/**
	 * Return file content
	 * 
	 * @param string $file
	 * @return string
	 */
	protected function get_file_contents($file) {
		return JCSSManager::transform_css_file($file);
	}

	/**
	 * Returns type of compressed file
	 *   
	 * @return string One of TYPE_X constants
	 */
	protected function get_db_type() {
		return $this->type;
	}	
}