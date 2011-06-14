<?php
class JCSSManagerCompressBaseCommand extends CommandDelegate {
	protected $in_files;
	protected $out_file;

	/**
	 * COnstructor
	 *  
	 * @param $in_files array
	 * @param $out_file string A template string for output naming
	 * @return void
	 */
	public function __construct($in_files, $out_file) {
		$this->in_files = $this->clean_in_files(Arr::force($in_files, false));
		$this->out_file = $out_file;
		parent::__construct(null, false);
	}
	
	/**
	 * Combine in files to groups
	 */
	protected function clean_in_files($in_files) {
		$ret = array();
		foreach($in_files as $key => $value) {
			if (is_numeric($key)) {
				$ret['default'][] = $value;
			}
			else {
				$ret[$key] = $value;
			}
		}
		return $ret;
	}
	
	/**
	 * Executes commands
	 * 
	 * @return Status
	 */
	public function execute() {
		Load::models('jcsscompressedfiles');
		$ret = new Status();
		
		$files_to_unlink = array();
		foreach($this->in_files as $groupname => $in_files) {
			$versioned_file_name = false;
			$out_file = $this->out_file;
			if ($groupname != 'default') {
				$arr = explode('.', $out_file);
				$ext = array_pop($arr);
				$arr[] = $groupname;
				$arr[] = $ext;
				$out_file = implode('.', $arr);
			}
			if (count($in_files)) {
				$ret->merge($this->compress($in_files, $out_file, $files_to_unlink));
			}
			else {
				file_put_contents($out_file, '');
			}
			if ($ret->is_ok()) {
				$dao = JCSSCompressedFiles::update_db($this->get_db_type(), $out_file, $in_files, $ret);
				if ($ret->is_ok()) {
					$versioned_file_name = JCSSManager::make_absolute($dao->get_versioned_filename());
					rename($out_file, $versioned_file_name);
				}
			}

			$gzip_file = false;
			if ($versioned_file_name && $ret->is_ok()) {
				$ret->merge($this->gzip($versioned_file_name, $gzip_file));
			}
		}

		foreach($files_to_unlink as $src) {
			@unlink($src);
		}
		
		return $ret;
	}
	
	/**
	 * COmpress given files 
	 * 
	 * @return Status
	 */
	protected function compress($in_files, $out_file, &$versioned_file_path) {
		$ret = new Status('JCSSManagerCompressBase::compress not implemented');
		return $ret;
	}
	
	/**
	 * Create a gzipped file
	 * 
	 * @return Status
	 */
	protected function gzip($file, &$gzip_file) {
		$ret = new Status();
		
		if (Config::has_feature(ConfigJCSSManager::ALSO_GZIP)) {
			$gzipped_file = $file . '.gz';
			$fp = gzopen($gzipped_file, 'w9');
			if ($fp) {
				gzwrite($fp, file_get_contents($file));
				gzclose($fp);
			}
			else {
				$ret->append('JCSSManager: Could not create gzip-file');
			}
		}
		return $ret;
	}
	
	/**
	 * Returns type of compressed file
	 *   
	 * @return string One of TYPE_X constants
	 */
	protected function get_db_type() {
		throw new Exception('get_db_type() not implemented');
	}
	
}