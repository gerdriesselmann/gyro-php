<?php
Load::commands('jcssmanager/yui/compress.base');

class JCSSManagerCompressCSSCsstidyCommand extends JCSSManagerCompressBaseCommand {
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
	 * COmpress given files 
	 * 
	 * @return Status
	 */
	protected function compress($in_files, $out_file, &$files_to_unlink) {
		$ret = new Status();
		if (count($in_files) > 0) {
			$ret->merge($this->run_csstidy(JCSSManager::concat_css_files($in_files), $out_file));
		}
		
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
	
	/**
	 * Invoke CSS Tidy
	 * 
	 * @param strnig $css
	 * @param string $out_file
	 * @return Status 
	 */
	protected function run_csstidy($css, $out_file) {
		$ret = new Status();
		
		$old_lang = GyroLocale::set_locale('C');
		$module_dir = Load::get_module_dir('jcssmanager');
		require_once $module_dir . '3rdparty/csstidy/class.csstidy.php';

		$tidy = new csstidy();
		$tidy->set_cfg('remove_last_;',TRUE);
		//$tidy->set_cfg('merge_selectors', 0);
		$tidy->load_template('highest_compression');
		$tidy->parse($css);

		if (file_put_contents($out_file, $tidy->print->plain()) === false) {
			$ret->append('CSS Tidy: Could not write output file ' . $out_file);
		}		
		GyroLocale::set_locale($old_lang);
		return $ret;		
	} 
}