<?php
Load::commands('jcssmanager/yui/compress.base');

class JCSSManagerCompressCSSYuiCommand extends JCSSManagerCompressBaseYuiCommand {
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
	 * Invoke YUICOmpressor
	 * 
	 * @param string $in_file
	 * @param string $out_file
	 * @return Status 
	 */
	protected function invoke_yui($in_file, $out_file) {
		$ret = $this->run_yui($in_file, $out_file, 'css');
		return $ret;
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