<?php
class JCSSManagerCompressBaseCommand extends CommandBase {
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
		$ret = new Status();
		
		$versioned_file_name = false;
		foreach($this->in_files as $groupname => $in_files) {
			$out_file = $this->out_file;
			if ($groupname != 'default') {
				$arr = explode('.', $out_file);
				$ext = array_pop($arr);
				$arr[] = $groupname;
				$arr[] = $ext;
				$out_file = implode('.', $arr);  
			}

			$ret->merge($this->compress($in_files, $out_file, $versioned_file_name));
			$gzip_file = false;
			if ($versioned_file_name && $ret->is_ok()) {
				$ret->merge($this->gzip($versioned_file_name, $gzip_file));
			}
		}
		return $ret;
	}
	
	/**
	 * COmpress given files 
	 * 
	 * @return Status
	 */
	protected function compress($in_files, $out_file, &$versioned_file_path) {
		$ret = new Status();
		if (count($in_files) > 0) {
			$sourcefile = $this->concat($in_files, $ret);
			$ret->merge($this->invoke_yui($sourcefile, $out_file));
			if ($ret->is_ok()) {
				Load::models('jcsscompressedfiles');
				$dao = JCSSCompressedFiles::update_db($this->get_db_type(), $out_file, $in_files, $ret);
				if ($ret->is_ok()) {
					$versioned_file_path = JCSSManager::make_absolute($dao->get_versioned_filename());
					rename($out_file, $versioned_file_path);  	
				}				
			}
			@unlink($sourcefile);	
		}
		
		return $ret;
	}
	
	/**
	 * Concat the gicen files into one
	 * 
	 * @param array $arr_files
	 * @param Status $err
	 * @return string Created concatenation file
	 */
	protected function concat($arr_files, Status $err) {
		$tmp_file = tempnam('/tmp', 'jcss');
		if ($tmp_file === false) {
			$err->merge('JCSSManager: Could not create tempfile');
		}
		else {
			$handle = fopen($tmp_file, 'w');
			foreach($arr_files as $file) {
				if (substr($file, 0, 1) !== '/') {
					$file = Config::get_value(Config::URL_ABSPATH) . $file;
				}
				fwrite($handle, $this->get_file_contents($file));
			}			
			fclose($handle);
		}
		return $tmp_file;
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
	
	/**
	 * Return file content
	 * 
	 * @param string $file
	 * @return string
	 */
	protected function get_file_contents($file) {
		return file_get_contents($file);
	}
	
	/**
	 * Invoke YUICOmpressor. TO be overloaded
	 * 
	 * @param string $in_file
	 * @param string $out_file
	 * @return Status 
	 */
	protected function invoke_yui($in_file, $out_file) {
		$ret = new Status();
		return $ret;
	}
	
	/**
	 * Invoke YUICOmpressor
	 * 
	 * @param string $in_file
	 * @param string $out_file
	 * @param array $yui_options
	 * @return Status 
	 */
	protected function run_yui($in_file, $out_file, $type) {
		$module_dir = Load::get_module_dir('jcssmanager');
		$yui_cmd = 'java -jar ' . $module_dir . '3rdparty/yuicompressor/yuicompressor.jar';
		
		$yui_options = array();
		$yui_options['--type'] = $type;
		$yui_options['--charset'] = GyroLocale::get_charset();
		$yui_options['-o'] = $out_file;
		
		$yui_cmd = $yui_cmd . ' ' . Arr::implode(' ', $yui_options, ' ') . ' ' . $in_file;
		
		$output = array();
		$return = 0;
		exec($yui_cmd, $output, $return);

		$ret = new Status();
		if ($return) {
			$ret->append('JCSSManager: Error running yuicompressor.');
			$ret->append(implode(' ', $output));
		}
		
		return $ret;		
	} 
}